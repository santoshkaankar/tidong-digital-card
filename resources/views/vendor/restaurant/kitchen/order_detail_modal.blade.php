<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-labelledby="orderDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="orderDetailModalLabel">
                    <i class="bi bi-receipt text-primary me-2"></i>Order Details <span id="modal-order-number" class="text-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fs-6 me-2" id="modal-table-number">Table --</span>
                        <span class="badge bg-info px-3 py-2 rounded-pill fs-6 text-uppercase" id="modal-order-status">--</span>
                    </div>
                    <div class="text-muted small" id="modal-order-time">
                        <i class="bi bi-clock me-1"></i>--:-- --
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Item Name</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody id="modal-order-items-body">
                            <!-- Dynamic Content Rendered Via Script -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>