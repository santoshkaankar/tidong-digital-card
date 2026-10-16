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

        // 2. Camera Setup (बेहतर व्यू के लिए थोड़ा पास और स्पष्ट)
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
        const groundGeo = new THREE.PlaneGeometry(600, 600, 32, 32);
        const groundMat = new THREE.MeshStandardMaterial({ 
            color: 0x2e8b57, 
            roughness: 0.8,
            metalness: 0.2
        });
        const ground = new THREE.Mesh(groundGeo, groundMat);
        ground.rotation.x = -Math.PI / 2;
        ground.receiveShadow = true;
        scene.add(ground);

        // --- 🛣️ शहर की सड़कें (Roads) जोड़ना ---
        const roadMat = new THREE.MeshStandardMaterial({ color: 0x333333 });
        
        // मुख्य सीधी सड़क (Main Road East-West)
        const roadGeo1 = new THREE.BoxGeometry(600, 0.1, 15);
        const mainRoad = new THREE.Mesh(roadGeo1, roadMat);
        mainRoad.position.y = 0.05;
        scene.add(mainRoad);

        // क्रॉस रोड (Cross Road North-South)
        const roadGeo2 = new THREE.BoxGeometry(15, 0.1, 600);
        const crossRoad = new THREE.Mesh(roadGeo2, roadMat);
        crossRoad.position.y = 0.06;
        scene.add(crossRoad);

        // --- 🌲 पेड़ और 💡 लाइट के खंभे (Trees & Street Lights) जोड़ना ---
        function createTree(x, z) {
            // तना (Trunk)
            const trunkGeo = new THREE.CylinderGeometry(0.8, 1, 6, 8);
            const trunkMat = new THREE.MeshStandardMaterial({ color: 0x8b5a2b });
            const trunk = new THREE.Mesh(trunkGeo, trunkMat);
            trunk.position.set(x, 3, z);
            
            // पत्तियां (Leaves)
            const leavesGeo = new THREE.ConeGeometry(3.5, 7, 8);
            const leavesMat = new THREE.MeshStandardMaterial({ color: 0x1e7e34 });
            const leaves = new THREE.Mesh(leavesGeo, leavesMat);
            leaves.position.set(x, 7.5, z);
            
            scene.add(trunk);
            scene.add(leaves);
        }

        function createStreetLight(x, z) {
            // पोल (Pole)
            const poleGeo = new THREE.CylinderGeometry(0.3, 0.3, 10, 8);
            const poleMat = new THREE.MeshStandardMaterial({ color: 0x555555 });
            const pole = new THREE.Mesh(poleGeo, poleMat);
            pole.position.set(x, 5, z);

            // लाइट बल्ब (Light Bulb Head)
            const bulbGeo = new THREE.SphereGeometry(0.8, 8, 8);
            const bulbMat = new THREE.MeshBasicMaterial({ color: 0xffff00 });
            const bulb = new THREE.Mesh(bulbGeo, bulbMat);
            bulb.position.set(x, 10, z);

            scene.add(pole);
            scene.add(bulb);
        }

        // सड़क के किनारे पेड़ और खंभे लगाना
        for (let i = -200; i <= 200; i += 40) {
            if (Math.abs(i) > 20) {
                createTree(i, 15);
                createTree(i, -15);
                createStreetLight(i - 10, 10);
                createStreetLight(i - 10, -10);
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