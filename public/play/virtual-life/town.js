(function(){'use strict';
 const T=window.TidongTown,S=window.TidongStory,SK='tidong.virtual-life.story.v10',K='tidong.virtual-life.town.v7',c=document.getElementById('town'),ctx=c.getContext('2d'),$=id=>document.getElementById(id);
 let state;try{state=T.safe(JSON.parse(localStorage.getItem(K)));}catch(_){state=T.start();}
 let story;try{story=S.safe(JSON.parse(localStorage.getItem(SK)));}catch(_){story=S.initial();}
 function persist(){try{localStorage.setItem(K,JSON.stringify(state));localStorage.setItem(SK,JSON.stringify(story));}catch(_){}}
 function rounded(x,y,w,h,fill,r=15){ctx.fillStyle=fill;ctx.beginPath();ctx.roundRect(x,y,w,h,r);ctx.fill();}
 function circle(x,y,r,color){ctx.fillStyle=color;ctx.beginPath();ctx.arc(x,y,r,0,Math.PI*2);ctx.fill();}
 function txt(s,x,y,size,color='#f9fff5'){ctx.fillStyle=color;ctx.font='700 '+size+'px system-ui';ctx.textAlign='center';ctx.fillText(s,x,y);}
 function tree(x,y){circle(x,y+7,24,'#163b36');circle(x,y-7,25,'#368a63');circle(x-9,y-14,17,'#49b477');circle(x+10,y-12,15,'#74c58a');}
 function draw(){
  const g=ctx.createLinearGradient(0,0,960,620);g.addColorStop(0,'#519d8e');g.addColorStop(1,'#385f6e');ctx.fillStyle=g;ctx.fillRect(0,0,960,620);
  for(let i=0;i<960;i+=58)for(let j=0;j<620;j+=58){circle(i+14,j+15,1.7,'#9be1ae66');}
  rounded(305,20,343,580,'#b7aa8d',28);rounded(18,265,925,125,'#b7aa8d',25);rounded(322,20,305,580,'#d6c6a5',20);rounded(18,285,925,85,'#d6c6a5',19);
  ctx.setLineDash([17,16]);ctx.strokeStyle='#fff1d0a8';ctx.lineWidth=3;ctx.beginPath();ctx.moveTo(477,25);ctx.lineTo(477,595);ctx.moveTo(20,325);ctx.lineTo(943,325);ctx.stroke();ctx.setLineDash([]);
  // Buildings and outdoor props have fixed collision zones.
  for(const [x,y,w,h,col,roof,label] of [[40,40,190,80,'#cd986f','#eea96d','GARDEN'],[700,40,210,92,'#b8858e','#dfb1af','MARKET'],[38,490,195,82,'#8b9dc3','#acc5df','LIBRARY'],[699,502,210,76,'#bd967b','#e3c9a1','CAFÉ']]){
   rounded(x+5,y+8,w,h,'#0c314858',12);rounded(x,y,w,h,col,12);rounded(x+10,y-4,w-20,25,roof,6);txt(label,x+w/2,y+h*.70,19,'#1b3743');
  }
  for(const p of [[93,187],[248,106],[873,223],[852,425],[155,393],[593,543],[380,122]])tree(...p);
  circle(475,307,62,'#315764');circle(475,307,52,'#70b6bd');circle(475,307,39,'#94d9dd');circle(475,307,18,'#d9faff');
  T.SPOTS.forEach(p=>{circle(p.x,p.y,34,'#153b4fb8');circle(p.x,p.y,28,state.visited.includes(p.id)?'#5ccf9e':'#f0dfae');txt(p.icon,p.x,p.y+9,26,'#163e42');});
  // V10 fictional neighbors on the same playable town canvas.
  S.NPCS.forEach(n=>{circle(n.x,n.y+8,18,'#143b3e9c');circle(n.x,n.y-5,12,'#f5bd99');rounded(n.x-13,n.y+4,26,21,'#ffd49c',8);txt(n.icon,n.x,n.y-23,19);txt(n.name,n.x,n.y+42,12);});
  // Avatar and soft shadow.
  circle(state.x,state.y+14,15,'#173b3f80');circle(state.x,state.y-11,15,'#e4ad8a');rounded(state.x-13,state.y,26,32,'#7fe9c3',10);circle(state.x-6,state.y-16,5,'#423d40');circle(state.x+7,state.y-17,5,'#423d40');txt('YOU',state.x,state.y-35,11);
  rounded(16,13,225,31,'#12323db5',12);txt('TIDONG • NEIGHBORHOOD',128,34,15,'#e7f9e9');
 }
 function refresh(){let count=state.visited.length;$('count').textContent=count+' / '+T.SPOTS.length+' landmarks';$('progressbar').style.width=(count/T.SPOTS.length*100)+'%';$('landmarks').replaceChildren();for(const p of T.SPOTS){let row=document.createElement('div');row.className='item'+(state.visited.includes(p.id)?' done':'');let name=document.createElement('span');name.textContent=p.icon+' '+p.name;let button=document.createElement('button');button.className='button alt';button.textContent=state.visited.includes(p.id)?'Visited':'Visit';button.disabled=state.visited.includes(p.id);button.addEventListener('click',()=>{const r=T.visit(state,p.id);$('message').textContent=r.message;refresh();persist();});row.append(name,button);$('landmarks').appendChild(row);}const neighbors=$('neighbors');neighbors.replaceChildren();for(const n of S.NPCS){let close=Math.hypot(state.x-n.x,state.y-n.y)<=88;let done=story.talked.includes(n.id);let row=document.createElement('div');row.className='item'+(done?' done':'');let name=document.createElement('span');name.textContent=n.icon+' '+n.name+' · '+n.role+(done?' ✓':'');let btn=document.createElement('button');btn.type='button';btn.className='button alt';btn.textContent=close?'Talk':'Approach';btn.disabled=!close;btn.addEventListener('click',()=>{const r=S.talk(state,story,n.id);$('dialogue').textContent=r.message;if(r.ok){persist();refresh();}});row.append(name,btn);neighbors.append(row);}draw();}
 const dirs={ArrowUp:[0,-1],ArrowDown:[0,1],ArrowLeft:[-1,0],ArrowRight:[1,0],w:[0,-1],s:[0,1],a:[-1,0],d:[1,0]};
 function walk(key){let direction=dirs[key];if(!direction)return;if(T.move(state,...direction)){persist();refresh();}else{$('hint').textContent='A building or the town boundary is blocking the way.';}}
 window.addEventListener('keydown',e=>{if(e.target.tagName==='BUTTON')return;if(dirs[e.key]){e.preventDefault();walk(e.key);}});
 for(const b of document.querySelectorAll('[data-move]'))b.addEventListener('click',()=>walk(({up:'ArrowUp',down:'ArrowDown',left:'ArrowLeft',right:'ArrowRight'})[b.dataset.move]));
 c.addEventListener('pointerdown',e=>{let box=c.getBoundingClientRect(),x=(e.clientX-box.left)/box.width*960,y=(e.clientY-box.top)/box.height*620;let dx=x-state.x,dy=y-state.y;if(Math.hypot(dx,dy)>12){T.move(state,dx/Math.hypot(dx,dy),dy/Math.hypot(dx,dy));persist();refresh();}});
 refresh();
})();