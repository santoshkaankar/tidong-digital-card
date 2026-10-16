<!-- File Path: resources/views/member/game/partials/trash-spawner.blade.php -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const checkSceneInterval = setInterval(function() {
            if (window.gameScene) {
                clearInterval(checkSceneInterval);
                initRealisticSpawner(window.gameScene);
            }
        }, 100);

        function initRealisticSpawner(scene) {
            const trashMaterials = [
                new THREE.MeshStandardMaterial({ color: 0x888888, roughness: 0.4, metalness: 0.8 }), // Metal Can
                new THREE.MeshStandardMaterial({ color: 0x4a3b32, roughness: 0.9 }), // Cardboard Box
                new THREE.MeshStandardMaterial({ color: 0xdc3545, roughness: 0.6 }), // Plastic Bottle
                new THREE.MeshStandardMaterial({ color: 0x6c757d, roughness: 0.5 })  // Waste Packet
            ];

            const geoBox = new THREE.BoxGeometry(0.7, 0.7, 0.7);
            const geoCylinder = new THREE.CylinderGeometry(0.3, 0.3, 0.9, 6);
            const geoSphere = new THREE.SphereGeometry(0.4, 5, 5);

            // Single function to spawn one piece of trash naturally
            window.spawnSingleTrash = function() {
                const geometries = [geoBox, geoCylinder, geoSphere];
                const randGeom = geometries[Math.floor(Math.random() * geometries.length)];
                const randMat = trashMaterials[Math.floor(Math.random() * trashMaterials.length)];

                const trashMesh = new THREE.Mesh(randGeom, randMat);
                
                // Random position within active field range
                const x = (Math.random() - 0.5) * 300;
                const z = (Math.random() - 0.5) * 300;

                trashMesh.position.set(x, 0.35, z);
                trashMesh.rotation.x = Math.random() * Math.PI;
                trashMesh.rotation.y = Math.random() * Math.PI;
                trashMesh.castShadow = true;
                trashMesh.receiveShadow = true;

                scene.add(trashMesh);
            };

            // Spawn initial 10 items for immediate start
            for (let i = 0; i < 10; i++) {
                window.spawnSingleTrash();
            }

            // Realistic Automatic Regeneration: Every 8 seconds new trash drops naturally
            setInterval(function() {
                window.spawnSingleTrash();
            }, 8000);
        }
    });
</script>