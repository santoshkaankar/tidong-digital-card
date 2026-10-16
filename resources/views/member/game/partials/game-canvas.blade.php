<!-- File Path: resources/views/member/game/partials/game-canvas.blade.php -->
<div id="tidong-game-container" class="position-relative w-100 rounded shadow-lg overflow-hidden" style="height: 65vh; background: linear-gradient(to bottom, #1e293b, #090d16);">
    <canvas id="tidongCanvas" class="w-100 h-100 d-block"></canvas>
    
    <!-- Loading Screen -->
    <div id="game-loading-overlay" class="position-absolute top-50 start-50 translate-middle text-center text-white">
        <div class="spinner-border text-info mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
        <h5 class="fw-bold tracking-wide">Building Tidong 3D World...</h5>
        <p class="text-muted small">Loading infinite roads & textures</p>
    </div>

    <!-- In-Game Interaction Prompt -->
    <div id="interaction-prompt" class="position-absolute bottom-0 start-50 translate-middle-x mb-3 d-none">
        <button class="btn btn-info fw-bold px-4 py-2 shadow-lg animate-bounce" onclick="openShowroom()">
            <i class="fas fa-door-open me-2"></i> Press [E] or Tap to Enter Showroom
        </button>
    </div>
</div>

<!-- Three.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
    let scene, camera, renderer, player, roads = [];

    function initTidongWorld() {
        const container = document.getElementById('tidong-game-container');
        
        // Scene setup
        scene = new THREE.Scene();
        scene.background = new THREE.Color(0x0f172a);
        scene.fog = new THREE.FogExp2(0x0f172a, 0.015);

        // Camera
        camera = new THREE.PerspectiveCamera(60, container.clientWidth / container.clientHeight, 0.1, 1000);
        camera.position.set(0, 8, 14);

        // Renderer
        renderer = new THREE.WebGLRenderer({ canvas: document.getElementById('tidongCanvas'), antialias: true });
        renderer.setSize(container.clientWidth, container.clientHeight);
        renderer.shadowMap.enabled = true;

        // Lighting
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
        scene.add(ambientLight);

        const sunLight = new THREE.DirectionalLight(0xffffff, 0.8);
        sunLight.position.set(20, 40, 20);
        sunLight.castShadow = true;
        scene.add(sunLight);

        // Ground / Infinite Roads
        const roadGeo = new THREE.PlaneGeometry(30, 1000);
        const roadMat = new THREE.MeshStandardMaterial({ color: 0x1e293b, roughness: 0.9 });
        const road = new THREE.Mesh(roadGeo, roadMat);
        road.rotation.x = -Math.PI / 2;
        road.receiveShadow = true;
        scene.add(road);

        // Player Avatar (Upgraded Realistic Capsule representation with custom color)
        const playerGeo = new THREE.CapsuleGeometry(0.6, 1.2, 8, 16);
        const playerMat = new THREE.MeshStandardMaterial({ color: 0x38bdf8, roughness: 0.3 });
        player = new THREE.Mesh(playerGeo, playerMat);
        player.position.set(0, 1, 0);
        player.castShadow = true;
        scene.add(player);

        // Remove Loading Screen
        document.getElementById('game-loading-overlay').style.display = 'none';

        // Window Resize Handler
        window.addEventListener('resize', onWindowResize, false);
        animateWorld();
    }

    function onWindowResize() {
        const container = document.getElementById('tidong-game-container');
        camera.aspect = container.clientWidth / container.clientHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(container.clientWidth, container.clientHeight);
    }

    function animateWorld() {
        requestAnimationFrame(animateWorld);
        
        // Infinite road scrolling effect / subtle movement check
        renderer.render(scene, camera);
    }

    window.addEventListener('DOMContentLoaded', initTidongWorld);
</script>