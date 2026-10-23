'use strict';
const test=require('node:test');
const assert=require('node:assert/strict');
const L=require('./fixtures/v11/logic.js');
const World=require('./fixtures/v11/world-logic.js');
const Launch=require('./fixtures/v11/launch-tools.js');
const minute=60000;
const now=Date.parse('2026-10-22T10:00:00.000Z');
function setup(){const s=L.newState();assert.equal(L.subscribe(s,now).ok,true);return s;}

test('25K grant minus annual 199 leaves 24,801 and only one charge while active',()=>{
 const s=L.newState();assert.equal(s.coins,25000);assert.equal(s.starterSpent,0);
 assert.equal(L.subscribe(s,now).ok,true);assert.equal(s.coins,24801);
 assert.equal(L.subscribe(s,now).ok,false);assert.equal(s.coins,24801);
 assert.ok(Date.parse(s.subscriptionEndsAt)>now);
 assert.equal(L.isValidState(s),true);
});

test('starter activities and rewards locked below target',()=>{
 const s=setup();assert.equal(L.buy(s,'car',now).ok,true);
 assert.equal(L.completeTask(s,'firstBuy',now).ok,false);
 assert.equal(L.changeStyle(s,'coral').ok,false);
 assert.equal(L.consume(s,1,now).ok,false);
 assert.equal(s.coins,24801-8900);
 assert.equal(s.tasks.length,0);
});

test('10-minute return is full refund and reverses target counting',()=>{
 const s=setup();L.buy(s,'sofa',now);
 assert.equal(L.returnItem(s,1,now+10*minute-1).ok,true);
 assert.equal(s.starterSpent,0);assert.equal(s.coins,24801);
 assert.equal(s.lots[0].status,'returned');assert.equal(s.inventory.length,0);
 assert.equal(L.returnItem(s,1,now+10*minute).ok,false);
 assert.equal(L.isValidState(s),true);
});

test('return at exactly 10 min rejected, kabadi sale counts no subtraction',()=>{
 const s=setup();L.buy(s,'sofa',now);
 assert.equal(L.returnItem(s,1,now+10*minute).ok,false);
 assert.equal(L.scrapItem(s,1,now+10*minute).ok,true);
 assert.equal(s.starterSpent,1450);
 assert.ok(s.coins>24801-1450 && s.coins<24801);
 assert.equal(L.scrapItem(s,1,now+10*minute).ok,false);
 assert.equal(L.isValidState(s),true);
});

test('selling used goods can re-fund further purchases to hit 25,000 without external top-up',()=>{
 const s=setup();
 ['car','car','villa','lamp','plant','towel'].forEach(id=>assert.equal(L.buy(s,id,now).ok,true));
 assert.equal(s.starterSpent,24800);assert.equal(s.coins,1);assert.equal(L.canPlay(s,now),false);
 assert.equal(L.scrapItem(s,1,now+10*minute).ok,true);
 assert.equal(L.buy(s,'outfit',now+11*minute).ok,true);
 assert.equal(s.starterSpent,25040);
 assert.equal(L.canPlay(s,now+11*minute),true);
 assert.equal(L.completeTask(s,'firstBuy',now+11*minute).ok,true);
 assert.equal(L.isValidState(s),true);
});

test('selling scrap cannot create repeated credits or enable buying via refunded item',()=>{
 const s=setup();L.buy(s,'watch',now);
 const before=s.coins;
 assert.equal(L.scrapItem(s,1,now+9*minute).ok,false);
 assert.equal(s.coins,before);
 assert.equal(L.returnItem(s,1,now+9*minute).ok,true);
 assert.equal(L.scrapItem(s,1,now+11*minute).ok,false);
 assert.equal(s.starterSpent,0);
});

test('duplicate items are individual units, return one leaves other owned',()=>{
 const s=setup();L.buy(s,'soap',now);L.buy(s,'soap',now);
 assert.equal(L.returnItem(s,1,now+minute).ok,true);
 assert.deepEqual(s.inventory,['soap']);assert.equal(s.starterSpent,35);
 assert.equal(s.lots[1].status,'owned');assert.equal(L.isValidState(s),true);
});

test('after annual expiry a new subscription is charged and play denied until renewed',()=>{
 const s=setup();s.starterSpent=25000; // not a valid persisted state, check entitlement only
 const expiry=Date.parse(s.subscriptionEndsAt);
 assert.equal(L.canPlay(s,expiry),false);
 assert.equal(L.subscribe(s,expiry).ok,true);
 assert.equal(s.coins,24801-199);
 assert.equal(L.isSubscribed(s,expiry+1),true);
});

test('condition decays with age and use, and no upgrades are available',()=>{
 const s=setup();L.buy(s,'car',now);
 const lot=s.lots[0];assert.equal(L.condition(lot,now),100);
 lot.uses=4;assert.equal(L.condition(lot,now+3*24*60*minute),74);
 assert.ok(L.scrapValue(lot,now+3*24*60*minute)<lot.price);
 assert.equal(typeof L.upgrade,'undefined');
});

test('offline backup includes receipt, subscription, and starter counting data',()=>{
 const s=setup();L.buy(s,'car',now);L.buy(s,'soap',now);
 const backup=Launch.exportSave(s,{completed:[]});
 const r=Launch.importSave(backup);
 assert.equal(r.game.starterSpent,8935);assert.equal(r.game.lots.length,2);
 assert.equal(r.game.subscriptionEndsAt,s.subscriptionEndsAt);
 assert.equal(L.isValidState(r.game),true);
});

test('legacy V10 browser data migrates with old items already used (no invented 10-min return)',()=>{
 const v2={...L.newState(),version:2};delete v2.lots;delete v2.nextSerial;
 delete v2.starterSpent;delete v2.hunger;delete v2.lastHungerAt;delete v2.subscriptionEndsAt;
 v2.inventory=['sofa'];v2.placed=[];v2.subscribed=true;
 const s=L.migrateV2(v2);
 assert.ok(s);assert.equal(s.starterSpent,1450);assert.equal(s.lots[0].purchasedAt,null);
 assert.equal(L.returnItem(s,1,now).ok,false);assert.equal(L.isValidState(s),true);
});

test('consuming food is available only after starter unlock and preserves original purchase count',()=>{
 const s=setup();L.buy(s,'apple',now);s.hunger=50;
 assert.equal(L.consume(s,1,now).ok,false);
 // Produce legitimate purchase history using repeated purchases then scrap after 10m
 for(let i=0;i<2;i++)L.buy(s,'car',now);
 L.buy(s,'villa',now);L.buy(s,'lamp',now);L.buy(s,'plant',now);
 // total 45+17800+6500+330+125=24800; wallet should be 1.
 assert.equal(s.starterSpent,24800);
 L.scrapItem(s,2,now+10*minute);
 L.buy(s,'outfit',now+11*minute);
 assert.equal(L.canPlay(s,now+11*minute),true);
 assert.equal(L.consume(s,1,now+11*minute).ok,true);
 assert.equal(s.hunger,75);assert.equal(s.lots[0].status,'consumed');
 assert.equal(s.starterSpent,25040);
 assert.equal(L.isValidState(s),true);
});