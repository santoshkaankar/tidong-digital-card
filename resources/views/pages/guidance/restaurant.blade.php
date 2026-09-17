<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant & Food Hub - User Guide | Tidong® Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1e293b; line-height: 1.8; }
        .guide-header { background: #0f172a; color: #fff; padding: 50px 0; border-bottom: 4px solid #2563eb; }
        .guide-content { background: #ffffff; border-radius: 16px; border: 1px solid #cbd5e1; padding: 40px; margin-bottom: 40px; }
        .section-title { color: #0f172a; border-left: 4px solid #2563eb; padding-left: 12px; margin-top: 30px; font-weight: 700; margin-bottom: 15px; }
        .step-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="guide-header">
        <div class="container">
            <a href="{{ url('/') }}" class="btn btn-outline-light btn-sm rounded-pill mb-3"><i class="fas fa-arrow-left me-1"></i> Return to Main Platform</a>
            <h1 class="fw-bold mb-2">Tidong Digital: Restaurant & Food Hub - User Guide</h1>
            <p class="text-light mb-0 small">Comprehensive operational guidelines for restaurants, tiffin services, and street food vendors covering digital identity, menu management, POS billing, and live kitchen displays</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="guide-content shadow-sm">

            <!-- Step 1 -->
            <h4 class="section-title">Step 1: Website Sign Up & Registration</h4>
            <div class="step-box">
                <ul class="lh-lg text-dark mb-0">
                    <li><strong>Visit Website:</strong> Open <code>tidong.in</code> in your browser to access the main platform portal.</li>
                    <li><strong>Click Register:</strong> Click the <strong>Register</strong> button at the top-right corner.</li>
                    <li><strong>Enter Details:</strong> Provide your name or business title, email address, mobile number, and secure password in the registration form.</li>
                    <li><strong>Select Account Type:</strong> 
                        <ul class="mt-1">
                            <li>Choose <strong>Business / Service Partner</strong> from the dropdown menu.</li>
                            <li>Under <strong>Select Your Business Service</strong>, select <strong>Restaurant (Dine-in / KDS / POS / Tiffin / Street Food)</strong> corresponding to your operations and complete registration.</li>
                        </ul>
                    </li>
                </ul>
            </div>

            <!-- Step 2 -->
            <h4 class="section-title">Step 2: Accessing Account Login</h4>
            <div class="step-box">
                <ul class="lh-lg text-dark mb-0">
                    <li><strong>Login Portal:</strong> If you already have an account, navigate to <code>tidong.in/login</code>.</li>
                    <li><strong>Authenticate:</strong> Input your registered email, mobile number, or username along with your password, then click <strong>LOG IN</strong> to open your dedicated <strong>Restaurant Partner Panel (Dashboard)</strong>.</li>
                </ul>
            </div>

            <!-- Step 3 -->
            <h4 class="section-title">Step 3: Navigating the Dashboard</h4>
            <div class="step-box">
                <p class="text-muted small mb-2"><i class="fas fa-info-circle text-primary me-1"></i> Upon logging in, you will access the main dashboard to oversee all establishment metrics:</p>
                <ul class="lh-lg text-dark mb-0">
                    <li><strong>Total Orders:</strong> Monitor daily and cumulative order volumes.</li>
                    <li><strong>Active / Running:</strong> Track live orders currently being prepared in the kitchen.</li>
                    <li><strong>Completed:</strong> Review successfully fulfilled orders.</li>
                    <li><strong>Today's Revenue:</strong> Track daily sales volume and earnings.</li>
                </ul>
            </div>

            <!-- Step 4 -->
            <h4 class="section-title">Step 4: Setting Up Food Categories & Thali/Menu Items</h4>
            <div class="step-box">
                <ul class="lh-lg text-dark mb-0">
                    <li><strong>Add Categories:</strong> Click <strong>Select Category</strong> in the left sidebar menu to organize food categories (e.g., Main Course - Veg, Breads / Roti, Rice & Biryani, Starters, Desserts).</li>
                    <li><strong>Create Custom Items or Thalis:</strong> Go to <strong>Thali / Tiffin Items</strong> or <strong>Create Catalog / Menu Card</strong>.
                        <ul class="mt-1">
                            <li>Enter the item or thali name (e.g., Special Thali or Paneer Tikka).</li>
                            <li>Configure the category, food classification (Veg/Non-Veg), tax rate, and pricing.</li>
                            <li>Upload item imagery and specify ingredients or components in the description (e.g., 4 rotis, dal fry, shahi paneer, rice), then click <strong>Save Custom Item</strong>.</li>
                        </ul>
                    </li>
                </ul>
            </div>

            <!-- Step 5 -->
            <h4 class="section-title">Step 5: POS & Counter Billing Operations</h4>
            <div class="step-box">
                <ul class="lh-lg text-dark mb-0">
                    <li><strong>POS Screen:</strong> Select <strong>POS / Counter Billing</strong> from the sidebar menu.</li>
                    <li><strong>Select Order Mode:</strong> Choose the service type (Dine In, Takeaway, or Tiffin Delivery) and specify the table number if Dine-in.</li>
                    <li><strong>Add Items:</strong> Click the <strong>+ Add</strong> button on menu items to populate the order cart.</li>
                    <li><strong>Print KOT:</strong> Input customer details (name/mobile) and click <strong>Place Order & Print KOT</strong> to transmit instructions directly to the kitchen and record the order.</li>
                </ul>
            </div>

            <!-- Step 6 -->
            <h4 class="section-title">Step 6: Live Kitchen Display System (KDS)</h4>
            <div class="step-box">
                <ul class="lh-lg text-dark mb-0">
                    <li><strong>KDS Screen:</strong> If display terminals are installed in the kitchen, open <strong>Live Kitchen (KDS)</strong> from the sidebar.</li>
                    <li><strong>Real-Time Updates:</strong> Orders placed at the counter appear instantly on the KDS screen, allowing chefs to initiate preparation without delay.</li>
                </ul>
            </div>

            <!-- Step 7 -->
            <h4 class="section-title">Step 7: Order History & Payment Tracking</h4>
            <div class="step-box">
                <ul class="lh-lg text-dark mb-0">
                    <li><strong>Orders History:</strong> Click <strong>Orders History</strong> in the sidebar to review logs of all past and active orders.</li>
                    <li><strong>Track Status:</strong> View billing amounts, payment statuses (Paid or Unpaid), and fulfillment statuses (Completed) for every ticket, with options to print, view, edit, or settle bills directly.</li>
                </ul>
            </div>

        </div>
    </div>

    <footer class="bg-dark text-white py-4 text-center">
        <div class="container">
            <p class="small mb-0 text-muted">&copy; {{ date('Y') }} Tidong Marketing Pvt. Ltd. | Registered Office: Agra, UP, India.</p>
        </div>
    </footer>

</body>
</html>