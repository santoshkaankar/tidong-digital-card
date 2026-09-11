<script>
    function openOrderDetailModal(orderId) {
        if (!orderId) return;

        fetch('/vendor/restaurant/kitchen-orders/' + orderId + '/details', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success && data.order) {
                var order = data.order;

                document.getElementById('modal-order-number').innerText = '#' + (order.order_number || 'ORD-' + order.id);
                
                var rawTable = (order.table && order.table.table_number) ? order.table.table_number : (order.table_id || 'N/A');
                var cleanTable = rawTable.toString().replace(/^table\s*#?/i, '').trim();
                document.getElementById('modal-table-number').innerText = 'Table ' + cleanTable;

                document.getElementById('modal-order-status').innerText = (order.status || 'Pending').toUpperCase();
                document.getElementById('modal-order-time').innerText = order.created_at_formatted || '';

                var tbody = document.getElementById('modal-order-items-body');
                tbody.innerHTML = '';

                if (order.items && order.items.length > 0) {
                    order.items.forEach(function(item, index) {
                        var itemName = 'Item #' + item.id;

                        if (item.display_name) {
                            itemName = item.display_name;
                        } else if (item.item_name) {
                            itemName = item.item_name;
                        } else if (item.name) {
                            itemName = item.name;
                        } else if (item.restaurant_item) {
                            if (item.restaurant_item.global_item && item.restaurant_item.global_item.name) {
                                itemName = item.restaurant_item.global_item.name;
                            } else if (item.restaurant_item.name) {
                                itemName = item.restaurant_item.name;
                            } else if (item.restaurant_item.title) {
                                itemName = item.restaurant_item.title;
                            }
                        }

                        var qty = item.quantity || 1;
                        var price = parseFloat(item.price || 0);
                        var total = (qty * price).toFixed(2);

                        var row = '<tr>' +
                            '<td>' + (index + 1) + '</td>' +
                            '<td class="fw-semibold text-start">' + itemName + '</td>' +
                            '<td class="text-center"><span class="badge bg-secondary rounded-pill px-2 py-1">' + qty + '</span></td>' +
                            '<td class="text-end">₹' + price.toFixed(2) + '</td>' +
                            '<td class="text-end fw-bold">₹' + total + '</td>' +
                            '</tr>';
                        
                        tbody.innerHTML += row;
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">No items found</td></tr>';
                }

                var modalElement = document.getElementById('orderDetailModal');
                var modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        })
        .catch(function(error) {
            console.error('Modal Load Error:', error);
        });
    }
</script>