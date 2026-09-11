<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle & Driver Verification - Tidong®</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .doc-card { border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.04); background: #fff; }
    </style>
</head>
<body class="p-4">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark m-0"><i class="fas fa-id-card-clip text-primary me-2"></i>Driver & Vehicle Verification</h3>
                <p class="text-muted small m-0">Upload documents and photos for tourist trust & platform verification.</p>
            </div>
            <a href="{{ route('vendor.dashboard') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Dashboard</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @php
            $docTypes = [
                'dl' => ['title' => 'Driving License (DL)', 'icon' => 'fa-id-card text-primary', 'desc' => 'Government issued DL (Main ID Proof)', 'has_dates' => true],
                'rc' => ['title' => 'Vehicle RC', 'icon' => 'fa-car-side text-warning', 'desc' => 'Registration Certificate of cab', 'has_dates' => true],
                'insurance' => ['title' => 'Insurance Policy', 'icon' => 'fa-shield-alt text-danger', 'desc' => 'Commercial Vehicle Insurance', 'has_dates' => true],
                'permit' => ['title' => 'Permit & Fitness', 'icon' => 'fa-file-medical text-secondary', 'desc' => 'Tourist Taxi Permit & Fitness Document', 'has_dates' => true],
                'driver_selfie' => ['title' => 'Driver Selfie Photo', 'icon' => 'fa-user-astronaut text-info', 'desc' => 'Clear face photo for tourist identification', 'has_dates' => false],
                'vehicle_photo' => ['title' => 'Vehicle Exterior Photo', 'icon' => 'fa-taxi text-success', 'desc' => 'Front view photo showing number plate', 'has_dates' => false]
            ];
        @endphp

        <div class="row g-4">
            @foreach($docTypes as $type => $info)
                @php 
                    $doc = $documents[$type] ?? null;
                    $status = $doc->verification_status ?? 'not_uploaded';
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="card doc-card p-3 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="fw-bold m-0" style="font-size: 1.05rem;"><i class="fas {{ $info['icon'] }} me-2"></i>{{ $info['title'] }}</h5>
                                @if($status == 'verified')
                                    <span class="badge bg-success">Verified</span>
                                @elseif($status == 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($status == 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-secondary">Not Uploaded</span>
                                @endif
                            </div>
                            
                            <p class="text-muted small mb-3">{{ $info['desc'] }}</p>

                            @if($doc)
                                <div class="bg-light p-2 rounded mb-3 small border">
                                    @if($info['has_dates'])
                                        <div class="text-truncate"><strong>Doc No:</strong> {{ $doc->document_number }}</div>
                                        <div><strong>Issue:</strong> {{ $doc->issue_date }}</div>
                                        <div><strong>Expiry:</strong> {{ $doc->expiry_date }}</div>
                                    @else
                                        <div class="text-muted text-center py-1"><i class="fas fa-check text-success me-1"></i> Photo uploaded for Tourist View</div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <button type="button" class="btn btn-sm {{ $doc ? 'btn-outline-primary' : 'btn-primary' }} w-100" 
                                onclick="openDocModal('{{ $type }}', '{{ $info['title'] }}', '{{ $info['has_dates'] ? '1' : '0' }}', '{{ $doc->document_number ?? '' }}', '{{ $doc->issue_date ?? '' }}', '{{ $doc->expiry_date ?? '' }}')">
                            <i class="fas fa-upload me-1"></i> {{ $doc ? 'Re-upload / Update' : 'Upload Now' }}
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Dynamic Modal -->
    <div class="modal fade" id="docUploadModal" tabindex="-1" aria-hidden="true" style="z-index: 1055;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('vendor.taxi.uploadDoc') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="document_type" id="modalDocType">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="modalTitle">Upload Verification</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        
                        <!-- DL / RC / Permit Date Fields -->
                        <div id="dateFieldsGroup">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Document Number <span class="text-danger">*</span></label>
                                <input type="text" name="document_number" id="modalDocNumber" class="form-control" placeholder="e.g. DL-1420110012345 / Permit No">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Issue Date <span class="text-danger">*</span></label>
                                    <input type="date" name="issue_date" id="modalIssueDate" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Expiry Date <span class="text-danger">*</span></label>
                                    <input type="date" name="expiry_date" id="modalExpiryDate" class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Image File Upload -->
                        <div class="mb-3">
                            <label class="form-label fw-bold" id="fileUploadLabel">Upload File / Photo <span class="text-danger">*</span></label>
                            <input type="file" name="document_file" class="form-control" accept="image/*,.pdf" required>
                            <small class="text-muted d-block mt-1" id="fileHelpText">Upload clear photo or document PDF.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success fw-bold"><i class="fas fa-check-circle me-1"></i> Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openDocModal(type, title, hasDates, docNo, issue, expiry) {
            document.getElementById('modalDocType').value = type;
            document.getElementById('modalTitle').innerText = 'Upload ' + title;
            
            let dateGroup = document.getElementById('dateFieldsGroup');
            let docNumInput = document.getElementById('modalDocNumber');
            let issueInput = document.getElementById('modalIssueDate');
            let expiryInput = document.getElementById('modalExpiryDate');
            let helpText = document.getElementById('fileHelpText');

            if (hasDates === '1') {
                dateGroup.style.display = 'block';
                docNumInput.required = true;
                issueInput.required = true;
                expiryInput.required = true;

                docNumInput.value = docNo;
                issueInput.value = issue;
                expiryInput.value = expiry;
                helpText.innerText = "Upload clear photo/PDF of legal document.";
            } else {
                dateGroup.style.display = 'none';
                docNumInput.required = false;
                issueInput.required = false;
                expiryInput.required = false;
                
                if (type === 'driver_selfie') {
                    helpText.innerText = "Take a well-lit photo of driver's face so tourists can recognize him.";
                } else {
                    helpText.innerText = "Take a clear photo of the taxi showing the front view & registration plate.";
                }
            }

            var modalElement = document.getElementById('docUploadModal');
            var modal = new bootstrap.Modal(modalElement);
            modal.show();
        }
    </script>
</body>
</html>