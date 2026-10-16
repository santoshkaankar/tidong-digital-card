<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tidong 3D Ultimate Open-World & Market Simulation</title>
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

        /* Top HUD Bar */
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
            background: rgba(15, 23, 42, 0.9);
            border: 2px solid #f59e0b;
            color: #fff;
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
            pointer-events: auto;
        }
        .wallet-badge {
            color: #10b981;
        }

        /* Touch Controls */
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
        }
        .touch-row {
            display: flex;
            gap: 20px;
        }

        /* Action Prompt */
        .shop-prompt-btn {
            position: absolute;
            bottom: 120px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 14px 30px;
            border-radius: 30px;
            font-size: 18px;
            font-weight: bold;
            border: 3px solid #ffffff;
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.6);
            cursor: pointer;
            z-index: 250000;
            display: none;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { transform: translateX(-50%) scale(1); }
            50% { transform: translateX(-50%) scale(1.08); }
            100% { transform: translateX(-50%) scale(1); }
        }

        /* Shopping Modal Popup */
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
        .modal-header h2 {
            margin: 0;
            font-size: 20px;
            color: #f59e0b;
        }
        .close-btn {
            background: #ef4444;
            color: white;
            border: none;
            font-size: 18px;
            font-weight: bold;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
        }
        .modal-body {
            padding: 20px;
            overflow-y: auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 15px;
        }
        .item-card {
            background: #334155;
            border-radius: 12px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 1px solid #475569;
            transition: transform 0.2s;
        }
        .item-card:hover {
            transform: translateY(-4px);
            border-color: #f59e0b;
        }
        .item-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 6px;
        }
        .item-price {
            color: #10b981;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 12px;
        }
        .buy-btn {
            background: #f59e0b;
            color: #000;
            border: none;
            padding: 10px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
        }
        .buy-btn:hover {
            background: #d97706;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="game-fullscreen-wrapper" id="gameFullscreenWrapper">
    <!-- HUD -->
    <div class="game-hud">
        <div class="hud-card">🏬 Tidong Open-World City</div>
        <div class="hud-card">💰 T-Coins: <span class="wallet-badge" id="walletBalance">75,000</span></div>
    </div>

    <!-- 3D Canvas -->
    <div id="gameContainer" style="width: 100%; height: 100%;"></div>

    <!-- Shop Prompt -->
    <button type="button" class="shop-prompt-btn" id="shopPromptBtn" onclick="openShowroomModal()">🛒 शोरूम के अंदर जाएँ और खरीदारी करें</button>

    <!-- Touch Controls -->
    <div class="game-touch-controls">
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

<!-- Modal -->
<div class="market-modal-overlay" id="marketModalOverlay">
    <div class="market-modal-card">
        <div class="modal-header">
            <h2 id="modalShowroomTitle">🏪 Showroom Store</h2>
            <button class="close-btn" onclick="closeShowroomModal()">✕</button>
        </div>
        <div class="modal-body" id="modalItemList"></div>
    </div>
</div>

<!-- Three.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
    const touchState = { up: false, down: false, left: false, right: false };
    function setTouchDirection(dir, isPressed) {
        touchState[dir] = isPressed;
    }

    // Complete Inventory & Store Database
    const showroomDatabase = {
        cars: {
            title: "🏎️ Tidong SuperCars Showroom",
            items: [
                { name: "Lamborgini Aventa (Red)", price: 15000, type: "car", desc: "हाई-स्पीड लग्जरी स्पोर्ट्स कार" },
                { name: "BMW M5 Competition", price: 10000, type: "car", desc: "प्रीमियम सेडान कार" }
            ]
        },
        gadgets: {
            title: "📱 Aepple & Samsing Gadgets Hub",
            items: [
                { name: "Aepple iPhone 16 Pro", price: 1500, type: "phone", desc: "टाइटेनियम फिनिश स्मार्टफोन" },
                { name: "Tidong VR Headset", price: 800, type: "vr", desc: "वर्चुअल रियलिटी ग्लासेज" }
            ]
        },
        clothing: {
            title: "👔 Gucchi Fashion & Customization Store",
            items: [
                { name: "Gucchi Designer Suit (Royal Blue)", price: 2500, type: "outfit", color: 0x1e3a8a, desc: "कैरेक्टर के कपड़े बदलें" },
                { name: "Niki Air Sneakers (Shoes)", price: 800, type: "shoes", desc: "प्रीमियम स्पोर्ट्स जूते" },
                { name: "Ray-Ban Sunglasses (चश्मा)", price: 400, type: "glasses", desc: "स्टाइलिश धूप का चश्मा" }
            ]
        },
        grocery: {
            title: "🍎 Tidong Fresh Grocery Mart",
            items: [
                { name: "ऑर्गेनिक फ्रूट बास्केट", price: 200, type: "grocery", desc: "ताजे फल एवं सब्जियां" },
                { name: "प्रीमियम राशन किट", price: 600, type: "grocery", desc: "मासिक घरेलू राशन" }
            ]
        }
    };

    let activeNearShowroom = null;
    let playerBodyMesh = null; // कैरेक्टर के कपड़े बदलने के लिए रेफरेंस

    function openShowroomModal() {
        if (!activeNearShowroom) return;
        const data = showroomDatabase[activeNearShowroom];
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

        // यदि कपड़ा या सूट खरीदा है, तो कैरेक्टर का रंग/रूप तुरंत बदलें
        if (item.type === 'outfit' && playerBodyMesh) {
            playerBodyMesh.material.color.setHex(item.color);
        }

        alert(`🎉 बधाई हो! आपने सफलतापूर्व खरीद लिया: ${item.name}\nशेष बैलेंस: 🪙 ${currentBalance.toLocaleString()} T-Coins`);
        closeShowroomModal();
    }

    document.addEventListener("DOMContentLoaded", function () {
        const wrapper = document.getElementById('gameContainer');
        const parentWrapper = document.getElementById('gameFullscreenWrapper');
        if (!wrapper) return;

        function triggerAutoFullscreen() {
            if (!document.fullscreenElement) {
                if (parentWrapper.requestFullscreen) parentWrapper.requestFullscreen().catch(() => {});
            }
        }
        window.addEventListener('click', triggerAutoFullscreen, { once: true });

        // 1. Scene
        const scene = new THREE.Scene();
        window.gameScene = scene;
        scene.background = new THREE.Color(0x7ec0ee);

        // 2. Camera
        const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 1000);
        window.gameCamera = camera;

        // 3. Renderer
        const renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.shadowMap.enabled = true;
        wrapper.appendChild(renderer.domElement);

        // 4. Lights
        scene.add(new THREE.AmbientLight(0xffffff, 0.75));
        const dirLight = new THREE.DirectionalLight(0xffffff, 1.0);
        dirLight.position.set(60, 120, 60);
        dirLight.castShadow = true;
        scene.add(dirLight);

        // 5. Ground & Roads
        const ground = new THREE.Mesh(
            new THREE.PlaneGeometry(1000, 1000),
            new THREE.MeshStandardMaterial({ color: 0x2d6a4f, roughness: 0.8 })
        );
        ground.rotation.x = -Math.PI / 2;
        scene.add(ground);

        const roadMat = new THREE.MeshStandardMaterial({ color: 0x1e1e1e });
        const mainRoad = new THREE.Mesh(new THREE.BoxGeometry(1000, 0.1, 26), roadMat);
        mainRoad.position.y = 0.05;
        scene.add(mainRoad);

        const crossRoad = new THREE.Mesh(new THREE.BoxGeometry(26, 0.1, 1000), roadMat);
        crossRoad.position.y = 0.06;
        scene.add(crossRoad);

        // 6. Showroom Locations
        const showroomTargets = [
            { id: 'cars', x: -65, z: -65, title: "SuperCars Showroom", color: 0x1e293b },
            { id: 'gadgets', x: 65, z: -65, title: "Gadgets Hub", color: 0x1d4ed8 },
            { id: 'grocery', x: -65, z: 65, title: "Fresh Mart", color: 0x047857 },
            { id: 'clothing', x: 65, z: 65, title: "Fashion Store", color: 0xb45309 }
        ];

        showroomTargets.forEach(s => {
            const group = new THREE.Group();
            const bMesh = new THREE.Mesh(
                new THREE.BoxGeometry(36, 18, 30),
                new THREE.MeshStandardMaterial({ color: s.color, roughness: 0.3 })
            );
            bMesh.position.y = 9;
            group.add(bMesh);

            const glass = new THREE.Mesh(
                new THREE.BoxGeometry(30, 12, 0.3),
                new THREE.MeshStandardMaterial({ color: 0x87ceeb, transparent: true, opacity: 0.5 })
            );
            glass.position.set(0, 7, 15.1);
            group.add(glass);

            group.position.set(s.x, 0, s.z);
            scene.add(group);
        });

        // 7. Player Setup
        const playerGroup = new THREE.Group();
        
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

        playerGroup.position.set(0, 0, 5);
        scene.add(playerGroup);

        const speed = 0.85;
        const keys = {};
        window.addEventListener('keydown', (e) => { keys[e.key.toLowerCase()] = true; });
        window.addEventListener('keyup', (e) => { keys[e.key.toLowerCase()] = false; });

        // Game Loop
        function animate() {
            requestAnimationFrame(animate);

            if (keys['arrowup'] || keys['w'] || touchState.up) playerGroup.position.z -= speed;
            if (keys['arrowdown'] || keys['s'] || touchState.down) playerGroup.position.z += speed;
            if (keys['arrowleft'] || keys['a'] || touchState.left) playerGroup.position.x -= speed;
            if (keys['arrowright'] || keys['d'] || touchState.right) playerGroup.position.x += speed;

            // Camera follow
            camera.position.x = playerGroup.position.x;
            camera.position.z = playerGroup.position.z + 22;
            camera.position.y = 12;
            camera.lookAt(playerGroup.position.x, playerGroup.position.y + 2, playerGroup.position.z);

            // Proximity Check
            let nearAny = false;
            showroomTargets.forEach(s => {
                const dist = Math.hypot(playerGroup.position.x - s.x, playerGroup.position.z - s.z);
                if (dist < 35) {
                    nearAny = true;
                    activeNearShowroom = s.id;
                }
            });

            const promptBtn = document.getElementById('shopPromptBtn');
            if (nearAny) {
                promptBtn.style.display = 'block';
            } else {
                promptBtn.style.display = 'none';
                activeNearShowroom = null;
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
</script>

</body>
</html>