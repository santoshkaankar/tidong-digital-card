<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realistic 3D Open World Game</title>
    <style>
        body {
            margin: 0;
            overflow: hidden;
            background-color: #000;
            font-family: sans-serif;
        }
        #ui {
            position: absolute;
            top: 20px;
            left: 20px;
            color: white;
            background: rgba(0, 0, 0, 0.6);
            padding: 15px;
            border-radius: 8px;
            pointer-events: none;
        }
        h1 { margin: 0 0 5px 0; font-size: 18px; color: #4CAF50; }
        p { margin: 0; font-size: 14px; }
    </style>
    <!-- Three.js लाइब्रेरी लोड कर रहे हैं -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
</head>
<body>
    <div id="ui">
        <h1>Tidong 3D Open World City</h1>
        <p>कैमरा व्यू: रियलिस्टिक कोस्टल सिटी सिमुलेशन</p>
    </div>

    <script>
        // सीन, कैमरा और रेंडरर सेटअप
        const scene = new THREE.Scene();
        scene.background = new THREE.Color(0x87CEEB); // आसमान का रंग
        scene.fog = new THREE.FogExp2(0x87CEEB, 0.003);

        const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 2000);
        const renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.shadowMap.enabled = true;
        document.body.appendChild(renderer.domElement);

        // लाइटिंग (रोशनी)
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.7);
        scene.add(ambientLight);

        const sunLight = new THREE.DirectionalLight(0xfffaf0, 1.2);
        sunLight.position.set(100, 200, 100);
        sunLight.castShadow = true;
        scene.add(sunLight);

        // 1. समुद्र (Ocean)
        const oceanGeometry = new THREE.PlaneGeometry(2000, 2000);
        const oceanMaterial = new THREE.MeshStandardMaterial({ color: 0x1E90FF, roughness: 0.1 });
        const ocean = new THREE.Mesh(oceanGeometry, oceanMaterial);
        ocean.rotation.x = -Math.PI / 2;
        ocean.position.set(-600, -1, 0);
        scene.add(ocean);

        // 2. घास का मैदान (Grassland / Park)
        const grassGeometry = new THREE.PlaneGeometry(800, 800);
        const grassMaterial = new THREE.MeshStandardMaterial({ color: 0x4CAF50, roughness: 0.8 });
        const grass = new THREE.Mesh(grassGeometry, grassMaterial);
        grass.rotation.x = -Math.PI / 2;
        grass.position.set(200, 0, 0);
        scene.add(grass);

        // 3. सड़कें (Roads & Dividers)
        const roadMaterial = new THREE.MeshStandardMaterial({ color: 0x333333, roughness: 0.5 });
        
        // मेन हाईवे
        const mainRoad = new THREE.Mesh(new THREE.PlaneGeometry(30, 800), roadMaterial);
        mainRoad.rotation.x = -Math.PI / 2;
        mainRoad.position.set(100, 0.1, 0);
        scene.add(mainRoad);

        // क्रॉस रोड
        const crossRoad = new THREE.Mesh(new THREE.PlaneGeometry(400, 20), roadMaterial);
        crossRoad.rotation.x = -Math.PI / 2;
        crossRoad.position.set(250, 0.1, -100);
        scene.add(crossRoad);

        // 4. शानदार बंगला / विला (Luxury Mansion)
        const mansionGroup = new THREE.Group();
        const baseMat = new THREE.MeshStandardMaterial({ color: 0xf5f5f5 });
        const roofMat = new THREE.MeshStandardMaterial({ color: 0x5c4033 });

        const houseBase = new THREE.Mesh(new THREE.BoxGeometry(40, 15, 30), baseMat);
        houseBase.position.set(0, 7.5, 0);
        mansionGroup.add(houseBase);

        const houseRoof = new THREE.Mesh(new THREE.ConeGeometry(30, 8, 4), roofMat);
        houseRoof.position.set(0, 19, 0);
        houseRoof.rotation.y = Math.PI / 4;
        mansionGroup.add(houseRoof);

        mansionGroup.position.set(-50, 0, 100);
        scene.add(mansionGroup);

        // 5. शोरूम और बिल्डिंग्स (Showrooms & Office Buildings)
        const showroomMat = new THREE.MeshStandardMaterial({ color: 0xcccccc, metalness: 0.5, roughness: 0.2 });
        const showroom = new THREE.Mesh(new THREE.BoxGeometry(60, 20, 40), showroomMat);
        showroom.position.set(180, 10, -50);
        scene.add(showroom);

        // टॉवर बिल्डिंग
        const towerMat = new THREE.MeshStandardMaterial({ color: 0x2196F3, roughness: 0.1 });
        const tower = new THREE.Mesh(new THREE.BoxGeometry(40, 90, 40), towerMat);
        tower.position.set(300, 45, -200);
        scene.add(tower);

        // 6. द्वीप / टापू (Islands in background)
        const islandMat = new THREE.MeshStandardMaterial({ color: 0x2e8b57, roughness: 0.9 });
        const island = new THREE.Mesh(new THREE.SphereGeometry(80, 16, 16), islandMat);
        island.scale.set(3, 0.5, 1.5);
        island.position.set(-700, 10, -400);
        scene.add(island);

        // कैमरा को ऐसा सेट किया है ताकि बिल्कुल फोटो जैसा एरियल/साइंटिफिक व्यू मिले
        camera.position.set(450, 300, 450);
        camera.lookAt(100, 0, 0);

        // एनिमेशन लूप (कैमरे को हल्का-हल्का घुमाने के लिए)
        let angle = 0;
        function animate() {
            requestAnimationFrame(animate);

            // सिनेमैटिक व्यू के लिए कैमरा का हल्का सा मूवमेंट
            angle += 0.002;
            camera.position.x = 350 + Math.sin(angle) * 50;
            camera.position.z = 350 + Math.cos(angle) * 50;
            camera.lookAt(100, 10, 0);

            renderer.render(scene, camera);
        }

        animate();

        // विंडो रिसाइज हैंडलर
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    </script>
</body>
</html>