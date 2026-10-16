<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tidong Pro 3D Open World & Showroom</title>
    <style>
        body { margin: 0; overflow: hidden; background: #000; font-family: sans-serif; }
        #loading {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            color: #fff; font-size: 18px; font-weight: bold; text-align: center; background: rgba(0,0,0,0.85);
            padding: 20px; border-radius: 10px; z-index: 100;
        }
        /* Database HUD & Showroom Market UI */
        #hud {
            position: absolute; top: 20px; left: 20px; color: white;
            background: rgba(0, 0, 0, 0.85); padding: 15px 20px; border-radius: 10px;
            z-index: 10; font-size: 14px; line-height: 1.6; border: 1px solid rgba(255,255,255,0.2);
            max-width: 300px;
        }
        #hud span { color: #facc15; font-weight: bold; }
        .item-card {
            background: rgba(255,255,255,0.1); margin-top: 8px; padding: 6px 10px;
            border-radius: 5px; font-size: 12px; display: flex; justify-content: space-between; align-items: center;
        }
    </style>
    <!-- Three.js Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/objects/Water.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
</head>
<body>
    <div id="loading">Tidong 3D World aur Showroom Load ho raha hai...</div>
    
    <!-- Database Connected HUD & Market Showroom Panel -->
    <div id="hud">
        <b>Tidong.in - Pro World HUD</b><br>
        • Wallet: <span id="wallet-balance">{{ $wallet->t_coins ?? 0 }}</span> T-Coins<br>
        • My Inventory: <span>{{ $inventory->count() }}</span> Items<br>
        <hr style="border:0; border-top:1px solid rgba(255,255,255,0.2); margin:8px 0;">
        <b>Database Showroom Categories:</b>
        <div style="max-height: 150px; overflow-y: auto; margin-top: 5px;">
            @if(isset($marketItems) && count($marketItems) > 0)
                @foreach($marketItems as $category => $items)
                    <div style="font-size: 11px; color: #38bdf8; margin-top: 6px; text-transform: uppercase;">📁 {{ $category }} ({{ count($items) }})</div>
                    @foreach($items->take(2) as $item)
                        <div class="item-card">
                            <span>{{ $item->name }}</span>
                            <span style="color: #facc15;">{{ $item->price }} T</span>
                        </div>
                    @endforeach
                @endforeach
            @else
                <div style="color: #ef4444; font-size: 12px;">Koi market item nahi mila! Seeder check karein.</div>
            @endif
        </div>
    </div>

    <script>
        // 1. Scene, Camera & Renderer Setup
        const scene = new THREE.Scene();
        scene.background = new THREE.Color(0x87CEEB);
        scene.fog = new THREE.FogExp2(0x87CEEB, 0.0015);

        const camera = new THREE.PerspectiveCamera(50, window.innerWidth / window.innerHeight, 1, 10000);
        camera.position.set(80, 50, 120);

        const renderer = new THREE.WebGLRenderer({ antialias: true, powerPreference: "high-performance" });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setPixelRatio(window.devicePixelRatio);
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.toneMappingExposure = 1.2;
        renderer.shadowMap.enabled = true;
        document.body.appendChild(renderer.domElement);

        const controls = new THREE.OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.maxPolarAngle = Math.PI / 2 - 0.01;

        // 2. Lighting
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
        scene.add(ambientLight);

        const dirLight = new THREE.DirectionalLight(0xfff5ea, 1.8);
        dirLight.position.set(150, 200, 100);
        dirLight.castShadow = true;
        scene.add(dirLight);

        // 3. Ocean / Sea Water Shader
        const waterGeometry = new THREE.PlaneGeometry(10000, 10000);
        const water = new THREE.Water(
            waterGeometry,
            {
                textureWidth: 512,
                textureHeight: 512,
                waterNormals: new THREE.TextureLoader().load('https://threejs.org/examples/textures/waternormals.jpg', function (texture) {
                    texture.wrapS = texture.wrapT = THREE.RepeatWrapping;
                }),
                sunDirection: new THREE.Vector3(),
                sunColor: 0xffffff,
                waterColor: 0x001e0f,
                distortionScale: 3.5,
            }
        );
        water.rotation.x = -Math.PI / 2;
        water.position.set(-400, -0.2, 0);
        scene.add(water);

        // 4. Ground & Road
        const ground = new THREE.Mesh(
            new THREE.PlaneGeometry(600, 600),
            new THREE.MeshStandardMaterial({ color: 0x2e6f40, roughness: 0.9 })
        );
        ground.rotation.x = -Math.PI / 2;
        ground.position.set(150, 0, 0);
        scene.add(ground);

        const road = new THREE.Mesh(
            new THREE.PlaneGeometry(30, 600),
            new THREE.MeshStandardMaterial({ color: 0x1a1a1a, roughness: 0.4 })
        );
        road.rotation.x = -Math.PI / 2;
        road.position.set(150, 0.1, 0);
        scene.add(road);

        // 5. Load Showroom Car Model (Ferrari)
        const loader = new THREE.GLTFLoader();
        loader.load('https://threejs.org/examples/models/gltf/Ferrari.glb', function (gltf) {
            const car = gltf.scene;
            car.scale.set(3, 3, 3);
            car.position.set(150, 0.2, 0);
            scene.add(car);
            
            // Loading screen hide karo
            document.getElementById('loading').style.display = 'none';
        }, undefined, function (error) {
            console.error('Error loading model:', error);
            document.getElementById('loading').innerText = '3D Model load karne me error aayi!';
        });

        // 6. Animation Loop
        function animate() {
            requestAnimationFrame(animate);
            water.material.uniforms['time'].value += 1.0 / 60.0;
            controls.update();
            renderer.render(scene, camera);
        }
        animate();

        // Window Resize
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    </script>
</body>
</html>