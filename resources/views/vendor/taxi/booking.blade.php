<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taxi & Sightseeing Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .spot-card { cursor: pointer; transition: 0.3s; border: 2px solid #ddd; }
        .spot-card.selected { border-color: #0d6efd; background-color: #e7f1ff; }
        .drag-item { background: #f8f9fa; border: 1px solid #ccc; padding: 10px; margin-bottom: 6px; border-radius: 6px; display: flex; align-items: center; justify-content: space-between; }
    </style>
</head>
<body class="bg-light">

<div class="container py-4">
    <h3 class="mb-3 text-center fw-bold"><i class="fa-solid =>fa-taxi text-warning"></i> Book Sightseeing Taxi</h3>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('vendor.taxi.store') }}" method="POST" id="bookingForm">
        @csrf
        <div class="row g-4">
            <!-- Left Panel: Places & Re-ordering -->
            <div class="col-md-7">
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white fw-bold">1. Select Tourist Spots</div>
                    <div class="card-body">
                        <div class="row g-2">
                            @forelse($spots as $spot)
                                <div class="col-6 col-sm-4">
                                    <div class="card spot-card p-2 text-center" onclick="toggleSpot({{ $spot->id }}, '{{ addslashes($spot->name) }}', this)">
                                        <div class="fw-bold fs-6">{{ $spot->name }}</div>
                                        <small class="text-muted">{{ $spot->city }}</small>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted text-center">No tourist spots available right now.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Sequence Order Card -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                        <span>2. Tour Route Order (Drag/Re-order)</span>
                        <small class="text-primary fs-7">Auto-arranged / Custom</small>
                    </div>
                    <div class="card-body">
                        <div id="selectedSequence" class="mb-2">
                            <p class="text-muted small text-center my-3">Select spots above to set tour order.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Pickup Details & Vehicle Selection -->
            <div class="col-md-5">
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white fw-bold">3. Journey Details</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Pickup Location</label>
                            <input type="text" name="pickup_location" class="form-control" placeholder="e.g. Railway Station / Hotel" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Pickup Date & Time</label>
                            <input type="datetime-local" name="pickup_datetime" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Select Vehicle</label>
                            <select name="taxi_id" class="form-select" required>
                                <option value="">-- Choose Car --</option>
                                @foreach($taxis as $taxi)
                                    <option value="{{ $taxi->id }}">
                                        {{ $taxi->vehicle_name }} ({{ $taxi->vehicle_type }}) - ₹{{ $taxi->rate_per_km }}/km
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm bg-dark text-white">
                    <div class="card-body text-center">
                        <h5>Estimated Total Spots: <span id="spotCount" class="text-warning">0</span></h5>
                        <button type="submit" class="btn btn-warning w-100 fw-bold mt-2 fs-5">Confirm Booking Request</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    let selectedSpots = [];

    function toggleSpot(id, name, element) {
        let index = selectedSpots.findIndex(s => s.id === id);
        if (index > -1) {
            selectedSpots.splice(index, 1);
            element.classList.remove('selected');
        } else {
            selectedSpots.push({ id: id, name: name });
            element.classList.add('selected');
        }
        renderSequence();
    }

    function renderSequence() {
        let container = document.getElementById('selectedSequence');
        document.getElementById('spotCount').innerText = selectedSpots.length;
        container.innerHTML = '';

        if (selectedSpots.length === 0) {
            container.innerHTML = '<p class="text-muted small text-center my-3">Select spots above to set tour order.</p>';
            return;
        }

        selectedSpots.forEach((spot, idx) => {
            container.innerHTML += `
                <div class="drag-item">
                    <div>
                        <span class="badge bg-primary me-2">${idx + 1}</span>
                        <strong>${spot.name}</strong>
                        <input type="hidden" name="selected_spots[]" value="${spot.id}">
                    </div>
                    <div>
                        ${idx > 0 ? `<button type="button" class="btn btn-sm btn-outline-secondary py-0" onclick="moveSpot(${idx}, -1)"><i class="fa-solid fa-arrow-up"></i></button>` : ''}
                        ${idx < selectedSpots.length - 1 ? `<button type="button" class="btn btn-sm btn-outline-secondary py-0" onclick="moveSpot(${idx}, 1)"><i class="fa-solid fa-arrow-down"></i></button>` : ''}
                    </div>
                </div>
            `;
        });
    }

    function moveSpot(index, direction) {
        let temp = selectedSpots[index];
        selectedSpots[index] = selectedSpots[index + direction];
        selectedSpots[index + direction] = temp;
        renderSequence();
    }
</script>
</body>
</html>