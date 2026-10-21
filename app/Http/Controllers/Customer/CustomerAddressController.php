<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\CustomerAddress;
use App\Services\Customer\CustomerAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CustomerAddressController extends Controller
{
    public function index(Request $request)
    {
        return view('customer.addresses.index', [
            'addresses' => $request->user()->customerAddresses()
                ->orderByDesc('is_default')->orderBy('id')->paginate(10),
        ]);
    }

    public function create()
    {
        return view('customer.addresses.form', ['address' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($request, $data) {
            $user = $request->user();
            $user->newQuery()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $addresses = $user->customerAddresses();
            $count = $addresses->count();
            if ($count >= (int) config('tidong_customer.max_addresses', 10)) {
                throw ValidationException::withMessages(['address' => 'The address book is full.']);
            }

            $makeDefault = (bool) $data['is_default'] || $count === 0;
            if ($makeDefault) {
                $user->customerAddresses()->where('is_default', true)->update(['is_default' => false]);
            }
            $address = $user->customerAddresses()->create(array_merge($data, ['is_default' => $makeDefault]));
            CustomerAudit::record($request, 'address_created', $address->id);
        });

        return redirect()->route('customer.addresses.index')->with('status', 'Address added.');
    }

    public function edit(Request $request, int $address)
    {
        return view('customer.addresses.form', ['address' => $this->owned($request, $address)]);
    }

    public function update(Request $request, int $address)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($request, $address, $data) {
            $user = $request->user();
            $user->newQuery()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $record = $this->owned($request, $address);
            $makeDefault = (bool) $data['is_default'];
            // Never leave an existing primary address without a replacement.
            if ($record->is_default && ! $makeDefault) {
                $makeDefault = true;
            }
            if ($makeDefault) {
                $user->customerAddresses()->where('is_default', true)->where('id', '!=', $record->id)
                    ->update(['is_default' => false]);
            }
            $record->update(array_merge($data, ['is_default' => $makeDefault]));
            CustomerAudit::record($request, 'address_updated', $record->id);
        });

        return redirect()->route('customer.addresses.index')->with('status', 'Address updated.');
    }

    public function destroy(Request $request, int $address)
    {
        DB::transaction(function () use ($request, $address) {
            $user = $request->user();
            $user->newQuery()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $record = $this->owned($request, $address);
            $oldId = $record->id;
            $wasDefault = $record->is_default;
            $record->delete();
            if ($wasDefault) {
                $next = $user->customerAddresses()->orderBy('id')->first();
                if ($next) {
                    $next->update(['is_default' => true]);
                }
            }
            CustomerAudit::record($request, 'address_deleted', $oldId);
        });

        return redirect()->route('customer.addresses.index')->with('status', 'Address removed.');
    }

    public function makeDefault(Request $request, int $address)
    {
        DB::transaction(function () use ($request, $address) {
            $user = $request->user();
            $user->newQuery()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $record = $this->owned($request, $address);
            $user->customerAddresses()->where('is_default', true)->update(['is_default' => false]);
            $record->update(['is_default' => true]);
            CustomerAudit::record($request, 'address_default_changed', $record->id);
        });

        return back()->with('status', 'Default address changed.');
    }

    private function owned(Request $request, int $id): CustomerAddress
    {
        // Every read/write resolves by the logged-in customer; never unscoped binding.
        return $request->user()->customerAddresses()->whereKey($id)->firstOrFail();
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'label' => ['required', 'in:Home,Work,Other'],
            'recipient_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:25', 'regex:/^[0-9+() \-]{7,25}$/'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9][A-Za-z0-9 \-]{2,19}$/'],
            'country_code' => ['required', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
            'is_default' => ['required', 'boolean'],
        ]);
    }
}
