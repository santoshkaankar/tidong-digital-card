<!-- File Path: resources/views/member/game/partials/trash-objects.blade.php -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Wait until the main scene is ready from vast-ground.blade.php
        const checkSceneInterval = setInterval(function() {
            if (window.gameScene) {
                clearInterval(checkSceneInterval);
                spawnTrashObjects(window.gameScene);
            }
        }, 100);

        function spawnTrashObjects(scene) {
            // Different materials for trash (metal cans, cartons, plastic debris)
            const trashMaterials = [
                new THREE.MeshStandardMaterial({ color: 0x888888, roughness: 0.4, metalness: 0.8 }), // Metal
                new THREE.MeshStandardMaterial({ color: 0x4a3b32, roughness: 0.9 }), // Cardboard brown
                new THREE.MeshStandardMaterial({ color: 0xdc3545, roughness: 0.6 }), // Red plastic
                new THREE.MeshStandardMaterial({ color: 0x6c757d, roughness: 0.5 })  // Grey debris
            ];

            const geoBox = new THREE.BoxGeometry(0.7, 0.7, 0.7);
            const geoCylinder = new THREE.CylinderGeometry(0.3, 0.3, 0.9, 6);
            const geoSphere = new THREE.SphereGeometry(0.4, 5, 5);

            // Scatter 50 trash items randomly across the field
            for (let i = 0; i < 50; i++) {
                let randGeom;
                const rType = Math.random();
                if (rType < 0.33) randGeom = geoBox;
                else if (rType < 0.66) randGeom = geoCylinder;
                else randGeom = geoSphere;

                const randMat = trashMaterials[Math.floor(Math.random() * trashMaterials.length)];
                const trashMesh = new THREE.Mesh(randGeom, randMat);

                const x = (Math.random() - 0.5) * 380;
                const z = (Math.random() - 0.5) * 380;
                
                trashMesh.position.set(x, 0.35, z);
                trashMesh.rotation.x = Math.random() * Math.PI;
                trashMesh.rotation.y = Math.random() * Math.PI;
                trashMesh.rotation.z = Math.random() * Math.PI;

                trashMesh.castShadow = true;
                trashMesh.receiveShadow = true;

                scene.add(trashMesh);
            }
        }
    });
</script>