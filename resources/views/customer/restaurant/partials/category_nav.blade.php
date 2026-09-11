<div class="container mt-2">
    <div class="category-scroll">
        <a class="category-badge active" onclick="filterCategory('all', this)">All Items</a>
        @foreach($categories as $category)
            <a class="category-badge" onclick="filterCategory('cat-{{ $category->id }}', this)">{{ $category->name }}</a>
        @endforeach
    </div>
</div>