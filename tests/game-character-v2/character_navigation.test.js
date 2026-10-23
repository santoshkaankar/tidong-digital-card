'use strict';
const test=require('node:test'),assert=require('node:assert/strict');
const N=require('../../resources/tidong/game/v10/character-navigation.js');
test('all activity destinations and respawns are valid on 3D walk surfaces',()=>{
 for(const area of ['home','garden']){
  assert.equal(N.walkable(area,N.SPAWNS[area]),true);
  const list=area==='home'?{...N.HOME_ACTIONS,...N.HOME_ACTIVITIES}:N.GARDEN_ACTIVITIES;
  for(const [name,d]of Object.entries(list))assert.equal(N.walkable(area,d),true,`${area}: ${name}`);
 }
});
test('furniture, basin, bed, pond, walls block movement',()=>{
 assert.equal(N.walkable('home',{x:1.85,z:3}),false);
 assert.equal(N.walkable('home',{x:3.05,z:-2.75}),false);
 assert.equal(N.walkable('garden',{x:2.4,z:-3.3}),false);
 assert.equal(N.walkable('home',{x:8,z:0}),false);
 assert.equal(N.segmentClear('garden',{x:.57,z:-2.05},{x:3,z:-4}),false);
});
test('every life action route and garden route uses valid, collision-free segments',()=>{
 for(const area of ['home','garden'])for(const [name,target] of Object.entries(area==='home'?N.HOME_ACTIONS:N.GARDEN_ACTIVITIES)){
  const result=N.route(area,N.SPAWNS[area],target);assert.ok(result.ok,`${area} ${name}: ${result.message}`);
  let point=N.SPAWNS[area];for(const next of result.path){
   assert.equal(N.segmentClear(area,point,next),true,`${area} ${name}`);point=next;
  }
  assert.ok(Math.hypot(point.x-target.x,point.z-target.z)<.01);
 }
});
test('garden pathfinding routes around the pond instead of walking through the water',()=>{
 const from={x:0,z:-3.3},target={x:4.2,z:-3.3};
 const result=N.route('garden',from,target);assert.ok(result.ok,result.message);
 assert.ok(result.path.length>1);
 let previous=from;for(const point of result.path){assert.equal(N.segmentClear('garden',previous,point),true);previous=point;}
});
test('placed items add collision without modifying original scene objects',()=>{
 const item=['villa'];const obstacles=N.obstacles('home',item);
 assert.equal(N.walkable('home',{x:-3.5,z:1.1},item),false);
 assert.equal(N.walkable('home',{x:-3.5,z:1.1},[]),true);
 assert.ok(obstacles.length>N.obstacles('home',[]).length);
});
test('reject malformed paths, nonfinite and blocked positions',()=>{
 assert.equal(N.route('none',{x:0,z:0},{x:1,z:1}).ok,false);
 assert.equal(N.route('home',{x:NaN,z:0},{x:1,z:1}).ok,false);
 assert.equal(N.route('home',{x:1.85,z:3},{x:0,z:1}).ok,false);
 assert.equal(N.segmentClear('home',{x:0,z:0},{x:Infinity,z:1}),false);
});