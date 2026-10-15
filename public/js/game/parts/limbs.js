import * as THREE from 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.module.js';

export function createLimbs() {
    const limbsGroup = new THREE.Group();
    const clothMat = new THREE.MeshStandardMaterial({ color: 0x1e293b });
    const skinMat = new THREE.MeshStandardMaterial({ color: 0xffdbac });

    const torso = new THREE.Mesh(new THREE.BoxGeometry(0.6, 0.8, 0.3), clothMat);
    torso.position.set(0, -0.3, 0);

    const armGeo = new THREE.CylinderGeometry(0.06, 0.05, 0.7, 8);
    const leftArm = new THREE.Mesh(armGeo, skinMat);
    leftArm.position.set(-0.4, -0.3, 0);
    leftArm.rotation.z = Math.PI / 12;

    const rightArm = new THREE.Mesh(armGeo, skinMat);
    rightArm.position.set(0.4, -0.3, 0);
    rightArm.rotation.z = -Math.PI / 12;

    const legGeo = new THREE.CylinderGeometry(0.08, 0.06, 0.8, 8);
    const leftLeg = new THREE.Mesh(legGeo, clothMat);
    leftLeg.position.set(-0.18, -1.1, 0);

    const rightLeg = new THREE.Mesh(legGeo, clothMat);
    rightLeg.position.set(0.18, -1.1, 0);

    limbsGroup.add(torso, leftArm, rightArm, leftLeg, rightLeg);
    return limbsGroup;
}