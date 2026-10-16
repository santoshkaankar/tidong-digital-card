<!-- File Path: resources/views/member/game/partials/player-character.blade.php -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        setTimeout(() => {
            const scene = window.gameScene;
            if (!scene) return;

            // --- 🧑‍🤝‍🧑 रियलिस्टिक 3D ह्यूमन कैरेक्टर बनाना ---
            const playerGroup = new THREE.Group();

            // 1. शरीर (Torso / Jacket)
            const torsoGeo = new THREE.BoxGeometry(2.0, 2.8, 1.2);
            const torsoMat = new THREE.MeshStandardMaterial({ color: 0x1d4ed8, roughness: 0.3 }); // स्टाइलिश जैकेट
            const torso = new THREE.Mesh(torsoGeo, torsoMat);
            torso.position.y = 2.4;
            torso.castShadow = true;
            playerGroup.add(torso);

            // 2. सिर (Head & Face)
            const headGeo = new THREE.SphereGeometry(0.8, 16, 16);
            const headMat = new THREE.MeshStandardMaterial({ color: 0xffdbac }); // स्किन टोन
            const head = new THREE.Mesh(headGeo, headMat);
            head.position.y = 4.3;
            head.castShadow = true;
            playerGroup.add(head);

            // 3. बाल (Hair)
            const hairGeo = new THREE.BoxGeometry(0.9, 0.4, 0.9);
            const hairMat = new THREE.MeshStandardMaterial({ color: 0x111111 }); // काले बाल
            const hair = new THREE.Mesh(hairGeo, hairMat);
            hair.position.set(0, 4.9, 0);
            playerGroup.add(hair);

            // 4. पैर (Left & Right Legs - चलने के लिए अलग पार्ट्स)
            const legGeo = new THREE.BoxGeometry(0.6, 2.2, 0.6);
            const legMat = new THREE.MeshStandardMaterial({ color: 0x1f2937 }); // पैंट
            
            const leftLeg = new THREE.Mesh(legGeo, legMat);
            leftLeg.position.set(-0.5, 0.5, 0);
            leftLeg.castShadow = true;
            playerGroup.add(leftLeg);

            const rightLeg = new THREE.Mesh(legGeo, legMat);
            rightLeg.position.set(0.5, 0.5, 0);
            rightLeg.castShadow = true;
            playerGroup.add(rightLeg);

            // कैरेक्टर की शुरुआती पोजीशन (सड़क पर)
            playerGroup.position.set(0, 0, 5);
            scene.add(playerGroup);
            window.playerCharacter = playerGroup;

            // --- 🎮 फंक्शनल कंट्रोल्स और मूवमेंट ---
            const speed = 0.6;
            const keys = {};

            window.addEventListener('keydown', (e) => { keys[e.key] = true; });
            window.addEventListener('keyup', (e) => { keys[e.key] = false; });

            // टच कंट्रोल बटनों के लिए फंक्शन
            window.movePlayerDirection = function(direction) {
                if (direction === 'up') playerGroup.position.z -= speed * 3;
                if (direction === 'down') playerGroup.position.z += speed * 3;
                if (direction === 'left') playerGroup.position.x -= speed * 3;
                if (direction === 'right') playerGroup.position.x += speed * 3;
            };

            let walkCycle = 0;

            // एनिमेशन और मूवमेंट लूप
            function updatePlayerMovement() {
                let moving = false;

                if (keys['ArrowUp'] || keys['w']) { playerGroup.position.z -= speed; moving = true; }
                if (keys['ArrowDown'] || keys['s']) { playerGroup.position.z += speed; moving = true; }
                if (keys['ArrowLeft'] || keys['a']) { playerGroup.position.x -= speed; moving = true; }
                if (keys['ArrowRight'] || keys['d']) { playerGroup.position.x += speed; moving = true; }

                // चलते वक्त पैर हिलाने का रियलिस्टिक एनिमेशन
                if (moving) {
                    walkCycle += 0.15;
                    leftLeg.rotation.x = Math.sin(walkCycle) * 0.6;
                    rightLeg.rotation.x = -Math.sin(walkCycle) * 0.6;
                } else {
                    leftLeg.rotation.x = 0;
                    rightLeg.rotation.x = 0;
                }

                // कैमरा को कैरेक्टर के पीछे सेट करना (Third Person View)
                if (window.gameCamera) {
                    window.gameCamera.position.x = playerGroup.position.x;
                    window.gameCamera.position.z = playerGroup.position.z + 18;
                    window.gameCamera.position.y = 10;
                    window.gameCamera.lookAt(playerGroup.position.x, playerGroup.position.y + 2, playerGroup.position.z);
                }

                requestAnimationFrame(updatePlayerMovement);
            }
            updatePlayerMovement();

        }, 1000);
    });
</script>