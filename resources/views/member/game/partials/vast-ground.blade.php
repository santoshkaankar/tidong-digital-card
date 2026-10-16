<!-- File Path: resources/views/member/game/partials/vast-ground.blade.php -->
<style>
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
</style>

<div class="game-fullscreen-wrapper" id="gameFullscreenWrapper">
    <!-- Three.js Canvas Container -->
    <div id="gameContainer" style="width: 100%; height: 100%;"></div>

    <!-- Include Touch Control Buttons -->
    @include('member.game.partials.touch-controls')

    <!-- Include Market Modal UI inside container context -->
    @include('member.game.partials.market-modal')
</div>

<!-- Load Three.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const wrapper = document.getElementById('gameContainer');
        const parentWrapper = document.getElementById('gameFullscreenWrapper');
        if (!wrapper) return;

        // Auto Fullscreen Request on First User Touch/Click
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

        // 4. Lighting Setup
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.7);
        scene.add(ambientLight);

        const dirLight = new THREE.DirectionalLight(0xffffff, 0.9);
        dirLight.position.set(50, 100, 50);
        dirLight.castShadow = true;
        scene.add(dirLight);

        // 5. Ground Setup (हरी घास का मैदान)
        const groundGeo = new THREE.PlaneGeometry(800, 800, 32, 32);
        const groundMat = new THREE.MeshStandardMaterial({ 
            color: 0x2e8b57, 
            roughness: 0.8,
            metalness: 0.2
        });
        const ground = new THREE.Mesh(groundGeo, groundMat);
        ground.rotation.x = -Math.PI / 2;
        ground.receiveShadow = true;
        scene.add(ground);

        // --- 🛣️ शहर की मुख्य सड़कें (Roads) ---
        const roadMat = new THREE.MeshStandardMaterial({ color: 0x222222 });
        const mainRoad = new THREE.Mesh(new THREE.BoxGeometry(800, 0.1, 20), roadMat);
        mainRoad.position.y = 0.05;
        scene.add(mainRoad);

        const crossRoad = new THREE.Mesh(new THREE.BoxGeometry(20, 0.1, 800), roadMat);
        crossRoad.position.y = 0.06;
        scene.add(crossRoad);

        // --- 🏢 3D शोरूम और इमारतें (Showrooms & Shops) बनाना ---
        function createShowroom(x, z, title, colorHex) {
            // शोरूम की मुख्य बॉडी (Building Box)
            const bGeo = new THREE.BoxGeometry(25, 14, 25);
            const bMat = new THREE.MeshStandardMaterial({ color: colorHex, roughness: 0.4 });
            const building = new THREE.Mesh(bGeo, bMat);
            building.position.set(x, 7, z);
            scene.add(building);

            // शोरूम का शीशा (Glass Front Window)
            const glassGeo = new THREE.BoxGeometry(18, 9, 0.5);
            const glassMat = new THREE.MeshStandardMaterial({ color: 0x38bdf8, transparent: true, opacity: 0.6 });
            const glass = new THREE.Mesh(glassGeo, glassMat);
            
            // दिशा के हिसाब से शीशा लगाना
            if (z > 0) {
                glass.position.set(x, 5, z - 12.3);
            } else {
                glass.position.set(x, 5, z + 12.3);
            }
            scene.add(glass);
        }

        // सड़क के दोनों तरफ अलग-अलग 3D शोरूम खड़े करना
        createShowroom(-40, -35, "Car Showroom", 0xef4444);      // लाल कार शोरूम
        createShowroom(40, -35, "Phone & Gadgets Hub", 0x3b82f6); // नीला गैजेट शोरूम
        createShowroom(-40, 35, "Super Market & Grocery", 0x10b981); // ग्रोसरी स्टोर
        createShowroom(40, 35, "Furniture & Decor", 0xf59e0b);   // फर्नीचर शोरूम

        // --- 🌲 पेड़ और 💡 स्ट्रीट लाइट ---
        function createTree(x, z) {
            const trunk = new THREE.Mesh(new THREE.CylinderGeometry(0.8, 1, 6, 8), new THREE.MeshStandardMaterial({ color: 0x8b5a2b }));
            trunk.position.set(x, 3, z);
            const leaves = new THREE.Mesh(new THREE.ConeGeometry(4, 8, 8), new THREE.MeshStandardMaterial({ color: 0x1e7e34 }));
            leaves.position.set(x, 7.5, z);
            scene.add(trunk);
            scene.add(leaves);
        }

        for (let i = -300; i <= 300; i += 50) {
            if (Math.abs(i) > 60) {
                createTree(i, 15);
                createTree(i, -15);
            }
        }

        // 6. Render Loop
        function animate() {
            requestAnimationFrame(animate);
            renderer.render(scene, camera);
        }
        animate();

        // Screen Resize Handling
        window.addEventListener('resize', function() {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    });
</script>

<!-- Modularly Include Spawner, Interactions, Character & Items -->
@include('member.game.partials.trash-spawner')
@include('member.game.partials.trash-pickup')
@include('member.game.partials.player-character')
@include('member.game.partials.item-spawner')