<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tidong 3D Open-World Simulation Pro</title>
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
            width: 75px;
            height: 75px;
            background: rgba(234, 179, 8, 0.95);
            border: 3px solid #fff;
            border-radius: 50%;
            color: #000;
            font-size: 28px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 20px rgba(0,0,0,0.6);
            cursor: pointer;
            outline: none;
            -webkit-tap-highlight-color: transparent;
        }
        .touch-row {
            display: flex;
            gap: 25px;
        }
        .hud-overlay {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #fff;
            font-family: Arial, sans-serif;
            font-size: 18px;
            background: rgba(0,0,0,0.6);
            padding: 10px 20px;
            border-radius: 8px;
            z-index: 100000;
            border: 1px solid #eab308;
        }
    </style>
</head>
<body>

<div class="game-fullscreen-wrapper" id="gameFullscreenWrapper">
    <div class="hud-overlay">🏢 Tidong Open-World | 탐험 모드</div>
    <div id="gameContainer" style="width: 100%; height: 100%;"></div>

    <!-- टच कंट्रोल्स -->
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
        scene.background = new THREE.Color(0x7ec0ee); // सुंदर आसमान

        // 2. Camera Setup
        const camera = new THREE.PerspectiveCamera(65, window.innerWidth / window.innerHeight, 0.1, 2000);
        window.gameCamera = camera;
        camera.position.set(0, 18, 35);
        camera.lookAt(0, 0, 0);

        // 3. Renderer Setup
        const renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.shadowMap.enabled = true;
        wrapper.appendChild(renderer.domElement);

        // 4. Lighting
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.75);
        scene.add(ambientLight);

        const dirLight = new THREE.DirectionalLight(0xffffff, 1.1);
        dirLight.position.set(100, 150, 100);
        dirLight.castShadow = true;
        scene.add(dirLight);

        // 5. Ground & Endless Infinite Roads Setup
        const groundGeo = new THREE.PlaneGeometry(3000, 3000);
        const groundMat = new THREE.MeshStandardMaterial({ color: 0x2d6a4f, roughness: 0.9 });
        const ground = new THREE.Mesh(groundGeo, groundMat);
        ground.rotation.x = -Math.PI / 2;
        ground.receiveShadow = true;
        scene.add(ground);

        // पानी का क्षेत्र और टापू (Water & Islands)
        const waterGeo = new THREE.PlaneGeometry(800, 800);
        const waterMat = new THREE.MeshStandardMaterial({ color: 0x1d3557, roughness: 0.1, metalness: 0.8, transparent: true, opacity: 0.85 });
        const water = new THREE.Mesh(waterGeo, waterMat);
        water.rotation.x = -Math.PI / 2;
        water.position.set(400, -0.2, 0);
        scene.add(water);

        // पानी में टापू (Island)
        const islandGeo = new THREE.CylinderGeometry(80, 90, 2, 32);
        const islandMat = new THREE.MeshStandardMaterial({ color: 0xd4a373 });
        const island = new THREE.Mesh(islandGeo, islandMat);
        island.position.set(400, 0.5, 0);
        scene.add(island);

        // टापू पर सुंदर पेड़
        function createDetailedTree(x, z) {
            const treeGroup = new THREE.Group();
            const trunkMat = new THREE.MeshStandardMaterial({ color: 0x8b5a2b });
            const trunk = new THREE.Mesh(new THREE.CylinderGeometry(0.6, 0.9, 5, 8), trunkMat);
            trunk.position.y = 2.5;
            trunk.castShadow = true;
            treeGroup.add(trunk);

            const leavesMat = new THREE.MeshStandardMaterial({ color: 0x2b9348, roughness: 0.6 });
            const leaves1 = new THREE.Mesh(new THREE.SphereGeometry(2.5, 8, 8), leavesMat);
            leaves1.position.y = 5.5;
            treeGroup.add(leaves1);

            const leaves2 = new THREE.Mesh(new THREE.SphereGeometry(1.8, 8, 8), leavesMat);
            leaves2.position.set(1.2, 6.2, 0);
            treeGroup.add(leaves2);

            treeGroup.position.set(x, 0, z);
            scene.add(treeGroup);
        }
        createDetailedTree(380, -10);
        createDetailedTree(420, 15);

        // लंबी और कभी न खत्म होने वाली सड़कें (Endless Roads)
        const roadMat = new THREE.MeshStandardMaterial({ color: 0x1a1a1a, roughness: 0.4 });
        const roads = [];
        for (let i = -5; i <= 5; i++) {
            const roadSegment = new THREE.Mesh(new THREE.BoxGeometry(450, 0.1, 28), roadMat);
            roadSegment.position.set(i * 450, 0.05, 0);
            roadSegment.receiveShadow = true;
            scene.add(roadSegment);
            roads.push(roadSegment);
        }

        // --- 🚗 रियलिस्टिक कार मॉडल ---
        function createRealisticCar(colorHex) {
            const carGroup = new THREE.Group();
            const bodyMat = new THREE.MeshStandardMaterial({ color: colorHex, metalness: 0.8, roughness: 0.2 });
            
            const lower = new THREE.Mesh(new THREE.BoxGeometry(4.8, 1.3, 9.5), bodyMat);
            lower.position.y = 0.9;
            lower.castShadow = true;
            carGroup.add(lower);

            const upper = new THREE.Mesh(new THREE.BoxGeometry(3.8, 1.1, 5.2), bodyMat);
            upper.position.set(0, 1.8, -0.4);
            upper.castShadow = true;
            carGroup.add(upper);

            const glassMat = new THREE.MeshStandardMaterial({ color: 0x111111, roughness: 0.1, metalness: 0.9 });
            const windshield = new THREE.Mesh(new THREE.BoxGeometry(3.6, 0.9, 0.1), glassMat);
            windshield.position.set(0, 1.8, 2.35);
            carGroup.add(windshield);

            const wheelGeo = new THREE.CylinderGeometry(0.55, 0.55, 0.4, 16);
            const wheelMat = new THREE.MeshStandardMaterial({ color: 0x111111 });
            [[ -2.4, 0.55, 3 ], [ 2.4, 0.55, 3 ], [ -2.4, 0.55, -3 ], [ 2.4, 0.55, -3 ]].forEach(pos => {
                const wheel = new THREE.Mesh(wheelGeo, wheelMat);
                wheel.rotation.z = Math.PI / 2;
                wheel.position.set(...pos);
                carGroup.add(wheel);
            });

            return carGroup;
        }

        // --- 🏢 भव्य और रियलिस्टिक शोरूम (Showrooms) ---
        const animatedWorkers = [];

        function createShowroom(x, z, width, height, depth, wallColor, signText, hasCars) {
            const group = new THREE.Group();

            // बिल्डिंग दीवारें
            const bMat = new THREE.MeshStandardMaterial({ color: wallColor, roughness: 0.3 });
            const building = new THREE.Mesh(new THREE.BoxGeometry(width, height, depth), bMat);
            building.position.y = height / 2;
            building.castShadow = true;
            building.receiveShadow = true;
            group.add(building);

            // कांच की बड़ी पारदर्शी दीवार (Glass Facade)
            const glassMat = new THREE.MeshStandardMaterial({ color: 0x87ceeb, transparent: true, opacity: 0.5, roughness: 0.1 });
            const glass = new THREE.Mesh(new THREE.BoxGeometry(width * 0.85, height * 0.7, 0.3), glassMat);
            glass.position.set(0, height * 0.38, depth / 2 + 0.1);
            group.add(glass);

            // शोरूम का चमकता हुआ बोर्ड
            const boardMat = new THREE.MeshStandardMaterial({ color: 0xf59e0b, emissive: 0xd97706, emissiveIntensity: 0.6 });
            const board = new THREE.Mesh(new THREE.BoxGeometry(width * 0.9, 2.8, 0.6), boardMat);
            board.position.set(0, height + 1.4, depth / 2);
            group.add(board);

            // अंदर टहलने वाला वर्कर
            const workerGroup = new THREE.Group();
            const wBody = new THREE.Mesh(new THREE.BoxGeometry(0.9, 1.6, 0.6), new THREE.MeshStandardMaterial({ color: 0xffffff }));
            wBody.position.y = 1.1;
            const wHead = new THREE.Mesh(new THREE.SphereGeometry(0.38, 12, 12), new THREE.MeshStandardMaterial({ color: 0xffdbac }));
            wHead.position.y = 2.1;
            workerGroup.add(wBody);
            workerGroup.add(wHead);
            workerGroup.position.set(0, 0, 0);
            group.add(workerGroup);

            animatedWorkers.push({
                mesh: workerGroup,
                minX: -width * 0.3,
                maxX: width * 0.3,
                speed: 0.03,
                dir: 1
            });

            // बाहर खड़ी गाड़ियाँ
            if (hasCars) {
                const car1 = createRealisticCar(0xef4444);
                car1.position.set(-7, 0, depth / 2 + 9);
                group.add(car1);

                const car2 = createRealisticCar(0x3b82f6);
                car2.position.set(7, 0, depth / 2 + 9);
                group.add(car2);
            }

            group.position.set(x, 0, z);
            scene.add(group);
        }

        // सड़क के दोनों तरफ भव्य शोरूम
        createShowroom(-70, -65, 38, 20, 30, 0x1e293b, "🏎️ Tidong SuperCars Hub", true);
        createShowroom(70, -65, 32, 17, 26, 0x1d4ed8, "📱 Aepple & Samsing Hub", false);
        createShowroom(-70, 65, 34, 18, 28, 0x047857, "🍎 Tidong Fresh Market", false);
        createShowroom(70, 65, 36, 19, 30, 0xb45309, "👔 Gucchi & Niki Fashion", false);

        // --- 🧑‍💻 रियलिस्टिक कैरेक्टर (Player) ---
        const playerGroup = new THREE.Group();

        const pTorso = new THREE.Mesh(new THREE.BoxGeometry(1.3, 2.2, 0.8), new THREE.MeshStandardMaterial({ color: 0x2563eb, roughness: 0.3 }));
        pTorso.position.y = 2.1;
        pTorso.castShadow = true;
        playerGroup.add(pTorso);

        const pHead = new THREE.Mesh(new THREE.SphereGeometry(0.55, 16, 16), new THREE.MeshStandardMaterial({ color: 0xffdbac }));
        pHead.position.y = 3.5;
        pHead.castShadow = true;
        playerGroup.add(pHead);

        const pHair = new THREE.Mesh(new THREE.BoxGeometry(0.6, 0.25, 0.6), new THREE.MeshStandardMaterial({ color: 0x111111 }));
        pHair.position.y = 3.9;
        playerGroup.add(pHair);

        const pLegMat = new THREE.MeshStandardMaterial({ color: 0x1f2937 });
        const leftLeg = new THREE.Mesh(new THREE.BoxGeometry(0.45, 1.6, 0.45), pLegMat);
        leftLeg.position.set(-0.35, 0.8, 0);
        playerGroup.add(leftLeg);

        const rightLeg = new THREE.Mesh(new THREE.BoxGeometry(0.45, 1.6, 0.45), pLegMat);
        rightLeg.position.set(0.35, 0.8, 0);
        playerGroup.add(rightLeg);

        playerGroup.position.set(0, 0, 5);
        scene.add(playerGroup);
        window.playerCharacter = playerGroup;

        // कंट्रोल्स और मूवमेंट
        const speed = 0.85;
        const keys = {};

        window.addEventListener('keydown', (e) => { keys[e.key.toLowerCase()] = true; });
        window.addEventListener('keyup', (e) => { keys[e.key.toLowerCase()] = false; });

        let walkCycle = 0;

        // 6. Render Loop & Infinite World Management
        function animate() {
            requestAnimationFrame(animate);

            // वर्कर्स का टहलना
            animatedWorkers.forEach(w => {
                w.mesh.position.x += w.speed * w.dir;
                if (w.mesh.position.x > w.maxX || w.mesh.position.x < w.minX) {
                    w.dir *= -1;
                }
            });

            // कैरेक्टर मूवमेंट
            let moving = false;
            if (keys['arrowup'] || keys['w'] || touchState.up) { playerGroup.position.z -= speed; moving = true; }
            if (keys['arrowdown'] || keys['s'] || touchState.down) { playerGroup.position.z += speed; moving = true; }
            if (keys['arrowleft'] || keys['a'] || touchState.left) { playerGroup.position.x -= speed; moving = true; }
            if (keys['arrowright'] || keys['d'] || touchState.right) { playerGroup.position.x += speed; moving = true; }

            // पैरों का चलने वाला रियलिस्टिक एनिमेशन
            if (moving) {
                walkCycle += 0.3;
                leftLeg.rotation.x = Math.sin(walkCycle) * 0.7;
                rightLeg.rotation.x = -Math.sin(walkCycle) * 0.7;
            } else {
                leftLeg.rotation.x = 0;
                rightLeg.rotation.x = 0;
            }

            // इनफिनिट रोड लूपिंग (सड़क कभी खत्म न हो, आगे अपने आप आगे खिसके)
            roads.forEach(road => {
                if (playerGroup.position.x - road.position.x > 600) {
                    road.position.x += 450 * roads.length;
                }
            });

            // कैमरा फॉलो (स्मूथ थर्ड पर्सन व्यू)
            if (window.gameCamera) {
                window.gameCamera.position.x = playerGroup.position.x;
                window.gameCamera.position.z = playerGroup.position.z + 22;
                window.gameCamera.position.y = 12;
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