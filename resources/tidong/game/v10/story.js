(function(){'use strict';const $=id=>document.getElementById(id),KEYS={game:'tidong.virtual-life.demo.v2',town:'tidong.virtual-life.town.v7',activity:'tidong.virtual-life.activities.v8',room:'tidong.virtual-life.room.v9',story:'tidong.virtual-life.story.v10'};
 function read(key){try{let data=localStorage.getItem(key);return data?JSON.parse(data):null;}catch(_){return null;}}
 const original=read(KEYS.game),game=window.TidongLogic.isValidState(original)?original:null;
 const town=window.TidongTown.safe(read(KEYS.town)),activities=window.TidongActivities.safe(read(KEYS.activity));
 const room=window.TidongRoom.safe(read(KEYS.room),game),story=window.TidongStory.safe(read(KEYS.story));
 const data=window.TidongStory.progress({game,town,activities,room,story});
 $('story-count').textContent=data.completed+' of '+data.total+' chapters complete';$('story-bar').style.width=(100*data.completed/data.total)+'%';
 const board=$('story-chapters');for(const [index,chapter] of data.chapters.entries()){
  const section=document.createElement('section');section.className='story-chapter'+(chapter.done?' done':'');
  const h=document.createElement('h3');h.textContent=(chapter.done?'✓ ':'○ ')+(index+1)+'. '+chapter.title;
  const p=document.createElement('p');p.textContent=chapter.description;
  const link=document.createElement('a');link.className='button alt';link.textContent=chapter.done?'Visit Again':chapter.action;link.href=chapter.link;
  section.append(h,p,link);board.append(section);
 }
 if(data.completed===data.total)$('story-message').textContent='All six fictional story chapters are complete. You can revisit any activity at your own pace.';
})();