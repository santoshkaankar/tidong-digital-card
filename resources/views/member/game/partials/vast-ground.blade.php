<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tidong 3D Open-World Simulation</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: #000;
        }
        .game-fullscreen-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 99999;
            background-color: #000;
            overflow: hidden;
        }
        .game-touch-controls {
            position: absolute;
            bottom: 30px;
            right: 30px;
            z-index: 200000;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            user-select: none;
            pointer-events: auto;
        }
        .touch-btn {
            width: 70px;
            height: 70px;
            background: rgba(234, 179, 8, 0.95);
            border: 3px solid #fff;
            border-radius: 50%;
            color: #000;
            font-size: 26px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 15px rgba(0,0,0,0.6);
            cursor: pointer;
            outline: none;
            -webkit-tap-highlight-color: transparent;
        }
        .touch-row {
            display: flex;
            gap: 25px;
        }
    </style>
</head>
<body>

<div class="game-fullscreen-wrapper" id="gameFullscreenWrapper">
    <div id="gameContainer" style="width: 100%; height: 100%;"></div>

    <!-- टच कंट्रोल बटन -->
    <div class="game-touch-controls">
        <button type="button" class="touch-btn" 
                ontouchstart="setTouchDirection('up', true); event.preventDefault();" 
                ontouchend="setTouchDirection('up', false)"
                onmousedown="setTouchDirection('up', true)" 
                onmouseup="setTouchDirection('up', false)">⬆️</button>
        
        <div class="touch-row">
            <button type="button" class="touch-btn" 
                    ontouchstart="setTouchDirection('left', true); event.preventDefault();" 
                    ontouchend="setTouchDirection('left', false)"
                    onmousedown="setTouchDirection('left', true)" 
                    onmouseup="setTouchDirection('left', false)">⬅️</button>
            
            <button type="button" class="touch-btn" 
                    ontouchstart="setTouchDirection('down', true); event.preventDefault();" 
                    ontouchend="setTouchDirection('down', false)"
                    onmousedown="setTouchDirection('down', true)" 
                    onmouseup="setTouchDirection('down', false)">⬇️</button>
            
            <button type="button" class="touch-btn" 
                    ontouchstart="setTouchDirection('right', true); event.preventDefault();" 
                    ontouchend="setTouchDirection('right', false)"
                    onmousedown="setTouchDirection('right', true)" 
                    onmouseup="setTouchDirection('right', false)">➡️</button>
        </div>
    </div>
</div>

