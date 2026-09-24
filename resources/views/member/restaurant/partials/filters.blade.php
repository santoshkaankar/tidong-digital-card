<div class="card border border-light-subtle rounded-4 shadow-sm p-3 mb-4 bg-white">
    <form action="{{ route('member.restaurant.index') }}" method="GET" class="row g-2 align-items-center">
        
        <input type="hidden" name="type" value="{{ request('type', 'all') }}">

        <!-- Search Input -->
        <div class="col-lg-9 col-md-8">
            <div class="input-group bg-light rounded-3 border">
                <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" name="search" class="form-control bg-transparent border-0 shadow-none py-2 text-dark" 
                       placeholder="Search restaurant name or city..." 
                       value="{{ request('search') }}">
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="col-lg-3 col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary rounded-3 px-4 w-100 fw-medium py-2">
                Search
            </button>
            @if(request('search') || request('type'))
                <a href="{{ route('member.restaurant.index') }}" class="btn btn-light border rounded-3 px-3 py-2 text-muted" title="Reset Filters">
                    <i class="fas fa-rotate-left"></i>
                </a>
            @endif
        </div>
    </form>

    <!-- Filter Pills -->
    <div class="d-flex align-items-center gap-2 pt-3 mt-3 border-top overflow-auto">
        <span class="small text-muted me-2 fw-medium">Filter:</span>
        
        <a href="{{ route('member.restaurant.index', array_merge(request()->query(), ['type' => 'all'])) }}" 
           class="filter-pill {{ (request('type') == 'all' || !request('type')) ? 'active-all' : '' }}">
            <i class="fas fa-border-all me-1"></i> All
        </a>

        <a href="{{ route('member.restaurant.index', array_merge(request()->query(), ['type' => 'veg'])) }}" 
           class="filter-pill {{ request('type') == 'veg' ? 'active-veg' : '' }}">
            <i class="fas fa-leaf text-success me-1"></i> Pure Veg
        </a>

        <a href="{{ route('member.restaurant.index', array_merge(request()->query(), ['type' => 'nonveg'])) }}" 
           class="filter-pill {{ request('type') == 'nonveg' ? 'active-nonveg' : '' }}">
            <i class="fas fa-drumstick-bite text-danger me-1"></i> Non-Veg
        </a>
    </div>
</div>