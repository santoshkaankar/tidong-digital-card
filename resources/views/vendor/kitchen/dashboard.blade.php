@extends('layouts.app')

@section('content')
<style>
    /* Mobile-First Custom Styles */
    .kitchen-header-card {
        border-radius: 12px;
        background: linear-gradient(135deg, #1e293b, #0f172a);
    }
    .order-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        transition: all 0.2s ease;
    }
    .order-card-header {
        background-color: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding: 12px 16px;
        border-radius: 12px 12px 0 0;
    }
    .badge-table {
        font-size: 0.95rem;
        padding: 6px 12px;
        border-radius: 8px;
    }
    .btn-mobile-touch {
        padding: 8px 12px;
        font-size: 0.9rem;
        border-radius: 8px;
    }
    @media (max-width: 576px) {
        .order-card-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 8px;
        }
        .header-right-side {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px dashed #e2e8f0;
            padding-top: 8px;
            margin-top: 4px;
        }
    }
</style>

<div class="container-fluid py-2 px-2 px-md-3">
    
    <!-- Top Sticky Sound Bar -->
    <div class="card kitchen-header-card text-white shadow-sm mb-3 sticky-top" style="top: 5px; z-index: 1020;">
        <div class="card-body p-2 p-md-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center">
                <span class="fs-4 me-2">🔔</span>
                <div>
                    <h6 class="mb-0 fw-bold text-warning">Kitchen Live Sound Bar</h6>
                    <small class="text-light d-none d-sm-inline" id="statusText">लाइव अलर्ट सक्रिय है। नया ऑर्डर आते ही रिंग बजेगी।</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 ms-auto">
                <button type="button" class="btn btn-sm btn-outline-warning btn-mobile-touch" onclick="speakOrderText('टेस्ट अलर्ट: टेबल नंबर 1, 1 पास्ता का ऑर्डर प्राप्त हुआ है।')">
                    🔊 Test
                </button>
                <a href="{{ route('vendor.dashboard') }}" class="btn btn-sm btn-light btn-mobile-touch">Dashboard</a>
            </div>
        </div>
    </div>

    <audio id="kitchenBell" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

    <!-- Orders Summary List Header -->
    <div class="d-flex justify-content-between align-items-center mb-2 px-1">
        <h6 class="fw-bold mb-0 text-dark">Running Orders ({{ $runningOrders->count() }})</h6>
        <small class="text-muted" style="font-size: 0.8rem;">टैप करके विवरण देखें</small>
    </div>

    <!-- Mobile-Optimized Orders List -->
    <div class="shadow-sm">
        @forelse($runningOrders as $order)
        @php
            $itemDetails = [];
            foreach($order->orderItems as $item) {
                $itemDetails[] = $item->quantity . ' ' . $item->item_name;
            }
            $itemsText = implode(', ', $itemDetails);
            $voiceSpeechText = "नया ऑर्डर आया है। " . $order->table_or_room . "। इसमें शामिल है: " . $itemsText;
        @endphp

        <div class="card mb-3 order-card" id="order-row-{{ $order->id }}">
            
            <!-- Mobile Header Layout -->
            <div class="order-card-header d-flex justify-content-between align-items-center" 
                 onclick="toggleOrderDetails({{ $order->id }})" 
                 style="cursor: pointer;">
                 
                <!-- Left Details -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-primary badge-table">📍 {{ $order->table_or_room }}</span>
                    <span class="fw-bold text-dark fs-6">#{{ $order->id }}</span>
                    @if($order->status == 'accepted')
                        <span class="badge bg-info text-dark">Accepted</span>
                    @elseif($order->status == 'ready')
                        <span class="badge bg-warning text-dark">Ready</span>
                    @else
                        <span class="badge bg-secondary">Pending</span>
                    @endif
                </div>

                <!-- Right Details -->
                <div class="header-right-side">
                    <button type="button" class="btn btn-sm btn-warning fw-bold btn-mobile-touch me-2" 
                            onclick="event.stopPropagation(); triggerOrderVoiceAlert('{{ addslashes($voiceSpeechText) }}');">
                        🔔 बोलकर सुनाएँ
                    </button>
                    <div>
                        <span class="fw-bold text-success fs-6 me-2">₹{{ $order->total_amount }}</span>
                        <span id="arrow-{{ $order->id }}" class="fw-bold text-muted">▼</span>
                    </div>
                </div>
            </div>

            <!-- Body Details -->
            <div id="details-{{ $order->id }}" class="card-body bg-light p-3" style="display: none; border-top: 1px solid #e2e8f0;">
                <div class="row g-3">
                    
                    <!-- Items List -->
                    <div class="col-12 col-md-7">
                        <h6 class="fw-bold border-bottom pb-2 text-primary">Order Items</h6>
                        <ul class="list-group mb-2 shadow-sm rounded-3">
                            @foreach($order->orderItems as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                <div>
                                    <span class="fw-bold text-dark">{{ $item->item_name }}</span>
                                    <div class="small text-muted">₹{{ $item->price }} × {{ $item->quantity }}</div>
                                </div>
                                <span class="badge bg-dark rounded-pill fs-6 px-3 py-2">x{{ $item->quantity }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Action Controls -->
                    <div class="col-12 col-md-5">
                        <div class="card p-3 border-0 bg-white shadow-sm rounded-3">
                            <h6 class="fw-bold border-bottom pb-2 text-secondary">Complete Order</h6>
                            <form action="{{ route('vendor.public.order.complete', $order->id) }}" method="POST" class="mt-1">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label small fw-bold text-muted mb-1">Payment Mode</label>
                                    <select name="payment_mode" class="form-select form-select-md" required>
                                        <option value="cash">Cash Payment</option>
                                        <option value="online">Online / UPI</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success btn-md w-100 fw-bold rounded-3 py-2 mt-2">
                                    Complete & Bill
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>

        </div>
        @empty
        <div class="card p-4 text-center text-muted shadow-sm rounded-3">
            <h6 class="mb-0">कोई रनिंग आर्डर उपलब्ध नहीं है।</h6>
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

function speakOrderText(textMessage) {
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
        
        let utterance = new SpeechSynthesisUtterance(textMessage);
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
    }, 1500);
}

if ('speechSynthesis' in window) {
    window.speechSynthesis.onvoiceschanged = () => {
        window.speechSynthesis.getVoices();
    };
}
</script>
@endsection