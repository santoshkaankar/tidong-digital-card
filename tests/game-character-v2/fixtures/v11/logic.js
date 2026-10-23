/* TIDONG Virtual Life V11: browser-only rule playtest; NOT a financial ledger. */
(function (root, factory) {
  const api = factory();
  if (typeof module === 'object' && module.exports) module.exports = api;
  else root.TidongLogic = api;
})(typeof globalThis !== 'undefined' ? globalThis : this, function () {
  'use strict';
  const START_COINS = 25000;
  const ACCESS_COST = 199;
  const STARTER_TARGET = 25000;
  const RETURN_WINDOW_MS = 10 * 60 * 1000;
  const ITEMS = Object.freeze([
    { id: 'apple', name: 'Fresh Apples', category: 'Food', price: 45, icon: '🍎', desc: 'A nutritious snack for your character.' },
    { id: 'banana', name: 'Bananas', category: 'Food', price: 30, icon: '🍌', desc: 'A quick energy snack.' },
    { id: 'bread', name: 'Fresh Bread', category: 'Food', price: 50, icon: '🍞', desc: 'A simple everyday meal.' },
    { id: 'milk', name: 'Milk', category: 'Food', price: 60, icon: '🥛', desc: 'A drink for your avatar.' },
    { id: 'soap', name: 'Refreshing Soap', category: 'Essentials', price: 35, icon: '🧼', desc: 'Everyday essentials for your avatar.' },
    { id: 'shampoo', name: 'Fresh Shampoo', category: 'Essentials', price: 65, icon: '🧴', desc: 'Keep your virtual routine fresh.' },
    { id: 'outfit', name: 'Everyday Outfit', category: 'Fashion', price: 240, icon: '👕', desc: 'Give your character a new look.' },
    { id: 'sneakers', name: 'Street Sneakers', category: 'Fashion', price: 320, icon: '👟', desc: 'Step out in your favorite style.' },
    { id: 'plant', name: 'Indoor Greenery', category: 'Home', price: 125, icon: '🪴', desc: 'A little color for your virtual home.' },
    { id: 'lamp', name: 'Modern Table Lamp', category: 'Home', price: 330, icon: '💡', desc: 'Add a soft glow to your home.' },
    { id: 'sofa', name: 'Signature Sofa', category: 'Home', price: 1450, icon: '🛋️', desc: 'Comfort and style for your living room.' },
    { id: 'toothpaste', name: 'Mint Toothpaste', category: 'Essentials', price: 25, icon: '🪥', desc: 'A little detail for your virtual morning routine.' },
    { id: 'towel', name: 'Soft Bath Towel', category: 'Essentials', price: 45, icon: '🧻', desc: 'Everyday comfort for your character.' },
    { id: 'boots', name: 'Everyday Boots', category: 'Fashion', price: 430, icon: '🥾', desc: 'Comfortable steps for a new adventure.' },
    { id: 'jacket', name: 'Weekend Jacket', category: 'Fashion', price: 560, icon: '🧥', desc: 'Wear a fresh look around town.' },
    { id: 'dress', name: 'Celebration Outfit', category: 'Fashion', price: 720, icon: '👗', desc: 'Bring a little celebration to your avatar.' },
    { id: 'rug', name: 'Cozy Floor Rug', category: 'Home', price: 280, icon: '🪄', desc: 'A warm accent for your living space.' },
    { id: 'books', name: 'Reading Corner', category: 'Home', price: 390, icon: '📚', desc: 'Make room for your imagination.' },
    { id: 'painting', name: 'Wall Art', category: 'Home', price: 440, icon: '🖼️', desc: 'Show your personality at home.' },
    { id: 'watch', name: 'Signature Watch', category: 'Luxury', price: 2100, icon: '⌚', desc: 'A virtual accessory, no real brand affiliation.' },
    { id: 'car', name: 'Luxury Sports Car', category: 'Luxury', price: 8900, icon: '🚗', desc: 'A virtual showpiece for your lifestyle.' },
    { id: 'villa', name: 'Dream Villa Theme', category: 'Luxury', price: 6500, icon: '🏠', desc: 'A decorative showcase theme, not real property.' }
  ]);
  const TASKS = Object.freeze([
    { id: 'market', title: 'Explore the Market', desc: 'Visit the virtual marketplace.', reward: 40 },
    { id: 'firstBuy', title: 'Your First Find', desc: 'Purchase any virtual item.', reward: 25 },
    { id: 'decorate', title: 'Make It Yours', desc: 'Place a purchased home item.', reward: 35 },
    { id: 'visit', title: 'Say Hello', desc: 'Visit a friend’s virtual home.', reward: 50 },
    { id: 'routine', title: 'A Fresh Start', desc: 'Complete your avatar’s daily routine.', reward: 20 },
    { id: 'style', title: 'Choose Your Style', desc: 'Personalize your avatar’s favorite color.', reward: 15 },
    { id: 'park', title: 'Take a Little Walk', desc: 'Explore the quiet park.', reward: 30 },
    { id: 'evening', title: 'Golden Hour', desc: 'Try a different time of day.', reward: 10 },
    { id: 'wardrobe', title: 'Ready to Go', desc: 'Try on something from your virtual wardrobe.', reward: 20 },
    { id: 'rearrange', title: 'Refresh Your Space', desc: 'Rearrange an owned home item.', reward: 15 }
  ]);
  function newState() {
    return { version: 3, coins: START_COINS, subscribed: false, subscriptionEndsAt: null,
      inventory: [], lots: [], nextSerial: 1, starterSpent: 0, hunger: 100, lastHungerAt: new Date().toISOString(),
      placed: [], tasks: [], scene: 'home', style: 'mint', timeOfDay: 'day', wornItem: null,
      avatar: { x: 4, y: 5 }, ledger: [{ id: 'welcome', kind: 'welcome', amount: START_COINS,
        detail: 'Welcome T-Coins (offline demonstration)', at: new Date().toISOString() }] };
  }
  function itemById(id) { return ITEMS.find(i => i.id === id); }
  function isValidState(s) {
    if (!s || s.version !== 3 || !Number.isSafeInteger(s.coins) || s.coins < 0 || s.coins > 100000000 ||
        typeof s.subscribed !== 'boolean' || (s.subscriptionEndsAt !== null &&
        (!Number.isFinite(Date.parse(s.subscriptionEndsAt)))) || !Array.isArray(s.inventory) ||
        !Array.isArray(s.lots) || s.lots.length > 5000 || !Number.isSafeInteger(s.nextSerial) ||
        s.nextSerial < 1 || !Number.isSafeInteger(s.starterSpent) || s.starterSpent < 0 ||
        !Number.isSafeInteger(s.hunger) || s.hunger < 0 || s.hunger > 100 ||
        !Number.isFinite(Date.parse(s.lastHungerAt)) ||
        !Array.isArray(s.placed) || !Array.isArray(s.tasks) || !Array.isArray(s.ledger) ||
        !s.avatar || !Number.isFinite(s.avatar.x) || !Number.isFinite(s.avatar.y) ||
        !['home','market','friend','park'].includes(s.scene) ||
        !['mint','coral','sky','violet'].includes(s.style) ||
        !['day','sunset','night'].includes(s.timeOfDay) ||
        !s.inventory.every(id => !!itemById(id)) ||
        !s.placed.every(id => itemById(id)?.category === 'Home') ||
        !s.tasks.every(id => TASKS.some(t => t.id === id)) ||
        (s.wornItem !== null && (!s.inventory.includes(s.wornItem) || itemById(s.wornItem)?.category !== 'Fashion'))) return false;
    const ids = new Set(); let sum = 0; const owned = [];
    for (const lot of s.lots) {
      if (!lot || !Number.isSafeInteger(lot.serial) || lot.serial < 1 || lot.serial >= s.nextSerial ||
          ids.has(lot.serial) || !itemById(lot.itemId) || lot.price !== itemById(lot.itemId).price ||
          (lot.purchasedAt !== null && !Number.isFinite(Date.parse(lot.purchasedAt))) ||
          !['owned','returned','scrapped','consumed'].includes(lot.status) ||
          !Number.isSafeInteger(lot.uses) || lot.uses < 0) return false;
      ids.add(lot.serial);
      if (lot.status !== 'returned') sum += lot.price;
      if (lot.status === 'owned') owned.push(lot.itemId);
    }
    if (sum !== s.starterSpent || owned.length !== s.inventory.length ||
        s.inventory.some(id => { const k = owned.indexOf(id); if (k < 0) return true; owned.splice(k,1); return false; })) return false;
    return s.placed.every(id => s.inventory.filter(x => x === id).length >= s.placed.filter(x => x === id).length);
  }
  function log(s, kind, amount, detail) {
    s.ledger.push({id: String(Date.now())+'-'+String(s.ledger.length), kind, amount, detail, at: new Date().toISOString()});
    if (s.ledger.length > 80) s.ledger = s.ledger.slice(-80);
  }
  function isSubscribed(s, now = Date.now()) {
    return !!(s.subscribed && s.subscriptionEndsAt && Date.parse(s.subscriptionEndsAt) > now);
  }
  function canPlay(s, now = Date.now()) { return isSubscribed(s, now) && s.starterSpent >= STARTER_TARGET; }
  function subscribe(s, now = Date.now()) {
    if (isSubscribed(s,now)) return {ok:false,message:'Your annual subscription is already active.'};
    if (s.coins < ACCESS_COST) return {ok:false,message:'Insufficient demo T-Coins for the annual subscription.'};
    const expiry = new Date(now); expiry.setUTCFullYear(expiry.getUTCFullYear() + 1);
    s.coins -= ACCESS_COST; s.subscribed = true; s.subscriptionEndsAt = expiry.toISOString();
    log(s,'subscription',-ACCESS_COST,'One-year game subscription (offline demonstration)');
    return {ok:true,message:'Annual game access started. Complete 25,000 T-Coin item purchases to unlock activities.'};
  }
  function buy(s, id, now = Date.now()) {
    const item = itemById(id);
    if (!isSubscribed(s,now)) return {ok:false,message:'Activate an annual game subscription first.'};
    if (!item) return {ok:false,message:'Unknown virtual item.'};
    if (s.coins < item.price) return {ok:false,message:'Not enough demonstration T-Coins.'};
    if (s.lots.length >= 5000) return {ok:false,message:'Demo inventory capacity reached.'};
    const lot = {serial:s.nextSerial++,itemId:id,price:item.price,purchasedAt:new Date(now).toISOString(),status:'owned',uses:0};
    s.coins -= item.price; s.inventory.push(id); s.lots.push(lot); s.starterSpent += item.price;
    log(s,'purchase',-item.price,'Virtual item #'+lot.serial+': '+item.name);
    return {ok:true,message:item.name+' added to your inventory.',item,lot};
  }
  function removeOwned(s, lot) {
    const i = s.inventory.indexOf(lot.itemId); if (i < 0) return false;
    s.inventory.splice(i,1);
    const pos = s.placed.indexOf(lot.itemId); if (pos >= 0) s.placed.splice(pos,1);
    if (s.wornItem === lot.itemId && !s.inventory.includes(lot.itemId)) s.wornItem = null;
    return true;
  }
  function returnItem(s, serial, now = Date.now()) {
    const lot = s.lots.find(l => l.serial === serial);
    if (!lot || lot.status !== 'owned') return {ok:false,message:'This item cannot be returned.'};
    if (lot.purchasedAt === null || now - Date.parse(lot.purchasedAt) >= RETURN_WINDOW_MS ||
        now < Date.parse(lot.purchasedAt)) return {ok:false,message:'The 10-minute full-return window has ended. Use the virtual scrap dealer.'};
    if (!removeOwned(s,lot)) return {ok:false,message:'This item is not in your inventory.'};
    lot.status='returned'; s.coins+=lot.price; s.starterSpent-=lot.price;
    log(s,'return',lot.price,'Full refund of item #'+lot.serial+'; removed from starter shopping count');
    return {ok:true,message:'100% of '+lot.price+' T-Coins refunded. The purchase no longer counts toward 25,000.'};
  }
  function condition(lot, now = Date.now()) {
    if (!lot || lot.purchasedAt === null) return 60; // Old offline V10 inventory has no purchase timestamp.
    const ageDays = Math.floor(Math.max(0,now-Date.parse(lot.purchasedAt))/(24*60*60*1000));
    return Math.max(0,100-ageDays*2-lot.uses*5);
  }
  function scrapValue(lot, now = Date.now()) {
    // Illustrative playtest formula only; final scrap rates require owner-defined admin pricing.
    return Math.max(0,Math.min(lot.price-1,Math.floor(lot.price*(0.15+condition(lot,now)*0.002))));
  }
  function scrapItem(s, serial, now = Date.now()) {
    const lot=s.lots.find(l=>l.serial===serial);
    if (!lot || lot.status!=='owned') return {ok:false,message:'Item is not available for scrap sale.'};
    if (lot.purchasedAt!==null && (now-Date.parse(lot.purchasedAt)<RETURN_WINDOW_MS || now<Date.parse(lot.purchasedAt)))
      return {ok:false,message:'The item is still within the full-return window. It cannot be sold as used.'};
    const amount=scrapValue(lot,now);
    if (!removeOwned(s,lot)) return {ok:false,message:'This item is not in your inventory.'};
    lot.status='scrapped';s.coins+=amount;
    log(s,'scrap',amount,'Virtual scrap dealer bought item #'+lot.serial+'; starter count preserved');
    return {ok:true,amount,message:'Virtual scrap dealer paid '+amount+' T-Coins. Your earlier purchase remains counted.'};
  }
  function advanceHunger(s, now=Date.now()) {
    if (!canPlay(s,now)) {s.lastHungerAt=new Date(now).toISOString();return false;}
    const elapsed=Math.max(0,Math.floor((now-Date.parse(s.lastHungerAt))/(5*60*1000)));
    if(!elapsed)return false;
    s.hunger=Math.max(0,s.hunger-Math.min(100,elapsed));
    s.lastHungerAt=new Date(now).toISOString();
    return true;
  }
  function consume(s,serial,now=Date.now()) {
    const lot=s.lots.find(l=>l.serial===serial);
    if (!canPlay(s,now)) return {ok:false,message:'Complete the 25,000 T-Coin starter purchases first.'};
    if (!lot || lot.status!=='owned' || itemById(lot.itemId)?.category!=='Food') return {ok:false,message:'Select food from your inventory.'};
    advanceHunger(s,now);removeOwned(s,lot); lot.status='consumed'; s.hunger=Math.min(100,s.hunger+25);
    log(s,'eat',0,'Ate virtual food #'+lot.serial);
    return {ok:true,message:'Your character enjoyed some food. Hunger satisfaction +25.'};
  }
  function completeTask(s,id,now=Date.now()) {
    const task=TASKS.find(t=>t.id===id);
    if (!task || !canPlay(s,now) || s.tasks.includes(id))
      return {ok:false,message:'Rewards stay locked until 25,000 T-Coins of valid item purchases.'};
    s.tasks.push(id);s.coins+=task.reward;
    log(s,'task',task.reward,'Demo task: '+task.title);
    return {ok:true,message:'Task completed! +'+task.reward+' T-Coins',task};
  }
  function place(s,id) {
    if (!canPlay(s)) return {ok:false,message:'Starter purchases are not complete.'};
    const item=itemById(id);if (!item || item.category!=='Home') return {ok:false,message:'Choose a home item.'};
    if (s.inventory.filter(x=>x===id).length<=s.placed.filter(x=>x===id).length) return {ok:false,message:'No unplaced item available.'};
    s.placed.push(id);const lot=s.lots.find(l=>l.status==='owned'&&l.itemId===id);if(lot)lot.uses++;
    return {ok:true,message:item.name+' placed in your virtual home.'};
  }
  function changeStyle(s,style) {
    if (!canPlay(s)||!['mint','coral','sky','violet'].includes(style)) return {ok:false,message:'First complete starter purchases.'};
    s.style=style;return {ok:true,message:'Your avatar style is updated.'};
  }
  function setTimeOfDay(s,time) {
    if (!canPlay(s)||!['day','sunset','night'].includes(time)) return {ok:false,message:'First complete starter purchases.'};
    s.timeOfDay=time;return {ok:true,message:'World lighting changed.'};
  }
  function wear(s,id) {
    if (!canPlay(s)||!s.inventory.includes(id)||itemById(id)?.category!=='Fashion') return {ok:false,message:'Outfit customization is locked until starter shopping is complete.'};
    s.wornItem=id;const lot=s.lots.find(l=>l.status==='owned'&&l.itemId===id);if(lot)lot.uses++;
    return {ok:true,message:'Your character is wearing a new look!'};
  }
  function unplace(s,index) {
    if (!canPlay(s)||!Number.isSafeInteger(index)||index<0||index>=s.placed.length) return {ok:false,message:'Not available until starter shopping is complete.'};
    s.placed.splice(index,1);return {ok:true,message:'Item moved back to your collection.'};
  }
  function migrateV2(old) {
    // Older browser-only saves cannot establish real purchase times. Never grant 10-minute returns on legacy items.
    if (!old || old.version!==2 || !Number.isSafeInteger(old.coins) || old.coins<0 ||
        !Array.isArray(old.inventory) || !Array.isArray(old.tasks) || !Array.isArray(old.ledger)) return null;
    const lots=old.inventory.map((id,i)=>({serial:i+1,itemId:id,price:itemById(id)?.price,purchasedAt:null,status:'owned',uses:0}));
    if (lots.some(l=>!Number.isSafeInteger(l.price))) return null;
    const next={...old,version:3,lots,nextSerial:lots.length+1,
      starterSpent:lots.reduce((n,l)=>n+l.price,0),hunger:100,lastHungerAt:new Date().toISOString(),
      subscriptionEndsAt:old.subscribed?new Date(new Date().setUTCFullYear(new Date().getUTCFullYear()+1)).toISOString():null};
    return isValidState(next)?next:null;
  }
  function migrateV1(old) {
    if (!old || old.version!==1) return null;
    const updated={...old,version:2,style:'mint',timeOfDay:'day',wornItem:null};
    return migrateV2(updated);
  }
  return {START_COINS,ACCESS_COST,STARTER_TARGET,RETURN_WINDOW_MS,ITEMS,TASKS,
    newState,isValidState,migrateV1,migrateV2,isSubscribed,canPlay,subscribe,buy,returnItem,scrapItem,
    scrapValue,condition,advanceHunger,consume,completeTask,place,unplace,wear,changeStyle,setTimeOfDay};
});