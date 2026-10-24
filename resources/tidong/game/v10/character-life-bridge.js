/* V11 demo receipts + V2 character life interactions. Browser playtest, NOT server authority. */
(function(){'use strict';
 const L=window.TidongLogic,C=window.TidongCharacterLife;
 if(!L||!C)return;
 const GAME_KEY='tidong.virtual-life.demo.v3',LIFE_KEY='tidong.virtual-life.character.v2',OLD_KEY='tidong.virtual-life.character.v1';
 function gameState(){try{const s=JSON.parse(localStorage.getItem(GAME_KEY));return L.isValidState(s)?s:null;}catch(_){return null;}}
 function ownerToken(s,previous=''){
   if(!s)return previous;
   const welcome=s.ledger?.find(e=>e&&e.id==='welcome');
   if(welcome&&typeof welcome.at==='string'&&welcome.at.length<100)return 'game:'+welcome.at;
   // V11 truncates history after 80 entries. Keep the already bound ID instead of
   // treating ledger pruning as a new registration. A fresh save restores welcome.
   return previous||'game:legacy-without-welcome';
 }
 function load(){
   let latest=null;
   try{latest=JSON.parse(localStorage.getItem(LIFE_KEY));}catch(_){}
   const existing=C.isValidState(latest)?latest:null;
   const token=ownerToken(gameState(),existing?.ownerToken||'');
   if(existing)return existing.ownerToken===token?existing:C.newState(token);
   // Only migrate V1 when no usable V2 save exists; avoid reviving stale V1
   // after an established V2 record becomes invalid/corrupt.
   if(latest===null){
     try{const legacy=JSON.parse(localStorage.getItem(OLD_KEY));const upgraded=C.migrateLegacy(legacy,token);if(upgraded)return upgraded;}catch(_){}
   }
   return C.newState(token);
 }
 let life=load(),running=false,busy=false,dirty=false,lastStore=0,lastRender=0,messageNode=null,runButton=null,routineNode=null;
 const bars={},allButtons=[];
 function allowed(){const s=gameState();return !!(s&&L.canPlay(s));}
 function notice(message){if(messageNode)messageNode.textContent=message;}
 function save(force=false){if(!dirty&&!force)return true;
   if(!force&&Date.now()-lastStore<3500)return true;
   try{localStorage.setItem(LIFE_KEY,JSON.stringify(life));dirty=false;lastStore=Date.now();return true;}
   catch(_){notice('Unable to save character progress on this device.');return false;}
 }
 function setBusy(value){busy=!!value;update();}
 function cancelAction(){if(busy){busy=false;update();notice('Activity cancelled. Your items were not used.');}}
 function update(){
   for(const [key,parts]of Object.entries(bars)){
     const amount=Math.round(life.stats[key]);parts.num.textContent=amount+'%';parts.fill.style.width=amount+'%';
     parts.meter.setAttribute('aria-valuenow',String(amount));
     parts.meter.classList.toggle('life-low',amount<=20);
   }
   if(runButton){runButton.setAttribute('aria-pressed',String(running));runButton.textContent=running?'🏃 Running ON':'🚶 Walking';runButton.disabled=!allowed()||busy;}
   if(routineNode){const progress=C.routineProgress(life);
     routineNode.textContent='Personal routine: '+progress.done+' / '+progress.total+' activities tried'+(progress.completed?' · Complete ✓':'')+' · No rewards';}
   const s=gameState();
   const food=!!s?.lots?.some(l=>l.status==='owned'&&L.ITEMS.some(i=>i.id===l.itemId&&i.category==='Food')&&l.itemId!=='milk');
   const milk=!!s?.lots?.some(l=>l.status==='owned'&&l.itemId==='milk');
   for(const b of allButtons)b.disabled=!allowed()||busy||(b.dataset.lifeAction==='eat'&&!food)||(b.dataset.lifeAction==='drink'&&!milk);
 }
 function markDirty(){dirty=true;const now=performance.now();if(now-lastRender>250){update();lastRender=now;}save();}
 function stepDistance(distance){if(!allowed()||!Number.isFinite(distance)||distance<=0)return;
   const mode=running&&life.stats.energy>=15?'run':'walk';const result=C.tick(life,Math.min(distance,5)*.85,mode);
   if(result.ok){life=result.state;markDirty();}
 }
 function tick(){if(document.visibilityState!=='visible'||!allowed()||busy)return;
   const result=C.tick(life,1,'idle');if(result.ok){life=result.state;markDirty();}
 }
 function requestAction(action){if(!allowed())return notice('Complete starter shopping and subscription before activities.');
   if(busy)return;
   const s=gameState();if(!s)return;
   if(action==='eat'&&!s.lots.some(l=>l.status==='owned'&&l.itemId!=='milk'&&L.ITEMS.some(i=>i.id===l.itemId&&i.category==='Food')))
     return notice('Buy food in the Market first.');
   if(action==='drink'&&!s.lots.some(l=>l.status==='owned'&&l.itemId==='milk'))return notice('Buy milk in the Market first.');
   notice('Walking to the '+({sleep:'bed',wash:'wash station',eat:'table',drink:'table',rest:'sofa'}[action])+'…');
   window.dispatchEvent(new CustomEvent('tidong:life-action-request',{detail:{action}}));
 }
 function finishAction(action){
   busy=false;
   if(!allowed()){update();return {ok:false,message:'Access requirements are not satisfied.'};}
   const s=gameState();if(!s){update();return {ok:false,message:'Game save is missing.'};}
   let consumed=false,lot=null;
   if(action==='eat'||action==='drink'){
     lot=s.lots.find(l=>l.status==='owned'&&(action==='drink'?l.itemId==='milk':l.itemId!=='milk'&&L.ITEMS.some(i=>i.id===l.itemId&&i.category==='Food')));
     if(!lot){update();return {ok:false,message:'You need a purchased item in the inventory.'};}
     consumed=true;
   }
   // Validate the character transition BEFORE modifying an item receipt.
   const next=C.action(life,action,{consumed});
   if(!next.ok){update();notice(next.message);return next;}
   if(lot){
     const result=L.consume(s,lot.serial);
     if(!result.ok){update();notice(result.message);return {ok:false,message:result.message};}
     try{localStorage.setItem(GAME_KEY,JSON.stringify(s));}
     catch(_){update();return {ok:false,message:'Inventory could not be saved; item has not been consumed.'};}
     window.dispatchEvent(new CustomEvent('tidong:game-save-updated'));
   }
   life=next.state;dirty=true;save(true);update();notice(next.message);return next;
 }
 function reloadIfNewSave(){const expected=ownerToken(gameState(),life.ownerToken);
   if(life.ownerToken!==expected){save(true);life=C.newState(expected);dirty=true;save(true);busy=false;running=false;notice('New game detected. Personal life stats were reset for this save.');}
   update();
 }
 function initUI(){if(!allowed())return;
   const side=document.querySelector('.sidebar');if(!side)return;
   const card=document.createElement('section');card.className='card life-card';card.setAttribute('aria-label','Character daily needs');
   const head=document.createElement('div');head.className='life-head';
   const heading=document.createElement('h3');heading.textContent='Character · Daily Life';
   const mark=document.createElement('span');mark.textContent='OFFLINE PLAYTEST';mark.className='life-mark';head.append(heading,mark);card.append(head);
   const desc=document.createElement('p');desc.textContent='Needs change while actively playing. Your character walks around furniture to reach daily activities.';card.append(desc);
   const labels={satiety:'🍲 Fullness',hydration:'💧 Hydration',energy:'⚡ Energy',hygiene:'🫧 Hygiene',health:'❤️ Health',mood:'✨ Mood'};
   for(const [key,label]of Object.entries(labels)){
     const row=document.createElement('div');row.className='life-stat';
     const top=document.createElement('div');top.className='life-label';
     const name=document.createElement('span');name.textContent=label;
     const num=document.createElement('output');top.append(name,num);
     const meter=document.createElement('div');meter.className='life-meter';meter.setAttribute('role','progressbar');
     meter.setAttribute('aria-label',label);meter.setAttribute('aria-valuemin','0');meter.setAttribute('aria-valuemax','100');
     const fill=document.createElement('div');fill.className='life-fill';meter.append(fill);row.append(top,meter);card.append(row);bars[key]={num,fill,meter};
   }
   routineNode=document.createElement('p');routineNode.className='life-routine';card.append(routineNode);
   const buttons=document.createElement('div');buttons.className='life-actions';
   for(const [a,label]of Object.entries({eat:'🍎 Eat',drink:'🥛 Drink',sleep:'🌙 Sleep',wash:'🚿 Wash',rest:'🛋 Rest'})){
     const btn=document.createElement('button');btn.type='button';btn.dataset.lifeAction=a;btn.textContent=label;
     btn.addEventListener('click',()=>requestAction(a));buttons.append(btn);allButtons.push(btn);
   }
   runButton=document.createElement('button');runButton.type='button';runButton.className='life-run';
   runButton.addEventListener('click',()=>{running=!running;update();notice(running?'Running uses more energy.':'Walking mode enabled.');});
   buttons.append(runButton);card.append(buttons);
   messageNode=document.createElement('p');messageNode.className='life-message';messageNode.setAttribute('role','status');
   messageNode.setAttribute('aria-live','polite');messageNode.textContent='Choose an action or explore the world.';card.append(messageNode);
   side.prepend(card);update();
 }
 document.addEventListener('visibilitychange',()=>save(true));window.addEventListener('pagehide',()=>save(true));
 window.addEventListener('tidong:game-save-updated',reloadIfNewSave);
 window.addEventListener('storage',e=>{
   if(e.key===GAME_KEY)reloadIfNewSave();
   if(e.key===LIFE_KEY&&!dirty){try{const s=JSON.parse(e.newValue);if(C.isValidState(s)&&s.ownerToken===life.ownerToken){life=s;update();}}catch(_){}}
 });
 // Migrate V1 save without requiring login or changing the legacy file.
 try{if(!localStorage.getItem(LIFE_KEY)){dirty=true;save(true);}}catch(_){}
 initUI();setInterval(tick,1000);
 window.TidongCharacterBridge={allowed,moveScale:()=>C.speed(life)*(running&&life.stats.energy>=15?1.45:1),
   recordMovement:stepDistance,finishAction,setBusy,cancelAction,
   setWorld:(_w)=>{},getLife:()=>JSON.parse(JSON.stringify(life))};
})();