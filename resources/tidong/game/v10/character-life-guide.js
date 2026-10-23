/* TIDONG Game CHAT02 V13: real controls walkthrough, no wallet/reward authority */
(function(){
'use strict';
const G=window.TidongLifeGuide,E=window.TidongLogic;
if(!G)return;
const gameKey='tidong.virtual-life.demo.v3',progressKey='tidong.character-life.chat02.v13.guide';
const isStudio=!!document.getElementById('world3d'),$=id=>document.getElementById(id);
function state(){try{const s=JSON.parse(localStorage.getItem(gameKey));return E&&E.isValidState(s)?s:null;}catch(_){return null;}}
let progress;try{progress=G.safeProgress(JSON.parse(sessionStorage.getItem(progressKey)));}catch(_){progress={done:[]};}
const launcher=document.createElement('button');
launcher.type='button';launcher.className='tidong-guide-launch';launcher.textContent='🎮 Game कैसे खेलें?';
launcher.setAttribute('aria-haspopup','dialog');launcher.setAttribute('aria-expanded','false');
const panel=document.createElement('section');panel.className='tidong-guide hidden';
panel.setAttribute('role','dialog');panel.setAttribute('aria-modal','false');panel.setAttribute('aria-labelledby','tidong-guide-title');
panel.innerHTML='<div class="tidong-guide-head"><div><small>CHARACTER & REAL LIFE · CHAT02 · V13</small><h2 id="tidong-guide-title">खेलना शुरू करें — Step by Step</h2></div><button class="tidong-guide-close" type="button" aria-label="Close guide">✕</button></div><p id="tidong-guide-overview"></p><div class="tidong-guide-goal"><span>पात्र सामान खरीदारी</span><strong id="tidong-guide-spent"></strong><div class="tidong-guide-track"><div id="tidong-guide-fill"></div></div><small id="tidong-guide-hint"></small></div><div id="tidong-guide-main"></div><p class="tidong-guide-footer">यह offline browser game का मार्गदर्शन है। इसमें कोई वास्तविक T-Coin भुगतान, reward या backend authorization नहीं है।</p>';
document.body.append(launcher,panel);
const main=$('tidong-guide-main');
function open(flag){panel.classList.toggle('hidden',!flag);launcher.setAttribute('aria-expanded',String(flag));if(flag){render();panel.querySelector('.tidong-guide-close').focus();}else launcher.focus();}
launcher.addEventListener('click',()=>open(panel.classList.contains('hidden')));
panel.querySelector('.tidong-guide-close').addEventListener('click',()=>open(false));
document.addEventListener('keydown',e=>{if(e.key==='Escape'&&!panel.classList.contains('hidden'))open(false);});
function el(tag,cls,txt){const x=document.createElement(tag);if(cls)x.className=cls;if(txt!==undefined)x.textContent=txt;return x;}
function btn(txt,cb){const x=el('button','tidong-guide-btn',txt);x.type='button';x.addEventListener('click',cb);return x;}
function shop(){open(false);if(!isStudio&&$('open-shop'))$('open-shop').click();else location.assign('index.html#market');}
function focus(id){const x=$(id);if(!x)return false;open(false);x.scrollIntoView({behavior:'smooth',block:'center'});if(typeof x.focus==='function')x.focus({preventScroll:true});return true;}
function addTaskLine(parent,title,description){parent.append(el('h3','',title),el('p','',description));}
function render(){
const d=G.summary(state(),E);$('tidong-guide-overview').textContent=d.message;
$('tidong-guide-spent').textContent=d.spent.toLocaleString('en-IN')+' / 25,000 TC';
$('tidong-guide-fill').style.width=Math.min(100,100*d.spent/G.TARGET)+'%';
$('tidong-guide-hint').textContent=d.phase==='life'?'पूरा access उपलब्ध — offline playtest':d.remaining.toLocaleString('en-IN')+' TC की पात्र खरीदारी बाकी है। Subscription के 199 TC इसमें नहीं जुड़ते।';
main.replaceChildren();
if(d.phase==='welcome'){
addTaskLine(main,'1. Game में प्रवेश','पहले existing TIDONG game शुरू करें। Keyboard या mobile के arrow controls से चलें।');
main.append(btn('Game खोलें →',()=>{if(isStudio)location.assign('index.html');else{open(false);const m=$('welcome-modal');if(m){m.classList.remove('hidden');$('enter-world')?.focus();}}}));return;
}
if(d.phase==='subscription'){
addTaskLine(main,'1. Demo annual access','25,000 demo TC starter balance मिलता है। सालाना 199 TC अलग कटते हैं। ये असली पैसे नहीं हैं।');
main.append(btn('Subscription शुरू करने की स्क्रीन →',()=>{if(isStudio)location.assign('index.html#starter');else{open(false);const m=$('welcome-modal');if(m){m.classList.remove('hidden');$('enter-world')?.focus();}}}));return;
}
if(d.phase==='shopping'){
addTaskLine(main,'2. Starter Shopping','अभी सीमित movement, neighbour lifestyle देखना और आवश्यक starter shopping उपलब्ध है। Full life, rewards, services लॉक हैं। 10 मिनट में return करने पर खरीद लक्ष्य घटता है। बाद में कबाड़ी बिक्री से पुरानी खरीद गिनती नहीं घटती।');
main.append(btn('🛍️ Market खोलें',shop));
if(!isStudio)main.append(btn('🚶 Starter area में चलें',()=>{open(false);const c=document.querySelector('canvas');if(c)c.focus({preventScroll:true});}));
return;
}
const stat=G.status(progress);addTaskLine(main,'3. रोजमर्रा की जिंदगी',stat.count+'/'+stat.total+' वास्तविक actions का अभ्यास पूरा। इस गाइड से कोई T-Coin reward नहीं मिलता।');
const list=el('ol','tidong-guide-steps');
for(const step of G.STEPS){const done=progress.done.includes(step.id),r=el('li',done?'done':'');r.append(el('b','',(done?'✓ ':'○ ')+step.name),el('span','',step.hint));list.append(r);}
main.append(list);
if(stat.complete){main.append(el('p','tidong-guide-success','✓ Daily routine पूरी हुई।'));main.append(btn('फिर से अभ्यास करें',()=>{progress={done:[]};save();render();}));}
else if(isStudio){
const key=stat.next.id;
if(key==='eat'||key==='wear')main.append(btn(key==='eat'?'🍽️ Food inventory देखें':'👕 Wardrobe देखें',()=>{if(!focus(key==='eat'?'life-eat':'life-wear'))shop();}));
else if(key==='walk')main.append(btn('🚶 3D movement controls',()=>focus('world3d')));
else main.append(btn('अगली activity देखें →',()=>{const b=document.querySelector('[data-life-action="'+key+'"]');if(b){open(false);b.scrollIntoView({behavior:'smooth',block:'center'});b.focus({preventScroll:true});}}));
}else main.append(btn('🏡 3D Character Life खोलें →',()=>location.assign('studio3d.html#life')));
main.append(btn('🛍️ और सामान खरीदें',shop));
}
function save(){try{sessionStorage.setItem(progressKey,JSON.stringify(progress));}catch(_){}}
function mark(key){if(!isStudio||!G.summary(state(),E).full)return;progress=G.mark(progress,key);save();if(!panel.classList.contains('hidden'))render();}
window.addEventListener('tidong:avatar-step',e=>{if(Number(e.detail?.distance)>0)mark('walk');});
window.addEventListener('tidong:life-action',e=>{if(typeof e.detail?.action==='string')mark(e.detail.action);});
window.addEventListener('storage',e=>{if(e.key===gameKey&&!panel.classList.contains('hidden'))render();});
if(new URLSearchParams(location.search).get('guide')==='open')open(true);
else {let seen=false;try{seen=!!sessionStorage.getItem(progressKey+'.seen');sessionStorage.setItem(progressKey+'.seen','1');}catch(_){}if(!seen)open(true);}
})();