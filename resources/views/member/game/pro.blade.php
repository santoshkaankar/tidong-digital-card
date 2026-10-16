<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tidong 3D Ultimate City Simulation</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: #000;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
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
        .game-hud {
            position: absolute;
            top: 15px;
            left: 15px;
            right: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 100000;
            pointer-events: none;
        }
        .hud-card {
            background: rgba(15, 23, 42, 0.95);
            border: 2px solid #f59e0b;
            color: #fff;
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
            pointer-events: auto;
        }
        .wallet-badge { color: #10b981; }
        .game-touch-controls {
            position: absolute;
            bottom: 25px;
            right: 25px;
            z-index: 200000;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            user-select: none;
        }
        .touch-btn {
            width: 70px;
            height: 70px;
            background: rgba(245, 158, 11, 0.95);
            border: 3px solid #fff;
            border-radius: 50%;
            color: #000;
            font-size: 26px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 18px rgba(0,0,0,0.6);
            cursor: pointer;
            outline: none;
            -webkit-tap-highlight-color: transparent;
            pointer-events: auto;
        }
        .touch-row { display: flex; gap: 20px; }
        .action-prompt-container {
            position: absolute;
            bottom: 120px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 15px;
            z-index: 250000;
            pointer-events: auto;
        }
        .action-btn {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 14px 24px;
            border-radius: 30px;
            font-size: 16px;
            font-weight: bold;
            border: 3px solid #ffffff;
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.6);
            cursor: pointer;
            display: none;
            pointer-events: auto;
        }
        .market-btn {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.6);
        }
        .market-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.85);
            z-index: 300000;
            display: none;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
            pointer-events: auto;
        }
        .market-modal-card {
            background: #1e293b;
            border: 2px solid #f59e0b;
            border-radius: 20px;
            width: 90%;
            max-width: 700px;
            max-height: 80vh;
            display: flex;
            flex-direction: column;
            color: #fff;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.8);
        }
        .modal-header {
            background: #0f172a;
            padding: 18px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #334155;
        }
        .modal-header h2 { margin: 0; font-size: 20px; color: #f59e0b; }
        .close-btn {
            background: #ef4444; color: white; border: none;
            font-size: 18px; font-weight: bold; width: 36px; height: 36px;
            border-radius: 50%; cursor: pointer;
        }
        .modal-body {
            padding: 20px; overflow-y: auto;
            display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 15px;
        }
        .item-card {
            background: #334155; border-radius: 12px; padding: 15px;
            display: flex; flex-direction: column; justify-content: space-between; border: 1px solid #475569;
        }
        .item-title { font-size: 16px; font-weight: bold; margin-bottom: 6px; }
        .item-price { color: #10b981; font-size: 18px; font-weight: bold; margin-bottom: 12px; }
        .buy-btn {
            background: #f59e0b; color: #000; border: none; padding: 10px;
            border-radius: 8px; font-weight: bold; cursor: pointer; text-align: center;
        }
    </style>
</head>
<body>

<div class="game-fullscreen-wrapper" id="gameFullscreenWrapper">
    <div class="game-hud">
        <div class="hud-card" id="hudLocationTitle">🌊 Tidong Coastal City & Real Estate</div>
        <div class="hud-card">💰 T-Coins: <span class="wallet-badge" id="walletBalance">2,00,000</span></div>
    </div>

    <div id="gameContainer" style="width: 100%; height: 100%; position: absolute; top:0; left:0; z-index: 1;"></div>

    <div class="action-prompt-container" style="z-index: 400000;">
        <button type="button" class="action-btn" id="enterShowroomBtn" onclick="enterShowroomInterior()">🏢 शोरूम / विला के अंदर प्रवेश करें</button>
        <button type="button" class="action-btn market-btn" id="openMarketBtn" onclick="openShowroomModal()">🛒 मार्केट कैटलॉग देखें</button>
        <button type="button" class="action-btn" id="exitShowroomBtn" style="background:#dc2626;" onclick="exitShowroomInterior()">🚪 बाहर सड़क पर वापस जाएँ</button>
    </div>

    <div class="game-touch-controls" style="z-index: 400000;">
        <button type="button" class="touch-btn" 
                ontouchstart="setTouchDirection('up', true); event.preventDefault();" 
                ontouchend="setTouchDirection('up', false)"
                onmousedown="setTouchDirection('up', true)" 
                onmouseup="setTouchDirection('up', false)">⬆️</button>
        
        <div class="touch-row">
            <button type="button" class="touch-btn" 
                    ontouchstart="setTouchDirection('left', true); event.preventDefault();" 
                    ontouchend="setTouchDirection('left', false)"
                    onmousedown="setTouchDirection('left', true)" 
                    onmouseup="setTouchDirection('left', false)">⬅️</button>
            
            <button type="button" class="touch-btn" 
                    ontouchstart="setTouchDirection('down', true); event.preventDefault();" 
                    ontouchend="setTouchDirection('down', false)"
                    onmousedown="setTouchDirection('down', true)" 
                    onmouseup="setTouchDirection('down', false)">⬇️</button>
            
            <button type="button" class="touch-btn" 
                    ontouchstart="setTouchDirection('right', true); event.preventDefault();" 
                    ontouchend="setTouchDirection('right', false)"
                    onmousedown="setTouchDirection('right', true)" 
                    onmouseup="setTouchDirection('right', false)">➡️</button>
        </div>
    </div>
</div>

<div class="market-modal-overlay" id="marketModalOverlay">
    <div class="market-modal-card">
        <div class="modal-header">
            <h2 id="modalShowroomTitle">🏪 Real Estate & Store</h2>
            <button class="close-btn" onclick="closeShowroomModal()">✕</button>
        </div>
        <div class="modal-body" id="modalItemList"></div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
<script>
    const touchState = { up: false, down: false, left: false, right: false };
    function setTouchDirection(dir, isPressed) { touchState[dir] = isPressed; }

    const showroomDatabase = {
        villa: {
            title: "🏡 Tidong Luxury Coastal Villa (Interior)",
            items: [
                { name: "Luxury Swimming Pool Deck", price: 25000, type: "decor", desc: "विला के लिए स्विमिंग पूल सेटअप" },
                { name: "Modern Living Room Sofa Set", price: 12000, type: "decor", desc: "आधुनिक इंटीरियर फर्नीचर" }
            ]
        },
        cars: {
            title: "🏎️ Tidong SuperCars Showroom (Interior)",
            items: [
                { name: "Lamborgini Aventa (Red)", price: 35000, type: "car", desc: "हाई-स्पीड लग्जरी स्पोर्ट्स कार" },
                { name: "BMW M5 Competition", price: 25000, type: "car", desc: "प्रीमियम सेडान कार" }
            ]
        },
        clothing: {
            title: "👔 Gucchi Fashion Store (Interior)",
            items: [
                { name: "Gucchi Designer Suit", price: 3500, type: "outfit", color: 0x1e3a8a, desc: "कैरेक्टर के कपड़े बदलें" },
                { name: "Niki Air Sneakers", price: 1200, type: "shoes", desc: "प्रीमियम स्पोर्ट्स जूते" }
            ]
        }
    };

    let activeNearShowroom = null;
    let isInsideInterior = false;
    let playerBodyMesh = null;
    let interiorSceneGroup = null;
    let exteriorSceneGroup = null;
    let scene, camera, renderer, playerGroup;
    let currentShowroomType = 'villa';
    const gltfLoader = new THREE.GLTFLoader();

    document.addEventListener("DOMContentLoaded", function () {
        const wrapper = document.getElementById('gameContainer');
        if (!wrapper) return;

        scene = new THREE.Scene();
        scene.background = new THREE.Color(0x60a5fa);

        camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 2000);
        camera.position.set(0, 15, 25);

        renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.shadowMap.enabled = true;
        wrapper.appendChild(renderer.domElement);

        scene.add(new THREE.AmbientLight(0xffffff, 0.9));
        const sunLight = new THREE.DirectionalLight(0xffffff, 1.2);
        sunLight.position.set(100, 200, 100);
        sunLight.castShadow = true;
        scene.add(sunLight);

        exteriorSceneGroup = new THREE.Group();
        interiorSceneGroup = new THREE.Group();
        interiorSceneGroup.visible = false;
        scene.add(exteriorSceneGroup);
        scene.add(interiorSceneGroup);

        // ज़मीन और समंदर
        const ground = new THREE.Mesh(
            new THREE.PlaneGeometry(1500, 1500),
            new THREE.MeshStandardMaterial({ color: 0x34d399, roughness: 0.8 })
        );
        ground.rotation.x = -Math.PI / 2;
        exteriorSceneGroup.add(ground);

        const sea = new THREE.Mesh(
            new THREE.PlaneGeometry(1000, 1500),
            new THREE.MeshStandardMaterial({ color: 0x0284c7, roughness: 0.1, metalness: 0.8, transparent: true, opacity: 0.85 })
        );
        sea.rotation.x = -Math.PI / 2;
        sea.position.set(600, -0.1, 0);
        exteriorSceneGroup.add(sea);

        const locations = [
            { id: 'villa', x: -50, z: -40, color: 0xf59e0b, title: "Luxury Coastal Villa" },
            { id: 'cars', x: 50, z: -50, color: 0x1e293b, title: "SuperCars Showroom" },
            { id: 'clothing', x: 50, z: 50, color: 0xb45309, title: "Fashion Store" }
        ];

        locations.forEach(loc => {
            const group = new THREE.Group();
            const bMesh = new THREE.Mesh(
                new THREE.BoxGeometry(35, 18, 30),
                new THREE.MeshStandardMaterial({ color: loc.color, roughness: 0.3 })
            );
            bMesh.position.y = 9;
            group.add(bMesh);
            group.position.set(loc.x, 0, loc.z);
            exteriorSceneGroup.add(group);
        });

        // 3D GLTF कार लोड करना
        gltfLoader.load('https://raw.githubusercontent.com/KhronosGroup/glTF-Sample-Models/master/2.0/ToyCar/glTF-Binary/ToyCar.glb', function (gltf) {
            const carModel = gltf.scene;
            carModel.scale.set(35, 35, 35);
            carModel.position.set(20, 0, -20);
            exteriorSceneGroup.add(carModel);
        });

        // प्लेयर कैरेक्टर
        playerGroup = new THREE.Group();
        playerBodyMesh = new THREE.Mesh(
            new THREE.BoxGeometry(1.4, 2.2, 0.8), 
            new THREE.MeshStandardMaterial({ color: 0x2563eb, roughness: 0.3 })
        );
        playerBodyMesh.position.y = 2.1;
        playerGroup.add(playerBodyMesh);

        const pHead = new THREE.Mesh(
            new THREE.SphereGeometry(0.55, 16, 16), 
            new THREE.MeshStandardMaterial({ color: 0xffdbac })
        );
        pHead.position.y = 3.5;
        playerGroup.add(pHead);

        playerGroup.position.set(0, 0, 10);
        scene.add(playerGroup);

        const speed = 0.85;
        const keys = {};
        window.addEventListener('keydown', (e) => { keys[e.key.toLowerCase()] = true; });
        window.addEventListener('keyup', (e) => { keys[e.key.toLowerCase()] = false; });

        function animate() {
            requestAnimationFrame(animate);

            if (keys['arrowup'] || keys['w'] || touchState.up) playerGroup.position.z -= speed;
            if (keys['arrowdown'] || keys['s'] || touchState.down) playerGroup.position.z += speed;
            if (keys['arrowleft'] || keys['a'] || touchState.left) playerGroup.position.x -= speed;
            if (keys['arrowright'] || keys['d'] || touchState.right) playerGroup.position.x += speed;

            if (!isInsideInterior) {
                let nearAny = false;
                locations.forEach(loc => {
                    const dist = Math.hypot(playerGroup.position.x - loc.x, playerGroup.position.z - loc.z);
                    if (dist < 40) {
                        nearAny = true;
                        activeNearShowroom = loc.id;
                        currentShowroomType = loc.id;
                    }
                });

                document.getElementById('enterShowroomBtn').style.display = nearAny ? 'block' : 'none';
                document.getElementById('openMarketBtn').style.display = 'none';
                document.getElementById('exitShowroomBtn').style.display = 'none';

                camera.position.x = playerGroup.position.x;
                camera.position.z = playerGroup.position.z + 24;
                camera.position.y = 12;
                camera.lookAt(playerGroup.position.x, playerGroup.position.y + 2, playerGroup.position.z);
            } else {
                playerGroup.position.x = Math.max(-18, Math.min(18, playerGroup.position.x));
                playerGroup.position.z = Math.max(-18, Math.min(18, playerGroup.position.z));

                camera.position.set(0, 14, 22);
                camera.lookAt(0, 2, 0);
            }

            renderer.render(scene, camera);
        }
        animate();

        window.addEventListener('resize', function() {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    });

    function enterShowroomInterior() {
        isInsideInterior = true;
        exteriorSceneGroup.visible = false;
        interiorSceneGroup.visible = true;

        while(interiorSceneGroup.children.length > 0){ 
            interiorSceneGroup.remove(interiorSceneGroup.children[0]); 
        }

        const floor = new THREE.Mesh(
            new THREE.PlaneGeometry(50, 50),
            new THREE.MeshStandardMaterial({ color: 0x1e293b, roughness: 0.2 })
        );
        floor.rotation.x = -Math.PI / 2;
        interiorSceneGroup.add(floor);

        const wall = new THREE.Mesh(
            new THREE.BoxGeometry(50, 20, 1),
            new THREE.MeshStandardMaterial({ color: 0x334155 })
        );
        wall.position.set(0, 10, -25);
        interiorSceneGroup.add(wall);

        document.getElementById('enterShowroomBtn').style.display = 'none';
        document.getElementById('openMarketBtn').style.display = 'block';
        document.getElementById('exitShowroomBtn').style.display = 'block';
        document.getElementById('hudLocationTitle').innerText = showroomDatabase[currentShowroomType].title;

        playerGroup.position.set(0, 0, 10);
    }

    function exitShowroomInterior() {
        isInsideInterior = false;
        interiorSceneGroup.visible = false;
        exteriorSceneGroup.visible = true;

        document.getElementById('enterShowroomBtn').style.display = 'block';
        document.getElementById('openMarketBtn').style.display = 'none';
        document.getElementById('exitShowroomBtn').style.display = 'none';
        document.getElementById('hudLocationTitle').innerText = "🌊 Tidong Coastal City & Real Estate";

        playerGroup.position.set(0, 0, 5);
    }

    function openShowroomModal() {
        const data = showroomDatabase[currentShowroomType];
        if (!data) return;

        document.getElementById('modalShowroomTitle').innerText = data.title;
        const listElem = document.getElementById('modalItemList');
        listElem.innerHTML = '';

        data.items.forEach(item => {
            const card = document.createElement('div');
            card.className = 'item-card';
            card.innerHTML = `
                <div>
                    <div class="item-title">${item.name}</div>
                    <div style="font-size: 13px; color: #94a3b8; margin-bottom: 8px;">${item.desc}</div>
                </div>
                <div>
                    <div class="item-price">🪙 ${item.price} T-Coins</div>
                    <button class="buy-btn" onclick='purchaseItem(${JSON.stringify(item)})'>खरीदें (Buy Now)</button>
                </div>
            `;
            listElem.appendChild(card);
        });

        document.getElementById('marketModalOverlay').style.display = 'flex';
    }

    function closeShowroomModal() {
        document.getElementById('marketModalOverlay').style.display = 'none';
    }

    function purchaseItem(item) {
        const walletElem = document.getElementById('walletBalance');
        let currentBalance = parseInt(walletElem.innerText.replace(/,/g, ''));

        if (currentBalance < item.price) {
            alert('❌ आपके वॉलेट में पर्याप्त T-Coins नहीं हैं!');
            return;
        }

        currentBalance -= item.price;
        walletElem.innerText = currentBalance.toLocaleString();

        if (item.type === 'outfit' && playerBodyMesh) {
            playerBodyMesh.material.color.setHex(item.color);
        }

        alert(`🎉 बधाई हो! आपने सफलतापूर्वक खरीदा: ${item.name}\nशेष बैलेंस: 🪙 ${currentBalance.toLocaleString()} T-Coins`);
        closeShowroomModal();
    }
</script>

</body>
</html>