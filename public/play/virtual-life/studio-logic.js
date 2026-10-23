/* Offline V4 3D furniture arrangement. Demo state only; not a wallet or a purchase authority. */
(function (root, factory) {
  const api = factory(typeof module === 'object' && module.exports ? require('./logic.js') : root.TidongLogic);
  if (typeof module === 'object' && module.exports) module.exports = api;
  else root.TidongStudio = api;
})(typeof globalThis !== 'undefined' ? globalThis : this, function (L) {
  'use strict';
  const HOME_ITEMS = L.ITEMS.filter(item => item.category === 'Home');
  const isReady = state => L.isValidState(state) && state.subscribed;
  const ownedCount = (state, id) => state.inventory.filter(x => x === id).length;
  const placedCount = (state, id) => state.placed.filter(x => x === id).length;
  function place(state, id) {
    if (!isReady(state)) return {ok: false, message: 'Enter the original demo to unlock your collection.'};
    if (!HOME_ITEMS.some(x => x.id === id)) return {ok: false, message: 'Choose a home item.'};
    if (state.placed.length >= 8) return {ok: false, message: 'Your 3D room has eight display spots.'};
    return L.place(state, id);
  }
  function remove(state, index) {
    if (!isReady(state)) return {ok: false, message: 'Enter the original demo first.'};
    return L.unplace(state, index);
  }
  function shift(state, index, direction) {
    if (!isReady(state) || !Number.isInteger(index) || ![-1,1].includes(direction))
      return {ok: false, message: 'Choose a displayed item.'};
    const other = index + direction;
    if (index < 0 || other < 0 || other >= state.placed.length)
      return {ok: false, message: 'Already at the edge of the room.'};
    [state.placed[index], state.placed[other]] = [state.placed[other], state.placed[index]];
    return {ok: true, message: 'Your home layout has been rearranged.'};
  }
  function inventory(state) {
    if (!isReady(state)) return [];
    return HOME_ITEMS.map(item => ({ id: item.id, name: item.name, icon: item.icon,
      owned: ownedCount(state, item.id), placed: placedCount(state, item.id) }))
      .filter(item => item.owned > 0);
  }
  return { HOME_ITEMS, isReady, place, remove, shift, inventory };
});
