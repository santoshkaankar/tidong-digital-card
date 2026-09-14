<script>
    function openQrModal(restName, tableName, address, targetUrl) {
        document.getElementById('qrRestName').innerText = restName || 'Restaurant Name';
        document.getElementById('qrModalTitle').innerText = tableName || 'Table No';
        document.getElementById('qrRestAddress').innerText = address || '';
        document.getElementById('qrModalImg').src = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(targetUrl)}`;
        
        new bootstrap.Modal(document.getElementById('qrViewModal')).show();
    }

    function openEditModal(id, tableName, selectedItems) {
        @if(Route::has('vendor.restaurant.menu-card.update'))
            let updateUrl = "{{ route('vendor.restaurant.menu-card.update', ':id') }}".replace(':id', id);
        @elseif(Route::has('vendor.restaurant.catalogs.update'))
            let updateUrl = "{{ route('vendor.restaurant.catalogs.update', ':id') }}".replace(':id', id);
        @else
            let updateUrl = "/vendor/restaurant/menu-card/" + id;
        @endif

        document.getElementById('editCatalogForm').action = updateUrl;
        document.getElementById('edit_table_name').value = tableName || '';

        const selectedSet = new Set((selectedItems || []).map(String));
        document.querySelectorAll('.edit-item-checkbox').forEach(cb => {
            cb.checked = selectedSet.has(String(cb.value));
        });

        new bootstrap.Modal(document.getElementById('editCatalogForm').closest('.modal')).show();
    }
</script>