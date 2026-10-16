<!-- File Path: resources/views/member/game/partials/vast-ground.blade.php -->
<div id="gameContainer" class="position-relative rounded-4 overflow-hidden shadow-lg bg-black" style="width: 100%; height: 600px; border: 2px solid rgba(245, 158, 11, 0.3);">
    <!-- Three.js Canvas will be injected here -->
</div>

<!-- Load Three.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const container = document.getElementById('gameContainer');
        if (!container) return;

        // 1. Scene Setup
        const scene = new THREE.Scene();
        window.gameScene = scene; // Expose scene globally for partials
        scene.background = new THREE.Color(0x87ceeb); // Sky blue color

        // 2. Camera Setup
        const camera = new THREE.PerspectiveCamera(60, container.clientWidth / container.clientHeight, 0.1, 1000);
        camera.position.set(0, 20, 50);
        camera.lookAt(0, 0, 0);

        // 3. Renderer Setup
        const renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.setSize(container.clientWidth, container.clientHeight);
        renderer.shadowMap.enabled = true;
        container.appendChild(renderer.domElement);

        // 4. Lighting
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
        scene.add(ambientLight);

        const dirLight = new THREE.DirectionalLight(0xffffff, 0.8);
        dirLight.position.set(50, 100, 50);
        dirLight.castShadow = true;
        scene.add(dirLight);

        // 5. Create Vast Ground / Maidan
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

        // Grid helper
        const gridHelper = new THREE.GridHelper(500, 50, 0xffffff, 0x444444);
        gridHelper.position.y = 0.01;
        scene.add(gridHelper);

        // 6. Animation Loop
        function animate() {
            requestAnimationFrame(animate);
            renderer.render(scene, camera);
        }
        animate();

        window.addEventListener('resize', function() {
            if (!container) return;
            camera.aspect = container.clientWidth / container.clientHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(container.clientWidth, container.clientHeight);
        });
    });
</script>

<!-- Modularly Include Trash Objects File -->
@include('member.game.partials.trash-objects')