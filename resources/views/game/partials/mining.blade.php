<div class="ui-overlay interactive" style="bottom: 20px; left: 20px;">
    <div style="background: rgba(0,0,0,0.8); padding: 12px; border-radius: 8px; color: #fff; border: 1px solid #ff9900;">
        <h4 style="color: #ff9900; margin-bottom: 8px;">⛏️ Resource Gathering</h4>
        <button onclick="startMining()" style="padding: 8px 12px; background: #ff9900; border: none; color: #000; font-weight: bold; cursor: pointer; border-radius: 4px;">चट्टान तोड़ें (Mine Ore)</button>
        <button onclick="startFarming()" style="padding: 8px 12px; background: #44aa44; border: none; color: #fff; font-weight: bold; cursor: pointer; border-radius: 4px; margin-left: 5px;">खेती करें (Farm)</button>
    </div>
</div>
<script>
    function startMining() {
        alert("⛏️ माइनिंग शुरू! अयस्क (Ore) एकत्र हो रहा है...");
    }
    function startFarming() {
        alert("🌾 खेती शुरू! फसल उगाई जा रही है...");
    }
</script>