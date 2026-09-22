<!-- Tiffin Production & Schedule Report Section -->
<div class="card border-0 shadow-sm rounded-4 mb-4 p-4 bg-white">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3 pb-3 border-bottom">
        <div>
            <h5 class="fw-bold mb-1 text-primary">
                <i class="bi bi-calendar-check-fill me-2"></i>Tiffin Production & Schedule Report
            </h5>
            <p class="text-muted small mb-0">Item-wise and meal-wise total kitchen requirement for tiffin orders.</p>
        </div>

        <!-- Date Controls & Toggle -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeTiffinDate(-1)">
                <i class="bi bi-chevron-left"></i> Prev
            </button>
            
            <input type="date" id="tiffinReportDate" class="form-control form-control-sm fw-bold text-center" value="{{ date('Y-m-d') }}" onchange="fetchTiffinProductionReport()" style="width: 155px;">

            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeTiffinDate(1)">
                Next <i class="bi bi-chevron-right"></i>
            </button>

            <button type="button" class="btn btn-primary btn-sm fw-semibold" onclick="setTodayTiffinDate()">
                Today
            </button>
        </div>
    </div>

    <!-- Report Content Container (4 Columns: Breakfast, Lunch, Snacks, Dinner) -->
    <div id="tiffinReportContainer">
        <div class="row g-3" id="mealSummaryCards">
            <div class="col-md-3 col-sm-6">
                <div class="p-3 border rounded-3 bg-light h-100">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-2"><i class="bi bi-sunrise text-warning me-1"></i> Breakfast</h6>
                    <div id="breakfastItemsList" class="small text-muted">Loading...</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="p-3 border rounded-3 bg-light h-100">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-2"><i class="bi bi-sun text-danger me-1"></i> Lunch</h6>
                    <div id="lunchItemsList" class="small text-muted">Loading...</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="p-3 border rounded-3 bg-light h-100">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-2"><i class="bi bi-cup-hot text-success me-1"></i> Snacks</h6>
                    <div id="snacksItemsList" class="small text-muted">Loading...</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="p-3 border rounded-3 bg-light h-100">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-2"><i class="bi bi-moon-stars text-primary me-1"></i> Dinner</h6>
                    <div id="dinnerItemsList" class="small text-muted">Loading...</div>
                </div>
            </div>
        </div>

        <!-- Grand Total Merged Item Requirement Bar -->
        <div class="mt-3 p-3 bg-primary bg-opacity-10 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <span class="fw-bold text-dark">Total Tiffin Orders: <span id="totalTiffinCount" class="badge bg-primary">0</span></span>
            </div>
            <div id="grandTotalSummaryText" class="fw-semibold text-primary small">
                Merged Items: --
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    fetchTiffinProductionReport();

    // Real-time polling har 10 seconds mein taaki naye tiffin orders apne aap update ho jayein
    setInterval(function() {
        fetchTiffinProductionReport();
    }, 10000);
});

function setTodayTiffinDate() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tiffinReportDate').value = today;
    fetchTiffinProductionReport();
}

function changeTiffinDate(days) {
    const dateInput = document.getElementById('tiffinReportDate');
    let currentDate = new Date(dateInput.value);
    currentDate.setDate(currentDate.getDate() + days);
    dateInput.value = currentDate.toISOString().split('T')[0];
    fetchTiffinProductionReport();
}

function fetchTiffinProductionReport() {
    const selectedDate = document.getElementById('tiffinReportDate').value;

    fetch("{{ route('vendor.restaurant.tiffin.report.data') }}?date=" + selectedDate, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('totalTiffinCount').innerText = data.total_orders;

            renderMealItems('breakfastItemsList', data.meal_summary.breakfast);
            renderMealItems('lunchItemsList', data.meal_summary.lunch);
            renderMealItems('snacksItemsList', data.meal_summary.snacks);
            renderMealItems('dinnerItemsList', data.meal_summary.dinner);

            let grandText = [];
            for (const [item, qty] of Object.entries(data.grand_total_items)) {
                grandText.push(`<b>${qty}x</b> ${item}`);
            }
            document.getElementById('grandTotalSummaryText').innerHTML = grandText.length > 0 
                ? '<b>Total Requirement:</b> ' + grandText.join(', ') 
                : 'No tiffin orders scheduled for this date.';
        }
    })
    .catch(err => console.error('Tiffin Report Error:', err));
}

function renderMealItems(elementId, itemsObj) {
    const container = document.getElementById(elementId);
    if (!itemsObj || Object.keys(itemsObj).length === 0) {
        container.innerHTML = '<span class="text-muted fst-italic">No items</span>';
        return;
    }

    let html = '<ul class="list-unstyled mb-0">';
    for (const [name, qty] of Object.entries(itemsObj)) {
        html += `<li class="d-flex justify-content-between py-1 border-bottom border-light"><span>${name}</span> <span class="badge bg-secondary rounded-pill">${qty}</span></li>`;
    }
    html += '</ul>';
    container.innerHTML = html;
}
</script>