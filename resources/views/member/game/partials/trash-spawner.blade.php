<!-- File Path: resources/views/member/game/partials/trash-spawner.blade.php -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        setTimeout(() => {
            const scene = window.gameScene;
            if (!scene) return;

            // बड़े साइज का कचरा/सामान जनरेट करने का फंक्शन
            function spawnLargeTrash() {
                const trashGeo = new THREE.BoxGeometry(2.5, 2.5, 2.5); // बड़ा और साफ़ दिखने वाला साइज
                const trashMat = new THREE.MeshStandardMaterial({ color: 0xef4444 }); // लाल रंग का डिब्बा
                const trash = new THREE.Mesh(trashGeo, trashMat);

                // सड़क और मैदान के आसपास रैंडम पोजीशन
                const rx = (Math.random() - 0.5) * 200;
                const rz = (Math.random() - 0.5) * 200;
                
                // सड़क के बीच में न आए, इसलिए थोड़ा साइड में रखें
                const finalX = Math.abs(rx) < 15 ? rx + 25 : rx;

                trash.position.set(finalX, 1.25, rz);
                trash.castShadow = true;

                scene.add(trash);
            }

            // मैदान में शुरुआत में 15 बड़े कचरे के डिब्बे गिराना
            for (let i = 0; i < 15; i++) {
                spawnLargeTrash();
            }
        }, 1500);
    });
</script>