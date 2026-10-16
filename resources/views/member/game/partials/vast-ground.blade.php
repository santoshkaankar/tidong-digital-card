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
    .showroom-label {
        position: absolute;
        color: white;
        background: rgba(0, 0, 0, 0.8);
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: bold;
        border: 1px solid #eab308;
        pointer-events: none;
        transform: translate(-50%, -100%);
    }
</style>

<div class="game-fullscreen-wrapper" id="gameFullscreenWrapper">
    <!-- Three.js Canvas Container -->
    <div id="gameContainer" style="width: 100%; height: 100%;"></div>

    <!-- Include Touch Control Buttons -->
    @include('member.game.partials.touch-controls')

    <!-- Include Market Modal UI -->
    @include('member.game.partials.market-modal')
</div>

<!-- Load Three.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const wrapper = document.getElementById('gameContainer');
        const parentWrapper = document.getElementById('gameFullscreenWrapper');
        if (!wrapper) return;

        // Auto Fullscreen on Touch/Click
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

        // 4. Lighting
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.7);
        scene.add(ambientLight);

        const dirLight = new THREE.DirectionalLight(0xffffff, 0.9);
        dirLight.position.set(50, 100, 50);
        dirLight.castShadow = true;
        scene.add(dirLight);

        // 5. Ground Setup (सड़कें और मैदान)
        const groundGeo = new THREE.PlaneGeometry(900, 900);
        const groundMat = new THREE.MeshStandardMaterial({ color: 0x2e8b57, roughness: 0.8 });
        const ground = new THREE.Mesh(groundGeo, groundMat);
        ground.rotation.x = -Math.PI / 2;
        ground.receiveShadow = true;
        scene.add(ground);

        // मुख्य सड़कें (Roads)
        const roadMat = new THREE.MeshStandardMaterial({ color: 0x1e1e1e });
        const mainRoad = new THREE.Mesh(new THREE.BoxGeometry(900, 0.1, 24), roadMat);
        mainRoad.position.y = 0.05;
        scene.add(mainRoad);

        const crossRoad = new THREE.Mesh(new THREE.BoxGeometry(24, 0.1, 900), roadMat);
        crossRoad.position.y = 0.06;
        scene.add(crossRoad);

        // --- 🏢 रियलिस्टिक और अलग-अलग डिजाइन के शोरूम (Showrooms) ---
        const animatedWorkers = [];

        function createShowroom(x, z, width, height, depth, color, title, hasCarsOutside) {
            const group = new THREE.Group();

            // मुख्य बिल्डिंग बॉडी
            const bGeo = new THREE.BoxGeometry(width, height, depth);
            const bMat = new THREE.MeshStandardMaterial({ color: color, roughness: 0.3 });
            const building = new THREE.Mesh(bGeo, bMat);
            building.position.y = height / 2;
            building.castShadow = true;
            building.receiveShadow = true;
            group.add(building);

            // कांच की बड़ी खिड़की (Glass Front Facade)
            const glassGeo = new THREE.BoxGeometry(width * 0.75, height * 0.6, 0.4);
            const glassMat = new THREE.MeshStandardMaterial({ 
                color: 0x38bdf8, 
                transparent: true, 
                opacity: 0.5, 
                roughness: 0.1 
            });
            const glass = new THREE.Mesh(glassGeo, glassMat);
            glass.position.set(0, height * 0.35, depth / 2 + 0.1);
            group.add(glass);

            // शोरूम के ऊपर चमकता हुआ बोर्ड (Signboard Banner)
            const boardGeo = new THREE.BoxGeometry(width * 0.8, 2.2, 0.6);
            const boardMat = new THREE.MeshStandardMaterial({ color: 0xf59e0b, emissive: 0xd97706, emissiveIntensity: 0.4 });
            const board = new THREE.Mesh(boardGeo, boardMat);
            board.position.set(0, height + 1.2, depth / 2);
            group.add(board);

            // शोरूम के अंदर टहलने वाला वर्कर (Animated Worker)
            const workerGeo = new THREE.BoxGeometry(1.2, 2.5, 0.8);
            const workerMat = new THREE.MeshStandardMaterial({ color: 0xffffff }); // वर्कर की यूनिफॉर्म
            const worker = new THREE.Mesh(workerGeo, workerMat);
            worker.position.set(0, 1.25, 0);
            group.add(worker);

            // वर्कर को एनिमेट करने के लिए डेटा सेव करना
            animatedWorkers.push({
                mesh: worker,
                minX: -width * 0.3,
                maxX: width * 0.3,
                speed: 0.03,
                dir: 1
            });

            // यदि कार शोरूम है, तो बाहर गाड़ियाँ खड़ी करना
            if (hasCarsOutside) {
                for (let c = -1; c <= 1; c += 2) {
                    const carGeo = new THREE.BoxGeometry(5, 2, 8);
                    const carMat = new THREE.MeshStandardMaterial({ color: c > 0 ? 0xdc2626 : 0x2563eb, metalness: 0.8 });
                    const parkedCar = new THREE.Mesh(carGeo, carMat);
                    parkedCar.position.set(c * 6, 1, depth / 2 + 7);
                    parkedCar.castShadow = true;
                    group.add(parkedCar);
                }
            }

            group.position.set(x, 0, z);
            scene.add(group);
        }

        // सड़क के चारों कोनों पर अलग-अलग शोरूम खड़े करना
        createShowroom(-50, -50, 30, 16, 25, 0x334155, "🏎️ Lamborgini & SuperCars Hub", true);
        createShowroom(50, -50, 26, 14, 24, 0x1e3a8a, "📱 Aepple & Samsing Gadgets", false);
        createShowroom(-50, 50, 28, 15, 26, 0x065f46, "🍎 Tidong Fresh Grocery Mart", false);
        createShowroom(50, 50, 32, 16, 28, 0x78350f, "👔 Gucchi & Niki Clothing Showroom", false);

        // --- 🌲 पेड़ और स्ट्रीट लाइट्स ---
        function createTree(x, z) {
            const trunk = new THREE.Mesh(new THREE.CylinderGeometry(0.8, 1, 6, 8), new THREE.MeshStandardMaterial({ color: 0x8b5a2b }));
            trunk.position.set(x, 3, z);
            const leaves = new THREE.Mesh(new THREE.ConeGeometry(4, 8, 8), new THREE.MeshStandardMaterial({ color: 0x1e7e34 }));
            leaves.position.set(x, 7.5, z);
            scene.add(trunk);
            scene.add(leaves);
        }

        for (let i = -350; i <= 350; i += 60) {
            if (Math.abs(i) > 70) {
                createTree(i, 16);
                createTree(i, -16);
            }
        }

        // 6. Render Loop (एनिमेशन और वर्कर्स का टहलना)
        function animate() {
            requestAnimationFrame(animate);

            // शोरूम के अंदर वर्कर्स को आगे-पीछे टहलाना
            animatedWorkers.forEach(w => {
                w.mesh.position.x += w.speed * w.dir;
                if (w.mesh.position.x > w.maxX || w.mesh.position.x < w.minX) {
                    w.dir *= -1; // दिशा बदलना
                }
            });

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

<!-- Modularly Include Spawner, Character & Items -->
@include('member.game.partials.trash-spawner')
@include('member.game.partials.trash-pickup')
@include('member.game.partials.player-character')
@include('member.game.partials.item-spawner')