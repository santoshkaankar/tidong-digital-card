/* Standalone visual game preview. All activity uses browser-local fictional balances. */
(function () {
  'use strict';
  const L = window.TidongLogic;
  const Launch = window.TidongLaunch;
  const World = window.TidongWorld;
  const $ = id => document.getElementById(id);
  const storageKey = 'tidong.virtual-life.demo.v2';
  const previousKey = 'tidong.virtual-life.demo.v1';
  function load() {
    try {
      const candidate = JSON.parse(localStorage.getItem(storageKey));
      if (L.isValidState(candidate)) return candidate;
      const older = JSON.parse(localStorage.getItem(previousKey));
      return L.migrateV1(older) || L.newState();
    } catch (_) { return L.newState(); }
  }
  let state = load();
  let currentFilter = 'All';
  let pendingItem = null;
  let toastTimer = null;
  let animationTime = 0;
  const canvas = $('world');
  const ctx = canvas.getContext('2d');
  const WIDTH = canvas.width, HEIGHT = canvas.height;
  const TILE_W = 86, TILE_H = 44;
  const MIN_X = .2, MAX_X = 8.8, MIN_Y = .2, MAX_Y = 6.8;
  let movement = { targetX: state.avatar.x, targetY: state.avatar.y };
  let lastTime = 0;
  function save() { try { localStorage.setItem(storageKey, JSON.stringify(state)); } catch (_) {} }
  function notify(message) {
    const el = $('toast');
    el.textContent = message;
    el.classList.add('visible');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => el.classList.remove('visible'), 2900);
  }
  const GARDEN_KEY = 'tidong.virtual-life.garden.v5';
  function getGarden() {
    try { return World.safeProgress(JSON.parse(localStorage.getItem(GARDEN_KEY))); }
    catch (_) { return {completed: []}; }
  }
  function updateJourney() {
    const guide = Launch.journey(state,getGarden());
    $('journey-progress').textContent = guide.completed + ' / ' + guide.total;
    const list = $('journey-steps'); list.replaceChildren();
    for (const [i,entry] of guide.milestones.entries()) {
      const row=document.createElement('div');
      row.className='journey-step'+(entry.done?' done':guide.next===entry.id?' current':'');
      const mark=document.createElement('span'); mark.className='journey-mark';
      mark.textContent=entry.done?'✓':String(i+1);
      const desc=document.createElement('span'); desc.textContent=entry.title;
      row.append(mark,desc); list.appendChild(row);
    }
    const homeOwned = state.inventory.some(id=>L.ITEMS.some(item=>item.id===id && item.category==='Home'));
    const button=$('journey-action');
    button.disabled = guide.next==='done';
    button.textContent = ({access:'Enter Virtual Life →',market:'Explore the Market →',
      firstItem:'Shop Your First Item →',decorate:homeOwned?'Place Your Furniture →':'Find Home Furniture →',
      garden:'Explore the 3D Garden →',done:'✓ First Adventure Complete'})[guide.next];
  }
  function shopFor(category) {
    currentFilter=category;
    for(const b of document.querySelectorAll('#filters .filter')) b.classList.toggle('active',b.dataset.filter===category);
    openShop();
  }
  $('journey-action').addEventListener('click',()=>{
    const next=Launch.journey(state,getGarden()).next;
    if(next==='access') { $('welcome-modal').classList.remove('hidden'); return $('enter-world').focus(); }
    if(next==='market') return changeScene('market');
    if(next==='firstItem') return shopFor('All');
    if(next==='decorate') {
      const homeOwned=state.inventory.some(id=>L.ITEMS.some(item=>item.id===id && item.category==='Home'));
      if(!homeOwned) return shopFor('Home');
      changeScene('home');
      $('inventory').scrollIntoView({behavior:'smooth',block:'center'});
      return notify('Choose Place beside your purchased home furniture.');
    }
    if(next==='garden') window.location.href='studio3d.html#garden';
  });
  $('export-save').addEventListener('click',()=>{
    try {
      const json=Launch.exportSave(state,getGarden());
      const url=URL.createObjectURL(new Blob([json],{type:'application/json'}));
      const a=document.createElement('a'); a.href=url;a.download='TIDONG_Virtual_Life_DEMO_Save_V6.json';
      document.body.appendChild(a);a.click();a.remove();
      setTimeout(()=>URL.revokeObjectURL(url),1000);
      $('save-status').textContent='Demo backup created. Store the JSON file safely.';
    } catch(e){$('save-status').textContent=e.message;}
  });
  $('import-save').addEventListener('click',()=>$('import-save-file').click());
  $('import-save-file').addEventListener('change',async e=>{
    const f=e.target.files[0];e.target.value='';if(!f)return;
    try {
      if(f.size>Launch.MAX_JSON)throw Error('Backup is too large (96 KB maximum).');
      const restore=Launch.importSave(await f.text());
      if(!window.confirm('Replace this browser demo progress with the selected backup? This affects fictional game progress only.'))return;
      // Recover prior keys if storage runs out while restoring.
      const previous=localStorage.getItem(storageKey),oldGarden=localStorage.getItem(GARDEN_KEY);
      try {
        localStorage.setItem(storageKey,JSON.stringify(restore.game));
        localStorage.setItem(GARDEN_KEY,JSON.stringify(restore.garden));
      } catch(e) {
        try {
          if(previous===null)localStorage.removeItem(storageKey);else localStorage.setItem(storageKey,previous);
          if(oldGarden===null)localStorage.removeItem(GARDEN_KEY);else localStorage.setItem(GARDEN_KEY,oldGarden);
        }catch(_){}
        throw Error('Browser storage is unavailable. Original progress has been kept when possible.');
      }
      state=restore.game;movement={targetX:state.avatar.x,targetY:state.avatar.y};
      closeShop();$('confirm-modal').classList.add('hidden');
      $('welcome-modal').classList.toggle('hidden',!!state.subscribed);
      updateUI();
      $('save-status').textContent='Demo progress restored. Both Home and Garden are available.';
      notify('Your offline demo world has been restored.');
    }catch(err){$('save-status').textContent=err.message;}
  });
  function updateUI() {
    $('balance').textContent = state.coins.toLocaleString('en-IN');
    $('task-count').textContent = state.tasks.length + '/' + L.TASKS.length;
    $('progress-fill').style.width = (state.tasks.length / L.TASKS.length * 100) + '%';
    $('tasks').replaceChildren();
    for (const task of L.TASKS) {
      const done = state.tasks.includes(task.id);
      const row = document.createElement('div');
      row.className = 'task' + (done ? ' done' : '');
      const check = document.createElement('span');
      check.className = 'task-check';
      check.textContent = done ? '✓' : '';
      const body = document.createElement('div');
      body.className = 'task-body';
      const title = document.createElement('b');
      title.textContent = task.title;
      const description = document.createElement('small');
      description.textContent = task.desc;
      body.append(title, description);
      const reward = document.createElement('span');
      reward.className = 'task-reward';
      reward.textContent = done ? 'DONE' : '+' + task.reward + ' T';
      row.append(check, body, reward);
      $('tasks').appendChild(row);
    }
    $('items-count').textContent = state.inventory.length + ' Items';
    $('inventory').replaceChildren();
    if (!state.inventory.length) {
      const info = document.createElement('p');
      info.className = 'muted';
      info.textContent = 'Your virtual purchases will appear here.';
      $('inventory').appendChild(info);
    }
    for (const [index, id] of state.inventory.entries()) {
      const item = L.ITEMS.find(i => i.id === id);
      const wrapper = document.createElement('div');
      wrapper.className = 'inventory-item';
      wrapper.title = item.name;
      wrapper.append(document.createTextNode(item.icon + ' ' + item.name));
      const remaining = state.inventory.slice(0, index + 1).filter(x => x === id).length > state.placed.filter(x => x === id).length;
      if (item.category === 'Fashion') {
        const button = document.createElement('button');
        button.type = 'button';
        button.textContent = state.wornItem === id ? 'Wearing' : 'Wear';
        button.disabled = state.wornItem === id;
        button.addEventListener('click', () => { const r=L.wear(state,id); if (r.ok) { taskOnce('wardrobe'); updateUI(); } notify(r.message); });
        wrapper.appendChild(button);
      }
      if (item.category === 'Home' && !remaining) {
        const button = document.createElement('button');
        button.textContent = 'Move'; button.type = 'button';
        button.addEventListener('click', () => {
          const index = state.placed.indexOf(id);
          const r=L.unplace(state,index);
          if (r.ok) { taskOnce('rearrange'); updateUI(); }
          notify(r.message);
        });
        wrapper.appendChild(button);
      }
      if (item.category === 'Home' && remaining) {
        const button = document.createElement('button');
        button.textContent = 'Place';
        button.type = 'button';
        button.addEventListener('click', () => {
          const result = L.place(state, id);
          if (result.ok) { changeScene('home'); taskOnce('decorate'); save(); updateUI(); }
          notify(result.message);
        });
        wrapper.appendChild(button);
      }
      $('inventory').appendChild(wrapper);
    }
    $('daily-routine').disabled = state.tasks.includes('routine') || !state.subscribed;
    $('daily-routine').textContent = state.tasks.includes('routine') ? '✓ Daily Routine Complete' : '✨ Complete Daily Routine  +20 T';
    const names = { home: 'My Home', market: 'The Marketplace', friend: 'Friend’s Home (Demo)', park: 'Peaceful Park' };
    $('avatar-style').value = state.style;
    $('world-light').value = state.timeOfDay;
    $('scene-title').textContent = names[state.scene];
    $('scene-tag').textContent = names[state.scene].toUpperCase();
    for (const [s, button] of [['home', 'go-home'], ['market', 'go-market'], ['friend', 'go-friend'], ['park', 'go-park']]) $(button).classList.toggle('active', state.scene === s);
    save();
    updateJourney();
  }
  function taskOnce(id) {
    const result = L.completeTask(state, id);
    if (result.ok) { updateUI(); notify(result.message); }
  }
  function changeScene(name) {
    if (!state.subscribed) return;
    state.scene = name;
    state.avatar = { x: 4, y: 5 };
    movement = { targetX: 4, targetY: 5 };
    if (name === 'market') taskOnce('market');
    if (name === 'friend') taskOnce('visit');
    if (name === 'park') taskOnce('park');
    $('hint').textContent = name === 'market' ? 'Browse virtual stalls or select Shop Virtual Items.' : name === 'friend' ? 'Welcome! Explore your friend’s home.' : name === 'park' ? 'Take a walk among the virtual trees.' : 'Tap the floor or use arrow keys to move your character.';
    updateUI();
  }
  function openShop() { if (!state.subscribed) return; renderShop(); $('shop-modal').classList.remove('hidden'); }
  function closeShop() { $('shop-modal').classList.add('hidden'); }
  function renderShop() {
    $('products').replaceChildren();
    for (const item of L.ITEMS.filter(i => currentFilter === 'All' || i.category === currentFilter)) {
      const card = document.createElement('div'); card.className = 'product';
      const art = document.createElement('div'); art.className = 'product-art'; art.textContent = item.icon;
      const details = document.createElement('div');
      const title = document.createElement('div'); title.className = 'product-title'; title.textContent = item.name;
      const desc = document.createElement('p'); desc.className = 'product-description'; desc.textContent = item.desc;
      const foot = document.createElement('div'); foot.className = 'product-bottom';
      const price = document.createElement('b'); price.textContent = item.price.toLocaleString('en-IN') + ' T · ₹' + item.price.toLocaleString('en-IN') + ' reference';
      const btn = document.createElement('button'); btn.type = 'button'; btn.textContent = 'View';
      btn.addEventListener('click', () => showBuyConfirmation(item));
      foot.append(price, btn); details.append(title, desc, foot); card.append(art, details);
      $('products').appendChild(card);
    }
  }
  function showBuyConfirmation(item) {
    pendingItem = item;
    $('confirm-emoji').textContent = item.icon;
    $('confirm-title').textContent = item.name;
    $('confirm-desc').textContent = item.desc;
    $('confirm-price').textContent = item.price.toLocaleString('en-IN') + ' T-Coins · ₹' + item.price.toLocaleString('en-IN') + ' price reference';
    $('confirm-buy').disabled = item.price > state.coins;
    $('confirm-buy').textContent = item.price > state.coins ? 'Insufficient demo coins' : 'Confirm virtual purchase';
    $('confirm-modal').classList.remove('hidden');
  }
  $('enter-world').addEventListener('click', () => {
    const result = L.subscribe(state);
    if (result.ok || state.subscribed) { $('welcome-modal').classList.add('hidden'); updateUI(); notify(result.message); if (location.hash==='#market') changeScene('market'); }
    else notify(result.message);
  });
  $('go-home').addEventListener('click', () => changeScene('home'));
  $('go-market').addEventListener('click', () => changeScene('market'));
  $('go-friend').addEventListener('click', () => changeScene('friend'));
  $('go-park').addEventListener('click', () => changeScene('park'));
  $('avatar-style').addEventListener('change', e => {
    const r = L.changeStyle(state, e.target.value);
    if (r.ok) { taskOnce('style'); updateUI(); }
    notify(r.message);
  });
  $('world-light').addEventListener('change', e => {
    const r = L.setTimeOfDay(state, e.target.value);
    if (r.ok) { taskOnce('evening'); updateUI(); }
    notify(r.message);
  });
  $('open-shop').addEventListener('click', openShop);
  $('shop-close').addEventListener('click', closeShop);
  $('confirm-cancel').addEventListener('click', () => $('confirm-modal').classList.add('hidden'));
  $('confirm-buy').addEventListener('click', () => {
    if (!pendingItem) return;
    const result = L.buy(state, pendingItem.id);
    $('confirm-modal').classList.add('hidden');
    if (result.ok) { taskOnce('firstBuy'); updateUI(); renderShop(); }
    notify(result.message);
    pendingItem = null;
  });
  $('filters').addEventListener('click', e => {
    const button = e.target.closest('button[data-filter]'); if (!button) return;
    currentFilter = button.dataset.filter;
    for (const node of $('filters').querySelectorAll('.filter')) node.classList.toggle('active', node === button);
    renderShop();
  });
  $('daily-routine').addEventListener('click', () => taskOnce('routine'));
  $('reset-demo').addEventListener('click', () => {
    if (window.confirm('Reset the entire local virtual world? This removes demo items, progress and coins from this browser only.')) {
      state = L.newState(); save(); try {localStorage.removeItem('tidong.virtual-life.garden.v5');}catch (_) {} movement = { targetX: 4, targetY: 5 }; updateUI(); $('welcome-modal').classList.remove('hidden'); notify('Demo reset successfully.');
    }
  });
  for (const modalId of ['shop-modal', 'confirm-modal']) $(modalId).addEventListener('click', e => {
    if (e.target === $(modalId)) $(modalId).classList.add('hidden');
  });
  window.addEventListener('keydown', e => {
    const overlays = ['welcome-modal', 'shop-modal', 'confirm-modal'].some(x => !$(x).classList.contains('hidden'));
    if (e.key === 'Escape') { closeShop(); $('confirm-modal').classList.add('hidden'); return; }
    if (overlays || !state.subscribed) return;
    const delta = { ArrowLeft: [-1,0], a: [-1,0], ArrowRight: [1,0], d: [1,0], ArrowUp: [0,-1], w: [0,-1], ArrowDown: [0,1], s: [0,1] }[e.key];
    if (delta) { e.preventDefault(); moveBy(delta); }
  });
  function clamp(v,min,max){return Math.min(max,Math.max(min,v));}
  function moveBy(delta) {
    movement.targetX = clamp(Math.round(movement.targetX + delta[0]), MIN_X, MAX_X);
    movement.targetY = clamp(Math.round(movement.targetY + delta[1]), MIN_Y, MAX_Y);
  }
  for (const button of document.querySelectorAll('[data-dir]')) button.addEventListener('click', () => {
    if (!state.subscribed) return;
    const directions = { left: [-1,0], right: [1,0], up: [0,-1], down: [0,1] };
    moveBy(directions[button.dataset.dir]);
  });
  canvas.addEventListener('pointerdown', e => {
    if (!state.subscribed) return;
    const box = canvas.getBoundingClientRect();
    const sx = (e.clientX - box.left) / box.width * WIDTH;
    const sy = (e.clientY - box.top) / box.height * HEIGHT;
    const ux = (sx - 480) / (TILE_W / 2);
    const uy = (sy - 138) / (TILE_H / 2);
    const x = (ux + uy)/2, y = (uy-ux)/2;
    if (x >= -1 && x <= 10 && y >= -1 && y <= 9) {
      movement.targetX = clamp(x, MIN_X, MAX_X);
      movement.targetY = clamp(y, MIN_Y, MAX_Y);
    }
  });
  function iso(x,y,z=0){return {x:480+(x-y)*TILE_W/2,y:138+(x+y)*TILE_H/2-z};}
  function polygon(points, fill, stroke, lineWidth=1){
    ctx.beginPath();ctx.moveTo(points[0].x,points[0].y);
    for(let i=1;i<points.length;i++)ctx.lineTo(points[i].x,points[i].y);
    ctx.closePath();ctx.fillStyle=fill;ctx.fill();
    if(stroke){ctx.strokeStyle=stroke;ctx.lineWidth=lineWidth;ctx.stroke();}
  }
  function roundedRect(x,y,w,h,r,color){ctx.fillStyle=color;ctx.beginPath();ctx.roundRect(x,y,w,h,r);ctx.fill();}
  function ellipse(x,y,rx,ry,color){ctx.beginPath();ctx.ellipse(x,y,rx,ry,0,0,Math.PI*2);ctx.fillStyle=color;ctx.fill();}
  function line(x,y,x2,y2,color,width=2){ctx.beginPath();ctx.moveTo(x,y);ctx.lineTo(x2,y2);ctx.strokeStyle=color;ctx.lineWidth=width;ctx.lineCap='round';ctx.stroke();}
  function isoBox(x,y,w=45,h=31,d=24,top='#82b0a5',left='#375a70',right='#496c7b'){
    polygon([{x:x-w,y:y-h},{x,y:y-h-d/2},{x:x+w,y:y-h},{x,y:y-h+d/2}],top);
    polygon([{x:x-w,y:y-h},{x,y:y-h+d/2},{x,y:y+d/2},{x:x-w,y:y}],left);
    polygon([{x,y:y-h+d/2},{x:x+w,y:y-h},{x:x+w,y:y},{x,y:y+d/2}],right);
  }
  function shadow(x,y,rx=35){ellipse(x,y,rx,10,'#050f1766');}
  function textAt(text,x,y,size=16,color='#d6efff',font='900'){ctx.fillStyle=color;ctx.textAlign='center';ctx.font=font+' '+size+'px system-ui,sans-serif';ctx.fillText(text,x,y);}
  function drawWall(){
    const top=iso(0,0),left=iso(0,8),right=iso(10,0);
    polygon([{x:top.x,y:top.y-112},{x:left.x-12,y:left.y-112},{x:left.x-12,y:left.y-8},{x:top.x,y:top.y+2}], '#374b5e');
    polygon([{x:top.x,y:top.y-112},{x:right.x+12,y:right.y-112},{x:right.x+12,y:right.y-8},{x:top.x,y:top.y+2}], '#476277');
    const w=iso(3,0,78); polygon([{x:w.x-25,y:w.y-25},{x:w.x+25,y:w.y},{x:w.x+25,y:w.y+57},{x:w.x-25,y:w.y+35}],'#c6d9da','#eddec5',3);
    line(w.x-25,w.y+5,w.x+25,w.y+28,'#557584',2);
    const w2=iso(0,4,78);polygon([{x:w2.x-24,y:w2.y},{x:w2.x+24,y:w2.y-23},{x:w2.x+24,y:w2.y+37},{x:w2.x-24,y:w2.y+61}],'#c6d9da','#eedcc4',3);
  }
  function drawFloor(){
    for(let x=0;x<10;x++)for(let y=0;y<8;y++){
      const c=iso(x,y);
      let fill;
      if(state.scene==='park')fill=((x+y)%2)?'#67967d':'#79a48c';
      else if(state.scene==='market')fill=((x+y)%2)?'#526e69':'#648276';
      else if(state.scene==='friend')fill=((x+y)%2)?'#876c77':'#967b88';
      else fill=((x+y)%2)?'#a48476':'#ac8b78';
      polygon([{x:c.x,y:c.y-TILE_H/2},{x:c.x+TILE_W/2,y:c.y},{x:c.x,y:c.y+TILE_H/2},{x:c.x-TILE_W/2,y:c.y}],fill,'#1c354331',.8);
    }
  }
  function drawFurniture(o){
    const p=iso(o.x,o.y);shadow(p.x,p.y,29);
    if(o.kind==='sofa' || o.kind==='rug' || o.kind==='villa'){isoBox(p.x,p.y-2,42,25,22,'#e6a875','#896c82','#ba7c72');roundedRect(p.x-34,p.y-45,66,20,6,'#eac29b');}
    else if(o.kind==='table' || o.kind==='books' || o.kind==='painting'){isoBox(p.x,p.y-4,29,26,18,'#b6dfbc','#597871','#70938a');ellipse(p.x,p.y-36,16,7,'#f1d4b6');}
    else if(o.kind==='plant'){isoBox(p.x,p.y-4,15,14,14,'#c98161','#76535b','#a86258');ellipse(p.x,p.y-31,19,24,'#3f9b79');ellipse(p.x-12,p.y-38,11,14,'#66bd7c');ellipse(p.x+9,p.y-45,10,17,'#83ce8a');}
    else if(o.kind==='lamp'){line(p.x,p.y-9,p.x,p.y-56,'#f0d69f',5);polygon([{x:p.x-20,y:p.y-55},{x:p.x+20,y:p.y-55},{x:p.x+13,y:p.y-74},{x:p.x-13,y:p.y-74}],'#ffde99');ellipse(p.x,p.y-68,24,7,'#ffdd9860');}
    else if(o.kind==='bed'){isoBox(p.x,p.y-1,49,15,30,'#b7d2dc','#6b879c','#7896a3');roundedRect(p.x-18,p.y-41,36,18,7,'#f0f6ff');}
    else if(o.kind==='car'){isoBox(p.x,p.y-5,49,16,27,'#ef7479','#7d4154','#b5546a');roundedRect(p.x-30,p.y-30,60,14,8,'#accbe0');ellipse(p.x-34,p.y+10,8,10,'#243344');ellipse(p.x+32,p.y+10,8,10,'#243344');}
  }
  function drawMarketStall(o){
    const p=iso(o.x,o.y);shadow(p.x,p.y,48);
    isoBox(p.x,p.y-4,53,58,33,'#7d9786','#506274','#69778a');
    isoBox(p.x,p.y-67,60,10,37,o.color||'#f9bf8d','#ad6a72','#d99082');
    roundedRect(p.x-36,p.y-42,72,14,5,'#dcebd9');
    textAt(o.label,p.x,p.y-32,13,'#24485a');
    for(let k=-1;k<=1;k++){ellipse(p.x+k*20,p.y-10,8,6,['#e1b885','#bce7ac','#f7d7ca'][k+1]);}
  }
  function drawPerson(x,y,color='#80e0d0',friend=false){
    const p=iso(x,y);const bob=Math.sin(animationTime*3+(friend?1:0))*1.8;
    shadow(p.x,p.y,17);
    const base=p.y+bob;
    if (!friend) {
      color = ({ mint:'#80e0d0', coral:'#f29b8b', sky:'#7db8f5', violet:'#b9a0f5' })[state.style] || color;
      const wardrobeColors = { outfit:'#78b7dd', sneakers:'#80d0a7', boots:'#ba8c5a', jacket:'#b578d9', dress:'#e9a0ba' };
      if (state.wornItem && wardrobeColors[state.wornItem]) color = wardrobeColors[state.wornItem];
    }
    line(p.x-7,base-17,p.x-8,base-4,'#29364a',7);
    line(p.x+8,base-17,p.x+9,base-4,'#26344a',7);
    line(p.x-11,base-46,p.x-24,base-30,color,7);
    line(p.x+11,base-46,p.x+24,base-30,color,7);
    roundedRect(p.x-15,base-62,30,48,12,color);
    if (!friend && state.wornItem === 'jacket') {
      line(p.x,base-58,p.x,base-19,'#e9f0ff',2);
      line(p.x-12,base-50,p.x-3,base-39,'#6e548a',2);
    } else if (!friend && state.wornItem === 'dress') {
      polygon([{x:p.x-14,y:base-41},{x:p.x+14,y:base-41},{x:p.x+23,y:base-14},{x:p.x-23,y:base-14}], '#e9a0ba');
    }
    ellipse(p.x,base-70,17,19,friend?'#d3a780':'#e4b69a');
    ellipse(p.x-7,base-74,1.5,2,'#334658');ellipse(p.x+7,base-74,1.5,2,'#334658');
    ctx.beginPath();ctx.arc(p.x,base-78,18,Math.PI,2*Math.PI);ctx.strokeStyle=friend?'#28394b':'#4b3a45';ctx.lineWidth=8;ctx.stroke();
    if(!friend){textAt('YOU',p.x,base-96,12,'#e5fff8');}
  }
  function drawTerrain(){
    const market=state.scene==='market', friend=state.scene==='friend';
    const grad=ctx.createLinearGradient(0,0,WIDTH,HEIGHT);
    if(market){grad.addColorStop(0,'#7ea5ad');grad.addColorStop(.48,'#b1baa4');grad.addColorStop(1,'#607c6b');}
    else if(state.scene==='park'){grad.addColorStop(0,'#39776e');grad.addColorStop(.56,'#a5bd85');grad.addColorStop(1,'#477d6c');}
    else if(friend){grad.addColorStop(0,'#40566f');grad.addColorStop(.56,'#897b89');grad.addColorStop(1,'#45566b');}
    else{grad.addColorStop(0,'#3e6075');grad.addColorStop(.55,'#738898');grad.addColorStop(1,'#344b65');}
    ctx.fillStyle=grad;ctx.fillRect(0,0,WIDTH,HEIGHT);
    ellipse(740,58,240,115,'#fff2b01b');ellipse(85,132,160,170,'#8ddcc91c');
    for(let i=0;i<17;i++){const x=(i*79+43)%960,y=30+(i*31)%250;ellipse(x,y,1.5,1.5,'#f8f9df71');}
    if(!market && state.scene!=='park')drawWall();
    else if(market) { roundedRect(0,405,WIDTH,210,0,'#254f5160');textAt('TIDONG VIRTUAL MARKET',480,65,24,'#f9f6d5'); }
    else textAt('A PEACEFUL PLACE TO PAUSE',480,66,22,'#f2f5dd');
    drawFloor();
    const items=[];
    if(market){items.push({x:2,y:2,kind:'stall',label:'FASHION',color:'#ecb192'},{x:6,y:1,kind:'stall',label:'ESSENTIALS',color:'#f2d57f'},{x:7,y:5,kind:'car'},{x:3,y:6,kind:'plant'},{x:1,y:5,kind:'plant'});}
    else if(state.scene==='park'){items.push({x:2,y:2,kind:'plant'},{x:6,y:2,kind:'plant'},{x:2,y:5,kind:'plant'},{x:7,y:5,kind:'plant'},{x:5,y:3,kind:'table'});}
    else if(friend){items.push({x:3,y:3,kind:'sofa'},{x:6,y:2,kind:'table'},{x:2,y:5,kind:'plant'},{x:7,y:5,kind:'lamp'},{x:5,y:4,kind:'friend'});}
    else{
      items.push({x:2,y:2,kind:'sofa'},{x:6,y:2,kind:'table'},{x:2,y:5,kind:'plant'},{x:7,y:5,kind:'lamp'});
      const spots=[[4,2],[5,3],[6,4],[3,5],[7,2],[5,6],[1,3],[8,3]];
      state.placed.forEach((id,i)=>{if(i<spots.length)items.push({x:spots[i][0],y:spots[i][1],kind:id});});
    }
    items.push({x:state.avatar.x,y:state.avatar.y,kind:'player'});
    items.sort((a,b)=>(a.x+a.y)-(b.x+b.y));
    for(const o of items){if(o.kind==='player')drawPerson(o.x,o.y);else if(o.kind==='friend')drawPerson(o.x,o.y,'#edc6de',true);else if(o.kind==='stall')drawMarketStall(o);else drawFurniture(o);}
    if(state.timeOfDay==='sunset'){ctx.fillStyle='#e99c7240';ctx.fillRect(0,0,WIDTH,HEIGHT);}
    if(state.timeOfDay==='night'){ctx.fillStyle='#06132a84';ctx.fillRect(0,0,WIDTH,HEIGHT);ellipse(778,80,35,35,'#f9f3c9a5');}
    if(friend){const p=iso(5,4);roundedRect(p.x-84,p.y-160,168,31,10,'#29384bbb');textAt('Welcome to my home!',p.x,p.y-138,15,'#f3f5ff');}
    if(!market && !friend && state.scene==='home'){const p=iso(2,2);roundedRect(p.x-55,p.y-106,112,25,9,'#29384bbb');textAt('YOUR SPACE',p.x,p.y-88,12,'#d6eee3');}
  }
  function draw(ts){
    if(!lastTime) lastTime=ts;
    const dt=Math.min(.05,(ts-lastTime)/1000); lastTime=ts; animationTime+=dt;
    const speed=Math.min(1,dt*4);
    state.avatar.x+=(movement.targetX-state.avatar.x)*speed;
    state.avatar.y+=(movement.targetY-state.avatar.y)*speed;
    ctx.clearRect(0,0,WIDTH,HEIGHT);
    drawTerrain();
    requestAnimationFrame(draw);
  }
  window.addEventListener('pageshow',e=>{
    if(e.persisted){state=load();movement={targetX:state.avatar.x,targetY:state.avatar.y};updateUI();}
    else updateJourney();
  });
  updateUI();
  if(state.subscribed){$('welcome-modal').classList.add('hidden'); if(location.hash==='#market')changeScene('market');}
  requestAnimationFrame(draw);
})();
