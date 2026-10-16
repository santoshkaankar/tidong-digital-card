<!-- File Path: resources/views/member/game/partials/touch-controls.blade.php -->
<style>
    .touch-controls-wrapper {
        position: absolute;
        bottom: 25px;
        right: 25px;
        z-index: 100000;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        user-select: none;
        touch-action: manipulation;
    }
    .ctrl-btn {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.65);
        border: 2px solid #f59e0b;
        color: #fff;
        font-size: 20px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.5);
    }
    .ctrl-btn:active {
        background: #f59e0b;
        color: #000;
        transform: scale(0.92);
    }
    .ctrl-row {
        display: flex;
        gap: 12px;
    }
</style>

<!-- Mobile Touch Joystick Buttons -->
<div class="touch-controls-wrapper">
    <button class="ctrl-btn" id="btnUp">⬆️</button>
    <div class="ctrl-row">
        <button class="ctrl-btn" id="btnLeft">⬅️</button>
        <button class="ctrl-btn" id="btnDown">⬇️</button>
        <button class="ctrl-btn" id="btnRight">➡️</button>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const checkPlayerInterval = setInterval(function() {
            if (window.gamePlayer) {
                clearInterval(checkPlayerInterval);
                initTouchMovement();
            }
        }, 100);

        function initTouchMovement() {
            const player = window.gamePlayer;
            const moveSpeed = 0.8;

            function move(dx, dz) {
                if (!player) return;
                player.position.x += dx;
                player.position.z += dz;

                // Move camera along with player
                if (window.gameCamera) {
                    window.gameCamera.position.x += dx;
                    window.gameCamera.position.z += dz;
                }
            }

            // Button Event Listeners for Touch & Click
            const attachHoldEvent = (btnId, dx, dz) => {
                const btn = document.getElementById(btnId);
                if (!btn) return;

                let timer = null;
                const startMove = (e) => {
                    e.preventDefault();
                    if (!timer) {
                        timer = setInterval(() => move(dx, dz), 30);
                    }
                };
                const stopMove = () => {
                    if (timer) {
                        clearInterval(timer);
                        timer = null;
                    }
                };

                btn.addEventListener('touchstart', startMove);
                btn.addEventListener('touchend', stopMove);
                btn.addEventListener('mousedown', startMove);
                btn.addEventListener('mouseup', stopMove);
                btn.addEventListener('mouseleave', stopMove);
            };

            attachHoldEvent('btnUp', 0, -moveSpeed);
            attachHoldEvent('btnDown', 0, moveSpeed);
            attachHoldEvent('btnLeft', -moveSpeed, 0);
            attachHoldEvent('btnRight', moveSpeed, 0);
        }
    });
</script>