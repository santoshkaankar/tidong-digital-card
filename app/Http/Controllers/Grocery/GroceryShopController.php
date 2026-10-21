<?php

namespace App\Http\Controllers\Grocery;

use App\Http\Controllers\Controller;
use App\Services\Grocery\GroceryFeatures as Features;
use App\Services\Grocery\GroceryMath as Math;
use App\Services\Grocery\GroceryOnlineStore as Shop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** Public read-only storefront, guest session cart, account-scoped checkout. */
final class GroceryShopController extends Controller
{
    public function stores(Request $request): View
    {
        Features::requireEnabled('online_store');
        foreach (['catalogue', 'pricing', 'inventory'] as $flag) Features::requireEnabled($flag);
        abort_unless(\Illuminate\Support\Facades\Schema::hasTable('grocery_online_orders'), 503);
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
        ]);
        $query = DB::table('branches as br')
            ->join('businesses as b', 'b.id', '=', 'br.business_id')
            ->join('vendors as v', 'v.id', '=', 'b.vendor_id')
            ->where('b.business_type', 'grocery')->where('b.status', 'approved')
            ->where('v.status', 'approved')->where('br.status', 'active')
            ->where('b.is_accepting_orders', true)->where('br.is_accepting_orders', true)
            ->whereNull('b.deleted_at')->whereNull('br.deleted_at')->whereNull('v.deleted_at')
            ->whereNotExists(function ($q): void {
                $q->selectRaw('1')->from('grocery_branch_features as f')
                    ->whereColumn('f.branch_id', 'br.id')
                    ->whereIn('f.feature', ['online_store', 'catalogue', 'pricing', 'inventory'])
                    ->where('f.enabled', false);
            });
        if (filled($filters['search'] ?? null)) {
            $term = '%'.mb_strtolower(trim($filters['search'])).'%';
            $query->where(fn ($q) => $q->whereRaw('LOWER(b.name) LIKE ?', [$term])
                ->orWhereRaw('LOWER(br.name) LIKE ?', [$term]));
        }
        if (filled($filters['city'] ?? null)) {
            $query->whereRaw('LOWER(br.city) LIKE ?', ['%'.mb_strtolower(trim($filters['city'])).'%']);
        }
        $stores = $query->select('br.*', 'b.name as business_name', 'b.id as grocery_business_id')
            ->orderBy('b.name')->orderBy('br.name')->paginate(24)->withQueryString();
        return view('grocery.stores', compact('stores', 'filters'));
    }

    public function catalog(Request $request, int $business, int $branch): View
    {
        [$store, $outlet] = Shop::store($business, $branch);
        $data = $request->validate([
            'q' => ['nullable', 'string', 'max:100'], 'category' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', Rule::in(['featured', 'name', 'price_low', 'price_high'])],
        ]);
        $query = Shop::productQuery($business, $branch)
            ->whereExists(function ($q) use ($business, $branch): void {
                $q->selectRaw('1')->from('grocery_stock_lots as l')
                    ->whereColumn('l.product_id', 'p.id')->where('l.business_id', $business)
                    ->where('l.branch_id', $branch)->where('l.is_sellable', true)
                    ->where('l.available_qty_milli', '>', 0)
                    ->where(fn ($x) => $x->whereNull('l.expiry_date')->orWhere('l.expiry_date', '>=', today()->toDateString()));
            });
        $categoryQuery = clone $query;
        $categories = $categoryQuery->select('p.category')->distinct()->orderBy('p.category')->pluck('category');
        if (filled($data['q'] ?? null)) {
            $term = '%'.mb_strtolower(trim($data['q'])).'%';
            $query->where(fn ($q) => $q->whereRaw('LOWER(p.name) LIKE ?', [$term])
                ->orWhereRaw('LOWER(p.brand) LIKE ?', [$term])
                ->orWhereRaw('LOWER(p.category) LIKE ?', [$term]));
        }
        if (filled($data['category'] ?? null)) $query->where('p.category', $data['category']);
        switch ($data['sort'] ?? 'featured') {
            case 'price_low': $query->orderByRaw('COALESCE(p.online_sale_paise, p.offline_sale_paise) ASC'); break;
            case 'price_high': $query->orderByRaw('COALESCE(p.online_sale_paise, p.offline_sale_paise) DESC'); break;
            case 'name': $query->orderBy('p.name'); break;
            default: $query->orderByDesc('p.is_featured')->orderBy('p.name');
        }
        $products = $query->select('p.*')->paginate(24)->withQueryString();
        $cart = Shop::viewCart($request->session()->get(Shop::CART_KEY), $business, $branch);
        return view('grocery.catalog', compact('store', 'outlet', 'products', 'categories', 'data', 'cart'));
    }

    public function detail(Request $request, int $business, int $branch, int $product): View
    {
        [$store, $outlet] = Shop::store($business, $branch);
        $p = Shop::productQuery($business, $branch)->where('p.id', $product)->firstOrFail();
        $stock = Shop::available($p->id, $business, $branch);
        abort_unless($stock > 0, 404);
        $cart = Shop::viewCart($request->session()->get(Shop::CART_KEY), $business, $branch);
        return view('grocery.product', compact('store', 'outlet', 'p', 'stock', 'cart'));
    }

    public function add(Request $request, int $business, int $branch): RedirectResponse
    {
        Shop::store($business, $branch);
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'min:1'],
            'quantity' => ['required', 'numeric', 'gt:0', 'max:100', 'decimal:0,3'],
        ]);
        $p = Shop::productQuery($business, $branch)->where('p.id', $data['product_id'])->first();
        if (!$p) throw ValidationException::withMessages(['product_id' => 'This product is unavailable.']);
        $qty = Math::milli((string) $data['quantity']);
        if (in_array($p->unit, ['pcs', 'pack'], true) && $qty % 1000 !== 0) {
            throw ValidationException::withMessages(['quantity' => 'Choose whole pieces or packs.']);
        }
        $cart = $request->session()->get(Shop::CART_KEY);
        if ($cart && (($cart['business'] ?? null) !== $business || ($cart['branch'] ?? null) !== $branch)) {
            throw ValidationException::withMessages(['cart' => 'Your cart belongs to another grocery store. Clear it before switching.']);
        }
        $cart ??= ['business' => $business, 'branch' => $branch, 'token' => (string) Str::uuid(), 'items' => []];
        if (count($cart['items']) >= 40 && !array_key_exists($data['product_id'], $cart['items'])) {
            throw ValidationException::withMessages(['cart' => 'Cart limit is 40 different products.']);
        }
        $new = (int) ($cart['items'][$data['product_id']] ?? 0) + $qty;
        if ($new > 100000 || $new > Shop::available((int)$p->id, $business, $branch)) {
            throw ValidationException::withMessages(['quantity' => 'Not enough available stock or quantity exceeds 100.']);
        }
        $cart['items'][$data['product_id']] = $new;
        $request->session()->put(Shop::CART_KEY, $cart);
        return back()->with('success', $p->name.' added to your basket.');
    }

    public function basket(Request $request, int $business, int $branch): View
    {
        [$store, $outlet] = Shop::store($business, $branch);
        $cart = Shop::viewCart($request->session()->get(Shop::CART_KEY), $business, $branch);
        return view('grocery.basket', compact('store', 'outlet', 'cart'));
    }

    public function updateBasket(Request $request, int $business, int $branch): RedirectResponse
    {
        Shop::store($business, $branch);
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'min:1'],
            'quantity' => ['required', 'numeric', 'min:0', 'max:100', 'decimal:0,3'],
        ]);
        $cart = $request->session()->get(Shop::CART_KEY);
        abort_unless(($cart['business'] ?? null) === $business && ($cart['branch'] ?? null) === $branch, 404);
        $productId = (int) $data['product_id'];
        abort_unless(isset($cart['items'][$productId]), 404);
        $quantity = Math::milli((string) $data['quantity']);
        if ($quantity === 0) unset($cart['items'][$productId]);
        else {
            $p = Shop::productQuery($business, $branch)->where('p.id', $productId)->firstOrFail();
            if (in_array($p->unit, ['pcs', 'pack'], true) && $quantity % 1000 !== 0) {
                throw ValidationException::withMessages(['quantity' => 'Whole packs/pieces only.']);
            }
            if ($quantity > Shop::available($productId, $business, $branch)) {
                throw ValidationException::withMessages(['quantity' => 'Not enough stock available.']);
            }
            $cart['items'][$productId] = $quantity;
        }
        if (empty($cart['items'])) $request->session()->forget(Shop::CART_KEY);
        else $request->session()->put(Shop::CART_KEY, $cart);
        return back()->with('success', 'Basket updated.');
    }

    public function clear(Request $request): RedirectResponse
    {
        $request->session()->forget(Shop::CART_KEY);
        return back()->with('success', 'Basket cleared.');
    }

    public function checkout(Request $request, int $business, int $branch): View|RedirectResponse
    {
        [$store, $outlet] = Shop::store($business, $branch);
        Features::requireEnabled('online_orders', $branch);
        $cart = Shop::viewCart($request->session()->get(Shop::CART_KEY), $business, $branch);
        if (empty($cart['lines'])) return redirect()->route('grocery.shop.catalog', [$business, $branch]);
        return view('grocery.checkout', compact('store', 'outlet', 'cart'));
    }

    public function place(Request $request, int $business, int $branch): RedirectResponse
    {
        [$store, $outlet] = Shop::store($business, $branch);
        $data = $request->validate([
            'contact_name' => ['required', 'string', 'min:2', 'max:120'],
            'contact_phone' => ['required', 'string', 'regex:/^[+0-9 ()-]{8,25}$/D'],
            'fulfilment_type' => ['required', Rule::in(['pickup', 'delivery_request'])],
            'address_line' => ['required_if:fulfilment_type,delivery_request', 'nullable', 'string', 'max:250'],
            'city' => ['required_if:fulfilment_type,delivery_request', 'nullable', 'string', 'max:120'],
            'postal_code' => ['required_if:fulfilment_type,delivery_request', 'nullable', 'string', 'max:15'],
            'supply_state' => ['required_if:fulfilment_type,delivery_request', 'nullable', 'string', 'max:100'],
            'customer_note' => ['nullable', 'string', 'max:1000'],
            'checkout_token' => ['required', 'uuid'],
        ]);
        $cart = $request->session()->get(Shop::CART_KEY);
        if (!hash_equals((string) ($cart['token'] ?? ''), $data['checkout_token'])) {
            throw ValidationException::withMessages(['cart' => 'Checkout session changed. Please return to the basket.']);
        }
        $order = Shop::place((int) $request->user()->id, $business, $branch, $cart, $data);
        $request->session()->forget(Shop::CART_KEY);
        return redirect()->route('grocery.shop.orders.show', $order->id)->with('success', 'Order request submitted. No online payment has been charged.');
    }

    public function myOrders(Request $request): View
    {
        $orders = DB::table('grocery_online_orders')->where('customer_user_id', $request->user()->id)
            ->orderByDesc('id')->paginate(20);
        return view('grocery.orders', compact('orders'));
    }

    public function order(Request $request, int $order): View
    {
        $data = DB::table('grocery_online_orders')->where('id', $order)
            ->where('customer_user_id', $request->user()->id)->firstOrFail();
        $items = DB::table('grocery_online_order_items')->where('order_id', $data->id)->get();
        $events = DB::table('grocery_online_order_events')->where('order_id', $data->id)->orderBy('id')->get();
        return view('grocery.order', ['order' => $data, 'items' => $items, 'events' => $events]);
    }

    public function cancel(Request $request, int $order): RedirectResponse
    {
        DB::transaction(function () use ($request, $order): void {
            $current = DB::table('grocery_online_orders')->where('id', $order)
                ->where('customer_user_id', $request->user()->id)->lockForUpdate()->firstOrFail();
            // Customer can cancel a still-pending request even if the admin has closed the shop.
            if ($current->status !== 'pending') {
                throw ValidationException::withMessages(['status' => 'Only pending order requests can be cancelled.']);
            }
            Shop::transition($current, $request->user()->id, 'cancelled', 'Customer cancelled before confirmation.');
        }, 3);
        return back()->with('success', 'Pending order cancelled. Stock has been restored.');
    }
}
