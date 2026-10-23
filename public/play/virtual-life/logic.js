/* TIDONG Virtual Life V2: isolated demonstration economy, never a financial ledger. */
(function (root, factory) {
  const api = factory();
  if (typeof module === 'object' && module.exports) module.exports = api;
  else root.TidongLogic = api;
})(typeof globalThis !== 'undefined' ? globalThis : this, function () {
  'use strict';
  const START_COINS = 25000;
  const ACCESS_COST = 199;
  const ITEMS = Object.freeze([
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
    return { version: 2, coins: START_COINS, subscribed: false, inventory: [], placed: [], tasks: [], scene: 'home', style: 'mint', timeOfDay: 'day', wornItem: null, avatar: { x: 4, y: 5 }, ledger: [{ id: 'welcome', kind: 'welcome', amount: START_COINS, detail: 'Welcome T-Coins (demo promotional)', at: new Date().toISOString() }] };
  }
  function isValidState(s) {
    return !!s && s.version === 2 && Number.isSafeInteger(s.coins) && s.coins >= 0 && s.coins <= 100000000 && typeof s.subscribed === 'boolean' && Array.isArray(s.inventory) && Array.isArray(s.placed) && Array.isArray(s.tasks) && Array.isArray(s.ledger) && s.avatar && Number.isFinite(s.avatar.x) && Number.isFinite(s.avatar.y) && ['home', 'market', 'friend', 'park'].includes(s.scene) && ['mint', 'coral', 'sky', 'violet'].includes(s.style) && ['day', 'sunset', 'night'].includes(s.timeOfDay) && (s.wornItem === null || (s.inventory.includes(s.wornItem) && ITEMS.some(i => i.id === s.wornItem && i.category === 'Fashion'))) && s.placed.every(x => s.inventory.filter(y => y === x).length >= s.placed.filter(y => y === x).length) && s.inventory.every(x => ITEMS.some(i => i.id === x)) && s.placed.every(x => ITEMS.some(i => i.id === x && i.category === 'Home')) && s.tasks.every(x => TASKS.some(t => t.id === x));
  }
  function log(s, kind, amount, detail) {
    s.ledger.push({ id: String(Date.now()) + '-' + String(s.ledger.length), kind, amount, detail, at: new Date().toISOString() });
    if (s.ledger.length > 80) s.ledger = s.ledger.slice(-80);
  }
  function subscribe(s) {
    if (s.subscribed) return { ok: false, message: 'Game access is already active.' };
    if (s.coins < ACCESS_COST) return { ok: false, message: 'Insufficient demo T-Coins.' };
    s.coins -= ACCESS_COST;
    s.subscribed = true;
    log(s, 'subscription', -ACCESS_COST, 'Game access (demo)');
    return { ok: true, message: 'Welcome to your virtual world!' };
  }
  function buy(s, id) {
    const item = ITEMS.find(i => i.id === id);
    if (!s.subscribed) return { ok: false, message: 'Enter the game first.' };
    if (!item) return { ok: false, message: 'Unknown virtual item.' };
    if (s.coins < item.price) return { ok: false, message: 'Not enough demo T-Coins.' };
    s.coins -= item.price;
    s.inventory.push(item.id);
    log(s, 'purchase', -item.price, 'Virtual item: ' + item.name);
    return { ok: true, message: item.name + ' added to your virtual collection.', item };
  }
  function completeTask(s, id) {
    const task = TASKS.find(t => t.id === id);
    if (!task || !s.subscribed || s.tasks.includes(id)) return { ok: false, message: 'Not available.' };
    s.tasks.push(id);
    s.coins += task.reward;
    log(s, 'task', task.reward, 'Demo task: ' + task.title);
    return { ok: true, message: 'Task completed! +' + task.reward + ' T-Coins', task };
  }
  function place(s, id) {
    const item = ITEMS.find(i => i.id === id);
    if (!item || item.category !== 'Home') return { ok: false, message: 'Choose a home item.' };
    const owned = s.inventory.filter(x => x === id).length;
    const placed = s.placed.filter(x => x === id).length;
    if (owned <= placed) return { ok: false, message: 'No unplaced item available.' };
    s.placed.push(id);
    return { ok: true, message: item.name + ' placed in your virtual home.' };
  }
  function changeStyle(s, style) {
    if (!s.subscribed || !['mint', 'coral', 'sky', 'violet'].includes(style)) return { ok: false, message: 'Choose a valid avatar style.' };
    s.style = style;
    return { ok: true, message: 'Your avatar style is updated.' };
  }
  function setTimeOfDay(s, time) {
    if (!s.subscribed || !['day', 'sunset', 'night'].includes(time)) return { ok: false, message: 'Choose a valid time of day.' };
    s.timeOfDay = time;
    return { ok: true, message: 'The world lighting has changed.' };
  }
  function wear(s, id) {
    if (!s.subscribed || !s.inventory.includes(id) || !ITEMS.some(i => i.id === id && i.category === 'Fashion')) return { ok: false, message: 'Buy this virtual outfit first.' };
    s.wornItem = id;
    return { ok: true, message: 'Your character is wearing a new look!' };
  }
  function unplace(s, index) {
    if (!s.subscribed || !Number.isSafeInteger(index) || index < 0 || index >= s.placed.length) return { ok: false, message: 'Select a placed item.' };
    s.placed.splice(index, 1);
    return { ok: true, message: 'Item moved back to your collection.' };
  }
  function migrateV1(old) {
    if (!old || old.version !== 1 || !Number.isSafeInteger(old.coins) || old.coins < 0 || old.coins > 100000000 || !Array.isArray(old.inventory) || !Array.isArray(old.placed) || !Array.isArray(old.tasks) || !Array.isArray(old.ledger) || typeof old.subscribed !== 'boolean' || !old.avatar || !Number.isFinite(old.avatar.x) || !Number.isFinite(old.avatar.y) || !['home', 'market', 'friend'].includes(old.scene)) return null;
    const next = { ...old, version: 2, style: 'mint', timeOfDay: 'day', wornItem: null };
    return isValidState(next) ? next : null;
  }
  return { START_COINS, ACCESS_COST, ITEMS, TASKS, newState, isValidState, migrateV1, subscribe, buy, completeTask, place, unplace, wear, changeStyle, setTimeOfDay };
});
