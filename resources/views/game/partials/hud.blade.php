<div class="ui-overlay" style="top: 20px; left: 20px;">
    <div style="background: rgba(0,0,0,0.8); padding: 15px; border-radius: 8px; color: #fff; border: 2px solid #00ff88;">
        <h3 style="color: #00ff88; margin-bottom: 5px;">🎮 Player Dashboard</h3>
        <p>Name: <strong style="color: #ffd700;">{{ $user->name ?? 'Gaurang' }}</strong></p>
        <p>Wallet Cash: <strong style="color: #00ff88;">${{ number_format($user->wallet_balance ?? 5000, 2) }}</strong></p>
    </div>
</div>