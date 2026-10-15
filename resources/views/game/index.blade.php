<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tidong Real 3D World</title>
    <style>
        body { margin: 0; overflow: hidden; font-family: sans-serif; }
        #hud {
            position: absolute; top: 15px; left: 15px;
            background: rgba(0,0,0,0.6); color: white;
            padding: 12px 20px; border-radius: 8px; backdrop-filter: blur(5px);
        }
        .controls {
            position: absolute; bottom: 20px; left: 20px;
            display: grid; grid-template-columns: repeat(3, 50px); gap: 5px;
        }
        .btn {
            width: 50px; height: 50px; background: rgba(255,255,255,0.3);
            border: none; color: white; font-size: 20px; border-radius: 8px;
        }
    </style>
</head>
<body>

    <div id="hud">
        <h3>🏡 Real 3D Estate Mission</h3>
        <p>TCoin Wallet: <span>0.00</span> TCoin</p>
        <p style="font-size: 12px; color: #aaa;">Mission: Cut the long grass in the garden!</p>
    </div>

    <!-- Mobile Controls -->
    <div class="controls">
        <div></div><button class="btn" id="up">▲</button><div></div>
        <button class="btn" id="left">◄</button><button class="btn" id="down">▼</button><button class="btn" id="right">►</button>
    </div>

    <script type="module">
        import * as THREE from 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.module.js';
        import { createEnvironment } from '/js/game/parts/environment.js';
        import { buildCompleteHuman } from '/js/game/character-assembler.js';
        import { MissionSystem } from '/js/game/parts/missions.js';

        // 1. Scene, Camera, Renderer
        const scene = new THREE.Scene();
        scene.background = new THREE.Color(0x87ceeb); // Sky blue color

        const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.shadowMap.enabled = true;
        document.body.appendChild(renderer.domElement);

        // 2. Load Environment & Mission
        createEnvironment(scene);
        const missions = new MissionSystem(scene);

        // 3. Load Player
        const player = buildCompleteHuman();
        player.position.set(0, 1.2, 5);
        scene.add(player);

        camera.position.set(0, 5, 12);
        camera.lookAt(player.position);

        // 4. Movement Logic
        const speed = 0.15;
        const keys = {};

        window.addEventListener('keydown', e => keys[e.key] = true);
        window.addEventListener('keyup', e => keys[e.key] = false);

        function animate() {
            requestAnimationFrame(animate);

            if (keys['ArrowUp'] || keys['w']) player.position.z -= speed;
            if (keys['ArrowDown'] || keys['s']) player.position.z += speed;
            if (keys['ArrowLeft'] || keys['a']) player.position.x -= speed;
            if (keys['ArrowRight'] || keys['d']) player.position.x += speed;

            // Camera follow player
            camera.position.x = player.position.x;
            camera.position.z = player.position.z + 7;
            camera.lookAt(player.position);

            // Mission Check
            missions.checkCutGrass(player.position);

            renderer.render(scene, camera);
        }

        animate();
    </script>
</body>
</html>