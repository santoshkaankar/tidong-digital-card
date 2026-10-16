<!-- File Path: resources/views/member/game/partials/item-spawner.blade.php -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Global Spawner Function called directly from Market Modal
        window.buyAndSpawnItem = function(type, colorHex) {
            if (!window.gameScene) {
                alert("Game scene loading, please wait!");
                return;
            }

            const scene = window.gameScene;
            const mat = new THREE.MeshStandardMaterial({ color: colorHex, roughness: 0.5 });

            if (type === 'car') {
                // 3D Car Model Group
                const carGroup = new THREE.Group();
                const body = new THREE.Mesh(new THREE.BoxGeometry(4, 1.5, 8), mat);
                body.position.y = 1;
                carGroup.add(body);

                // Cabin
                const cabinMat = new THREE.MeshStandardMaterial({ color: 0x111111 });
                const cabin = new THREE.Mesh(new THREE.BoxGeometry(3.5, 1.2, 4), cabinMat);
                cabin.position.set(0, 2.2, -0.5);
                carGroup.add(cabin);

                carGroup.position.set(10, 0, 10);
                scene.add(carGroup);
                alert("🏎️ Car delivered to your field!");

            } else if (type === 'house') {
                // 3D House/Room Structure
                const houseGroup = new THREE.Group();
                const walls = new THREE.Mesh(new THREE.BoxGeometry(15, 8, 15), new THREE.MeshStandardMaterial({ color: 0xf5f5dc }));
                walls.position.y = 4;
                houseGroup.add(walls);

                const roof = new THREE.Mesh(new THREE.ConeGeometry(12, 5, 4), new THREE.MeshStandardMaterial({ color: 0x8b0000 }));
                roof.position.y = 10.5;
                roof.rotation.y = Math.PI / 4;
                houseGroup.add(roof);

                houseGroup.position.set(-20, 0, -20);
                scene.add(houseGroup);
                alert("🏡 Villa built on your estate!");

            } else if (type === 'sofa') {
                const sofa = new THREE.Mesh(new THREE.BoxGeometry(3, 1.2, 1.5), mat);
                sofa.position.set(-18, 0.6, -18);
                scene.add(sofa);
                alert("🛋️ Sofa placed inside your villa!");

            } else if (type === 'table') {
                const table = new THREE.Mesh(new THREE.CylinderGeometry(1.5, 1.5, 1.2, 12), mat);
                table.position.set(-22, 0.6, -18);
                scene.add(table);
                alert("🪑 Table placed inside your villa!");
            }

            // Close Modal after buying
            const modalEl = document.getElementById('gameMarketModal');
            if (modalEl) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            }
        };
    });
</script>