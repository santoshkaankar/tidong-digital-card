/* Standalone furniture floor-plan builder. No real ownership, payments or shared data. */
(function(root,factory){const api=factory();if(typeof module==='object'&&module.exports)module.exports=api;else root.TidongRoom=factory();})(typeof globalThis!=='undefined'?globalThis:this,function(){'use strict';
 const W=8,H=6,ITEMS=['plant','lamp','sofa','rug','books','painting','villa'];
 function initial(){return {version:1,placed:[]};}
 function owned(game,id){return !!game&&Array.isArray(game.inventory)&&ITEMS.includes(id)&&game.inventory.filter(x=>x===id).length;}
 function canPlace(game,layout,id,x,y){if(!ITEMS.includes(id)||!Number.isInteger(x)||!Number.isInteger(y)||x<0||x>=W||y<0||y>=H)return {ok:false,message:'Choose a valid position.'};if(layout.placed.some(p=>p.x===x&&p.y===y))return {ok:false,message:'This tile is already occupied.'};const count=layout.placed.filter(p=>p.id===id).length;if(count>=owned(game,id))return {ok:false,message:'You need to own another '+id+' in the demo Market.'};return {ok:true,message:'Placed '+id+'.'};}
 function put(game,layout,id,x,y){const result=canPlace(game,layout,id,x,y);if(result.ok)layout.placed.push({id,x,y});return result;}
 function remove(layout,x,y){let i=layout.placed.findIndex(p=>p.x===x&&p.y===y);if(i===-1)return false;layout.placed.splice(i,1);return true;}
 function safe(raw,game){const layout=initial();if(!raw||raw.version!==1||!Array.isArray(raw.placed)||raw.placed.length>W*H)return layout;for(let p of raw.placed){if(!p||typeof p!=='object')continue;put(game,layout,p.id,p.x,p.y);}return layout;}
 return {W,H,ITEMS,initial,owned,canPlace,put,remove,safe};
});