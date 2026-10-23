(function(){'use strict';
const L=window.TidongPetCare,key='tidong.virtual-life.pet-care.demo.v1';
let state;try{state=L.safe(JSON.parse(localStorage.getItem(key)));}catch(_){state=L.fresh();}
const ids=['food','joy','energy'],pet=document.getElementById('pet'),message=document.getElementById('message');
function draw(){ids.forEach(id=>document.getElementById(id).value=state[id]);pet.classList.toggle('happy',state.joy>=65&&state.food>=25);pet.textContent=state.energy<18?'😴':'🐶';try{localStorage.setItem(key,JSON.stringify(state));}catch(_){}}
document.querySelectorAll('[data-action]').forEach(button=>button.addEventListener('click',()=>{const result=L.act(state,button.dataset.action);state=result.state;message.textContent=result.message;draw();}));
document.getElementById('restart').addEventListener('click',()=>{state=L.fresh();message.textContent='A fresh day with your puppy!';draw();});draw();
})();