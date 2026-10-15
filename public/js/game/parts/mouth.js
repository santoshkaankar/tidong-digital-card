import * as THREE from 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.module.js';

export function createMouth() {
    const mouthGroup = new THREE.Group();

    const lipGeo = new THREE.BoxGeometry(0.2, 0.04, 0.05);
    const lipMat = new THREE.MeshStandardMaterial({ color: 0xcc6666 });

    const upperLip = new THREE.Mesh(lipGeo, lipMat);
    upperLip.position.set(0, 0.15, 0.42);

    const lowerLip = new THREE.Mesh(lipGeo, lipMat);
    lowerLip.position.set(0, 0.09, 0.42);

    mouthGroup.add(upperLip, lowerLip);
    return mouthGroup;
}