/* Standalone child-friendly virtual pet demo, no monetary or account state. */
(function(root,factory){const api=factory();if(typeof module==='object'&&module.exports)module.exports=api;else root.TidongPetCare=api;})(typeof globalThis!=='undefined'?globalThis:this,function(){'use strict';
const clamp=n=>Math.max(0,Math.min(100,n));
function fresh(){return {food:55,joy:55,energy:55,care:0};}
function safe(o){if(!o||!['food','joy','energy','care'].every(k=>Number.isInteger(o[k])))return fresh();return {food:clamp(o.food),joy:clamp(o.joy),energy:clamp(o.energy),care:clamp(o.care)};}
function act(state,action){const s=safe(state);
switch(action){
case 'feed':s.food=clamp(s.food+22);s.energy=clamp(s.energy-4);return {state:s,message:'Yum! Your puppy enjoyed a healthy meal. 🍎'};
case 'play':if(s.energy<12)return {state:s,message:'Puppy is tired. Give them a rest first. 💤'};s.joy=clamp(s.joy+22);s.food=clamp(s.food-8);s.energy=clamp(s.energy-12);return {state:s,message:'Great game of fetch! 🎾'};
case 'rest':s.energy=clamp(s.energy+25);s.food=clamp(s.food-5);return {state:s,message:'A good nap makes puppy feel refreshed! 🛏️'};
case 'clean':s.joy=clamp(s.joy+8);s.care=clamp(s.care+1);return {state:s,message:'All clean! Your puppy feels loved. 🧼'};
default:return {state:s,message:'Choose a care activity.'};
}}
return {fresh,safe,act};
});