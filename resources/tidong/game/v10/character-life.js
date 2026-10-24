/* TIDONG Character Life V2 — local/offline prototype rates, no payment or reward authority. */
(function(root,factory){const api=factory();if(typeof module==='object'&&module.exports)module.exports=api;else root.TidongCharacterLife=api;})(typeof globalThis!=='undefined'?globalThis:this,function(){
 'use strict';
 const VERSION=2,MAX_TICK=30;
 const DEFAULTS=Object.freeze({satiety:85,hydration:85,energy:90,hygiene:90,health:100,mood:80});
 const ACTIONS=Object.freeze(['eat','drink','sleep','wash','rest']);
 const clamp=v=>Math.round(Math.min(100,Math.max(0,v))*100)/100;
 const statsValid=stats=>!!(stats&&Object.keys(DEFAULTS).every(k=>typeof stats[k]==='number'&&Number.isFinite(stats[k])&&stats[k]>=0&&stats[k]<=100));
 function newState(ownerToken=''){return {version:VERSION,ownerToken,stats:{...DEFAULTS},lastAction:'',activeSeconds:0,routine:[]};}
 function isValidState(s){return !!(s&&s.version===VERSION&&typeof s.ownerToken==='string'&&s.ownerToken.length<=120&&
  statsValid(s.stats)&&typeof s.lastAction==='string'&&s.lastAction.length<=40&&Number.isFinite(s.activeSeconds)&&s.activeSeconds>=0&&s.activeSeconds<=100000000&&
  Array.isArray(s.routine)&&s.routine.length<=ACTIONS.length&&s.routine.every(a=>ACTIONS.includes(a))&&new Set(s.routine).size===s.routine.length);}
 function migrateLegacy(s,ownerToken=''){
  if(typeof ownerToken!=='string'||ownerToken.length>120)return null;
  if(!s||s.version!==1||!statsValid(s.stats)||typeof s.lastAction!=='string'||s.lastAction.length>40||
    !Number.isFinite(s.activeSeconds)||s.activeSeconds<0||s.activeSeconds>100000000)return null;
  return {version:VERSION,ownerToken,stats:{...s.stats},lastAction:s.lastAction,activeSeconds:s.activeSeconds,routine:[]};
 }
 function copy(s){return {version:VERSION,ownerToken:s.ownerToken,stats:{...s.stats},lastAction:s.lastAction,activeSeconds:s.activeSeconds,routine:[...s.routine]};}
 function speed(s){if(!isValidState(s))return .4;
  const energy=s.stats.energy<12?.5:s.stats.energy<30?.72:1;
  return s.stats.health<15?energy*.66:s.stats.health<35?energy*.85:energy;
 }
 function tick(s,seconds=0,mode='idle'){
  if(!isValidState(s)||!Number.isFinite(seconds)||seconds<0||!['idle','walk','run'].includes(mode))return {ok:false,message:'Invalid character state or activity.'};
  const d=Math.min(seconds,MAX_TICK),p=copy(s),n=p.stats,r={idle:1,walk:1.35,run:2.1}[mode];
  n.satiety=clamp(n.satiety-d*r*.0015);
  n.hydration=clamp(n.hydration-d*r*.0028);
  n.hygiene=clamp(n.hygiene-d*r*.0012);
  n.energy=clamp(n.energy-d*(mode==='run'?.026:mode==='walk'?.008:.002));
  if(n.satiety<12||n.hydration<12)n.health=clamp(n.health-d*.004);
  else if(n.health<100&&n.satiety>65&&n.hydration>65&&n.energy>50&&n.hygiene>35)n.health=clamp(n.health+d*.001);
  n.mood=clamp(n.mood-d*(n.energy<15||n.hygiene<20?.004:.0005));
  p.activeSeconds=Math.min(100000000,p.activeSeconds+d);
  return {ok:true,state:p};
 }
 function action(s,name,{consumed=false}={}){
  if(!isValidState(s)||!ACTIONS.includes(name))return {ok:false,message:'Invalid character action.'};
  if((name==='eat'||name==='drink')&&consumed!==true)return {ok:false,message:'A purchased food item must be consumed first.'};
  const p=copy(s),n=p.stats;
  if(name==='eat'){n.satiety=clamp(n.satiety+28);n.mood=clamp(n.mood+2);}
  if(name==='drink'){n.hydration=clamp(n.hydration+35);}
  if(name==='sleep'){n.energy=clamp(n.energy+52);n.satiety=clamp(n.satiety-6);n.hydration=clamp(n.hydration-5);n.mood=clamp(n.mood+8);}
  if(name==='wash'){n.hygiene=clamp(n.hygiene+45);n.mood=clamp(n.mood+3);}
  if(name==='rest'){n.energy=clamp(n.energy+19);n.mood=clamp(n.mood+5);}
  p.lastAction=name;
  if(!p.routine.includes(name))p.routine.push(name);
  return {ok:true,state:p,message:{eat:'Finished eating.',drink:'Finished drinking.',sleep:'A refreshing sleep.',wash:'Feeling clean and fresh.',rest:'Relaxed and rested.'}[name]};
 }
 function routineProgress(s){if(!isValidState(s))return {done:0,total:ACTIONS.length,completed:false};
  return {done:s.routine.length,total:ACTIONS.length,completed:s.routine.length===ACTIONS.length};
 }
 return {VERSION,DEFAULTS,ACTIONS,newState,isValidState,migrateLegacy,speed,tick,action,routineProgress};
});