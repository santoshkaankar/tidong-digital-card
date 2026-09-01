<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="m-0 fw-bold">स्मार्ट कैटलॉग</h4>
    <div>
        <a href="{{ route('vendor.dashboard') }}" class="btn btn-outline-secondary me-2">
            <i class="fas fa-arrow-left me-1"></i> डैशबोर्ड
        </a>
        <a href="{{ route('vendor.inventory.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-boxes me-1"></i> इन्वेंट्री
        </a>
    </div>
</div>
@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Catalog Create Form -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fas fa-plus-circle me-1"></i> नया कैटलॉग बनाएं
                </div>
                <div class="card-body">
                    <form id="catalogForm" action="{{ route('vendor.catalogs.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="catalog_id" id="catalog_id" value="">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">एड्रेस / टेबल / रूम नंबर <span class="text-danger">*</span></label>
                            <input type="text" name="address" id="addressInput" class="form-control" placeholder="उदा. Table 01 या Room 102" required>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-semibold mb-0">आइटम्स चुनें</label>
                                <a href="{{ route('vendor.inventory.index') }}" class="btn btn-sm btn-link text-decoration-none p-0">
                                    <i class="fas fa-edit me-1"></i>इन्वेंट्री / आइटम सही करें
                                </a>
                            </div>
                            
                            <div class="border rounded p-2" style="max-height: 350px; overflow-y: auto; background-color: #fdfdfd;">
                                @forelse($menuItems as $item)
                                    @php
                                        $imgPath = $item->image ?? $item->item_image ?? $item->photo ?? null;
                                        $imgUrl = $imgPath ? (Str::startsWith($imgPath, ['http://', 'https://']) ? $imgPath : asset('storage/' . ltrim($imgPath, '/'))) : 'https://via.placeholder.com/60';
                                    @endphp
                                    <div class="border-bottom p-2 mb-2 bg-white rounded shadow-sm">
                                        <div class="form-check d-flex align-items-start gap-2">
                                            <input class="form-check-input mt-2 item-checkbox" type="checkbox" name="items[]" value="{{ $item->id }}" id="item_{{ $item->id }}" checked>
                                            
                                            <!-- Item Image -->
                                            <img src="{{ $imgUrl }}" 
                                                 alt="{{ $item->item_name }}" 
                                                 class="rounded border" 
                                                 style="width: 50px; height: 50px; object-fit: cover;"
                                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/60';">
                                            
                                            <!-- Item Info -->
                                            <div class="flex-grow-1">
                                                <label class="form-check-label d-block fw-bold text-dark" for="item_{{ $item->id }}">
                                                    {{ $item->item_name }}
                                                </label>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="fw-bold text-success">₹{{ number_format($item->price ?? $item->sale_price ?? 0, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted small mb-0 text-center py-3">कोई उपलब्ध आइटम नहीं मिला।</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" id="submitBtn" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-save me-1"></i> सेव करें
                            </button>
                            <button type="button" onclick="resetForm()" id="cancelEditBtn" class="btn btn-outline-danger d-none">
                                रद्द करें
                            </button>
                            <button type="button" onclick="copyAndNext()" class="btn btn-outline-secondary">
                                <i class="fas fa-copy me-1"></i> Copy To Next
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Catalog List -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white fw-bold d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-list me-1"></i> आपके कैटलॉग्स</span>
                    <span class="badge bg-secondary">{{ count($catalogs) }} Total</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>एड्रेस</th>
                                    <th>आइटम्स</th>
                                    <th>पब्लिक लिंक</th>
                                    <th class="text-end" style="min-width: 180px;">एक्शन</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($catalogs as $index => $catalog)
                                    @php
                                        $catalogUrl = url('/c/' . $catalog->slug);
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="fw-bold text-primary">{{ $catalog->address }}</td>
                                        <td>
                                            <span class="badge bg-info text-dark">
                                                {{ count($catalog->item_ids ?? []) }} Items
                                            </span>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm" style="max-width: 170px;">
                                                <input type="text" class="form-control" id="link_{{ $catalog->id }}" value="{{ $catalogUrl }}" readonly>
                                                <button class="btn btn-outline-secondary" type="button" onclick="copyLink('link_{{ $catalog->id }}')" title="Copy Link">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <!-- Eye / View Button -->
                                                <button type="button" class="btn btn-outline-info" onclick="viewCatalog('{{ $catalog->address }}', '{{ $catalogUrl }}')" title="View Live Catalog Card">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                <!-- EDIT BUTTON -->
                                                <button type="button" class="btn btn-outline-warning" onclick="editCatalog('{{ $catalog->id }}', '{{ $catalog->address }}', {{ json_encode($catalog->item_ids ?? []) }})" title="Edit Catalog">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <!-- Share Button (WhatsApp) -->
                                                <a href="https://wa.me/?text=Check%20our%20catalog%20for%20{{ urlencode($catalog->address) }}:%20{{ urlencode($catalogUrl) }}" target="_blank" class="btn btn-outline-success" title="Share on WhatsApp">
                                                    <i class="fab fa-whatsapp"></i>
                                                </a>

                                                <!-- QR Code View -->
                                                <a href="{{ route('vendor.catalogs.qr', $catalog->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
    <i class="bi bi-qr-code"></i> View QR
</a>

                                                <!-- Delete Button -->
                                                <form action="{{ route('vendor.catalogs.destroy', $catalog->id) }}" method="POST" class="d-inline" onsubmit="return confirm('क्या आप इस कैटलॉग को हटाना चाहते हैं?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">कोई कैटलॉग उपलब्ध नहीं है।</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Live Previewing Full Catalog Card -->
<div class="modal fade" id="catalogViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-mobile-alt me-2 text-warning"></i>कैटलॉग कार्ड प्रिव्यू - <span id="modalAddress" class="text-info"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-0 bg-secondary-subtle d-flex justify-content-center align-items-center" style="min-height: 520px;">
                <div class="my-3 shadow-lg rounded-4 overflow-hidden border border-3 border-dark" style="width: 360px; height: 500px; background: #fff;">
                    <iframe id="modalCatalogIframe" src="" style="width: 100%; height: 100%; border: none;"></iframe>
                </div>
            </div>

            <div class="modal-footer bg-light border-0">
                <a id="modalOpenBtn" href="" target="_blank" class="btn btn-success w-100 fw-bold py-2">
                    <i class="fas fa-external-link-alt me-1"></i> कार्ड को नए टैब में खोलें
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Edit Catalog
function editCatalog(id, address, itemIds) {
    document.getElementById('catalog_id').value = id;
    document.getElementById('addressInput').value = address;
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-sync-alt me-1"></i> अपडेट करें';
    document.getElementById('cancelEditBtn').classList.remove('d-none');

    // Check items
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = itemIds.includes(parseInt(cb.value)) || itemIds.includes(cb.value);
    });

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function resetForm() {
    document.getElementById('catalog_id').value = '';
    document.getElementById('addressInput').value = '';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save me-1"></i> सेव करें';
    document.getElementById('cancelEditBtn').classList.add('d-none');
    
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = true);
}

function copyAndNext() {
    const form = document.getElementById('catalogForm');
    const addressInput = document.getElementById('addressInput');

    if (!addressInput.value.trim()) {
        alert('कृपया पहले एड्रेस / टेबल नंबर दर्ज करें!');
        addressInput.focus();
        return;
    }

    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('कैटलॉग सफलतापूर्वक सेव हो गया!');
            addressInput.value = '';
            addressInput.focus();
            location.reload();
        } else {
            alert('कैटलॉग सेव करने में त्रुटि हुई।');
        }
    })
    .catch(error => {
        form.submit();
    });
}

function copyLink(elementId) {
    const input = document.getElementById(elementId);
    input.select();
    navigator.clipboard.writeText(input.value);
    alert('लिंक कॉपी हो गया!');
}

function viewCatalog(address, url) {
    document.getElementById('modalAddress').innerText = address;
    document.getElementById('modalCatalogIframe').src = url;
    document.getElementById('modalOpenBtn').href = url;

    const catalogModal = new bootstrap.Modal(document.getElementById('catalogViewModal'));
    catalogModal.show();
}
</script>
@endsection