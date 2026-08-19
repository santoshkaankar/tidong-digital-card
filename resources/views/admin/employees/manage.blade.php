<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee 360° Management - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        #sidebar { position: fixed; top: 0; left: 0; height: 100vh; width: 250px; background: #1e293b; color: #fff; overflow-y: auto; z-index: 1000; }
        #sidebar .brand { font-size: 1.1rem; padding: 15px; background: #0f172a; text-align: center; font-weight: bold; border-bottom: 1px solid #334155; }
        #sidebar .nav-link { color: #94a3b8; padding: 7px 12px; margin: 2px 8px; border-radius: 6px; font-size: 0.88rem; }
        #sidebar .nav-link:hover, #sidebar .nav-link.active { color: #fff; background: #2563eb; }
        #main-content { margin-left: 250px; padding: 30px; }
        @media (max-width: 768px) {
            #sidebar { width: 100%; height: auto; position: relative; }
            #main-content { margin-left: 0; padding: 15px; }
        }
    </style>
</head>
<body>

    <!-- Sidebar Include -->
    @include('admin.layouts.sidebar')

    <!-- Main Content Area -->
    <div id="main-content">
        <div class="container-fluid">
            <h3 class="fw-bold mb-4"><i class="fas fa-user-tie me-2"></i> 360° Employee & Staff Management</h3>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Universal Search Box for Employees -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body">
                    <form action="{{ route('admin.employees.manage') }}" method="GET">
                        <label class="form-label fw-semibold">Search Employee by Email ID or Mobile Number</label>
                        <div class="input-group">
                            <input type="text" name="query" class="form-control" placeholder="Enter employee email or mobile..." value="{{ $query ?? '' }}" required>
                            <button class="btn btn-primary px-4" type="submit">
                                <i class="fas fa-search me-1"></i> Search / Open Staff Hub
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Employee Hub (Appears after search) -->
            @if(isset($query))
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            @if($employee)
                                <i class="fas fa-user-shield me-2 text-info"></i> Managing Employee: {{ $employee->name }} ({{ $employee->email }})
                            @else
                                <i class="fas fa-user-plus me-2 text-warning"></i> New Staff Setup (Auto-Register)
                            @endif
                        </h5>
                        @if($employee)
                            <span class="badge bg-success fs-6">Active Staff</span>
                        @endif
                    </div>
                    
                    <div class="card-body p-4">
                        @if($employee)
                            <!-- NAVIGATION TABS FOR EMPLOYEE MODULES -->
                            <ul class="nav nav-tabs mb-4" id="employeeTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active fw-semibold" id="e-profile-tab" data-bs-toggle="tab" data-bs-target="#e-profile" type="button" role="tab">
                                        <i class="fas fa-id-badge me-1"></i> Staff Profile & Info
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-semibold" id="e-tasks-tab" data-bs-toggle="tab" data-bs-target="#e-tasks" type="button" role="tab">
                                        <i class="fas fa-tasks me-1"></i> Assigned Tasks
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-semibold" id="e-attendance-tab" data-bs-toggle="tab" data-bs-target="#e-attendance" type="button" role="tab">
                                        <i class="fas fa-calendar-check me-1"></i> Attendance & Logs
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-semibold" id="e-salary-tab" data-bs-toggle="tab" data-bs-target="#e-salary" type="button" role="tab">
                                        <i class="fas fa-money-check-alt me-1"></i> Salary & Payouts
                                    </button>
                                </li>
                            </ul>
                        @endif

                        <!-- TAB CONTENTS -->
                        <div class="tab-content" id="employeeTabContent">
                            
                            <!-- 1. EMPLOYEE PROFILE TAB -->
                            <div class="tab-pane fade show active" id="e-profile" role="tabpanel">
                                <form action="{{ route('admin.employees.save') }}" method="POST">
                                    @csrf
                                    <h5 class="text-primary mb-3"><i class="fas fa-user-cog me-1"></i> Official Credentials & Access</h5>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Full Name *</label>
                                            <input type="text" name="name" class="form-control" value="{{ old('name', $employee->name ?? '') }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Email Address *</label>
                                            <input type="email" name="email" class="form-control" value="{{ old('email', $employee->email ?? $query) }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Mobile Number *</label>
                                            <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $employee->mobile ?? (is_numeric($query) ? $query : '')) }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Set / Reset Password</label>
                                            <input type="text" name="password" class="form-control" placeholder="Leave blank to keep old">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Account Status</label>
                                            <select name="status" class="form-select">
                                                <option value="approved" {{ (isset($employee) && $employee->status == 'approved') ? 'selected' : '' }}>Active / Approved</option>
                                                <option value="suspended" {{ (isset($employee) && $employee->status == 'suspended') ? 'selected' : '' }}>Suspended</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <button type="submit" class="btn btn-success px-4 fw-semibold">
                                            <i class="fas fa-save me-1"></i> Save Employee Details
                                        </button>
                                    </div>
                                </form>
                            </div>

                            @if($employee)
                                <!-- 2. ASSIGNED TASKS TAB -->
                                <div class="tab-pane fade" id="e-tasks" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="text-dark mb-0">Tasks Assigned to Staff</h5>
                                        <button class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i> Assign New Task</button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped align-middle">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Task Title</th>
                                                    <th>Deadline</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">No active tasks assigned to this employee.</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- 3. ATTENDANCE & LOGS TAB -->
                                <div class="tab-pane fade" id="e-attendance" role="tabpanel">
                                    <h5 class="text-dark mb-3">Attendance Logs</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped align-middle">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Check-In Time</th>
                                                    <th>Check-Out Time</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">No attendance logs available for this period.</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- 4. SALARY & PAYOUTS TAB -->
                                <div class="tab-pane fade" id="e-salary" role="tabpanel">
                                    <h5 class="text-dark mb-3">Salary Structure & History</h5>
                                    <div class="card p-3 bg-white border shadow-sm mb-3">
                                        <span class="text-muted small">Monthly Base Salary</span>
                                        <h3 class="fw-bold text-primary mt-1">₹ 0.00</h3>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>