<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tidong Real 3D Asset City Simulation</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: #000;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        .game-hud {
            position: absolute;
            top: 15px;
            left: 15px;
            right: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 100000;
            pointer-events: none;
        }
        .hud-card {
            background: rgba(15, 23, 42, 0.95);
            border: 2px solid #f59e0b;
            color: #fff;
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
            pointer-events: auto;
        }
        .wallet-badge { color: #10b981; }
        .loading-status {
            position: absolute;
            top: 70px;
            left: 15px;
            background: rgba(0,0,0,0.8);
            color: #38bdf8;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 100000;
        }
        .game-touch-controls {
            position: absolute;
            bottom: 25px;
            right: 25px;
            z-index: 200000;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            user-select: none;
        }
        .touch-btn {
            width: 70px;
            height: 70px;
            background: rgba(245, 158, 11, 0.95);
            border: 3px solid #fff;
            border-radius: 50%;
            color: #000;
            font-size: 26px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 18px rgba(0,0,0,0.6);
            cursor: pointer;
            outline: none;
            -webkit-tap-highlight-color: transparent;
        }
        .touch-row { display: flex; gap: 20px; }
        .action-prompt-container {
            position: absolute;
            bottom: 120px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 15px;
            z-index: 250000;
        }
        .action-btn {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 14px 24px;
            border-radius: 30px;
            font-size: 16px;
            font-weight: bold;
            border: 3px solid #ffffff;
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.6);
            cursor: pointer;
            display: none;
        }
    </style>
</head>
<body>

<div class="game-fullscreen-wrapper" id="gameFullscreenWrapper">
    <div class="game-hud">
        <div class="hud-card" id="hudLocationTitle">🌊 Tidong Real 3D Coastal Estate</div>
        <div class="hud-card">💰 T-Coins: <span class="wallet-badge" id="walletBalance">2,00,000</span></div>
    </div>
    <div class="loading-status" id="loadingStatus">🔄 3D मॉडल (GLTF Assets) लोड हो रहे हैं...</div>

    <div id="gameContainer" style="width: 100%; height: 100%; position: absolute; top:0; left:0; z-index: 1;"></div>

    <div class="action-prompt-container" style="z-index: 400000;">
        <button type="button" class="action-btn" id="enterShowroomBtn" onclick="enterShowroomInterior()">🏢 विला / शोरूम के अंदर जाएँ</button>
    </div>

    <div class="game-touch-controls" style="z-index: 400000;">
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

<!-- Three.js + GLTFLoader CDNs -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>

<script>
    const touchState = { up: false, down: false, left: false, right: false };
    function setTouchDirection(dir, isPressed) { touchState[dir] = isPressed; }

    let scene, camera, renderer, playerGroup;
    const gltfLoader = new THREE.GLTFLoader();

    document.addEventListener("DOMContentLoaded", function () {
        const wrapper = document.getElementById('gameContainer');
        if (!wrapper) return;

        // 1. Scene
        scene = new THREE.Scene();
        scene.background = new THREE.Color(0x60a5fa); // तटीय नीला आसमान

        // 2. Camera
        camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 2000);
        camera.position.set(0, 15, 25);

        // 3. Renderer
        renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.shadowMap.enabled = true;
        wrapper.appendChild(renderer.domElement);

        // 4. Lighting
        scene.add(new THREE.AmbientLight(0xffffff, 0.9));
        const sunLight = new THREE.DirectionalLight(0xffffff, 1.2);
        sunLight.position.set(100, 200, 100);
        sunLight.castShadow = true;
        scene.add(sunLight);

        // 5. Ground & Coastal Ocean
        const ground = new THREE.Mesh(
            new THREE.PlaneGeometry(1500, 1500),
            new THREE.MeshStandardMaterial({ color: 0x34d399, roughness: 0.8 })
        );
        ground.rotation.x = -Math.PI / 2;
        scene.add(ground);

        // समुद्र (Sea Coast)
        const sea = new THREE.Mesh(
            new THREE.PlaneGeometry(1000, 1500),
            new THREE.MeshStandardMaterial({ color: 0x0284c7, roughness: 0.1, metalness: 0.8, transparent: true, opacity: 0.85 })
        );
        sea.rotation.x = -Math.PI / 2;
        sea.position.set(600, -0.1, 0);
        scene.add(sea);

        // 6. Player Group
        playerGroup = new THREE.Group();
        playerGroup.position.set(0, 0, 10);
        scene.add(playerGroup);

        // --- 🚀 REAL 3D ASSETS LOADING (GLTF/GLB) ---
        const statusElem = document.getElementById('loadingStatus');

        // A. लोड करें असली 3D Car Model (.glb)
        const carUrl = 'https://raw.githubusercontent.com/KhronosGroup/glTF-Sample-Models/master/2.0/ToyCar/glTF-Binary/ToyCar.glb';
        gltfLoader.load(carUrl, function (gltf) {
            const carModel = gltf.scene;
            carModel.scale.set(40, 40, 40);
            carModel.position.set(-20, 0, -10);
            scene.add(carModel);
            statusElem.innerText = "✅ 3D कार मॉडल लोड हो गया!";
        }, undefined, function (err) {
            console.log("Car load fallback");
        });

        // B. लोड करें असली 3D Building / Villa Model (.glb)
        const houseUrl = 'https://raw.githubusercontent.com/KhronosGroup/glTF-Sample-Models/master/2.0/CesiumMilkTruck/glTF-Binary/CesiumMilkTruck.glb';
        gltfLoader.load(houseUrl, function (gltf) {
            const houseModel = gltf.scene;
            houseModel.scale.set(3, 3, 3);
            houseModel.position.set(20, 0, -30);
            scene.add(houseModel);
        });

        // C. कैरेक्टर का 3D अवतार
        const charMesh = new THREE.Mesh(
            new THREE.CylinderGeometry(0.6, 0.6, 2.5, 16),
            new THREE.MeshStandardMaterial({ color: 0x2563eb })
        );
        charMesh.position.y = 1.25;
        playerGroup.add(charMesh);

        // Controls & Loop
        const speed = 0.8;
        const keys = {};
        window.addEventListener('keydown', (e) => { keys[e.key.toLowerCase()] = true; });
        window.addEventListener('keyup', (e) => { keys[e.key.toLowerCase()] = false; });

        function animate() {
            requestAnimationFrame(animate);

            if (keys['arrowup'] || keys['w'] || touchState.up) playerGroup.position.z -= speed;
            if (keys['arrowdown'] || keys['s'] || touchState.down) playerGroup.position.z += speed;
            if (keys['arrowleft'] || keys['a'] || touchState.left) playerGroup.position.x -= speed;
            if (keys['arrowright'] || keys['d'] || touchState.right) playerGroup.position.x += speed;

            camera.position.x = playerGroup.position.x;
            camera.position.z = playerGroup.position.z + 20;
            camera.position.y = 12;
            camera.lookAt(playerGroup.position.x, playerGroup.position.y + 1, playerGroup.position.z);

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