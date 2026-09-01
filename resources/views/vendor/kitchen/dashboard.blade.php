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
                <button type="button" class="btn btn-sm btn-outline-warning" onclick="speakOrderText('टेस्ट अलर्ट: टेबल नंबर 1, 2 चाय और 1 समोसा का नया ऑर्डर प्राप्त हुआ है।')">Test Voice 🔊</button>
                <a href="{{ route('vendor.dashboard') }}" class="btn btn-sm btn-light">Dashboard</a>
            </div>
        </div>
    </div>

    <audio id="kitchenBell" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

    <!-- Orders Summary List Header -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="fw-bold mb-0">Running Orders ({{ $runningOrders->count() }})</h5>
        <small class="text-muted">ऑर्डर विवरण देखने के लिए कार्ड पर क्लिक करें</small>
    </div>

    <!-- Manual JS Toggle Cards List (Conflict-Free) -->
    <div class="shadow-sm">
        @forelse($runningOrders as $order)
        @php
            // Clear Speech Sentence Formation (Direct Quantity + Item Name)
            $itemDetails = [];
            foreach($order->orderItems as $item) {
                // Direct quantity aur item name (e.g., "1 पास्ता")
                $itemDetails[] = $item->quantity . ' ' . $item->item_name;
            }
            $itemsText = implode(', ', $itemDetails);
            
            // Sentence layout with natural pauses
            $voiceSpeechText = "नया ऑर्डर आया है। " . $order->table_or_room . "। इसमें शामिल है: " . $itemsText;
        @endphp

        <div class="card mb-2 border rounded overflow-hidden" id="order-row-{{ $order->id }}">
            
            <!-- Custom Clickable Header -->
            <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center" 
                 onclick="toggleOrderDetails({{ $order->id }})" 
                 style="cursor: pointer; user-select: none;">
                 
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary fs-6 px-2 py-1">📍 {{ $order->table_or_room }}</span>
                    <span class="fw-bold text-dark">Order #{{ $order->id }}</span>
                    @if($order->status == 'accepted')
                        <span class="badge bg-info text-dark">Accepted</span>
                    @elseif($order->status == 'ready')
                        <span class="badge bg-warning text-dark">Ready / Packed</span>
                    @else
                        <span class="badge bg-secondary">Pending</span>
                    @endif
                </div>

                <div class="text-end d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-warning py-0 px-2 fw-bold" 
                            onclick="event.stopPropagation(); triggerOrderVoiceAlert('{{ addslashes($voiceSpeechText) }}');" 
                            title="Play Sound & Read Order Again">
                        🔔 बोलकर सुनाएँ
                    </button>
                    <span class="fw-bold text-success fs-6">₹{{ $order->total_amount }}</span>
                    <small class="text-muted d-none d-sm-inline">{{ $order->updated_at ? $order->updated_at->diffForHumans() : '' }}</small>
                    <span id="arrow-{{ $order->id }}" class="ms-1 fw-bold">▼</span>
                </div>
            </div>

            <!-- Custom Details Body -->
            <div id="details-{{ $order->id }}" class="card-body bg-light p-3" style="display: none;">
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
                                <h6 class="fw-bold border-bottom pb-1">Order Details</h6>
                                <p class="small text-muted mb-2">
                                    <strong>Table / Location:</strong> {{ $order->table_or_room }}<br>
                                    <strong>Time:</strong> {{ $order->created_at ? $order->created_at->format('h:i A, d M') : '' }}
                                </p>
                            </div>

                            <div>
                                <form action="{{ route('vendor.public.order.complete', $order->id) }}" method="POST" class="mt-2 pt-2 border-top">
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
        @empty
        <div class="card p-5 text-center text-muted shadow-sm">
            <h5>कोई रनिंग आर्डर उपलब्ध नहीं है।</h5>
        </div>
        @endforelse
    </div>

</div>

<script>
function toggleOrderDetails(orderId) {
    let detailsDiv = document.getElementById('details-' + orderId);
    let arrowSpan = document.getElementById('arrow-' + orderId);
    
    if (detailsDiv.style.display === 'none' || detailsDiv.style.display === '') {
        detailsDiv.style.display = 'block';
        if(arrowSpan) arrowSpan.innerText = '▲';
    } else {
        detailsDiv.style.display = 'none';
        if(arrowSpan) arrowSpan.innerText = '▼';
    }
}

function playBellSound() {
    let audio = document.getElementById('kitchenBell');
    if(audio) {
        audio.currentTime = 0;
        audio.play().catch(e => console.log('Autoplay blocked'));
    }
}

// Improved Speech Synthesis Function without 'piece'
function speakOrderText(textMessage) {
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
        
        let utterance = new SpeechSynthesisUtterance(textMessage);
        
        // Natural speed and pitch for clear Hindi audio
        utterance.rate = 0.82;
        utterance.pitch = 1.0;
        
        let voices = window.speechSynthesis.getVoices();
        let selectedVoice = voices.find(v => v.lang.includes('hi') || v.lang.includes('hi-IN')) ||
                            voices.find(v => v.lang.includes('en-IN'));
                            
        if (selectedVoice) {
            utterance.voice = selectedVoice;
        } else {
            utterance.lang = 'hi-IN';
        }
        
        window.speechSynthesis.speak(utterance);
    }
}

function triggerOrderVoiceAlert(voiceText) {
    playBellSound();
    setTimeout(() => {
        speakOrderText(voiceText);
    }, 1500); // 1.5 seconds delay after bell
}

// Pre-load voices on page load
if ('speechSynthesis' in window) {
    window.speechSynthesis.onvoiceschanged = () => {
        window.speechSynthesis.getVoices();
    };
}
</script>
@endsection