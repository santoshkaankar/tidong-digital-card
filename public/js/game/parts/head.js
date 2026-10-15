import * as THREE from 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.module.js';
import { createEyes } from './eyes.js';
import { createMouth } from './mouth.js';

export function createHead() {
    const headGroup = new THREE.Group();

    const skinMat = new THREE.MeshStandardMaterial({ color: 0xffdbac, roughness: 0.6 });
    const skullGeo = new THREE.BoxGeometry(0.5, 0.6, 0.55);
    const skull = new THREE.Mesh(skullGeo, skinMat);
    skull.position.set(0, 0.3, 0);

    const noseGeo = new THREE.BoxGeometry(0.06, 0.12, 0.08);
    const nose = new THREE.Mesh(noseGeo, skinMat);
    nose.position.set(0, 0.28, 0.42);

    const earGeo = new THREE.BoxGeometry(0.05, 0.12, 0.08);
    const leftEar = new THREE.Mesh(earGeo, skinMat);
    leftEar.position.set(-0.28, 0.3, 0);

    const rightEar = new THREE.Mesh(earGeo, skinMat);
    rightEar.position.set(0.28, 0.3, 0);

    const eyes = createEyes();
    const mouth = createMouth();

    headGroup.add(skull, nose, leftEar, rightEar, eyes, mouth);
    return headGroup;
}