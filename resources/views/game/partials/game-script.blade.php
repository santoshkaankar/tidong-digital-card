<script type="module">
    import * as THREE from 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.module.js';

    const scene = new THREE.Scene();
    scene.background = new THREE.Color(0x87ceeb);

    const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({ antialias: false }); // लोड कम रखने के लिए antialias बंद रखा है
    renderer.setSize(window.innerWidth, window.innerHeight);
    document.body.appendChild(renderer.domElement);

    // Light
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.7);
    scene.add(ambientLight);
    const sunLight = new THREE.DirectionalLight(0xfff5e6, 1.2);
    sunLight.position.set(20, 40, 20);
    scene.add(sunLight);

    // Ground (मैदान)
    const ground = new THREE.Mesh(
        new THREE.PlaneGeometry(100, 100),
        new THREE.MeshStandardMaterial({ color: 0x3b7a2f, roughness: 0.9 })
    );
    ground.rotation.x = -Math.PI / 2;
    scene.add(ground);

    // House (बंगला)
    const houseGroup = new THREE.Group();
    const walls = new THREE.Mesh(new THREE.BoxGeometry(7, 3.5, 5), new THREE.MeshStandardMaterial({ color: 0xeeddcc }));
    walls.position.set(0, 1.75, -15);
    houseGroup.add(walls);

    const roof = new THREE.Mesh(new THREE.ConeGeometry(5.5, 2, 4), new THREE.MeshStandardMaterial({ color: 0xb22222 }));
    roof.position.set(0, 4.5, -15);
    roof.rotation.y = Math.PI / 4;
    houseGroup.add(roof);
    scene.add(houseGroup);

    // Car (गाड़ी)
    const car = new THREE.Mesh(
        new THREE.BoxGeometry(2.2, 1, 4),
        new THREE.MeshStandardMaterial({ color: 0x224488 })
    );
    car.position.set(8, 0.5, -10);
    scene.add(car);

    // Player (खिलाड़ी)
    const player = new THREE.Group();
    const body = new THREE.Mesh(new THREE.BoxGeometry(0.6, 1, 0.3), new THREE.MeshStandardMaterial({ color: 0x1e90ff }));
    body.position.y = 0.5;
    player.add(body);

    const head = new THREE.Mesh(new THREE.SphereGeometry(0.25, 12, 12), new THREE.MeshStandardMaterial({ color: 0xffdbac }));
    head.position.y = 1.2;
    player.add(head);

    player.position.set(0, 0, 5);
    scene.add(player);

    camera.position.set(0, 4, 11);
    camera.lookAt(player.position);

    // Grass Patches (घास काटने के मिशन्स)
    const grassPatches = [];
    for (let i = 0; i < 10; i++) {
        const patch = new THREE.Mesh(
            new THREE.BoxGeometry(0.8, 0.3, 0.8),
            new THREE.MeshStandardMaterial({ color: 0x225511 })
        );
        patch.position.set((Math.random() - 0.5) * 20, 0.15, (Math.random() - 0.5) * 20);
        scene.add(patch);
        grassPatches.push(patch);
    }

    // Movement Controls
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
        camera.position.z = player.position.z + 6;
        camera.lookAt(player.position);

        grassPatches.forEach(patch => {
            if (patch.visible && player.position.distanceTo(patch.position) < 1.2) {
                patch.visible = false;
                minedCount += 1;
                document.getElementById('tcoin-balance').innerText = (minedCount * 10).toFixed(2);
            }
        });

        renderer.render(scene, camera);
    }
    animate();
</script>