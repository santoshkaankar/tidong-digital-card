@if(isset($cashRequests) && count($cashRequests) > 0)
    <div class="mb-4">
        <h5 class="text-success fw-bold mb-3">
            <i class="bi bi-wallet-fill"></i> Cash Payment Requests ({{ count($cashRequests) }})
        </h5>
        
        @foreach($cashRequests as $request)
            <div class="card border-success mb-2 shadow-sm" id="cash-card-{{ $request->id }}" style="background-color: #f0fff4;">
                <div class="card-body d-flex justify-content-between align-items-center p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="bi bi-cash-stack fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-success">
                                Collect Cash Payment from {{ $request->table->table_number ?? 'Table 1' }}
                            </h6>
                            <small class="text-muted">
                                <i class="bi bi-clock"></i> Requested at {{ \Carbon\Carbon::parse($request->created_at)->format('h:i A') }}
                            </small>
                        </div>
                    </div>
                    
                    <button type="button" onclick="resolveCashRequest({{ $request->id }})" class="btn btn-success btn-sm fw-bold px-3 rounded-pill">
                        <i class="bi bi-check-circle-fill"></i> Cash Received
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@endif

<script>
function resolveCashRequest(id) {
    if (!id) return;

    var token = document.querySelector('meta[name="csrf-token"]')?.content 
             || document.querySelector('input[name="_token"]')?.value;

    var card = document.getElementById('cash-card-' + id);
    if (card) {
        card.remove();
    }

    fetch('/vendor/restaurant/cash-call/resolve/' + id, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (!data.success) {
            console.error('Resolve Failed:', data.message);
        }
    })
    .catch(function(err) {
        console.error('Cash Request Error:', err);
    });
}
</script>