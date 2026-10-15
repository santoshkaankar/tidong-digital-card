import * as THREE from 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.module.js';

export function createEnvironment(scene) {
    // 1. Realistic Lights (सूरज की रोशनी और परछाई)
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.7);
    scene.add(ambientLight);

    const sunLight = new THREE.DirectionalLight(0xfff5e6, 1.5);
    sunLight.position.set(20, 40, 20);
    sunLight.castShadow = true;
    sunLight.shadow.mapSize.width = 2048;
    sunLight.shadow.mapSize.height = 2048;
    scene.add(sunLight);

    // 2. Ground / Green Lawn (हरी घास का मैदान)
    const groundGeo = new THREE.PlaneGeometry(100, 100);
    const groundMat = new THREE.MeshStandardMaterial({ 
        color: 0x3b7a2f, 
        roughness: 0.9 
    });
    const ground = new THREE.Mesh(groundGeo, groundMat);
    ground.rotation.x = -Math.PI / 2;
    ground.receiveShadow = true;
    scene.add(ground);

    // 3. Driveway Path (रास्ता)
    const pathGeo = new THREE.PlaneGeometry(6, 40);
    const pathMat = new THREE.MeshStandardMaterial({ color: 0x888888, roughness: 0.8 });
    const path = new THREE.Mesh(pathGeo, pathMat);
    path.rotation.x = -Math.PI / 2;
    path.position.set(5, 0.01, -10);
    scene.add(path);
}