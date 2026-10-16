<!-- File Path: resources/views/member/game/partials/player-character.blade.php -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // जब तक Three.js का scene लोड न हो, तब तक चेक करते रहें
        const checkSceneInterval = setInterval(() => {
            if (window.gameScene) {
                clearInterval(checkSceneInterval);
                initPlayer(window.gameScene);
            }
        }, 100);

        function initPlayer(scene) {
            // --- 🧑‍💻 रियलिस्टिक कैरेक्टर ग्रुप ---
            const playerGroup = new THREE.Group();

            // शरीर (Torso)
            const torsoGeo = new THREE.BoxGeometry(2.0, 3.0, 1.2);
            const torsoMat = new THREE.MeshStandardMaterial({ color: 0x1d4ed8 }); // नीली जैकेट
            const torso = new THREE.Mesh(torsoGeo, torsoMat);
            torso.position.y = 2.5;
            torso.castShadow = true;
            playerGroup.add(torso);

            // सिर (Head)
            const headGeo = new THREE.SphereGeometry(0.9, 16, 16);
            const headMat = new THREE.MeshStandardMaterial({ color: 0xffdbac }); // चेहरा
            const head = new THREE.Mesh(headGeo, headMat);
            head.position.y = 4.5;
            playerGroup.add(head);

            // पैर (Legs)
            const legGeo = new THREE.BoxGeometry(0.6, 2.2, 0.6);
            const legMat = new THREE.MeshStandardMaterial({ color: 0x111111 }); // पैंट
            
            const leftLeg = new THREE.Mesh(legGeo, legMat);
            leftLeg.position.set(-0.5, 0.5, 0);
            playerGroup.add(leftLeg);

            const rightLeg = new THREE.Mesh(legGeo, legMat);
            rightLeg.position.set(0.5, 0.5, 0);
            playerGroup.add(rightLeg);

            // शुरुआती पोजीशन (सड़क के पास)
            playerGroup.position.set(0, 0, 5);
            scene.add(playerGroup);
            window.playerCharacter = playerGroup;

            // --- 🎮 कीबोर्ड और टच कंट्रोल्स ---
            const speed = 0.7;
            const keys = {};

            window.addEventListener('keydown', (e) => { 
                keys[e.key.toLowerCase()] = true; 
                if (['arrowup', 'arrowdown', 'arrowleft', 'arrowright', 'w', 's', 'a', 'd'].includes(e.key.toLowerCase())) {
                    e.preventDefault();
                }
            });

            window.addEventListener('keyup', (e) => { 
                keys[e.key.toLowerCase()] = false; 
            });

            // स्क्रीन के टच बटन के लिए फंक्शन
            window.movePlayerDirection = function(direction) {
                if (!window.playerCharacter) return;
                if (direction === 'up') window.playerCharacter.position.z -= speed * 3;
                if (direction === 'down') window.playerCharacter.position.z += speed * 3;
                if (direction === 'left') window.playerCharacter.position.x -= speed * 3;
                if (direction === 'right') window.playerCharacter.position.x += speed * 3;
            };

            let walkCycle = 0;

            // एनिमेशन लूप (कैरेक्टर मूवमेंट और कैमरा)
            function animatePlayer() {
                requestAnimationFrame(animatePlayer);

                let moving = false;

                if (keys['arrowup'] || keys['w']) { playerGroup.position.z -= speed; moving = true; }
                if (keys['arrowdown'] || keys['s']) { playerGroup.position.z += speed; moving = true; }
                if (keys['arrowleft'] || keys['a']) { playerGroup.position.x -= speed; moving = true; }
                if (keys['arrowright'] || keys['d']) { playerGroup.position.x += speed; moving = true; }

                // चलते वक्त पैर हिलाने का एनिमेशन
                if (moving) {
                    walkCycle += 0.2;
                    leftLeg.rotation.x = Math.sin(walkCycle) * 0.7;
                    rightLeg.rotation.x = -Math.sin(walkCycle) * 0.7;
                } else {
                    leftLeg.rotation.x = 0;
                    rightLeg.rotation.x = 0;
                }

                // कैमरा को कैरेक्टर के पीछे सेट करना
                if (window.gameCamera) {
                    window.gameCamera.position.x = playerGroup.position.x;
                    window.gameCamera.position.z = playerGroup.position.z + 18;
                    window.gameCamera.position.y = 10;
                    window.gameCamera.lookAt(playerGroup.position.x, playerGroup.position.y + 2, playerGroup.position.z);
                }
            }
            animatePlayer();
        }
    });
</script>