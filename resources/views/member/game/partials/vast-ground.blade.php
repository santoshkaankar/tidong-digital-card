<!-- File Path: resources/views/member/game/vast-game.blade.php (या आपकी मुख्य गेम फाइल) -->
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
        /* टच कंट्रोल बटन स्टाइल */
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
        }
        .touch-row {
            display: flex;
            gap: 25px;
        }
    </style>
</head>
<body>

<div class="game-fullscreen-wrapper" id="gameFullscreenWrapper">
    <!-- Three.js Canvas Container -->
    <div id="gameContainer" style="width: 100%; height: 100%;"></div>

    <!-- टच कंट्रोल बटन (एक ही फाइल में शामिल) -->
    <div class="game-touch-controls">
        <button type="button" class="touch-btn" ontouchstart="movePlayerDirection('up')" onclick="movePlayerDirection('up')">⬆️</button>
        <div class="touch-row">
            <button type="button" class="touch-btn" ontouchstart="movePlayerDirection('left')" onclick="movePlayerDirection('left')">⬅️</button>
            <button type="button" class="touch-btn" ontouchstart="movePlayerDirection('down')" onclick="movePlayerDirection('down')">⬇️</button>
            <button type="button" class="touch-btn" ontouchstart="movePlayerDirection('right')" onclick="movePlayerDirection('right')">➡️</button>
        </div>
    </div>
</div>

