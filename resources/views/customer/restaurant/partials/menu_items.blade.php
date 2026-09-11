<div class="container mt-2">
    @foreach($categories as $category)
        <div class="category-section cat-{{ $category->id }}">
            <h6 class="fw-bold text-muted my-2 px-1">{{ $category->name }}</h6>
            @foreach($category->items as $item)
                @php
                    $displayName = $item->globalItem->item_name ?? $item->name ?? 'Food Item';
                    $foodType = strtolower($item->globalItem->food_type ?? 'veg');
                @endphp
                <div class="card food-card p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="food-type-icon {{ $foodType == 'non-veg' || $foodType == 'non_veg' ? 'type-nonveg' : 'type-veg' }}"></span>
                            <strong class="fs-6 text-dark">{{ $displayName }}</strong>
                            <div class="mt-1">
                                <span class="fw-bold text-dark">₹{{ number_format($item->price, 2) }}</span>
                                @if($item->mrp > $item->price)
                                    <small class="text-muted text-decoration-line-through ms-1">₹{{ number_format($item->mrp, 2) }}</small>
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="qty-btn-group" id="btn-group-{{ $item->id }}">
                                <button onclick="updateCart({{ $item->id }}, '{{ addslashes($displayName) }}', {{ $item->price }}, -1)">-</button>
                                <span id="qty-{{ $item->id }}">0</span>
                                <button onclick="updateCart({{ $item->id }}, '{{ addslashes($displayName) }}', {{ $item->price }}, 1)">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>