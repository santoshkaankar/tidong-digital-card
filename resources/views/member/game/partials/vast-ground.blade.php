<!-- File Path: resources/views/member/game/partials/game-canvas.blade.php -->
<div id="tidong-game-canvas">
    <canvas id="renderCanvas" style="width: 100%; height: 100%; display: block;"></canvas>
    
    <!-- Loading Overlay -->
    <div id="game-loading" class="position-absolute top-50 start-50 translate-middle text-white text-center">
        <div class="spinner-border text-light mb-2" role="status"></div>
        <h5 class="fw-semibold">Loading Tidong Living World...</h5>
    </div>
</div>

<!-- Three.js CDN for High Fidelity 3D Graphics (Avoids plain boxes) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
    // Basic Three.js Scene Setup to replace boring plain boxes with a dynamic living world
    const container = document.getElementById('tidong-game-canvas');
    const scene = new THREE.Scene();
    scene.background = new THREE.Color(0x0f172a);

    const camera = new THREE.PerspectiveCamera(60, container.clientWidth / container.clientHeight, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(container.clientWidth, container.clientHeight);
    document.getElementById('renderCanvas').replaceWith(renderer.domElement);

    // Lighting for realistic shadows and environment
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.7);
    scene.add(ambientLight);

    const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
    directionalLight.position.set(10, 20, 10);
    scene.add(directionalLight);

    // Ground / Infinite Road Base
    const groundGeo = new THREE.PlaneGeometry(500, 500);
    const groundMat = new THREE.MeshStandardMaterial({ color: 0x1e293b, roughness: 0.8 });
    const ground = new THREE.Mesh(groundGeo, groundMat);
    ground.rotation.x = -Math.PI / 2;
    scene.add(ground);

    // Sample Player Avatar Mesh (Upgraded from simple box to styled capsule/avatar representation)
    const playerGeo = new THREE.CapsuleGeometry(0.5, 1, 4, 8);
    const playerMat = new THREE.MeshStandardMaterial({ color: 0x3b82f6 });
    const player = new THREE.Mesh(playerGeo, playerMat);
    player.position.set(0, 1, 0);
    scene.add(player);

    camera.position.set(0, 5, 10);
    camera.lookAt(player.position);

    // Hide loading once ready
    document.getElementById('game-loading').style.display = 'none';

    // Animation Loop
    function animate() {
        requestAnimationFrame(animate);
        renderer.render(scene, camera);
    }
    animate();
</script>