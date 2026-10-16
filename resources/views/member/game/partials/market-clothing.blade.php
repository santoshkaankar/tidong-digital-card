<div class="row g-3 market-item-list" style="max-height: 420px; overflow-y: auto;">
    @foreach(($marketItems ?? \App\Models\Member\MarketItem::all())->where('category', 'clothing') as $item)
    <div class="col-md-4 market-card-item" data-name="{{ strtolower($item->name) }}">
        <div class="card bg-secondary text-white text-center p-3 border-0 rounded-3 h-100">
            <h6>👕 {{ $item->name }}</h6>
            <p class="small text-warning fw-bold mb-2">{{ $item->price }} T-Coins</p>
            <button class="btn btn-sm btn-warning fw-bold rounded-pill mt-auto" onclick="buyItem('{{ $item->name }}', {{ $item->price }}, '{{ $item->item_type }}', '{{ $item->color_hex }}')">Buy & Wear</button>
        </div>
    </div>
    @endforeach
</div>