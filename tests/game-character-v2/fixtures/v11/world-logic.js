/* V5 offline exploration helpers. Garden progress is cosmetic, not financial state. */
(function (root, factory) {
  const api = factory();
  if (typeof module === 'object' && module.exports) module.exports = api;
  else root.TidongWorld = api;
})(typeof globalThis !== 'undefined' ? globalThis : this, function () {
  'use strict';
  const ACTIVITIES = Object.freeze({
    flowers: Object.freeze({title:'Water the flowers', x:-3.1, z:-2.25, message:'You cared for the garden flowers.'}),
    pond: Object.freeze({title:'Visit the pond', x:1.65, z:-2.45, message:'You enjoyed a quiet moment by the pond.'}),
    bench: Object.freeze({title:'Relax on the bench', x:2.7, z:2.35, message:'You took a peaceful break in the garden.'})
  });
  const IDS = Object.keys(ACTIVITIES);
  function safeProgress(value) {
    if (!value || typeof value !== 'object' || !Array.isArray(value.completed)) return {completed: []};
    return {completed: IDS.filter(id => value.completed.includes(id))};
  }
  function markVisited(progress, id) {
    if (!Object.hasOwn(ACTIVITIES,id)) return {ok:false, message:'Unknown garden activity.'};
    const clean=safeProgress(progress);
    if (!clean.completed.includes(id)) clean.completed.push(id);
    return {ok:true, progress:safeProgress(clean), message:ACTIVITIES[id].message};
  }
  function moveTowards(current, target, distance) {
    if (!current || !target || ![current.x,current.z,target.x,target.z,distance].every(Number.isFinite) || distance<0) return null;
    const dx=target.x-current.x,dz=target.z-current.z,len=Math.hypot(dx,dz);
    if (len<.001 || distance>=len) return {x:target.x,z:target.z,arrived:true};
    return {x:current.x+dx/len*distance,z:current.z+dz/len*distance,arrived:false};
  }
  return {ACTIVITIES,IDS,safeProgress,markVisited,moveTowards};
});