<!-- Load Three.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
    const touchState = { up: false, down: false, left: false, right: false };
    function setTouchDirection(dir, isPressed) {
        touchState[dir] = isPressed;
    }

    document.addEventListener("DOMContentLoaded", function () {
        const wrapper = document.getElementById('gameContainer');
        const parentWrapper = document.getElementById('gameFullscreenWrapper');
        if (!wrapper) return;

        function triggerAutoFullscreen() {
            if (!document.fullscreenElement) {
                if (parentWrapper.requestFullscreen) {
                    parentWrapper.requestFullscreen().catch(() => {});
                } else if (parentWrapper.webkitRequestFullscreen) {
                    parentWrapper.webkitRequestFullscreen();
                }
            }
        }
        window.addEventListener('click', triggerAutoFullscreen, { once: true });
        window.addEventListener('touchstart', triggerAutoFullscreen, { once: true });

        // 1. Scene Setup
        const scene = new THREE.Scene();
        window.gameScene = scene;
        scene.background = new THREE.Color(0x87ceeb);

        // 2. Camera Setup
        const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 1000);
        window.gameCamera = camera;
        camera.position.set(0, 15, 30);
        camera.lookAt(0, 0, 0);

        // 3. Renderer Setup
        const renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.shadowMap.enabled = true;
        wrapper.appendChild(renderer.domElement);

        // 4. Lighting
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.7);
        scene.add(ambientLight);

        const dirLight = new THREE.DirectionalLight(0xffffff, 1.0);
        dirLight.position.set(50, 100, 50);
        dirLight.castShadow = true;
        scene.add(dirLight);

        // 5. Ground & Roads
        const groundGeo = new THREE.PlaneGeometry(1000, 1000);
        const groundMat = new THREE.MeshStandardMaterial({ color: 0x3a5a40, roughness: 0.9 });
        const ground = new THREE.Mesh(groundGeo, groundMat);
        ground.rotation.x = -Math.PI / 2;
        ground.receiveShadow = true;
        scene.add(ground);

        const roadMat = new THREE.MeshStandardMaterial({ color: 0x222222, roughness: 0.5 });
        const mainRoad = new THREE.Mesh(new THREE.BoxGeometry(1000, 0.1, 26), roadMat);
        mainRoad.position.y = 0.05;
        scene.add(mainRoad);

        const crossRoad = new THREE.Mesh(new THREE.BoxGeometry(26, 0.1, 1000), roadMat);
        crossRoad.position.y = 0.06;
        scene.add(crossRoad);

        // --- 🚗 रियलिस्टिक कार बनाने का फंक्शन ---
        function createRealisticCar(colorHex) {
            const carGroup = new THREE.Group();
            
            // कार की बॉडी (नीला निचला हिस्सा)
            const bodyMat = new THREE.MeshStandardMaterial({ color: colorHex, metalness: 0.7, roughness: 0.2 });
            const lowerBody = new THREE.Mesh(new THREE.BoxGeometry(4.5, 1.2, 9), bodyMat);
            lowerBody.position.y = 0.8;
            lowerBody.castShadow = true;
            carGroup.add(lowerBody);

            // कार का केबिन (ऊपरी हिस्सा)
            const upperBody = new THREE.Mesh(new THREE.BoxGeometry(3.8, 1.0, 5), bodyMat);
            upperBody.position.set(0, 1.7, -0.3);
            upperBody.castShadow = true;
            carGroup.add(upperBody);

            // कांच (Windshield)
            const glassMat = new THREE.MeshStandardMaterial({ color: 0x111111, roughness: 0.1, metalness: 0.9 });
            const windshield = new THREE.Mesh(new THREE.BoxGeometry(3.6, 0.8, 0.1), glassMat);
            windshield.position.set(0, 1.7, 2.2);
            carGroup.add(windshield);

            // पहिए (Wheels)
            const wheelGeo = new THREE.CylinderGeometry(0.5, 0.5, 0.4, 16);
            const wheelMat = new THREE.MeshStandardMaterial({ color: 0x111111 });
            const wheelPositions = [
                [-2.3, 0.5, 2.8], [2.3, 0.5, 2.8],
                [-2.3, 0.5, -2.8], [2.3, 0.5, -2.8]
            ];
            wheelPositions.forEach(pos => {
                const wheel = new THREE.Mesh(wheelGeo, wheelMat);
                wheel.rotation.z = Math.PI / 2;
                wheel.position.set(...pos);
                carGroup.add(wheel);
            });

            return carGroup;
        }

        // --- 🏢 रियलिस्टिक शोरूम और गाड़ियाँ ---
        const animatedWorkers = [];

        function createShowroom(x, z, width, height, depth, color, title, hasCars) {
            const group = new THREE.Group();

            // बिल्डिंग ढांचा
            const bGeo = new THREE.BoxGeometry(width, height, depth);
            const bMat = new THREE.MeshStandardMaterial({ color: color, roughness: 0.3 });
            const building = new THREE.Mesh(bGeo, bMat);
            building.position.y = height / 2;
            building.castShadow = true;
            building.receiveShadow = true;
            group.add(building);

            // कांच का खूबसूरत बड़ा फसाड (Glass Storefront)
            const glassGeo = new THREE.BoxGeometry(width * 0.8, height * 0.65, 0.3);
            const glassMat = new THREE.MeshStandardMaterial({ color: 0x87ceeb, transparent: true, opacity: 0.6, roughness: 0.1 });
            const glass = new THREE.Mesh(glassGeo, glassMat);
            glass.position.set(0, height * 0.35, depth / 2 + 0.1);
            group.add(glass);

            // शोरूम का बोर्ड
            const boardGeo = new THREE.BoxGeometry(width * 0.85, 2.5, 0.5);
            const boardMat = new THREE.MeshStandardMaterial({ color: 0xf59e0b, emissive: 0xd97706, emissiveIntensity: 0.5 });
            const board = new THREE.Mesh(boardGeo, boardMat);
            board.position.set(0, height + 1.2, depth / 2);
            group.add(board);

            // अंदर टहलने वाला वर्कर (इंसान जैसा आकार)
            const workerGroup = new THREE.Group();
            const wTorso = new THREE.Mesh(new THREE.BoxGeometry(0.8, 1.4, 0.5), new THREE.MeshStandardMaterial({ color: 0xffffff }));
            wTorso.position.y = 1.0;
            const wHead = new THREE.Mesh(new THREE.SphereGeometry(0.35, 12, 12), new THREE.MeshStandardMaterial({ color: 0xffdbac }));
            wHead.position.y = 1.9;
            workerGroup.add(wTorso);
            workerGroup.add(wHead);
            workerGroup.position.set(0, 0, 0);
            group.add(workerGroup);

            animatedWorkers.push({
                mesh: workerGroup,
                minX: -width * 0.3,
                maxX: width * 0.3,
                speed: 0.025,
                dir: 1
            });

            // शोरूम के बाहर शानदार गाड़ियाँ खड़ी करना
            if (hasCars) {
                const car1 = createRealisticCar(0x3b82f6);
                car1.position.set(-6, 0, depth / 2 + 8);
                group.add(car1);

                const car2 = createRealisticCar(0xef4444);
                car2.position.set(6, 0, depth / 2 + 8);
                group.add(car2);
            }

            group.position.set(x, 0, z);
            scene.add(group);
        }

        createShowroom(-60, -60, 34, 18, 28, 0x1f2937, "🏎️ SuperCars Showroom", true);
        createShowroom(60, -60, 28, 15, 25, 0x1e3a8a, "📱 Mobile & Gadgets Hub", false);
        createShowroom(-60, 60, 30, 16, 26, 0x065f46, "🍎 Tidong Fresh Mart", false);
        createShowroom(60, 60, 32, 17, 28, 0x78350f, "👔 Luxury Clothing", false);

        // --- 🧑‍💻 रियलिस्टिक इंसान जैसा कैरेक्टर (Player) ---
        const playerGroup = new THREE.Group();

        // शरीर (Torso)
        const pTorso = new THREE.Mesh(new THREE.BoxGeometry(1.2, 2.0, 0.7), new THREE.MeshStandardMaterial({ color: 0x2563eb, roughness: 0.3 }));
        pTorso.position.y = 2.0;
        pTorso.castShadow = true;
        playerGroup.add(pTorso);

        // सिर (Head)
        const pHead = new THREE.Mesh(new THREE.SphereGeometry(0.5, 16, 16), new THREE.MeshStandardMaterial({ color: 0xffdbac }));
        pHead.position.y = 3.3;
        pHead.castShadow = true;
        playerGroup.add(pHead);

        // बाल (Hair)
        const pHair = new THREE.Mesh(new THREE.BoxGeometry(0.55, 0.2, 0.55), new THREE.MeshStandardMaterial({ color: 0x111111 }));
        pHair.position.y = 3.65;
        playerGroup.add(pHair);

        // पैर (Legs)
        const pLegMat = new THREE.MeshStandardMaterial({ color: 0x1f2937 });
        const leftLeg = new THREE.Mesh(new THREE.BoxGeometry(0.4, 1.5, 0.4), pLegMat);
        leftLeg.position.set(-0.3, 0.75, 0);
        playerGroup.add(leftLeg);

        const rightLeg = new THREE.Mesh(new THREE.BoxGeometry(0.4, 1.5, 0.4), pLegMat);
        rightLeg.position.set(0.3, 0.75, 0);
        playerGroup.add(rightLeg);

        playerGroup.position.set(0, 0, 5);
        scene.add(playerGroup);
        window.playerCharacter = playerGroup;

        // कंट्रोल्स और मूवमेंट
        const speed = 0.7;
        const keys = {};

        window.addEventListener('keydown', (e) => { keys[e.key.toLowerCase()] = true; });
        window.addEventListener('keyup', (e) => { keys[e.key.toLowerCase()] = false; });

        let walkCycle = 0;

        // Render Loop & Animation
        function animate() {
            requestAnimationFrame(animate);

            // शोरूम के अंदर वर्कर्स का टहलना
            animatedWorkers.forEach(w => {
                w.mesh.position.x += w.speed * w.dir;
                if (w.mesh.position.x > w.maxX || w.mesh.position.x < w.minX) {
                    w.dir *= -1;
                }
            });

            // कैरेक्टर चलना और पैर हिलाना
            let moving = false;
            if (keys['arrowup'] || keys['w'] || touchState.up) { playerGroup.position.z -= speed; moving = true; }
            if (keys['arrowdown'] || keys['s'] || touchState.down) { playerGroup.position.z += speed; moving = true; }
            if (keys['arrowleft'] || keys['a'] || touchState.left) { playerGroup.position.x -= speed; moving = true; }
            if (keys['arrowright'] || keys['d'] || touchState.right) { playerGroup.position.x += speed; moving = true; }

            if (moving) {
                walkCycle += 0.25;
                leftLeg.rotation.x = Math.sin(walkCycle) * 0.6;
                rightLeg.rotation.x = -Math.sin(walkCycle) * 0.6;
            } else {
                leftLeg.rotation.x = 0;
                rightLeg.rotation.x = 0;
            }

            // कैमरा फॉलो
            if (window.gameCamera) {
                window.gameCamera.position.x = playerGroup.position.x;
                window.gameCamera.position.z = playerGroup.position.z + 18;
                window.gameCamera.position.y = 10;
                window.gameCamera.lookAt(playerGroup.position.x, playerGroup.position.y + 2, playerGroup.position.z);
            }

            renderer.render(scene, camera);
        }
        animate();

        window.addEventListener('resize', function() {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    });
</script>

</body>
</html>