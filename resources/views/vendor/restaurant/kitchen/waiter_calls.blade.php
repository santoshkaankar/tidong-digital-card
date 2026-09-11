@if(isset($waiterCalls) && count($waiterCalls) > 0)
    <div class="mb-4">
        <h5 class="text-danger fw-bold mb-3">
            <i class="bi bi-bell-fill"></i> Active Waiter Calls ({{ count($waiterCalls) }})
        </h5>
        
        @foreach($waiterCalls as $call)
            <div class="card border-danger mb-2 shadow-sm" id="waiter-call-card-{{ $call->id }}" style="background-color: #fff5f5;">
                <div class="card-body d-flex justify-content-between align-items-center p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="bi bi-person-fill fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-danger">
                                Send Waiter to {{ $call->table->table_number ?? 'Table 1' }}
                            </h6>
                            <small class="text-muted">
                                <i class="bi bi-clock"></i> Requested at {{ \Carbon\Carbon::parse($call->created_at)->format('h:i A') }}
                            </small>
                        </div>
                    </div>
                    
                    <button type="button" onclick="resolveWaiterCall({{ $call->id }})" class="btn btn-danger btn-sm fw-bold px-3 rounded-pill">
                        <i class="bi bi-check-circle-fill"></i> Resolved / OK
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@endif