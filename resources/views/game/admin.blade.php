<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Admin Panel - Management Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: sans-serif; }
        body { background: #111; color: #fff; padding: 30px; }
        h1 { color: #ff9900; margin-bottom: 20px; }
        .card { background: #1e1e24; padding: 20px; border-radius: 8px; border: 1px solid #ff9900; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #333; }
        th { color: #00ff88; }
        .btn { padding: 6px 12px; background: #00ff88; border: none; font-weight: bold; cursor: pointer; border-radius: 4px; }
    </style>
</head>
<body>

    <h1>⚙️ Game Owner & Admin Dashboard</h1>
    <p>Manager / Admin: <strong>Gaurang</strong> (Tidong System Control)</p>

    <div class="card">
        <h3>👥 Registered Players Management</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Player Name</th>
                    <th>Email</th>
                    <th>Wallet Cash ($)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($players as $player)
                <tr>
                    <td>{{ $player->id }}</td>
                    <td>{{ $player->name }}</td>
                    <td>{{ $player->email }}</td>
                    <td>${{ number_format($player->wallet_balance ?? 5000, 2) }}</td>
                    <td>
                        <form action="#" method="POST">
                            @csrf
                            <input type="number" name="wallet_balance" value="{{ $player->wallet_balance ?? 5000 }}" style="width: 90px; padding: 4px;">
                            <button type="submit" class="btn">Update</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</body>
</html>