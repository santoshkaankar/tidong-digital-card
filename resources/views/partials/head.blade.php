<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Restaurant Hub — {{ Auth::user()->name }}</title>

<!-- Fonts & Framework CSS -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    /* Global Color Variables */
    :root {
        --primary-color: #4f46e5;
        --primary-hover: #4338ca;
        --bg-canvas: #f8fafc;
        --card-bg: #ffffff;
        --sidebar-bg: #ffffff;
        --topbar-bg: rgba(255, 255, 255, 0.95);
        --text-main: #0f172a;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
    }

    /* Automated Dark Theme Mode */
    [data-bs-theme="dark"] {
        --primary-color: #6366f1;
        --primary-hover: #4f46e5;
        --bg-canvas: #0f172a;
        --card-bg: #1e293b;
        --sidebar-bg: #1e293b;
        --topbar-bg: rgba(30, 41, 59, 0.95);
        --text-main: #f8fafc;
        --text-muted: #94a3b8;
        --border-color: #334155;
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: var(--bg-canvas);
        color: var(--text-main);
        overflow-x: hidden;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .dashboard-wrapper {
        display: flex;
        min-height: 100vh;
        flex-direction: column;
    }

    @media (min-width: 992px) {
        .dashboard-wrapper { flex-direction: row; }
    }

    .main-viewport {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    /* Mobile Responsive Top Navigation Bar */
    .top-navbar {
        min-height: 72px;
        background: var(--topbar-bg);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid var(--border-color);
        padding: 12px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 100;
        gap: 12px;
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }

    .search-bar {
        position: relative;
        width: 100%;
        max-width: 320px;
    }

    @media (max-width: 576px) {
        .top-navbar { padding: 10px 14px; }
        .search-bar { max-width: 150px; }
    }

    .search-bar input {
        background: var(--bg-canvas);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 8px 14px 8px 38px;
        font-size: 0.8125rem;
        color: var(--text-main);
    }

    .search-bar input:focus {
        background: var(--card-bg);
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }

    .search-bar i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
    }

    /* Cards & Layout Styling */
    .metric-card, .action-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 24px;
        height: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.3s ease, border-color 0.3s ease;
    }

    .metric-card:hover, .action-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.08);
    }

    .icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .hero-banner {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
        border-radius: 20px;
        padding: 32px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(67, 56, 202, 0.2);
    }

    @media (max-width: 768px) {
        .hero-banner { padding: 20px; }
        .hero-banner h1 { font-size: 1.4rem; }
    }

    .btn-action {
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 600;
        font-size: 0.875rem;
    }
</style>