import * as THREE from 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.module.js';
import { createHead } from './parts/head.js';
import { createLimbs } from './parts/limbs.js';

export function buildCompleteHuman() {
    const human = new THREE.Group();

    const head = createHead();
    head.position.set(0, 0.3, 0);

    const limbs = createLimbs();

    human.add(head, limbs);
    human.scale.set(1.2, 1.2, 1.2);
    return human;
}