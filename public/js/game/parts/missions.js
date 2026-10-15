import * as THREE from 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.module.js';

export class MissionSystem {
    constructor(scene) {
        this.scene = scene;
        this.grassPatches = [];
        this.initGrassMission();
    }

    initGrassMission() {
        const grassGeo = new THREE.BoxGeometry(0.8, 0.5, 0.8);
        const grassMat = new THREE.MeshStandardMaterial({ color: 0x2e6f21 });

        // मैदान में जगह-जगह बड़ी घास के पेच बनाना
        for (let i = 0; i < 15; i++) {
            const patch = new THREE.Mesh(grassGeo, grassMat);
            patch.position.set(
                (Math.random() - 0.5) * 25,
                0.25,
                (Math.random() - 0.5) * 25
            );
            this.scene.add(patch);
            this.grassPatches.push(patch);
        }
    }

    checkCutGrass(playerPosition) {
        this.grassPatches.forEach((patch, index) => {
            if (patch.visible && playerPosition.distanceTo(patch.position) < 1.2) {
                patch.visible = false; // खिलाड़ी पास जाएगा तो घास कट जाएगी
                console.log("Grass Cut!");
            }
        });
    }
}