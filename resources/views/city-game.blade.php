<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tidong Laravel 3D Realistic City</title>
    <style>
        body { margin: 0; overflow: hidden; background: #000; font-family: sans-serif; }
        #loading {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            color: #fff; font-size: 18px; font-weight: bold; text-align: center; background: rgba(0,0,0,0.85);
            padding: 20px; border-radius: 10px; z-index: 100;
        }
        #info {
            position: absolute; top: 20px; left: 20px; color: white;
            background: rgba(0, 0, 0, 0.7); padding: 12px 18px; border-radius: 8px;
            pointer-events: none; font-size: 14px; line-height: 1.5;
        }
    </style>
    <!-- Three.js और जरूरी लाइब्रेरीज (CDN) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/objects/Water.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
</head>
<body>
    <div id="loading">लारावेल प्रोजेक्ट में रियलिस्टिक 3D वर्ल्ड लोड हो रहा है...</div>
    
    <div id="info">
        <b>Tidong.in - Laravel 3D Engine</b><br>
        • माउस से क्लिक करके चारों तरफ का व्यू देखें<br>
        • ज़ूम इन/आउट करें
    </div>

    <script>
        // 1. सीन, कैमरा और रेंडरर सेटअप
        const scene = new THREE.Scene();
        scene.background = new THREE.Color(0x87CEEB);
        scene.fog = new THREE.FogExp2(0x87CEEB, 0.002);

        const camera = new THREE.PerspectiveCamera(50, window.innerWidth / window.innerHeight, 1, 10000);
        camera.position.set(60, 40, 90);

        const renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setPixelRatio(window.devicePixelRatio);
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.toneMappingExposure = 1.1;
        renderer.shadowMap.enabled = true;
        document.body.appendChild(renderer.domElement);

        const controls = new THREE.OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.maxPolarAngle = Math.PI / 2 - 0.02;

        // 2. लाइटिंग (धूप और परछाइयां)
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.7);
        scene.add(ambientLight);

        const dirLight = new THREE.DirectionalLight(0xfff5ea, 1.5);
        dirLight.position.set(100, 150, 50);
        scene.add(dirLight);

        // 3. समुद्र (Realistic Water Shader)
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
                distortionScale: 3.0,
            }
        );
        water.rotation.x = -Math.PI / 2;
        water.position.set(-300, -0.5, 0);
        scene.add(water);

        // 4. घास का मैदान और रोड
        const ground = new THREE.Mesh(
            new THREE.PlaneGeometry(400, 400),
            new THREE.MeshStandardMaterial({ color: 0x3b7a57, roughness: 0.9 })
        );
        ground.rotation.x = -Math.PI / 2;
        ground.position.set(100, 0, 0);
        scene.add(ground);

        const road = new THREE.Mesh(
            new THREE.PlaneGeometry(25, 400),
            new THREE.MeshStandardMaterial({ color: 0x222222, roughness: 0.5 })
        );
        road.rotation.x = -Math.PI / 2;
        road.position.set(100, 0.1, 0);
        scene.add(road);

        // 5. बाहरी 3D मॉडल (जैसे कार/शोरूम) को ऑनलाइन सोर्स से इम्पोर्ट करना
        const loader = new THREE.GLTFLoader();
        loader.load('https://threejs.org/examples/models/gltf/Ferrari.glb', function (gltf) {
            const importedAsset = gltf.scene;
            importedAsset.scale.set(3, 3, 3);
            importedAsset.position.set(100, 0.2, 0);
            scene.add(importedAsset);
            
            // लोडिंग स्क्रीन हटाएं
            document.getElementById('loading').style.display = 'none';
        }, undefined, function (error) {
            console.error('मॉडल लोड करने में त्रुटि:', error);
            document.getElementById('loading').innerText = 'मॉडल लोड करने में विफल!';
        });

        // 6. एनिमेशन लूप
        function animate() {
            requestAnimationFrame(animate);
            water.material.uniforms['time'].value += 1.0 / 60.0;
            controls.update();
            renderer.render(scene, camera);
        }
        animate();

        // रिसाइज फिक्स
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    </script>
</body>
</html>