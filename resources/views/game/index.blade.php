<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Minecraft Real 3D - Mobile & PC Economic Survival</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: sans-serif; user-select: none; }
        body, html { width: 100%; height: 100%; overflow: hidden; background: #000; touch-action: none; }
        #game-canvas { width: 100%; height: 100%; position: absolute; top:0; left:0; z-index: 1; }
        
        /* Crosshair */
        #crosshair {
            position: absolute; top: 50%; left: 50%; width: 12px; height: 12px;
            margin-top: -6px; margin-left: -6px; z-index: 10; pointer-events: none;
            border: 2px solid rgba(255, 255, 255, 0.8); border-radius: 50%; background: rgba(0,255,136,0.5);
        }

        /* Player HUD Header */
        #hud {
            position: absolute; top: 15px; left: 15px; z-index: 10;
            background: rgba(15, 23, 42, 0.85); padding: 12px 18px;
            border-radius: 10px; color: #fff; border: 1.5px solid #00ff88;
            backdrop-filter: blur(4px);
        }
        #hud h3 { color: #00ff88; font-size: 15px; margin-bottom: 3px; }
        .stat-val { color: #ffd700; font-weight: bold; }

        /* Onscreen Mobile Controls & Action Bar */
        .mobile-controls {
            position: absolute; bottom: 20px; left: 20px; z-index: 10;
            display: grid; grid-template-columns: repeat(3, 50px); grid-template-rows: repeat(3, 50px); gap: 5px;
        }
        .ctrl-btn {
            background: rgba(15, 23, 42, 0.8); border: 1px solid #00ff88; color: #fff;
            font-size: 18px; font-weight: bold; border-radius: 8px; display: flex;
            align-items: center; justify-content: center; cursor: pointer; active { background: #00ff88; color: #000; }
        }
        .action-bar {
            position: absolute; bottom: 20px; right: 20px; z-index: 10;
            display: flex; flex-direction: column; gap: 10px;
        }
        .action-btn {
            background: linear-gradient(135deg, #00ff88, #00bbff); border: none; color: #000;
            padding: 12px 20px; font-weight: bold; border-radius: 8px; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.5);
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
</head>
<body>

    <div id="game-canvas"></div>
    <div id="crosshair"></div>

    <!-- HUD -->
    <div id="hud">
        <h3>🎮 Minecraft Real 3D</h3>
        <p>Player: <span class="stat-val">{{ Auth::user()->name ?? 'meenu' }}</span></p>
        <p>Wallet: <span class="stat-val">${{ number_format(Auth::user()->wallet_balance ?? 5000, 2) }}</span></p>
        <p style="font-size: 11px; color: #94a3b8; margin-top: 4px;">Items Mined: <span id="mined-count" class="stat-val">0</span></p>
    </div>

    <!-- Mobile Touch D-Pad Controls -->
    <div class="mobile-controls">
        <div></div>
        <div class="ctrl-btn" id="btn-up">▲</div>
        <div></div>
        <div class="ctrl-btn" id="btn-left">◀</div>
        <div class="ctrl-btn" style="background: rgba(255,255,255,0.1);" id="btn-mine">⛏️</div>
        <div class="ctrl-btn" id="btn-right">▶</div>
        <div></div>
        <div class="ctrl-btn" id="btn-down">▼</div>
        <div></div>
    </div>

    <!-- Action Modules Shortcuts -->
    <div class="action-bar">
        <button class="action-btn" onclick="openFactory()">🏭 Factory Module</button>
        <button class="action-btn" style="background: linear-gradient(135deg, #ff9900, #ff0055); color:#fff;" onclick="openMarket()">🛒 OLX Market</button>
    </div>

    <script>
        // --- 1. Three.js Scene Setup ---
        const scene = new THREE.Scene();
        scene.background = new THREE.Color(0x87ceeb);

        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        camera.position.set(0, 2, 8);

        const renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        document.getElementById('game-canvas').appendChild(renderer.domElement);

        const ambientLight = new THREE.AmbientLight(0xffffff, 0.8);
        scene.add(ambientLight);
        const dirLight = new THREE.DirectionalLight(0xffffff, 0.5);
        dirLight.position.set(10, 20, 10);
        scene.add(dirLight);

        // --- 2. World Generation (Interactive Blocks) ---
        const blocks = [];
        const blockGeo = new THREE.BoxGeometry(1, 1, 1);
        const grassMat = new THREE.MeshStandardMaterial({ color: 0x44aa44 });
        const dirtMat = new THREE.MeshStandardMaterial({ color: 0x8B4513 });
        const oreMat = new THREE.MeshStandardMaterial({ color: 0x708090 });

        for (let x = -8; x <= 8; x++) {
            for (let z = -8; z <= 8; z++) {
                // Top Grass
                const grass = new THREE.Mesh(blockGeo, grassMat);
                grass.position.set(x, 0, z);
                scene.add(grass);
                blocks.push(grass);

                // Dirt layer
                const dirt = new THREE.Mesh(blockGeo, dirtMat);
                dirt.position.set(x, -1, z);
                scene.add(dirt);
                blocks.push(dirt);
            }
        }

        // Add Ore Rock Blocks
        for(let i=0; i<8; i++) {
            const ore = new THREE.Mesh(blockGeo, oreMat);
            ore.position.set((Math.random()-0.5)*12, 1, (Math.random()-0.5)*12);
            scene.add(ore);
            blocks.push(ore);
        }

        // --- 3. Controls & Movement Logic ---
        let moveForward = false, moveBackward = false, moveLeft = false, moveRight = false;
        let minedItems = 0;

        // Keyboard Event Listeners (PC)
        window.addEventListener('keydown', (e) => {
            if(e.key === 'w' || e.key === 'W') moveForward = true;
            if(e.key === 's' || e.key === 'S') moveBackward = true;
            if(e.key === 'a' || e.key === 'A') moveLeft = true;
            if(e.key === 'd' || e.key === 'D') moveRight = true;
        });
        window.addEventListener('keyup', (e) => {
            if(e.key === 'w' || e.key === 'W') moveForward = false;
            if(e.key === 's' || e.key === 'S') moveBackward = false;
            if(e.key === 'a' || e.key === 'A') moveLeft = false;
            if(e.key === 'd' || e.key === 'D') moveRight = false;
        });

        // Touch Control Listeners (Mobile D-Pad)
        const bindTouch = (id, onStart, onEnd) => {
            const el = document.getElementById(id);
            el.addEventListener('touchstart', (e) => { e.preventDefault(); onStart(); });
            el.addEventListener('touchend', (e) => { e.preventDefault(); onEnd(); });
            el.addEventListener('mousedown', onStart);
            el.addEventListener('mouseup', onEnd);
        };

        bindTouch('btn-up', () => moveForward = true, () => moveForward = false);
        bindTouch('btn-down', () => moveBackward = true, () => moveBackward = false);
        bindTouch('btn-left', () => moveLeft = true, () => moveLeft = false);
        bindTouch('btn-right', () => moveRight = true, () => moveRight = false);
        bindTouch('btn-mine', mineBlock, () => {});

        // Touch Swipe / Drag Camera Rotate
        let isDragging = false, previousTouch = { x: 0, y: 0 };
        window.addEventListener('pointerdown', (e) => {
            if(e.target.tagName !== 'BUTTON' && !e.target.classList.contains('ctrl-btn')) {
                isDragging = true;
                previousTouch = { x: e.clientX, y: e.clientY };
            }
        });
        window.addEventListener('pointermove', (e) => {
            if (isDragging) {
                const deltaX = e.clientX - previousTouch.x;
                const deltaY = e.clientY - previousTouch.y;
                camera.rotation.y -= deltaX * 0.005;
                camera.rotation.x -= deltaY * 0.005;
                previousTouch = { x: e.clientX, y: e.clientY };
            }
        });
        window.addEventListener('pointerup', () => isDragging = false);

        // --- 4. Mining / Break Block Logic (Raycasting) ---
        const raycaster = new THREE.Raycaster();
        function mineBlock() {
            raycaster.setFromCamera(new THREE.Vector2(0, 0), camera);
            const intersects = raycaster.intersectObjects(blocks);

            if (intersects.length > 0) {
                const hitObj = intersects[0].object;
                scene.remove(hitObj);
                const idx = blocks.indexOf(hitObj);
                if (idx > -1) blocks.splice(idx, 1);

                minedItems++;
                document.getElementById('mined-count').innerText = minedItems;
            }
        }
        window.addEventListener('click', (e) => {
            if(e.target.tagName !== 'BUTTON' && !e.target.classList.contains('ctrl-btn')) {
                mineBlock();
            }
        });

        // --- 5. Game Animation Loop ---
        function animate() {
            requestAnimationFrame(animate);

            const speed = 0.15;
            if (moveForward) camera.translateZ(-speed);
            if (moveBackward) camera.translateZ(speed);
            if (moveLeft) camera.translateX(-speed);
            if (moveRight) camera.translateX(speed);

            renderer.render(scene, camera);
        }
        animate();

        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });

        function openFactory() { alert("🏭 Factory Module: Mined Ore se gadiyan aur weapons manufacture karein."); }
        function openMarket() { alert("🛒 OLX Market: Used weapons/items sell & buy karein."); }
    </script>
</body>
</html>