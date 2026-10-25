{{-- Food-only cashier interface; final pricing and authorization remain server-side. --}}
@php
    $cashierItems = $items->filter(fn ($item) => (bool) $item->is_active && (int) $item->price_paise > 0)->values();
@endphp
<section class="rounded-2xl bg-white p-4 shadow-sm sm:p-6" aria-labelledby="cashier-heading">
    <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 id="cashier-heading" class="text-xl font-bold text-slate-900">Cashier POS</h2>
            <p class="mt-1 text-sm text-slate-500">1. बोलें या आइटम चुनें → 2. टेबल चुनें → 3. ऑर्डर जाँचकर कन्फर्म करें। Final prices are verified by TIDONG.</p>
        </div>
        <a href="{{ route('pos.menu-library.index', $branchRecord->id) }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium hover:bg-slate-50">Manage Menu</a>
    </div>
    <div class="grid gap-5 xl:grid-cols-[minmax(0,1.5fr)_minmax(320px,1fr)]">
        <div class="min-w-0">
            <label for="cashier-search" class="mb-2 block text-sm font-semibold text-slate-700">या मेन्यू से आइटम चुनें</label>
            <input id="cashier-search" type="search" autocomplete="off" placeholder="Search by item name..."
                class="mb-4 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100">
            <div class="mb-4 rounded-xl border bg-indigo-50 p-3"><label for="cashier-voice-text" class="block font-semibold">Step 1 · बोलकर ऑर्डर / Voice Order</label><p class="text-xs">Say or type: 2 Paneer, 3 Roti. Exact active menu names only. Confirm manually before ordering.</p><div class="mt-2 flex gap-2"><button id="cashier-voice-mic" type="button" class="rounded border bg-white px-3 py-2">🎙️ बोलें</button><input id="cashier-voice-text" maxlength="400" class="min-w-0 flex-1 rounded border px-2" aria-label="Spoken order text"><button id="cashier-voice-parse" type="button" class="rounded bg-indigo-700 px-3 text-white">ऑर्डर तैयार करें</button></div><p id="cashier-voice-status" role="status" class="mt-2 text-xs">AI can make mistake. Voice never submits an order.</p></div><div id="cashier-menu" class="grid max-h-[560px] grid-cols-2 gap-3 overflow-y-auto pb-2 sm:grid-cols-3" aria-label="Menu items">
                @forelse ($cashierItems as $item)
                    <button type="button" data-cashier-item
                        data-id="{{ (int) $item->id }}"
                        data-name="{{ $item->name }}"
                        data-price="{{ (int) $item->price_paise }}"
                        class="flex min-h-28 flex-col justify-between rounded-xl border border-slate-200 bg-slate-50 p-3 text-left transition hover:border-indigo-500 hover:bg-indigo-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                        aria-label="Add {{ $item->name }} to order">
                        <span class="break-words font-semibold text-slate-900">{{ $item->name }}</span>
                        <span class="mt-3 font-bold text-indigo-700">₹{{ number_format($item->price_paise / 100, 2) }}</span>
                    </button>
                @empty
                    <p class="col-span-full rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">No active, priced items are available. Add menu items before taking orders.</p>
                @endforelse
            </div>
            <p id="cashier-no-match" class="mt-3 hidden text-sm text-slate-600" role="status">No matching items found.</p>
        </div>
        <div class="min-w-0 rounded-xl border border-slate-200 bg-slate-50 p-4">
            <div class="flex items-center justify-between gap-2">
                <h3 class="text-lg font-bold text-slate-900">Current Order</h3>
                <button type="button" id="cashier-clear" class="text-sm font-semibold text-rose-700 hover:underline">Clear</button>
            </div>
            <p id="cashier-empty" class="mt-6 rounded-lg border border-dashed border-slate-300 p-5 text-center text-sm text-slate-500">Select items from the menu to start.</p>
            <ul id="cashier-cart" class="mt-4 space-y-2" aria-live="polite" aria-label="Selected items"></ul>
            <div class="mt-5 flex items-center justify-between border-t border-slate-200 pt-4">
                <span class="font-semibold text-slate-700">Estimated Total</span>
                <output id="cashier-total" class="text-2xl font-extrabold text-slate-900" aria-live="polite">₹0.00</output>
            </div>
            <p class="mt-1 text-xs text-slate-500">Amount is estimated; final price is calculated by the server.</p>
            <form id="cashier-order-form" method="POST" action="{{ route('pos.orders.store', $branchRecord->id) }}" class="mt-5 space-y-4">
                @csrf
                <div id="cashier-hidden-items"></div>
                <div>
                    <label for="cashier-table" class="mb-1 block text-sm font-semibold">Step 2 · टेबल चुनें / Counter</label>
                    <select id="cashier-table" name="dining_table_id" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-3">
                        <option value="">Counter / Takeaway</option>
                        @foreach ($diningTables as $diningTable)
                            <option value="{{ (int) $diningTable->id }}" @selected(old('dining_table_id') == $diningTable->id)>{{ $diningTable->label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="cashier-note" class="mb-1 block text-sm font-semibold">Kitchen Note (optional)</label>
                    <textarea id="cashier-note" name="customer_note" maxlength="500" rows="2" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-3" placeholder="Instructions for the kitchen">{{ old('customer_note') }}</textarea>
                </div>
                <button id="cashier-place" type="submit" disabled
                    class="w-full rounded-xl bg-indigo-700 px-4 py-3 font-bold text-white hover:bg-indigo-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed disabled:opacity-50">
                    Step 3 · Confirm & Place Order
                </button>
                <p class="text-xs text-slate-600">The existing order workflow records new orders as <strong>unpaid</strong>. A bill request or a button click does not confirm a payment.</p>
            </form>
        </div>
    </div>
</section>
<script>
(() => {
    const menu = document.getElementById('cashier-menu');
    const cartList = document.getElementById('cashier-cart');
    const total = document.getElementById('cashier-total');
    const empty = document.getElementById('cashier-empty');
    const form = document.getElementById('cashier-order-form');
    const hidden = document.getElementById('cashier-hidden-items');
    const submit = document.getElementById('cashier-place');
    const search = document.getElementById('cashier-search');
    const noMatch = document.getElementById('cashier-no-match');
    if (!menu || !form) return;

    const cart = new Map();
    const money = new Intl.NumberFormat('en-IN', {style:'currency', currency:'INR'});
    const makeButton = (text, label, click) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.textContent = text;
        button.setAttribute('aria-label', label);
        button.className = 'min-h-9 min-w-9 rounded-lg border border-slate-300 bg-white px-2 text-sm font-bold hover:bg-indigo-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-600';
        button.addEventListener('click', click);
        return button;
    };
    const draw = () => {
        cartList.replaceChildren();
        hidden.replaceChildren();
        let paise = 0;
        let index = 0;
        for (const item of cart.values()) {
            paise += item.price * item.quantity;
            const row = document.createElement('li');
            row.className = 'flex flex-wrap items-center justify-between gap-2 rounded-lg bg-white p-3 shadow-sm';
            const info = document.createElement('div');
            info.className = 'min-w-0 flex-1';
            const name = document.createElement('p');
            name.className = 'break-words text-sm font-semibold';
            name.textContent = item.name;
            const line = document.createElement('p');
            line.className = 'text-xs text-slate-500';
            line.textContent = money.format(item.price * item.quantity / 100);
            info.append(name, line);
            const controls = document.createElement('div');
            controls.className = 'flex items-center gap-2';
            controls.append(
                makeButton('−', 'Remove one ' + item.name, () => change(item.id, -1))
            );
            const qty = document.createElement('span');
            qty.className = 'w-6 text-center text-sm font-bold';
            qty.textContent = String(item.quantity);
            controls.append(qty, makeButton('+', 'Add one ' + item.name, () => change(item.id, 1)));
            row.append(info, controls);
            cartList.append(row);
            for (const [field, value] of [['menu_item_id', item.id], ['quantity', item.quantity]]) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'items[' + index + '][' + field + ']';
                input.value = String(value);
                hidden.append(input);
            }
            index++;
        }
        total.textContent = money.format(paise / 100);
        empty.classList.toggle('hidden', cart.size > 0);
        submit.disabled = cart.size === 0;
    };
    const change = (id, amount) => {
        const item = cart.get(id);
        if (!item) return;
        const next = item.quantity + amount;
        if (next < 1) cart.delete(id);
        else if (next <= 100) item.quantity = next;
        draw();
    };
    menu.querySelectorAll('[data-cashier-item]').forEach(button => {
        button.addEventListener('click', () => {
            const id = Number(button.dataset.id);
            const price = Number(button.dataset.price);
            const name = button.dataset.name;
            if (!Number.isSafeInteger(id) || id <= 0 || !Number.isSafeInteger(price) || price <= 0) return;
            const current = cart.get(id);
            if (current) {
                if (current.quantity < 100) current.quantity++;
            } else {
                cart.set(id, {id, name, price, quantity:1});
            }
            draw();
        });
    });
    const voiceText = document.getElementById('cashier-voice-text');
    const voiceStatus = document.getElementById('cashier-voice-status');
    const voiceMic = document.getElementById('cashier-voice-mic');
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRecognition) { voiceMic.disabled = true; voiceMic.title = 'Type the order instead'; }
    else voiceMic.addEventListener('click', () => {
        const recognition = new SpeechRecognition();
        recognition.lang = 'hi-IN';
        recognition.onresult = event => { voiceText.value = event.results[0][0].transcript; voiceStatus.textContent = 'Speech captured. Prepare and review draft.'; };
        recognition.onerror = () => { voiceStatus.textContent = 'Speech unavailable; type the order.'; };
        recognition.start();
    });
    document.getElementById('cashier-voice-parse').addEventListener('click', () => {
        const parts = voiceText.value.split(/[,;।]+/).map(x => x.trim()).filter(Boolean);
        if (!parts.length || parts.length > 25) { voiceStatus.textContent = 'Enter 1 to 25 items.'; return; }
        const draft = new Map();
        const buttons = Array.from(menu.querySelectorAll('[data-cashier-item]'));
        for (const part of parts) {
            const m = part.match(/^(\\d{1,3})\\s+(.*)$/u);
            if (!m || Number(m[1]) < 1 || Number(m[1]) > 100) { voiceStatus.textContent = 'Check quantity: ' + part; return; }
            const found = buttons.filter(x => x.dataset.name.trim().toLocaleLowerCase() === m[2].trim().toLocaleLowerCase());
            if (found.length !== 1) { voiceStatus.textContent = 'Exact menu match missing: ' + m[2]; return; }
            const button = found[0], id = Number(button.dataset.id), price = Number(button.dataset.price);
            const quantity = Number(m[1]) + (draft.get(id)?.quantity || 0);
            if (!Number.isSafeInteger(id) || !Number.isSafeInteger(price) || price < 1 || quantity > 100) { voiceStatus.textContent = 'Invalid item or quantity'; return; }
            draft.set(id, {id, price, quantity, name:button.dataset.name});
        }
        cart.clear();
        for (const [id, item] of draft) cart.set(id, item);
        draw();
        voiceStatus.textContent = 'ऑर्डर तैयार है। टेबल, आइटम, मात्रा और कुल राशि जाँचें; फिर Step 3 पर कन्फर्म करें।';
    });
    search.addEventListener('input', () => {
        const q = search.value.trim().toLocaleLowerCase();
        let matches = 0;
        menu.querySelectorAll('[data-cashier-item]').forEach(button => {
            const visible = button.dataset.name.toLocaleLowerCase().includes(q);
            button.classList.toggle('hidden', !visible);
            if (visible) matches++;
        });
        noMatch.classList.toggle('hidden', matches > 0 || q === '');
    });
    document.getElementById('cashier-clear').addEventListener('click', () => {cart.clear(); draw();});
    form.addEventListener('submit', event => {
        if (cart.size === 0) {event.preventDefault(); return;}
        submit.disabled = true;
        submit.textContent = 'Placing Order…';
    });
    draw();
})();
</script>
