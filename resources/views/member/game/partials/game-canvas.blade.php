<div id="game-viewport" class="position-relative w-100 rounded overflow-hidden shadow-lg" style="height: 70vh; min-height: 400px; background: #000; touch-action: none;">
    <canvas id="tidong3DCanvas" class="w-100 h-100 d-block"></canvas>

    <!-- Task Action Prompt -->
    <div id="task-prompt" class="position-absolute bottom-0 start-50 translate-middle-x mb-4 d-none">
        <button id="task-action-btn" class="btn btn-lg btn-warning fw-bold shadow-lg" onclick="triggerActiveTask()">
            <i class="fas fa-cut me-2"></i> Clean Lawn / Cut Grass (+50 T-Coins)
        </button>
    </div>

    <!-- Loading Overlay -->
    <div id="canvas-loading" class="position-absolute top-50 start-50 translate-middle text-center text-white">
        <div class="spinner-border text-info mb-2" style="width: 3rem; height: 3rem;" role="status"></div>
        <h5 class="fw-bold">Loading Tidong Living World...</h5>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
let scene, camera, renderer, player;
let roadSegments = [];
let grassPatches = [];
let hungerLevel = 80;
const ROAD_LENGTH = 200;

function initCinematicWorld() {
    const container = document.getElementById('game-viewport');
    if (!container) return;

    scene = new THREE.Scene();
    scene.background = new THREE.Color(0x0a0f1d);
    scene.fog = new THREE.FogExp2(0x0a0f1d, 0.008);

    // Camera aspect ratio fix for mobile & desktop
    camera = new THREE.PerspectiveCamera(60, container.clientWidth / container.clientHeight, 0.1, 1000);
    camera.position.set(0, 4, 10);

    renderer = new THREE.WebGLRenderer({ canvas: document.getElementById('tidong3DCanvas'), antialias: false, powerPreference: "high-performance" });
    renderer.setSize(container.clientWidth, container.clientHeight);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.shadowMap.enabled = true;

    // Lighting
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
    scene.add(ambientLight);

    const sunLight = new THREE.DirectionalLight(0xfffaed, 1.2);
    sunLight.position.set(50, 100, 50);
    sunLight.castShadow = true;
    scene.add(sunLight);

    // Roads & Player setup
    createRoadSegment(0);
    createRoadSegment(-ROAD_LENGTH);
    createPlayerAvatar();
    createGrassLawn();

    // Hide loading screen instantly so buffering stops
    const loader = document.getElementById('canvas-loading');
    if (loader) loader.style.display = 'none';

    // Window Resize Handler for Mobile Orientation Change
    window.addEventListener('resize', () => {
        if (!container) return;
        camera.aspect = container.clientWidth / container.clientHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(container.clientWidth, container.clientHeight);
    });

    animate();
}

function createRoadSegment(zOffset) {
    const group = new THREE.Group();
    const roadGeo = new THREE.PlaneGeometry(24, ROAD_LENGTH);
    const roadMat = new THREE.MeshStandardMaterial({ color: 0x1e293b, roughness: 0.8 });
    const road = new THREE.Mesh(roadGeo, roadMat);
    road.rotation.x = -Math.PI / 2;
    group.add(road);

    const fieldGeo = new THREE.PlaneGeometry(150, ROAD_LENGTH);
    const fieldMat = new THREE.MeshStandardMaterial({ color: 0x064e3b, roughness: 0.9 });
    const leftField = new THREE.Mesh(fieldGeo, fieldMat);
    leftField.rotation.x = -Math.PI / 2;
    leftField.position.x = -87;
    group.add(leftField);

    const rightField = leftField.clone();
    rightField.position.x = 87;
    group.add(rightField);

    group.position.z = zOffset;
    scene.add(group);
    roadSegments.push(group);
}

function createPlayerAvatar() {
    const group = new THREE.Group();
    const torsoGeo = new THREE.CylinderGeometry(0.5, 0.4, 1.4, 16);
    const torsoMat = new THREE.MeshStandardMaterial({ color: 0x0284c7 });
    const torso = new THREE.Mesh(torsoGeo, torsoMat);
    torso.position.y = 1.4;
    group.add(torso);

    const headGeo = new THREE.SphereGeometry(0.35, 32, 32);
    const headMat = new THREE.MeshStandardMaterial({ color: 0xfde047 });
    const head = new THREE.Mesh(headGeo, headMat);
    head.position.y = 2.4;
    group.add(head);

    player = group;
    player.position.set(0, 0, 0);
    scene.add(player);
}

function createGrassLawn() {
    for (let i = 0; i < 4; i++) {
        const grassGeo = new THREE.BoxGeometry(3, 0.4, 3);
        const grassMat = new THREE.MeshStandardMaterial({ color: 0x16a34a });
        const grass = new THREE.Mesh(grassGeo, grassMat);
        grass.position.set(-16, 0.2, -15 - (i * 20));
        scene.add(grass);
        grassPatches.push(grass);
    }
}

function animate() {
    requestAnimationFrame(animate);

    roadSegments.forEach(segment => {
        if (player && player.position.z < segment.position.z - ROAD_LENGTH) {
            segment.position.z -= ROAD_LENGTH * 2;
        }
    });

    renderer.render(scene, camera);
}

// Ensure DOM is fully loaded before init
if (document.readyState === 'complete') {
    initCinematicWorld();
} else {
    window.addEventListener('DOMContentLoaded', initCinematicWorld);
}
</script>