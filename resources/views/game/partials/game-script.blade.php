<script type="module">
    import * as THREE from 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.module.js';

    const scene = new THREE.Scene();
    scene.background = new THREE.Color(0x7ec8e3); // Rich sky blue

    const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({ antialias: true }); // Better graphics smoothing
    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    document.body.appendChild(renderer.domElement);

    // Advanced Lighting
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
    scene.add(ambientLight);

    const sunLight = new THREE.DirectionalLight(0xfffbe8, 1.4);
    sunLight.position.set(30, 50, 30);
    sunLight.castShadow = true;
    sunLight.shadow.mapSize.width = 1024;
    sunLight.shadow.mapSize.height = 1024;
    scene.add(sunLight);

    // Ground / Lawn (Detailed green grass look)
    const ground = new THREE.Mesh(
        new THREE.PlaneGeometry(120, 120),
        new THREE.MeshStandardMaterial({ color: 0x2e6f40, roughness: 0.85 })
    );
    ground.rotation.x = -Math.PI / 2;
    ground.receiveShadow = true;
    scene.add(ground);

    // Driveway Path
    const path = new THREE.Mesh(
        new THREE.PlaneGeometry(8, 60),
        new THREE.MeshStandardMaterial({ color: 0x555555, roughness: 0.7 })
    );
    path.rotation.x = -Math.PI / 2;
    path.position.set(6, 0.01, -10);
    path.receiveShadow = true;
    scene.add(path);

    // Modern Bungalow (House)
    const houseGroup = new THREE.Group();
    
    // Main walls
    const walls = new THREE.Mesh(
        new THREE.BoxGeometry(8, 4, 6),
        new THREE.MeshStandardMaterial({ color: 0xf3f4f6, roughness: 0.5 })
    );
    walls.position.set(0, 2, -18);
    walls.castShadow = true;
    walls.receiveShadow = true;
    houseGroup.add(walls);

    // Roof
    const roof = new THREE.Mesh(
        new THREE.ConeGeometry(6, 2.5, 4),
        new THREE.MeshStandardMaterial({ color: 0x991b1b, roughness: 0.4 })
    );
    roof.position.set(0, 5.25, -18);
    roof.rotation.y = Math.PI / 4;
    roof.castShadow = true;
    houseGroup.add(roof);

    // Glass Windows & Door
    const door = new THREE.Mesh(
        new THREE.BoxGeometry(1.4, 2.2, 0.2),
        new THREE.MeshStandardMaterial({ color: 0x78350f })
    );
    door.position.set(0, 1.1, -14.9);
    houseGroup.add(door);
    scene.add(houseGroup);

    // Luxury Car
    const carGroup = new THREE.Group();
    const carBody = new THREE.Mesh(
        new THREE.BoxGeometry(2.4, 1.1, 4.5),
        new THREE.MeshStandardMaterial({ color: 0x1e3a8a, metalness: 0.8, roughness: 0.2 })
    );
    carBody.position.set(0, 0.6, 0);
    carBody.castShadow = true;
    carGroup.add(carBody);

    const wheelGeo = new THREE.CylinderGeometry(0.35, 0.35, 0.3, 16);
    const wheelMat = new THREE.MeshStandardMaterial({ color: 0x111111 });
    [-1.2, 1.2].forEach(x => {
        [-1.5, 1.5].forEach(z => {
            const wheel = new THREE.Mesh(wheelGeo, wheelMat);
            wheel.rotation.z = Math.PI / 2;
            wheel.position.set(x, 0.35, z);
            carGroup.add(wheel);
        });
    });
    carGroup.position.set(11, 0, -8);
    scene.add(carGroup);

    // Player Avatar
    const player = new THREE.Group();
    const body = new THREE.Mesh(
        new THREE.BoxGeometry(0.6, 1.1, 0.35),
        new THREE.MeshStandardMaterial({ color: 0x2563eb })
    );
    body.position.y = 0.55;
    body.castShadow = true;
    player.add(body);

    const head = new THREE.Mesh(
        new THREE.SphereGeometry(0.25, 16, 16),
        new THREE.MeshStandardMaterial({ color: 0xfde047 })
    );
    head.position.y = 1.25;
    head.castShadow = true;
    player.add(head);

    player.position.set(0, 0, 5);
    scene.add(player);

    camera.position.set(0, 5, 12);
    camera.lookAt(player.position);

    // Grass Mission Patches
    const grassPatches = [];
    for (let i = 0; i < 12; i++) {
        const patch = new THREE.Mesh(
            new THREE.BoxGeometry(1.2, 0.35, 1.2),
            new THREE.MeshStandardMaterial({ color: 0x15803d, roughness: 0.9 })
        );
        patch.position.set((Math.random() - 0.5) * 25, 0.17, (Math.random() - 0.5) * 25);
        patch.castShadow = true;
        scene.add(patch);
        grassPatches.push(patch);
    }

    // Controls Logic
    const speed = 0.15;
    const keys = {};
    window.addEventListener('keydown', e => keys[e.key] = true);
    window.addEventListener('keyup', e => keys[e.key] = false);

    const bindBtn = (id, keyName) => {
        const btn = document.getElementById(id);
        if(btn) {
            btn.addEventListener('mousedown', () => keys[keyName] = true);
            btn.addEventListener('mouseup', () => keys[keyName] = false);
            btn.addEventListener('touchstart', (e) => { e.preventDefault(); keys[keyName] = true; });
            btn.addEventListener('touchend', (e) => { e.preventDefault(); keys[keyName] = false; });
        }
    };
    bindBtn('up', 'ArrowUp'); bindBtn('down', 'ArrowDown');
    bindBtn('left', 'ArrowLeft'); bindBtn('right', 'ArrowRight');

    let minedCount = 0;

    function animate() {
        requestAnimationFrame(animate);

        if (keys['ArrowUp'] || keys['w'] || keys['W']) player.position.z -= speed;
        if (keys['ArrowDown'] || keys['s'] || keys['S']) player.position.z += speed;
        if (keys['ArrowLeft'] || keys['a'] || keys['A']) player.position.x -= speed;
        if (keys['ArrowRight'] || keys['d'] || keys['D']) player.position.x += speed;

        camera.position.x = player.position.x;
        camera.position.z = player.position.z + 7;
        camera.lookAt(player.position);

        grassPatches.forEach(patch => {
            if (patch.visible && player.position.distanceTo(patch.position) < 1.3) {
                patch.visible = false;
                minedCount += 1;
                document.getElementById('tcoin-balance').innerText = (minedCount * 10).toFixed(2);
            }
        });

        renderer.render(scene, camera);
    }
    animate();
</script>