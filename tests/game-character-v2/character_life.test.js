'use strict';
const test=require('node:test');
const assert=require('node:assert/strict');
const C=require('../../resources/tidong/game/v10/character-life.js');
const L=require('./fixtures/v11/logic.js');

test('new character stats are valid, versioned, bounded',()=>{
 const s=C.newState();assert.equal(C.isValidState(s),true);
 assert.equal(s.stats.energy,90);assert.equal(C.isValidState({...s,stats:{...s.stats,energy:101}}),false);
 assert.equal(C.isValidState({...s,version:1}),false);
});
test('running uses more energy and hydration than walking; stats stay inside range',()=>{
 const s=C.newState();const walk=C.tick(s,30,'walk');const run=C.tick(s,30,'run');
 assert.equal(walk.ok,true);assert.equal(run.ok,true);
 assert.ok(run.state.stats.energy < walk.state.stats.energy);
 assert.ok(run.state.stats.hydration < walk.state.stats.hydration);
 assert.equal(s.stats.energy,90);
});
test('cannot mutate stats through invalid, negative or huge elapsed time',()=>{
 const s=C.newState();assert.equal(C.tick(s,-5).ok,false);
 assert.equal(C.tick(s,NaN).ok,false);
 assert.equal(C.tick(s,1000000).state.activeSeconds,30);
 let a=s;for(let i=0;i<20000;i++)a=C.tick(a,30,'run').state;
 assert.ok(Object.values(a.stats).every(n=>n>=0&&n<=100));assert.equal(C.isValidState(a),true);
});
test('food and milk cannot affect character without verified consumption',()=>{
 const s=C.newState();assert.equal(C.action(s,'eat').ok,false);
 assert.equal(C.action(s,'drink').ok,false);
 assert.equal(C.action(s,'eat',{consumed:true}).state.stats.satiety,100);
 assert.equal(C.action(s,'drink',{consumed:true}).state.stats.hydration,100);
 assert.equal(s.stats.satiety,85);
});
test('sleep, wash, rest have different bounded effects and cannot create currency',()=>{
 const s=C.newState();s.stats.energy=20;const sleep=C.action(s,'sleep'),wash=C.action(s,'wash'),rest=C.action(s,'rest');
 assert.ok(sleep.state.stats.energy>rest.state.stats.energy);
 assert.ok(wash.state.stats.hygiene>s.stats.hygiene);
 assert.equal(Object.keys(sleep.state).some(k=>/coin|wallet|ledger|purchas/i.test(k)),false);
 assert.equal(C.action(s,'upgrade').ok,false);
});
test('very low energy reduces movement speed',()=>{
 const s=C.newState();s.stats.energy=10;
 assert.equal(C.speed(s),.5);
 s.stats.energy=25;assert.equal(C.speed(s),.72);
 s.stats.energy=85;assert.equal(C.speed(s),1);
});
test('life system does not modify the V11 T-Coin balance or 25,000 shopping counter',()=>{
 const game=L.newState();L.subscribe(game);
 const before=JSON.stringify(game);
 let life=C.newState();for(let i=0;i<8;i++)life=C.action(life,'rest').state;
 assert.equal(JSON.stringify(game),before);
 assert.equal(L.canPlay(game),false);
});
test('V11 receipt-based consumption leaves qualifying purchase count intact',()=>{
 const now=Date.now(),game=L.newState();L.subscribe(game,now);
 for(const id of ['car','car','villa','lamp','plant','towel'])L.buy(game,id,now);
 assert.equal(game.starterSpent,24800);
 L.scrapItem(game,1,now+10*60000);
 L.buy(game,'apple',now+11*60000);L.buy(game,'outfit',now+11*60000);
 assert.equal(L.canPlay(game,now+11*60000),true);
 const coins=game.coins,target=game.starterSpent,lot=game.lots.find(l=>l.itemId==='apple'&&l.status==='owned');
 assert.equal(L.consume(game,lot.serial,now+11*60000).ok,true);
 const life=C.action(C.newState(),'eat',{consumed:true});
 assert.equal(life.ok,true);assert.equal(game.coins,coins);assert.equal(game.starterSpent,target);
 assert.equal(L.isValidState(game),true);
});

test('V1 character life saves upgrade without wiping personal stats',()=>{
 const v1={version:1,stats:{...C.DEFAULTS,energy:12},lastAction:'rest',activeSeconds:302};
 const s=C.migrateLegacy(v1,'game:2026-10-22');
 assert.equal(C.isValidState(s),true);assert.equal(s.version,2);assert.equal(s.stats.energy,12);
 assert.deepEqual(s.routine,[]);assert.equal(s.ownerToken,'game:2026-10-22');
 assert.equal(C.migrateLegacy({...v1,stats:{...v1.stats,health:300}},'test'),null);
});
test('session routine is cosmetic and does not create virtual currency',()=>{
 let s=C.newState('one');for(const a of C.ACTIONS)s=C.action(s,a,{consumed:true}).state;
 assert.deepEqual(C.routineProgress(s),{done:5,total:5,completed:true});
 assert.equal(C.isValidState(s),true);
 assert.ok(!('coins' in s));
 assert.equal(C.action(s,'wash').state.routine.length,5);
 assert.equal(C.tick(s,4,'idle').state.ownerToken,'one');
 assert.equal(C.isValidState({...s,routine:['wash','wash']}),false);
});
test('low health also reduces movement pace and poor hygiene affects mood',()=>{
 const weak=C.newState();weak.stats.health=10;assert.ok(C.speed(weak)<1);
 weak.stats.hygiene=0;weak.stats.mood=5;
 assert.ok(C.tick(weak,30,'idle').state.stats.mood<5);
});