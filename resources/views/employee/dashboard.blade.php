<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee Dashboard</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f3f5f9; color: #172033; }
        header { background: #14532d; color: white; padding: 18px 22px; }
        main { max-width: 1000px; margin: 25px auto; padding: 0 16px; }
        .card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 3px 12px #0000000d; }
        form { margin: 0; }
        button { background: #dc2626; color: white; border: 0; padding: 10px 16px; border-radius: 7px; cursor: pointer; }
        .top { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; }
    </style>
</head>
<body>
    <header>
        <div class="top">
            <strong>Platform Employee</strong>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>
    </header>

    <main>
        <h1>Employee Dashboard</h1>
        <div class="card">
            <h2>Welcome, {{ auth()->user()->name }}</h2>
            <p>Yahaan sirf aapke assigned role aur permissions ke mutabik features add kiye jayenge.</p>
        </div>
        @if (auth()->user()->hasPlatformPermission('businesses.view'))
            <p><a href="{{ route('employee.businesses.index') }}" style="display:inline-block;margin-top:16px;padding:10px 16px;background:#1d4ed8;color:#fff;border-radius:7px;text-decoration:none">Business Approvals</a></p>
        @endif
        <nav class="card" aria-label="Employee navigation" style="margin-top:16px;">
            <h2>Quick Navigation</h2>
            <a href="{{ route('employee.home') }}">Dashboard</a>
            @if(auth()->user()->hasPlatformPermission('vendors.view'))
                <p><a href="{{ route('employee.vendors.index') }}">Vendor Management</a></p>
            @endif
            @if(auth()->user()->hasPlatformPermission('businesses.view'))
                <p><a href="{{ route('employee.businesses.index') }}">Business Approvals</a></p>
            @endif
            @if(auth()->user()->hasPlatformPermission('approvals.view'))
                <p><a href="{{ route('employee.branch-changes.index') }}">Branch Change Approvals</a></p>
            @endif
            @if(auth()->user()->hasPlatformPermission('finance.view'))
                <p><a href="{{ route('employee.finance.index') }}">Finance</a></p>
            @endif
            @if(app(\App\Services\Platform\PlatformCapability::class)->allows(auth()->user(), 'modules.view'))
                <p><a href="{{ route('employee.modules.index') }}">Module Management</a></p>
            @endif
            @if(app(\App\Services\Platform\PlatformCapability::class)->allows(auth()->user(), 'delivery.view'))
                <p><a href="{{ route('employee.delivery.index') }}">Delivery Management</a></p>
            @endif
            @if(app(\App\Services\Platform\PlatformCapability::class)->isSupervisor(auth()->user()))
                <p><a href="{{ route('employee.employee-permissions.index') }}">Employee Permissions</a></p>
            @endif
        </nav>
    </main>
</body>
</html>