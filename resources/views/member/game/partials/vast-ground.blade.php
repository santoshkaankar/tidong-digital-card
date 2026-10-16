<!-- File Path: resources/views/member/game/partials/vast-ground.blade.php -->
<div class="position-relative w-100 overflow-hidden shadow-lg bg-black rounded-4 mb-3" style="height: 75vh; min-height: 550px; border: 2px solid rgba(245, 158, 11, 0.3);">
    
    <!-- Full Screen Mode Button -->
    <button id="toggleFullscreenBtn" class="position-absolute top-0 end-0 m-3 btn btn-sm btn-warning fw-bold z-3 shadow px-3 py-2 rounded-pill">
        <i class="fas fa-expand me-1"></i> Full Screen
    </button>
    
    <!-- Canvas Container -->
    <div id="gameContainer" style="width: 100%; height: 100%;"></div>

    <!-- Include Market Modal UI inside container context -->
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
        const camera = new THREE.PerspectiveCamera(60, wrapper.clientWidth / wrapper.clientHeight, 0.1, 1000);
        window.gameCamera = camera;
        camera.position.set(0, 20, 50);
        camera.lookAt(0, 0, 0);

        // 3. Renderer Setup
        const renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.setSize(wrapper.clientWidth, wrapper.clientHeight);
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

        window.addEventListener('resize', function() {
            if (!wrapper) return;
            camera.aspect = wrapper.clientWidth / wrapper.clientHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(wrapper.clientWidth, wrapper.clientHeight);
        });

        // Fullscreen Toggle
        const fullscreenBtn = document.getElementById('toggleFullscreenBtn');
        const parentCard = wrapper.closest('.position-relative');
        if (fullscreenBtn && parentCard) {
            fullscreenBtn.addEventListener('click', function() {
                if (!document.fullscreenElement) {
                    if (parentCard.requestFullscreen) parentCard.requestFullscreen();
                    fullscreenBtn.innerHTML = '<i class="fas fa-compress me-1"></i> Exit Full';
                } else {
                    if (document.exitFullscreen) document.exitFullscreen();
                    fullscreenBtn.innerHTML = '<i class="fas fa-expand me-1"></i> Full Screen';
                }
            });
        }
    });
</script>

<!-- Modularly Include Spawner & Interaction Files -->
@include('member.game.partials.trash-spawner')
@include('member.game.partials.trash-pickup')
@include('member.game.partials.player-character')