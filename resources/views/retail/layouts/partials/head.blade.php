<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Restaurant Hub — {{ Auth::user()->name }}</title>

<!-- Fonts & Framework CSS -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    :root {
        --primary-color: #4f46e5;
        --bg-canvas: #f8fafc;
        --card-bg: #ffffff;
        --sidebar-bg: #ffffff;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
        --sidebar-width: 260px;
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: var(--bg-canvas);
        color: var(--text-main);
        overflow-x: hidden;
    }

    .restaurant-layout-wrapper {
        display: flex;
        width: 100%;
        min-height: 100vh;
    }

    .restaurant-main-viewport {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
        width: 100%;
    }

    /* Desktop Layout */
    @media (min-width: 992px) {
        .restaurant-main-viewport {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
        }
    }

    /* Mobile Responsive Sidebar */
    @media (max-width: 991.98px) {
        .restaurant-sidebar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            height: 100vh !important;
            width: 270px !important;
            z-index: 1050 !important;
            transform: translateX(-100%) !important;
            transition: transform 0.3s ease-in-out !important;
            background: #ffffff !important;
        }

        .restaurant-sidebar.show {
            transform: translateX(0) !important;
            box-shadow: 0 0 20px rgba(0,0,0,0.15) !important;
        }

        .restaurant-sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
        }

        .restaurant-sidebar-backdrop.show {
            display: block;
        }
    }
</style>