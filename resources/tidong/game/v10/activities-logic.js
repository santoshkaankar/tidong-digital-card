/* Zero-monetization offline puzzles. Results are browser-local, not server-authoritative. */
(function(root,factory){const api=factory();if(typeof module==='object'&&module.exports)module.exports=api;else root.TidongActivities=api;})(typeof globalThis!=='undefined'?globalThis:this,function(){'use strict';
 const ICONS=['🌸','🐈','⭐','🌿'];
 function newState(){return {version:1,completed:[],garden:0,sortIndex:0,matching:[]};}
 function safe(s){if(!s||s.version!==1)return newState();return {version:1,completed:Array.isArray(s.completed)?[...new Set(s.completed.filter(x=>['memory','sort','garden'].includes(x)))]:[],garden:Number.isInteger(s.garden)?Math.max(0,Math.min(3,s.garden)):0,sortIndex:Number.isInteger(s.sortIndex)?Math.max(0,Math.min(4,s.sortIndex)):0,matching:Array.isArray(s.matching)?[...new Set(s.matching.filter(x=>ICONS.includes(x)))]:[]};}
 function memoryDeck(seed=2){const deck=[...ICONS,...ICONS].map((icon,i)=>({icon,id:i}));let n=(Math.floor(seed)||2)>>>0;for(let i=deck.length-1;i>0;i--){n=(1664525*n+1013904223)>>>0;let j=n%(i+1);[deck[i],deck[j]]=[deck[j],deck[i]];}return deck;}
 function match(a,b){return !!a&&!!b&&a.id!==b.id&&a.icon===b.icon;}
 const PACKAGES=Object.freeze([{name:'Books',slot:'Library',icon:'📚'},{name:'Flowers',slot:'Garden',icon:'🌻'},{name:'Groceries',slot:'Market',icon:'🥦'},{name:'Painting',slot:'Home',icon:'🖼️'}]);
 function sort(state,slot){if(state.sortIndex>=PACKAGES.length)return {ok:false,message:'Sorting already finished.'};let expected=PACKAGES[state.sortIndex].slot;if(slot!==expected)return {ok:false,message:'Try again. This parcel belongs at '+expected+'.'};state.sortIndex++;if(state.sortIndex===4)finish(state,'sort');return {ok:true,message:'Correct delivery!'};}
 const GARDEN_STEPS=['Seed','Water','Sun'];
 function garden(state,action){if(state.garden>=3)return {ok:false,message:'Garden puzzle complete.'};if(action!==GARDEN_STEPS[state.garden])return {ok:false,message:'Try the next garden step in order.'};state.garden++;if(state.garden===3)finish(state,'garden');return {ok:true,message:'Plant growth advanced.'};}
 function finish(state,id){if(['memory','sort','garden'].includes(id)&&!state.completed.includes(id)){state.completed.push(id);return true;}return false;}
 return {ICONS,PACKAGES,GARDEN_STEPS,newState,safe,memoryDeck,match,sort,garden,finish};
});