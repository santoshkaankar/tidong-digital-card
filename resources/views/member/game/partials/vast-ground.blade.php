<!-- File Path: resources/views/member/game/partials/vast-ground.blade.php -->
<style>
    /* पूरे स्क्रीन पर गेम को एज-टू-एज (Edge-to-Edge) फिट करने के लिए CSS */
    .game-fullscreen-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 9999;
        background-color: #000;
        overflow: hidden;
    }
</style>

<div class="game-fullscreen-wrapper" id="gameFullscreenWrapper">
    <!-- Full Screen Toggle Button -->
    <button id="toggleFullscreenBtn" class="btn btn-sm btn-warning fw-bold position-absolute top-0 end-0 m-3 z-3 shadow px-3 py-2 rounded-pill">
        <i class="fas fa-expand me-1"></i> Exit / Full
    </button>
    
    <!-- Three.js Canvas Container -->
    <div id="gameContainer" style="width: 100%; height: 100%;"></div>

    <!-- Include Market Modal UI inside full context -->
    @include('member.game.partials.market-modal')
</div>

<!-- Load Three.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const wrapper = document.getElementById('gameContainer');
        if (!wrapper) return;

        // 1. Scene Setup
        const scene = new THREE.Scene();
        window.gameScene = scene;
        scene.background = new THREE.Color(0x87ceeb);

        // 2. Camera Setup
        const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 1000);
        window.gameCamera = camera;
        camera.position.set(0, 20, 50);
        camera.lookAt(0, 0, 0);

        // 3. Renderer Setup
        const renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.shadowMap.enabled = true;
        wrapper.appendChild(renderer.domElement);

        // 4. Lighting Setup
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
        scene.add(ambientLight);

        const dirLight = new THREE.DirectionalLight(0xffffff, 0.8);
        dirLight.position.set(50, 100, 50);
        dirLight.castShadow = true;
        scene.add(dirLight);

        // 5. Ground Setup
        const groundGeo = new THREE.PlaneGeometry(500, 500, 32, 32);
        const groundMat = new THREE.MeshStandardMaterial({ 
            color: 0x2e8b57, 
            roughness: 0.8,
            metalness: 0.2
        });
        const ground = new THREE.Mesh(groundGeo, groundMat);
        ground.rotation.x = -Math.PI / 2;
        ground.receiveShadow = true;
        scene.add(ground);

        // Grid Helper
        const gridHelper = new THREE.GridHelper(500, 50, 0xffffff, 0x444444);
        gridHelper.position.y = 0.01;
        scene.add(gridHelper);

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

        // Fullscreen Toggle (Native API)
        const fullscreenBtn = document.getElementById('toggleFullscreenBtn');
        const parentWrapper = document.getElementById('gameFullscreenWrapper');
        if (fullscreenBtn && parentWrapper) {
            fullscreenBtn.addEventListener('click', function() {
                if (!document.fullscreenElement) {
                    if (parentWrapper.requestFullscreen) parentWrapper.requestFullscreen();
                    fullscreenBtn.innerHTML = '<i class="fas fa-compress me-1"></i> Exit Full';
                } else {
                    if (document.exitFullscreen) document.exitFullscreen();
                    fullscreenBtn.innerHTML = '<i class="fas fa-expand me-1"></i> Full Screen';
                }
            });
        }
    });
</script>

<!-- Modularly Include Spawner, Interactions & Character -->
@include('member.game.partials.trash-spawner')
@include('member.game.partials.trash-pickup')
@include('member.game.partials.player-character')