<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tidong Real 3D World</title>
    <style>
        body { margin: 0; overflow: hidden; font-family: sans-serif; background: #87ceeb; }
        #hud {
            position: absolute; top: 15px; left: 15px;
            background: rgba(0,0,0,0.75); color: white;
            padding: 12px 20px; border-radius: 8px; backdrop-filter: blur(5px);
            z-index: 10;
        }
        .controls {
            position: absolute; bottom: 20px; left: 20px;
            display: grid; grid-template-columns: repeat(3, 50px); gap: 5px;
            z-index: 10;
        }
        .btn {
            width: 50px; height: 50px; background: rgba(255,255,255,0.3);
            border: none; color: white; font-size: 20px; border-radius: 8px;
            cursor: pointer;
        }
        .btn:active { background: rgba(255,255,255,0.6); }
    </style>
</head>
<body>

    <div id="hud">
        <h3>🏡 Tidong Estate & Lawn Mission</h3>
        <p>TCoin Wallet: <span id="tcoin-balance">0.00</span> TCoin</p>
        <p style="font-size: 12px; color: #ffeb3b;">Mission: Move around and cut the tall grass!</p>
    </div>

    <!-- Controls -->
    <div class="controls">
        <div></div><button class="btn" id="up">▲</button><div></div>
        <button class="btn" id="left">◄</button><button class="btn" id="down">▼</button><button class="btn" id="right">►</button>
    </div>

    <script type="module">
        import * as THREE from 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.module.js';

        // Scene & Camera
        const scene = new THREE.Scene();
        scene.background = new THREE.Color(0x87ceeb);

        const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ antialias: false }); // Antialias false से लैपटॉप पर लोड कम पड़ेगा और बंद नहीं होगा!
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.shadowMap.enabled = true;
        document.body.appendChild(renderer.domElement);

        // Lights
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
        scene.add(ambientLight);

        const sunLight = new THREE.DirectionalLight(0xfff5e6, 1.2);
        sunLight.position.set(30, 50, 20);
        sunLight.castShadow = true;
        scene.add(sunLight);

        // Ground (हरा मैदान / लॉन)
        const groundGeo = new THREE.PlaneGeometry(120, 120);
        const groundMat = new THREE.MeshStandardMaterial({ color: 0x3b7a2f, roughness: 0.9 });
        const ground = new THREE.Mesh(groundGeo, groundMat);
        ground.rotation.x = -Math.PI / 2;
        ground.receiveShadow = true;
        scene.add(ground);

        // Driveway (रास्ता)
        const pathGeo = new THREE.PlaneGeometry(6, 50);
        const pathMat = new THREE.MeshStandardMaterial({ color: 0x777777, roughness: 0.8 });
        const path = new THREE.Mesh(pathGeo, pathMat);
        path.rotation.x = -Math.PI / 2;
        path.position.set(6, 0.01, -10);
        scene.add(path);

        // Modern House (बंगला - दीवारें और छत)
        const houseGroup = new THREE.Group();
        
        // Base Wall
        const wallMat = new THREE.MeshStandardMaterial({ color: 0xeeddcc, roughness: 0.7 });
        const walls = new THREE.Mesh(new THREE.BoxGeometry(8, 4, 6), wallMat);
        walls.position.set(0, 2, -18);
        houseGroup.add(walls);

        // Roof
        const roofMat = new THREE.MeshStandardMaterial({ color: 0xb22222 });
        const roof = new THREE.Mesh(new THREE.ConeGeometry(6, 2.5, 4), roofMat);
        roof.position.set(0, 5.25, -18);
        roof.rotation.y = Math.PI / 4;
        houseGroup.add(roof);

        // Door
        const door = new THREE.Mesh(new THREE.BoxGeometry(1.2, 2.2, 0.2), new THREE.MeshStandardMaterial({ color: 0x5c4033 }));
        door.position.set(0, 1.1, -14.9);
        houseGroup.add(door);
        scene.add(houseGroup);

        // Car (गाड़ी)
        const carGroup = new THREE.Group();
        const carBody = new THREE.Mesh(new THREE.BoxGeometry(2.4, 1.2, 4.5), new THREE.MeshStandardMaterial({ color: 0x224488, metalness: 0.5 }));
        carBody.position.set(0, 0.7, 0);
        carGroup.add(carBody);
        
        const wheelGeo = new THREE.CylinderGeometry(0.4, 0.4, 0.3, 16);
        const wheelMat = new THREE.MeshStandardMaterial({ color: 0x111111 });
        [-1.2, 1.2].forEach(x => {
            [-1.5, 1.5].forEach(z => {
                const wheel = new THREE.Mesh(wheelGeo, wheelMat);
                wheel.rotation.z = Math.PI / 2;
                wheel.position.set(x, 0.4, z);
                carGroup.add(wheel);
            });
        });
        carGroup.position.set(10, 0, -10);
        scene.add(carGroup);

        // Player (खिलाड़ी + घास काटने की छोटी मशीन)
        const player = new THREE.Group();
        
        // Body
        const body = new THREE.Mesh(new THREE.BoxGeometry(0.7, 1.1, 0.4), new THREE.MeshStandardMaterial({ color: 0x1e90ff }));
        body.position.y = 0.55;
        player.add(body);

        // Head
        const head = new THREE.Mesh(new THREE.SphereGeometry(0.25, 16, 16), new THREE.MeshStandardMaterial({ color: 0xffdbac }));
        head.position.y = 1.3;
        player.add(head);

        // Lawn Mower (हाथ वाली घास काटने की मशीन)
        const mower = new THREE.Mesh(new THREE.BoxGeometry(0.8, 0.5, 0.9), new THREE.MeshStandardMaterial({ color: 0x444444 }));
        mower.position.set(0, 0.25, 0.8);
        player.add(mower);

        player.position.set(0, 0, 5);
        scene.add(player);

        camera.position.set(0, 5, 12);
        camera.lookAt(player.position);

        // Grass Patches (लंबी घास के टुकड़े जो काटने हैं)
        const grassPatches = [];
        const patchGeo = new THREE.BoxGeometry(1, 0.4, 1);
        const patchMat = new THREE.MeshStandardMaterial({ color: 0x225511 });

        for (let i = 0; i < 12; i++) {
            const patch = new THREE.Mesh(patchGeo, patchMat);
            patch.position.set((Math.random() - 0.5) * 25, 0.2, (Math.random() - 0.5) * 25);
            scene.add(patch);
            grassPatches.push(patch);
        }

        // Controls Logic
        const speed = 0.15;
        const keys = {};

        window.addEventListener('keydown', e => keys[e.key] = true);
        window.addEventListener('keyup', e => keys[e.key] = false);

        const bindButton = (id, keyName) => {
            const btn = document.getElementById(id);
            if(btn) {
                btn.addEventListener('mousedown', () => keys[keyName] = true);
                btn.addEventListener('mouseup', () => keys[keyName] = false);
                btn.addEventListener('touchstart', (e) => { e.preventDefault(); keys[keyName] = true; });
                btn.addEventListener('touchend', (e) => { e.preventDefault(); keys[keyName] = false; });
            }
        };
        bindButton('up', 'ArrowUp');
        bindButton('down', 'ArrowDown');
        bindButton('left', 'ArrowLeft');
        bindButton('right', 'ArrowRight');

        let minedCount = 0;

        function animate() {
            requestAnimationFrame(animate);

            if (keys['ArrowUp'] || keys['w'] || keys['W']) player.position.z -= speed;
            if (keys['ArrowDown'] || keys['s'] || keys['S']) player.position.z += speed;
            if (keys['ArrowLeft'] || keys['a'] || keys['A']) player.position.x -= speed;
            if (keys['ArrowRight'] || keys['d'] || keys['D']) player.position.x += speed;

            // Camera follow
            camera.position.x = player.position.x;
            camera.position.z = player.position.z + 7;
            camera.lookAt(player.position);

            // Mission check
            grassPatches.forEach(patch => {
                if (patch.visible && player.position.distanceTo(patch.position) < 1.5) {
                    patch.visible = false;
                    minedCount += 1;
                    document.getElementById('tcoin-balance').innerText = (minedCount * 10).toFixed(2);
                }
            });

            renderer.render(scene, camera);
        }

        animate();

        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    </script>
</body>
</html>