<!-- Load Three.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const wrapper = document.getElementById('gameContainer');
        const parentWrapper = document.getElementById('gameFullscreenWrapper');
        if (!wrapper) return;

        // फुलस्क्रीन इनेबल करना
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

        const dirLight = new THREE.DirectionalLight(0xffffff, 0.9);
        dirLight.position.set(50, 100, 50);
        dirLight.castShadow = true;
        scene.add(dirLight);

        // 5. Ground & Roads
        const groundGeo = new THREE.PlaneGeometry(900, 900);
        const groundMat = new THREE.MeshStandardMaterial({ color: 0x2e8b57, roughness: 0.8 });
        const ground = new THREE.Mesh(groundGeo, groundMat);
        ground.rotation.x = -Math.PI / 2;
        ground.receiveShadow = true;
        scene.add(ground);

        const roadMat = new THREE.MeshStandardMaterial({ color: 0x1e1e1e });
        const mainRoad = new THREE.Mesh(new THREE.BoxGeometry(900, 0.1, 24), roadMat);
        mainRoad.position.y = 0.05;
        scene.add(mainRoad);

        const crossRoad = new THREE.Mesh(new THREE.BoxGeometry(24, 0.1, 900), roadMat);
        crossRoad.position.y = 0.06;
        scene.add(crossRoad);

        // 6. Showrooms & Workers
        const animatedWorkers = [];

        function createShowroom(x, z, width, height, depth, color, title, hasCarsOutside) {
            const group = new THREE.Group();

            const bGeo = new THREE.BoxGeometry(width, height, depth);
            const bMat = new THREE.MeshStandardMaterial({ color: color, roughness: 0.3 });
            const building = new THREE.Mesh(bGeo, bMat);
            building.position.y = height / 2;
            building.castShadow = true;
            building.receiveShadow = true;
            group.add(building);

            const glassGeo = new THREE.BoxGeometry(width * 0.75, height * 0.6, 0.4);
            const glassMat = new THREE.MeshStandardMaterial({ color: 0x38bdf8, transparent: true, opacity: 0.5, roughness: 0.1 });
            const glass = new THREE.Mesh(glassGeo, glassMat);
            glass.position.set(0, height * 0.35, depth / 2 + 0.1);
            group.add(glass);

            const boardGeo = new THREE.BoxGeometry(width * 0.8, 2.2, 0.6);
            const boardMat = new THREE.MeshStandardMaterial({ color: 0xf59e0b, emissive: 0xd97706, emissiveIntensity: 0.4 });
            const board = new THREE.Mesh(boardGeo, boardMat);
            board.position.set(0, height + 1.2, depth / 2);
            group.add(board);

            const workerGeo = new THREE.BoxGeometry(1.2, 2.5, 0.8);
            const workerMat = new THREE.MeshStandardMaterial({ color: 0xffffff });
            const worker = new THREE.Mesh(workerGeo, workerMat);
            worker.position.set(0, 1.25, 0);
            group.add(worker);

            animatedWorkers.push({
                mesh: worker,
                minX: -width * 0.3,
                maxX: width * 0.3,
                speed: 0.03,
                dir: 1
            });

            if (hasCarsOutside) {
                for (let c = -1; c <= 1; c += 2) {
                    const carGeo = new THREE.BoxGeometry(5, 2, 8);
                    const carMat = new THREE.MeshStandardMaterial({ color: c > 0 ? 0xdc2626 : 0x2563eb, metalness: 0.8 });
                    const parkedCar = new THREE.Mesh(carGeo, carMat);
                    parkedCar.position.set(c * 6, 1, depth / 2 + 7);
                    parkedCar.castShadow = true;
                    group.add(parkedCar);
                }
            }

            group.position.set(x, 0, z);
            scene.add(group);
        }

        createShowroom(-50, -50, 30, 16, 25, 0x334155, "🏎️ SuperCars Hub", true);
        createShowroom(50, -50, 26, 14, 24, 0x1e3a8a, "📱 Gadgets Hub", false);
        createShowroom(-50, 50, 28, 15, 26, 0x065f46, "🍎 Grocery Mart", false);
        createShowroom(50, 50, 32, 16, 28, 0x78350f, "👔 Clothing Showroom", false);

        // 7. Player Character Setup (एकदम साफ और फंक्शनल)
        const playerGroup = new THREE.Group();

        const torsoGeo = new THREE.BoxGeometry(2.0, 3.0, 1.2);
        const torsoMat = new THREE.MeshStandardMaterial({ color: 0x1d4ed8 });
        const torso = new THREE.Mesh(torsoGeo, torsoMat);
        torso.position.y = 2.5;
        torso.castShadow = true;
        playerGroup.add(torso);

        const headGeo = new THREE.SphereGeometry(0.9, 16, 16);
        const headMat = new THREE.MeshStandardMaterial({ color: 0xffdbac });
        const head = new THREE.Mesh(headGeo, headMat);
        head.position.y = 4.5;
        playerGroup.add(head);

        const legGeo = new THREE.BoxGeometry(0.6, 2.2, 0.6);
        const legMat = new THREE.MeshStandardMaterial({ color: 0x111111 });
        
        const leftLeg = new THREE.Mesh(legGeo, legMat);
        leftLeg.position.set(-0.5, 0.5, 0);
        playerGroup.add(leftLeg);

        const rightLeg = new THREE.Mesh(legGeo, legMat);
        rightLeg.position.set(0.5, 0.5, 0);
        playerGroup.add(rightLeg);

        playerGroup.position.set(0, 0, 5);
        scene.add(playerGroup);
        window.playerCharacter = playerGroup;

        // कंट्रोल्स और मूवमेंट
        const speed = 0.8;
        const keys = {};

        window.addEventListener('keydown', (e) => { keys[e.key.toLowerCase()] = true; });
        window.addEventListener('keyup', (e) => { keys[e.key.toLowerCase()] = false; });

        window.movePlayerDirection = function(direction) {
            if (!window.playerCharacter) return;
            if (direction === 'up') window.playerCharacter.position.z -= 3.5;
            if (direction === 'down') window.playerCharacter.position.z += 3.5;
            if (direction === 'left') window.playerCharacter.position.x -= 3.5;
            if (direction === 'right') window.playerCharacter.position.x += 3.5;
        };

        let walkCycle = 0;

        // Render Loop & Animation
        function animate() {
            requestAnimationFrame(animate);

            // वर्कर्स का चलना
            animatedWorkers.forEach(w => {
                w.mesh.position.x += w.speed * w.dir;
                if (w.mesh.position.x > w.maxX || w.mesh.position.x < w.minX) {
                    w.dir *= -1;
                }
            });

            // कैरेक्टर की बोर्ड कीबोर्ड मूवमेंट
            let moving = false;
            if (keys['arrowup'] || keys['w']) { playerGroup.position.z -= speed; moving = true; }
            if (keys['arrowdown'] || keys['s']) { playerGroup.position.z += speed; moving = true; }
            if (keys['arrowleft'] || keys['a']) { playerGroup.position.x -= speed; moving = true; }
            if (keys['arrowright'] || keys['d']) { playerGroup.position.x += speed; moving = true; }

            if (moving) {
                walkCycle += 0.2;
                leftLeg.rotation.x = Math.sin(walkCycle) * 0.7;
                rightLeg.rotation.x = -Math.sin(walkCycle) * 0.7;
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