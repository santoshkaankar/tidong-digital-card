/* Local-only V11 journey and portable DEMO backup. Never a server-side wallet. */
(function (root, factory) {
  const api = factory(typeof module === 'object' && module.exports ? require('./logic.js') : root.TidongLogic,
    typeof module === 'object' && module.exports ? require('./world-logic.js') : root.TidongWorld);
  if (typeof module === 'object' && module.exports) module.exports = api;
  else root.TidongLaunch = api;
})(typeof globalThis !== 'undefined' ? globalThis : this, function (L, W) {
  'use strict';
  const KIND = 'tidong-virtual-life-offline-demo';
  const MAX_JSON = 2 * 1024 * 1024;
  const MILESTONES = Object.freeze([
    {id:'access',title:'Enter your virtual world'},
    {id:'market',title:'Explore the virtual market'},
    {id:'firstItem',title:'Discover your first virtual item'},
    {id:'starter',title:'Complete 25,000 T-Coin starter shopping'},
    {id:'decorate',title:'Decorate your home'},
    {id:'garden',title:'Explore the 3D garden'}
  ]);
  function journey(state, progress) {
    const s = L.isValidState(state) ? state : L.newState();
    const g = W.safeProgress(progress);
    const completed = [L.isSubscribed(s),s.scene==='market'||s.tasks.includes('market'),s.inventory.length>0,
      s.starterSpent>=L.STARTER_TARGET,s.placed.length>0,g.completed.length>0];
    const next = completed.findIndex(x => !x);
    return {completed: completed.filter(Boolean).length, total:MILESTONES.length,
      milestones:MILESTONES.map((m,i)=>({...m,done:completed[i]})),
      next:next<0?'done':MILESTONES[next].id};
  }
  function validateSave(raw) {
    const s=raw && raw.version===2 ? L.migrateV2(raw) : raw;
    if (!L.isValidState(s)) throw new Error('This is not a valid TIDONG V11 offline demo save.');
    if (s.lots.length>5000 || s.placed.length>100 || s.tasks.length>L.TASKS.length ||
      s.ledger.length>80 || !s.tasks.every((t,i)=>s.tasks.indexOf(t)===i) ||
      ![s.avatar.x,s.avatar.y].every(v=>v>=0 && v<=10) ||
      !s.ledger.every(e=>e && typeof e.id==='string' && e.id.length<100 &&
      typeof e.kind==='string' && e.kind.length<100 && typeof e.detail==='string' &&
      e.detail.length<300 && typeof e.at==='string' && e.at.length<60 &&
      Number.isSafeInteger(e.amount) && Math.abs(e.amount)<=1e8))
      throw new Error('Offline demo save exceeds safety limits.');
    // Offline backups are editable, never trusted for real T-Coins or paid subscription.
    return JSON.parse(JSON.stringify(s));
  }
  function exportSave(state, garden) {
    const s = validateSave(state);
    return JSON.stringify({kind:KIND,format:1,exportedAt:new Date().toISOString(),
      notice:'Fictional offline demo progress only. No real currency or payment rights.',
      game:s,garden:W.safeProgress(garden)},null,2);
  }
  function importSave(text) {
    if (typeof text!=='string' || text.length>MAX_JSON || text.length<20)
      throw new Error('Choose a small, valid TIDONG demo JSON save (96 KB maximum).');
    let p;
    try {p=JSON.parse(text);} catch (_) {throw new Error('The selected file is not valid JSON.');}
    if (!p || p.kind!==KIND || p.format!==1 || !p.game)
      throw new Error('This file is not a TIDONG V6 demo backup.');
    return {game:validateSave(p.game),garden:W.safeProgress(p.garden)};
  }
  return {KIND,MAX_JSON,MILESTONES,journey,exportSave,importSave};
});