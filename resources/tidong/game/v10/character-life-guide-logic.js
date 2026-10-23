/* TIDONG Character Life CHAT02 V13 guided play logic: cosmetic walkthrough, never payment authority */
(function(root,factory){const api=factory();if(typeof module==='object'&&module.exports)module.exports=api;else root.TidongLifeGuide=api;})(typeof globalThis!=='undefined'?globalThis:this,function(){
'use strict';
const TARGET=25000;
const STEPS=Object.freeze([
{id:'walk',name:'Walk around your home',hint:'Use W/A/S/D, arrow keys, or the on-screen arrows.'},
{id:'drink',name:'Drink water',hint:'Use Drink Water at home.'},
{id:'eat',name:'Eat purchased food',hint:'Buy Food in the demo market, select it, then eat.'},
{id:'wear',name:'Wear purchased clothes',hint:'Buy and equip a Fashion item.'},
{id:'exercise',name:'Exercise',hint:'Exercise when you have enough energy and water.'},
{id:'shower',name:'Take a shower',hint:'Go home and shower.'},
{id:'rest',name:'Rest',hint:'Rest at home to restore energy.'}
]);
function summary(state,engine,now=Date.now()){
if(!engine||typeof engine.isValidState!=='function'||!engine.isValidState(state))return {phase:'welcome',spent:0,remaining:TARGET,full:false,coins:null,message:'Enter the offline demo. Registration is not needed to preview.'};
const spent=Number.isSafeInteger(state.starterSpent)&&state.starterSpent>=0?state.starterSpent:0;
const remaining=Math.max(0,TARGET-spent);
const subscribed=!!state.subscribed;
const full=typeof engine.canPlay==='function'?!!engine.canPlay(state,now):(subscribed&&spent>=TARGET);
return {phase:!subscribed?'subscription':!full?'shopping':'life',spent,remaining,full,coins:state.coins,message:!subscribed?'Activate the demo annual subscription.':!full?'Limited movement, neighbour observation and starter shopping only. Daily life remains locked.':'Character Life unlocked in this offline demo.'};
}
function safeProgress(data){if(!data||!Array.isArray(data.done))return {done:[]};return {done:[...new Set(data.done.filter(x=>typeof x==='string'&&STEPS.some(s=>s.id===x)))].slice(0,STEPS.length)};}
function mark(progress,event){const p=safeProgress(progress);if(STEPS.some(s=>s.id===event)&&!p.done.includes(event))p.done.push(event);return p;}
function status(progress){const p=safeProgress(progress);return {count:p.done.length,total:STEPS.length,complete:p.done.length===STEPS.length,next:STEPS.find(s=>!p.done.includes(s.id))||null};}
return {TARGET,STEPS,summary,safeProgress,mark,status};
});