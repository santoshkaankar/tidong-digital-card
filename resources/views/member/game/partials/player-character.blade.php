<!-- File Path: resources/views/member/game/partials/player-character.blade.php -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const checkSceneInterval = setInterval(function() {
            if (window.gameScene) {
                clearInterval(checkSceneInterval);
                initPlayerCharacter(window.gameScene);
            }
        }, 100);

        function initPlayerCharacter(scene) {
            // Player Parent Group (Avatar Container)
            const playerGroup = new THREE.Group();
            playerGroup.position.set(0, 0, 0);

            // 1. Character Body Materials
            const bodyMat = new THREE.MeshStandardMaterial({ color: 0x3b82f6, roughness: 0.5 }); // Blue Outfit
            const skinMat = new THREE.MeshStandardMaterial({ color: 0xffcc99, roughness: 0.6 }); // Face Skin
            const eyeMat = new THREE.MeshBasicMaterial({ color: 0x000000 }); // Black Eyes
            const shirtMat = new THREE.MeshStandardMaterial({ color: 0xf59e0b, roughness: 0.4 }); // Yellow Shirt

            // 2. Head (Talking Tom Style Round Head)
            const headGeo = new THREE.SphereGeometry(1.2, 16, 16);
            const head = new THREE.Mesh(headGeo, skinMat);
            head.position.y = 3.2;
            head.castShadow = true;
            playerGroup.add(head);

            // 3. Ears (Cute Bear/Tom Ears)
            const earGeo = new THREE.SphereGeometry(0.4, 8, 8);
            const leftEar = new THREE.Mesh(earGeo, skinMat);
            leftEar.position.set(-0.9, 4.1, 0);
            playerGroup.add(leftEar);

            const rightEar = new THREE.Mesh(earGeo, skinMat);
            rightEar.position.set(0.9, 4.1, 0);
            playerGroup.add(rightEar);

            // 4. Eyes
            const eyeGeo = new THREE.SphereGeometry(0.15, 8, 8);
            const leftEye = new THREE.Mesh(eyeGeo, eyeMat);
            leftEye.position.set(-0.4, 3.4, 1.05);
            playerGroup.add(leftEye);

            const rightEye = new THREE.Mesh(eyeGeo, eyeMat);
            rightEye.position.set(0.4, 3.4, 1.05);
            playerGroup.add(rightEye);

            // 5. Torso / Body (Shirt)
            const bodyGeo = new THREE.CylinderGeometry(0.8, 0.9, 1.8, 12);
            const body = new THREE.Mesh(bodyGeo, shirtMat);
            body.position.y = 1.8;
            body.castShadow = true;
            playerGroup.add(body);

            // 6. Legs
            const legGeo = new THREE.CylinderGeometry(0.3, 0.3, 1, 8);
            const leftLeg = new THREE.Mesh(legGeo, bodyMat);
            leftLeg.position.set(-0.4, 0.5, 0);
            leftLeg.castShadow = true;
            playerGroup.add(leftLeg);

            const rightLeg = new THREE.Mesh(legGeo, bodyMat);
            rightLeg.position.set(0.4, 0.5, 0);
            rightLeg.castShadow = true;
            playerGroup.add(rightLeg);

            // Add Player to Main Game Scene
            scene.add(playerGroup);
            window.gamePlayer = playerGroup;

            // Idle Breathing Animation Effect
            let clock = new THREE.Clock();
            function animatePlayer() {
                requestAnimationFrame(animatePlayer);
                const elapsedTime = clock.getElapsedTime();
                
                // Gentle bobbing motion (Breathing/Idle)
                playerGroup.position.y = Math.sin(elapsedTime * 2) * 0.1;
                head.rotation.y = Math.sin(elapsedTime * 1.5) * 0.1;
            }
            animatePlayer();
        }
    });
</script>