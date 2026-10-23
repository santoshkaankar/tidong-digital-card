/* Fictional NPC conversations and story progress. Offline only; no financial rewards. */
(function(root,factory){const api=factory();if(typeof module==='object'&&module.exports)module.exports=api;else root.TidongStory=api;})(typeof globalThis!=='undefined'?globalThis:this,function(){
 'use strict';
 const NPCS=Object.freeze([
  {id:'mira',name:'Mira',role:'Garden Caretaker',icon:'🌼',x:275,y:218,lines:['Welcome! The town garden grows through small acts of care.','See the garden gate? Walk there whenever you want a quiet break.','The world feels brighter when we take time for it.']},
  {id:'arun',name:'Arun',role:'Town Guide',icon:'🧭',x:581,y:406,lines:['Hello, explorer! Our plaza connects every part of town.','Try visiting the library and café. Both have their own stories.','Every friendly hello makes our small town feel bigger.']},
  {id:'leela',name:'Leela',role:'Community Librarian',icon:'📚',x:274,y:446,lines:['Welcome! The library is filled with imaginary adventures.','You can try Memory Match in the Mini-Games corner.','Wonderful stories begin with curiosity.']},
  {id:'noor',name:'Noor',role:'Café Host',icon:'☕',x:677,y:426,lines:['Hi! This is a fictional café, so no real orders or money.','You can decorate your own home with the Room Designer.','Thanks for visiting our little neighborhood!']}
 ]);
 const CHAPTERS=Object.freeze([
  {id:'first-step',title:'The First Step',description:'Discover the Town Plaza on the neighborhood map.',link:'town.html',action:'Walk the Town'},
  {id:'meet-mira',title:'A Garden Hello',description:'Walk near Mira and talk to her in the neighborhood.',link:'town.html',action:'Meet Mira'},
  {id:'puzzle',title:'A Small Challenge',description:'Complete any one of the three offline Mini-Games.',link:'activities.html',action:'Play Mini-Games'},
  {id:'make-home',title:'A Place of Your Own',description:'Buy a virtual Home item, then place it in Room Designer.',link:'room.html',action:'Design a Room'},
  {id:'meet-everyone',title:'Friendly Faces',description:'Talk to all four fictional town characters.',link:'town.html',action:'Meet Neighbors'},
  {id:'full-tour',title:'The Town Explorer',description:'Discover all five neighborhood landmarks.',link:'town.html',action:'Explore Town'}
 ]);
 function initial(){return {version:1,talked:[],times:{}};}
 function safe(raw){if(!raw||raw.version!==1||!Array.isArray(raw.talked))return initial();const allowed=NPCS.map(n=>n.id),talked=[...new Set(raw.talked.filter(id=>allowed.includes(id)))],times={};for(const id of allowed){const n=raw.times&&raw.times[id];if(Number.isSafeInteger(n)&&n>=0&&n<=3)times[id]=n;}return {version:1,talked,times};}
 function talk(town,story,id){const npc=NPCS.find(n=>n.id===id);if(!npc)return {ok:false,message:'Unknown character.'};if(!town||!Number.isFinite(town.x)||!Number.isFinite(town.y)||Math.hypot(town.x-npc.x,town.y-npc.y)>88)return {ok:false,message:'Walk closer to '+npc.name+' before talking.'};if(!story||!Array.isArray(story.talked)||!story.times)return {ok:false,message:'Story data is unavailable.'};const i=Math.min(story.times[id]||0,npc.lines.length-1);story.times[id]=Math.min(3,(story.times[id]||0)+1);if(!story.talked.includes(id))story.talked.push(id);return {ok:true,message:npc.icon+' '+npc.name+': “'+npc.lines[i]+'”',npc:npc.id};}
 function progress(source){const town=source&&source.town||{},story=source&&source.story||{},activities=source&&source.activities||{},room=source&&source.room||{};const visited=Array.isArray(town.visited)?town.visited:[],talked=Array.isArray(story.talked)?story.talked:[],completed=Array.isArray(activities.completed)?activities.completed:[],placed=Array.isArray(room.placed)?room.placed:[];const results={ 'first-step':visited.includes('plaza'),'meet-mira':talked.includes('mira'),'puzzle':completed.length>=1,'make-home':placed.length>=1,'meet-everyone':NPCS.every(n=>talked.includes(n.id)),'full-tour':['plaza','market','library','garden','cafe'].every(id=>visited.includes(id))};return {chapters:CHAPTERS.map(q=>({...q,done:!!results[q.id]})),completed:CHAPTERS.filter(q=>results[q.id]).length,total:CHAPTERS.length};}
 return {NPCS,CHAPTERS,initial,safe,talk,progress};
});
