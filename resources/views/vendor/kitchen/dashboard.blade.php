@extends('layouts.app')

@section('content')
<div class="container-fluid py-2 px-3">
    
    <!-- Top Sticky Sound & Notification Screen Bar -->
    <div class="card bg-dark text-white shadow-sm mb-3 sticky-top" style="top: 10px; z-index: 1020;">
        <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center">
                <span class="fs-5 me-2">🔔</span>
                <div>
                    <h6 class="mb-0 fw-bold text-warning">Kitchen Live Sound & Alert Bar</h6>
                    <small class="text-light" id="statusText">लाइव अलर्ट सक्रिय है। नया या अपडेटेड ऑर्डर आते ही रिंग बजेगी और बोलकर सुनाया जाएगा।</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 mt-1 mt-md-0">
                <button class="btn btn-sm btn-outline-warning" onclick="testSoundAndVoice()">Test Sound 🔊</button>
                <a href="{{ route('vendor.dashboard') }}" class="btn btn-sm btn-light">Dashboard</a>
            </div>
        </div>
        <div id="liveAlertContainer"></div>
    </div>

    <audio id="kitchenBell" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

    <!-- Orders Summary List Header -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="fw-bold mb-0">Running Orders ({{ $runningOrders->count() }})</h5>
        <small class="text-muted">ऑर्डर विवरण और एक्शन बटन देखने के लिए पट्टी पर क्लिक करें</small>
    </div>

    <!-- Minimal Clickable Orders List -->
    <div class="accordion shadow-sm" id="ordersAccordion">
        @forelse($runningOrders as $order)
        <div class="accordion-item mb-2 border rounded overflow-hidden" id="order-row-{{ $order->id }}">
            
            <!-- Accordion Header (Minimal Info) -->
            <h2 class="accordion-header" id="heading-{{ $order->id }}">
                <button class="accordion-button collapsed py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $order->id }}" aria-expanded="false" aria-controls="collapse-{{ $order->id }}">
                    <div class="d-flex justify-content-between align-items-center w-100 me-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary fs-6 px-2 py-1">📍 {{ $order->table_or_room }}</span>
                            <span class="fw-bold text-dark">Order #{{ $order->id }}</span>
                            @if($order->order_status == 'accepted')
                                <span class="badge bg-info text-dark">Accepted</span>
                            @elseif($order->order_status == 'ready')
                                <span class="badge bg-warning text-dark">Ready / Packed</span>
                            @else
                                <span class="badge bg-secondary">Pending</span>
                            @endif
                        </div>
                        <div class="text-end">
                            <span class="fw-bold text-success fs-6 me-2">₹{{ $order->total_amount }}</span>
                            <small class="text-muted d-none d-sm-inline">{{ $order->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </button>
            </h2>

            <!-- Accordion Body (Full Details & Actions) -->
            <div id="collapse-{{ $order->id }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $order->id }}" data-bs-parent="#ordersAccordion">
                <div class="accordion-body bg-light p-3">
                    <div class="row g-3">
                        
                        <!-- Items List -->
                        <div class="col-md-7">
                            <h6 class="fw-bold border-bottom pb-1">Order Items</h6>
                            <ul class="list-group mb-2">
                                @foreach($order->orderItems as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    <div>
                                        <span class="fw-bold">{{ $item->item_name }}</span>
                                        <div class="small text-muted">₹{{ $item->price }} x {{ $item->quantity }}</div>
                                    </div>
                                    <span class="badge bg-dark rounded-pill fs-6">x{{ $item->quantity }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Action Controls -->
                        <div class="col-md-5">
                            <div class="card p-3 border-0 bg-white shadow-sm h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <h6 class="fw-bold border-bottom pb-1">Order Actions</h6>
                                    <p class="small text-muted mb-2">
                                        <strong>Location:</strong> {{ $order->table_or_room }}<br>
                                        <strong>Time:</strong> {{ $order->created_at->format('h:i A, d M') }}
                                    </p>
                                </div>

                                <div>
                                    <!-- Status Update Actions -->
                                    <div class="d-flex gap-2 mb-2">
                                        <button class="btn btn-sm btn-outline-success w-50" onclick="updateStatus({{ $order->id }}, 'accepted')">Accept</button>
                                        <button class="btn btn-sm btn-outline-warning w-50" onclick="updateStatus({{ $order->id }}, 'ready')">Packed / Ready</button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="updateStatus({{ $order->id }}, 'cancelled')">Reject</button>
                                    </div>

                                    <!-- Complete & Bill Form -->
                                    <form action="{{ route('vendor.public.order.complete', $order->id) }}" method="POST" target="_blank" class="mt-2 pt-2 border-top">
                                        @csrf
                                        <label class="form-label small fw-bold mb-1">Payment & Complete Bill</label>
                                        <div class="input-group input-group-sm">
                                            <select name="payment_mode" class="form-select" required>
                                                <option value="cash">Cash Payment</option>
                                                <option value="online">Online / UPI</option>
                                            </select>
                                            <button type="submit" class="btn btn-success fw-bold">Complete & Bill</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
        @empty
        <div class="card p-5 text-center text-muted shadow-sm">
            <h5>कोई रनिंग आर्डर उपलब्ध नहीं है।</h5>
        </div>
        @endforelse
    </div>

</div>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    // 1. ऑडियो बेल प्ले करने का फ़ंक्शन
    function playSound(callback) {
        var audio = document.getElementById('kitchenBell');
        if (audio) {
            audio.currentTime = 0;
            var playPromise = audio.play();
            if (playPromise !== undefined) {
                playPromise.then(() => {
                    if (callback) {
                        setTimeout(callback, 1200); // बेल ख़त्म होने के बाद वॉइस कॉल होगी
                    }
                }).catch(e => {
                    console.log("Audio blocked by browser", e);
                    if (callback) callback();
                });
            }
        } else if (callback) {
            callback();
        }
    }

    // 2. ऑर्डर डिटेल हिंदी में बोलकर सुनाने का फ़ंक्शन (SpeechSynthesis API)
    function speakOrderDetails(location, details) {
        playSound(function() {
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel(); // पुरानी कोई आवाज़ चल रही हो तो उसे रोके

                let text = "नया ऑर्डर आया है। ";
                if (location) {
                    text += "स्थान: " + location + "। ";
                }
                if (details) {
                    text += details;
                }

                let msg = new SpeechSynthesisUtterance(text);
                msg.lang = 'hi-IN'; // हिंदी आवाज़
                msg.rate = 0.9;     // बोलने की स्पीड
                msg.pitch = 1.0;

                window.speechSynthesis.speak(msg);
            }
        });
    }

    // 3. टेस्ट बटन फ़ंक्शन
    function testSoundAndVoice() {
        speakOrderDetails("टेबल नंबर 5", "ऑर्डर में 2 हाफ चाउमीन और 1 कोल्ड ड्रिंक शामिल है");
    }

    // 4. Pusher Realtime Event Listener
    var pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
        cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
        forceTLS: true
    });

    var channel = pusher.subscribe('kitchen-channel.{{ auth()->id() }}');
    channel.bind('order.updated', function(data) {
        
        // रियल-टाइम अलर्ट आने पर बेल बजेगी और जानकारी बोली जाएगी
        speakOrderDetails(data.location || '', data.message || '');

        document.getElementById('liveAlertContainer').innerHTML = `
            <div class="alert alert-warning mb-0 rounded-0 py-2 px-3 fw-bold text-dark d-flex justify-content-between align-items-center">
                <span>🚨 Alert for ${data.location || 'Kitchen'}: ${data.message || 'New Update'}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        // 4 सेकंड बाद पेज रीलोड होगा ताकि आवाज़ पूरी तरह सुनाई दे दे
        setTimeout(() => { location.reload(); }, 4000);
    });

    function updateStatus(orderId, status) {
        alert("Order #" + orderId + " marked as " + status);
    }
</script>
@endsection