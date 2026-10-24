'use strict';
const test=require('node:test');const assert=require('node:assert/strict');const fs=require('fs'),path=require('path');
const P=path.join(__dirname,'..','..','resources','tidong','game','v10');
const read=name=>fs.readFileSync(path.join(P,name),'utf8');
test('3D HTML references V11 gate, new life scripts, stylesheet in correct order',()=>{
 const doc=read('studio3d-character.html');
 const scripts=['logic.js','access-gate.js','studio-logic.js','world-logic.js','character-life.js','character-navigation.js','character-life-bridge.js','studio3d-character.js'];
 const positions=scripts.map(s=>doc.indexOf(`<script src="${s}"></script>`));
 assert.ok(positions.every(p=>p>=0));assert.deepEqual(positions,[...positions].sort((a,b)=>a-b));
 assert.match(doc,/href="character-life.css"/);
});
test('3D studio reads the V11 save key, and never old v2 key',()=>{
 const src=read('studio3d-character.js');assert.match(src,/tidong\.virtual-life\.demo\.v3/);
 assert.doesNotMatch(src,/tidong\.virtual-life\.demo\.v2/);
 assert.match(src,/gameLogic\.canPlay\(candidate\)/);
});
test('3D actions are requested and committed after arriving, not on button press',()=>{
 const scene=read('studio3d-character.js'),bridge=read('character-life-bridge.js');
 assert.match(scene,/tidong:life-action-request/);
 assert.match(scene,/next\.arrived/);
 assert.match(scene,/lifeBridge\.finishAction\(lifeAction\)/);
 assert.match(bridge,/L\.consume\(s,lot\.serial\)/);
 assert.match(bridge,/localStorage\.setItem\(GAME_KEY/);
 assert.match(scene,/charNav\.route/);assert.match(scene,/charNav\.segmentClear/);
});

test('no production registration access bypass added to character files',()=>{
 const scene=read('studio3d-character.js');const bridge=read('character-life-bridge.js');
 assert.match(scene,/gameLogic\.canPlay\(candidate\)/);
 assert.match(bridge,/L\.canPlay\(s\)/);
 assert.doesNotMatch(bridge,/starterSpent\s*=/);
});