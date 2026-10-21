/* Local-only V6 first-play guidance and portable demo saves. No real-money authority. */
(function (root, factory) {
  const api = factory(typeof module === 'object' && module.exports ? require('./logic.js') : root.TidongLogic,
    typeof module === 'object' && module.exports ? require('./world-logic.js') : root.TidongWorld);
  if (typeof module === 'object' && module.exports) module.exports = api;
  else root.TidongLaunch = api;
})(typeof globalThis !== 'undefined' ? globalThis : this, function (L, W) {
  'use strict';
  const KIND = 'tidong-virtual-life-offline-demo';
  const MAX_JSON = 96000;
  const MILESTONES = Object.freeze([
    {id:'access',title:'Enter your virtual world'},
    {id:'market',title:'Explore the virtual market'},
    {id:'firstItem',title:'Discover your first virtual item'},
    {id:'decorate',title:'Decorate your home'},
    {id:'garden',title:'Explore the 3D garden'}
  ]);
  function journey(state, progress) {
    const s = L.isValidState(state) ? state : L.newState();
    const g = W.safeProgress(progress);
    const completed = [!!s.subscribed,s.tasks.includes('market'),s.inventory.length>0,s.placed.length>0,g.completed.length>0];
    const next = completed.findIndex(x => !x);
    return {completed: completed.filter(Boolean).length, total:MILESTONES.length,
      milestones:MILESTONES.map((m,i)=>({...m,done:completed[i]})),
      next:next<0?'done':MILESTONES[next].id};
  }
  function validateSave(s) {
    if (!L.isValidState(s)) throw new Error('This is not a valid TIDONG demo save.');
    if (s.inventory.length>300 || s.placed.length>8 || s.tasks.length>L.TASKS.length || s.ledger.length>80 ||
        !s.tasks.every((t,i)=>s.tasks.indexOf(t)===i) ||
        ![s.avatar.x,s.avatar.y].every(v=>v>=0 && v<=10))
      throw new Error('Demo save has invalid progress or exceeds its limits.');
    if (!s.ledger.every(e=>e && typeof e.id==='string' && e.id.length<100 && typeof e.kind==='string' && e.kind.length<100 &&
      typeof e.detail==='string' && e.detail.length<300 && typeof e.at==='string' && e.at.length<60 &&
      Number.isSafeInteger(e.amount) && Math.abs(e.amount)<=1e8))
      throw new Error('Demo save has invalid history.');
    return JSON.parse(JSON.stringify({version:2,coins:s.coins,subscribed:s.subscribed,inventory:s.inventory,
      placed:s.placed,tasks:s.tasks,scene:s.scene,style:s.style,timeOfDay:s.timeOfDay,
      wornItem:s.wornItem,avatar:{x:s.avatar.x,y:s.avatar.y},ledger:s.ledger.map(e=>({id:e.id,kind:e.kind,amount:e.amount,detail:e.detail,at:e.at}))}));
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
