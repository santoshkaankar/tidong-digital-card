/* TIDONG Character Navigation V2 — geometry-aware offline playtest, no economic authority. */
(function(root,factory){const api=factory();if(typeof module==='object'&&module.exports)module.exports=api;else root.TidongCharacterNavigation=api;})(typeof globalThis!=='undefined'?globalThis:this,function(){
 'use strict';
 const CELL=.30, RADIUS=.22;
 const AREAS=Object.freeze({
  home:Object.freeze({minX:-4.55,maxX:4.55,minZ:-4.55,maxZ:4.55}),
  garden:Object.freeze({minX:-5.65,maxX:5.65,minZ:-5.65,maxZ:5.65})
 });
 const HOME=Object.freeze([
  {shape:'rect',x:-2.05,z:-1.45,w:2.68,d:1.25}, // couch
  {shape:'rect',x:1.85,z:3.0,w:2.65,d:1.8},     // bed
  {shape:'circle',x:-.3,z:-.55,r:.83},          // dining table
  {shape:'rect',x:3.05,z:-2.75,w:1.02,d:.72}, // wash basin
  {shape:'rect',x:3.55,z:1.9,w:1.67,d:.56},   // shelf
  {shape:'circle',x:3.9,z:-3.66,r:.38},
  {shape:'circle',x:-3.55,z:-3.64,r:.32}
 ]);
 const GARDEN=Object.freeze([
  {shape:'circle',x:2.4,z:-3.3,r:1.58}, // pond
  {shape:'rect',x:2.8,z:3.22,w:2.32,d:.86}, // bench
  {shape:'rect',x:-3.25,z:-2.78,w:2.65,d:1.95}, // flowers
  ...[[-5,-5],[-5,4.5],[5,5],[5,-.4],[-1.6,5.5]].map(([x,z])=>({shape:'circle',x,z,r:.30}))
 ]);
 const SPOTS=Object.freeze([[-3.5,1.1],[2.85,1.15],[1.2,2.7],[.8,-3.15],[-3.5,-3.35],[3.6,-.4],[-.2,3.5],[2.2,-2.25]]);
 const ITEM_SHAPES=Object.freeze({plant:{shape:'circle',r:.35},lamp:{shape:'circle',r:.20},sofa:{shape:'rect',w:2.68,d:1.25},rug:null,bed:{shape:'rect',w:2.65,d:1.8},books:{shape:'rect',w:1.67,d:.56},painting:null,table:{shape:'circle',r:.83},villa:{shape:'rect',w:1.4,d:1.4}});
 // Safe action points are BESIDE furniture; never inside solid geometry.
 const HOME_ACTIONS=Object.freeze({eat:{x:-.30,z: .72},drink:{x:-.30,z:.72},sleep:{x:1.75,z:1.74},wash:{x:3.05,z:-1.92},rest:{x:-2.05,z:-.36}});
 const HOME_ACTIVITIES=Object.freeze({relax:{x:-2.05,z:-.36},read:{x:2.65,z:1.22},plant:{x:3,z:-3.7}});
 const GARDEN_ACTIVITIES=Object.freeze({flowers:{x:-2.3,z:-1.18},pond:{x: .57,z:-2.05},bench:{x:2.8,z:2.35}});
 const SPAWNS=Object.freeze({home:{x:0.10,z:1.52},garden:{x:0,z:4.45}});
 function obstacles(area,placed=[]){if(!AREAS[area])return [];
  const base=area==='garden'?GARDEN:HOME;
  if(area!=='home'||!Array.isArray(placed))return [...base];
  return [...base,...placed.slice(0,8).flatMap((id,i)=>{const shape=Object.hasOwn(ITEM_SHAPES,id)?ITEM_SHAPES[id]:null;
    if(!shape||!SPOTS[i])return [];return [{...shape,x:SPOTS[i][0],z:SPOTS[i][1]}];})];
 }
 function walkable(area,p,placed=[],radius=RADIUS){const b=AREAS[area];if(!b||!p||!Number.isFinite(p.x)||!Number.isFinite(p.z)||!Number.isFinite(radius)||radius<0||radius>1)return false;
  if(p.x<b.minX+radius||p.x>b.maxX-radius||p.z<b.minZ+radius||p.z>b.maxZ-radius)return false;
  for(const o of obstacles(area,placed)){if(o.shape==='circle'&&Math.hypot(p.x-o.x,p.z-o.z)<o.r+radius)return false;
   if(o.shape==='rect'&&Math.abs(p.x-o.x)<o.w/2+radius&&Math.abs(p.z-o.z)<o.d/2+radius)return false;}
  return true;
 }
 function segmentClear(area,a,b,placed=[],radius=RADIUS){if(!a||!b||![a.x,a.z,b.x,b.z].every(Number.isFinite))return false;
  const length=Math.hypot(a.x-b.x,a.z-b.z),n=Math.max(1,Math.ceil(length/(CELL/3)));
  for(let i=0;i<=n;i++){const t=i/n;if(!walkable(area,{x:a.x+(b.x-a.x)*t,z:a.z+(b.z-a.z)*t},placed,radius))return false;}return true;
 }
 function destination(area,point,placed=[]){if(walkable(area,point,placed))return {...point};
  if(!point||![point.x,point.z].every(Number.isFinite))return null;
  // Nearest reachable stand-off within 1.2m; never snap through a wall.
  for(let d=CELL;d<=1.21;d+=CELL)for(let a=0;a<24;a++){
   const angle=a*Math.PI/12,p={x:point.x+Math.cos(angle)*d,z:point.z+Math.sin(angle)*d};
   if(walkable(area,p,placed))return p;
  }return null;
 }
 function route(area,from,to,placed=[]){const bounds=AREAS[area];if(!bounds||!walkable(area,from,placed))return {ok:false,path:[],message:'The character is outside the walking area.'};
  const target=destination(area,to,placed);if(!target)return {ok:false,path:[],message:'There is no safe place to stand near that activity.'};
  if(segmentClear(area,from,target,placed))return {ok:true,path:[target],destination:target};
  // Deterministic bounded A* grid, with corner-cutting prohibited.
  const cols=Math.floor((bounds.maxX-bounds.minX)/CELL)+1,rows=Math.floor((bounds.maxZ-bounds.minZ)/CELL)+1;
  const key=(i,j)=>j*cols+i;
  const point=(i,j)=>({x:bounds.minX+i*CELL,z:bounds.minZ+j*CELL});
  const indices=p=>[Math.max(0,Math.min(cols-1,Math.round((p.x-bounds.minX)/CELL))),Math.max(0,Math.min(rows-1,Math.round((p.z-bounds.minZ)/CELL)))];
  function nearestCell(p){const [ci,cj]=indices(p);let found=null,dist=Infinity;
   for(let dj=-4;dj<=4;dj++)for(let di=-4;di<=4;di++){const i=ci+di,j=cj+dj;if(i<0||j<0||i>=cols||j>=rows)continue;
    const pt=point(i,j),d=Math.hypot(pt.x-p.x,pt.z-p.z);
    if(d<dist&&walkable(area,pt,placed)&&segmentClear(area,p,pt,placed)){found=[i,j];dist=d;}}
   return found;
  }
  const start=nearestCell(from),end=nearestCell(target);
  if(!start||!end)return {ok:false,path:[],message:'No walkable grid connection is available.'};
  const startId=key(...start),goalId=key(...end),frontier=new Set([startId]),closed=new Set(),g=new Map([[startId,0]]),parent=new Map();
  const h=id=>Math.hypot(id%cols-end[0],Math.floor(id/cols)-end[1]);
  const steps=[[1,0],[-1,0],[0,1],[0,-1],[1,1],[1,-1],[-1,1],[-1,-1]];
  let found=false;
  for(let iteration=0;frontier.size&&iteration<cols*rows;iteration++){
   let current=-1,score=Infinity;
   for(const id of frontier){const value=(g.get(id)??Infinity)+h(id);if(value<score){score=value;current=id;}}
   if(current===goalId){found=true;break;}
   frontier.delete(current);closed.add(current);
   const ci=current%cols,cj=Math.floor(current/cols),p=point(ci,cj);
   for(const [di,dj] of steps){const i=ci+di,j=cj+dj;if(i<0||j<0||i>=cols||j>=rows)continue;
    const nid=key(i,j),np=point(i,j);if(closed.has(nid)||!walkable(area,np,placed))continue;
    if(di&&dj&&(!walkable(area,point(ci+di,cj),placed)||!walkable(area,point(ci,cj+dj),placed)))continue;
    if(!segmentClear(area,p,np,placed))continue;
    const cost=(g.get(current)??Infinity)+Math.hypot(di,dj);
    if(cost<(g.get(nid)??Infinity)){g.set(nid,cost);parent.set(nid,current);frontier.add(nid);}
   }
  }
  if(!found)return {ok:false,path:[],message:'No obstacle-free route was found.'};
  const cells=[];let cur=goalId;
  for(let n=0;n<cols*rows&&cur!==startId;n++){cells.push(point(cur%cols,Math.floor(cur/cols)));cur=parent.get(cur);if(cur===undefined)return {ok:false,path:[],message:'Route reconstruction failed.'};}
  cells.reverse();
  // String-pull waypoints when direct travel is obstacle-free.
  const raw=[from,...cells,target],path=[];let i=0;
  while(i<raw.length-1){let j=raw.length-1;for(;j>i+1;j--)if(segmentClear(area,raw[i],raw[j],placed))break;
   if(j<=i) return {ok:false,path:[],message:'Invalid route geometry.'};path.push(raw[j]);i=j;
  }
  return {ok:true,path,destination:target};
 }
 return {CELL,RADIUS,AREAS,HOME_ACTIONS,HOME_ACTIVITIES,GARDEN_ACTIVITIES,SPAWNS,obstacles,walkable,segmentClear,destination,route};
});