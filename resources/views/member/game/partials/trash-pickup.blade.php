<!-- File Path: resources/views/member/game/partials/trash-pickup.blade.php -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const checkReadyInterval = setInterval(function() {
            if (window.gameScene && window.gameCamera) {
                clearInterval(checkReadyInterval);
                initTrashPickup(window.gameScene, window.gameCamera);
            }
        }, 100);

        function initTrashPickup(scene, camera) {
            const raycaster = new THREE.Raycaster();
            const mouse = new THREE.Vector2();
            let cleanedCount = 0;

            const container = document.getElementById('gameContainer');
            if (container) {
                const hud = document.createElement('div');
                hud.style.position = 'absolute';
                hud.style.top = '15px';
                hud.style.left = '15px';
                hud.style.background = 'rgba(0, 0, 0, 0.7)';
                hud.style.color = '#ffc107';
                hud.style.padding = '8px 15px';
                hud.style.borderRadius = '20px';
                hud.style.fontFamily = 'monospace';
                hud.style.fontSize = '14px';
                hud.style.fontWeight = 'bold';
                hud.style.zIndex = '10';
                hud.innerHTML = '🧹 Cleaned Debris: <span id="cleanedScore">0</span> / 50';
                container.appendChild(hud);
            }

            window.addEventListener('click', function(event) {
                const rendererDom = container.querySelector('canvas');
                if (!rendererDom) return;

                const rect = rendererDom.getBoundingClientRect();
                if (event.clientX < rect.left || event.clientX > rect.right || 
                    event.clientY < rect.top || event.clientY > rect.bottom) {
                    return;
                }

                mouse.x = ((event.clientX - rect.left) / rendererDom.clientWidth) * 2 - 1;
                mouse.y = -((event.clientY - rect.top) / rendererDom.clientHeight) * 2 + 1;

                raycaster.setFromCamera(mouse, camera);
                const intersects = raycaster.intersectObjects(scene.children);

                for (let i = 0; i < intersects.length; i++) {
                    const hitObject = intersects[i].object;
                    if (hitObject.geometry && hitObject.geometry.type !== 'PlaneGeometry' && hitObject.geometry.type !== 'GridHelper') {
                        scene.remove(hitObject);
                        cleanedCount++;
                        
                        const scoreEl = document.getElementById('cleanedScore');
                        if (scoreEl) scoreEl.innerText = cleanedCount;
                        break;
                    }
                }
            });
        }
    });
</script>