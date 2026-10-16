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