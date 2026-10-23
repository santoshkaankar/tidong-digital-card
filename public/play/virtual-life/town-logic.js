/* Offline town demo progression; a deliberate separate namespace from the demo wallet. */
(function(root,factory){const api=factory();if(typeof module==='object'&&module.exports)module.exports=api;else root.TidongTown=api;})(typeof globalThis!=='undefined'?globalThis:this,function(){
 'use strict';
 const SPOTS=Object.freeze([
  {id:'plaza',name:'Town Plaza',x:470,y:315,icon:'⛲',story:'Meet your town at the central square.'},
  {id:'market',name:'Shopping Lane',x:750,y:210,icon:'🛍',story:'See the virtual market stalls; purchases stay in the original game.'},
  {id:'library',name:'Community Library',x:215,y:440,icon:'📚',story:'Take a quiet moment among the books.'},
  {id:'garden',name:'Garden Gate',x:170,y:190,icon:'🌿',story:'Enter a peaceful garden path.'},
  {id:'cafe',name:'Town Café',x:743,y:445,icon:'☕',story:'Pause at the café; there is no real order or payment.'}
 ]);
 const BLOCKS=Object.freeze([{x:40,y:40,w:190,h:80},{x:700,y:40,w:210,h:92},{x:38,y:490,w:195,h:82},{x:699,y:502,w:210,h:76}]);
 const start=()=>({version:1,x:470,y:470,visited:[]});
 const bounded=(x,y)=>Math.min(915,Math.max(45,x))===x&&Math.min(565,Math.max(70,y))===y;
 const blocked=(x,y)=>BLOCKS.some(o=>x>=o.x-11&&x<=o.x+o.w+11&&y>=o.y-11&&y<=o.y+o.h+11);
 function safe(input){if(!input||input.version!==1||!Number.isFinite(input.x)||!Number.isFinite(input.y)||!Array.isArray(input.visited)||input.visited.length>SPOTS.length)return start();let x=Math.max(45,Math.min(915,input.x)),y=Math.max(70,Math.min(565,input.y));if(!bounded(x,y)||blocked(x,y))return start();return {version:1,x,y,visited:[...new Set(input.visited.filter(id=>SPOTS.some(s=>s.id===id)))]};}
 function move(state,dx,dy){if(!Number.isFinite(dx)||!Number.isFinite(dy)||Math.abs(dx)>1||Math.abs(dy)>1)return false;const step=19,mag=Math.hypot(dx,dy)||1,x=state.x+dx/mag*step,y=state.y+dy/mag*step;if(!bounded(x,y)||blocked(x,y))return false;state.x=x;state.y=y;return true;}
 function visit(state,id){const spot=SPOTS.find(p=>p.id===id);if(!spot)return {ok:false,message:'Unknown location.'};if(Math.hypot(state.x-spot.x,state.y-spot.y)>86)return {ok:false,message:'Walk closer to '+spot.name+'.'};if(!state.visited.includes(id))state.visited.push(id);return {ok:true,message:spot.story};}
 return {SPOTS,BLOCKS,start,safe,move,visit,blocked};
});