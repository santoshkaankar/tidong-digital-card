'use strict';
const test=require('node:test');
const assert=require('node:assert/strict');
const {spawn}=require('node:child_process');
const path=require('node:path');
const fs=require('node:fs');
const PORT=18744;
const base=`http://127.0.0.1:${PORT}`;
const src=path.resolve(__dirname,'../modules/game-social/local-server.js');
let server;
async function started(){server=spawn(process.execPath,[src],{env:{...process.env,PORT:String(PORT)},stdio:['ignore','pipe','pipe']});
 return await new Promise((resolve,reject)=>{const tm=setTimeout(()=>reject(new Error('server timeout')),4000);server.stdout.on('data',d=>{if(d.toString().includes('PLAYTEST')){clearTimeout(tm);resolve();}});server.once('exit',c=>{clearTimeout(tm);reject(new Error('server died: '+c));});});}
function peer(name){return new Promise((resolve,reject)=>{const ws=new WebSocket(`ws://127.0.0.1:${PORT}/socket`);const events=[];let id='';ws.addEventListener('error',()=>reject(new Error('websocket failed')));ws.addEventListener('open',()=>ws.send(JSON.stringify({type:'join',name,world:'garden'})));ws.addEventListener('message',e=>{const m=JSON.parse(e.data);events.push(m);if(m.type==='welcome'){id=m.id;resolve({ws,events,id,send(x){ws.send(JSON.stringify(x));},wait(t){return until(events,t);}});}});});}
function until(events,type,timeout=1800){return new Promise((resolve,reject)=>{const started=Date.now();const int=setInterval(()=>{const i=events.findIndex(x=>x.type===type);if(i>=0){clearInterval(int);resolve(events.splice(i,1)[0]);}else if(Date.now()-started>timeout){clearInterval(int);reject(new Error('timeout waiting '+type+' got '+events.map(e=>e.type).join(',')));}},20);});}
test('local social playtest: consent-gated friends, invites, voice, lip levels, block',async t=>{
 await started();t.after(()=>server.kill('SIGTERM'));
 const home=await fetch(base+'/');assert.equal(home.status,200);assert.match(await home.text(),/Social hub/);
 const js=await fetch(base+'/social-preview.js');assert.equal(js.status,200);
 const scene=await fetch(base+'/social-scene.html');assert.equal(scene.status,200);
 assert.doesNotMatch(await scene.text(),/src="access-gate.js"/);
 const a=await peer('Alpha');const b=await peer('Beta');t.after(()=>{a.ws.close();b.ws.close();});
 assert.notEqual(a.id,b.id);
 assert.equal((await a.wait('joined')).player.name,'Beta');
 a.send({type:'voice_request',target:b.id});assert.match((await a.wait('error')).message,/friendship/i);
 a.send({type:'invite_reply',target:b.id,accept:true});await new Promise(r=>setTimeout(r,70));assert.equal(b.events.some(e=>e.type==='invite_reply'),false);
 a.send({type:'friend_request',target:b.id});assert.equal((await b.wait('friend_request')).name,'Alpha');
 b.send({type:'friend_reply',target:a.id,accept:true});assert.equal((await a.wait('friend_reply')).accept,true);
 a.send({type:'invite',target:b.id,place:'garden'});assert.equal((await b.wait('invite')).place,'garden');
 b.send({type:'invite_reply',target:a.id,accept:true});assert.equal((await a.wait('invite_reply')).accept,true);
 b.send({type:'invite_reply',target:a.id,accept:true});await new Promise(r=>setTimeout(r,70));assert.equal(a.events.some(e=>e.type==='invite_reply'),false);
 a.send({type:'lip',level:0.8});await new Promise(r=>setTimeout(r,70));assert.equal(b.events.some(e=>e.type==='lip'),false);
 a.send({type:'webrtc_offer',target:b.id,sdp:{type:'offer',sdp:'dummy'}});assert.match((await a.wait('error')).message,/consent/i);
 a.send({type:'voice_request',target:b.id});assert.equal((await b.wait('voice_request')).name,'Alpha');
 b.send({type:'voice_reply',target:a.id,accept:true});assert.equal((await a.wait('voice_reply')).accept,true);
 a.send({type:'webrtc_offer',target:b.id,sdp:{type:'offer',sdp:'local-test'}});assert.equal((await b.wait('webrtc_offer')).sdp.sdp,'local-test');
 a.send({type:'lip',level:0.7});assert.equal((await b.wait('lip')).level,0.7);
 a.send({type:'position',x:2,z:2,world:'garden'});assert.equal((await b.wait('position')).x,2);
 a.send({type:'block',target:b.id});assert.equal((await b.wait('blocked')).from,a.id);
 b.send({type:'invite',target:a.id,place:'home'});
 await new Promise(r=>setTimeout(r,130));assert.equal(b.events.some(e=>e.type==='invite'),false);
});
test('patch files do not edit the legacy economy or access gates',()=>{
 const root=path.resolve(__dirname,'../public/play/virtual-life');
 const s=fs.readFileSync(path.join(root,'social-studio3d.js'),'utf8');
 assert.match(s,/window\.TidongSocialScene/);
 assert.match(s,/socialVisitors/);
 assert.match(fs.readFileSync(path.join(root,'social-scene.html'),'utf8'),/src="social-studio3d\.js"/);
 assert.ok(fs.statSync(path.join(root,'social-preview.html')).size>1000);
});