/* TIDONG Game Chat 05: isolated LOCAL multiplayer/social preview. NOT production auth or wallet access. */
(() => {
  'use strict';
  const $=id=>document.getElementById(id);
  const serverBadge=$('server-status'), modeTag=$('mode-tag');
  const colors=['mint','coral','sky','violet'];
  const sample=[
    {id:'sample1',name:'Anaya',status:'Gardening at home',style:'coral',x:-2.6,z:-1.1,world:'garden'},
    {id:'sample2',name:'Kabir',status:'Exploring the park',style:'sky',x:2.9,z:1.0,world:'garden'},
    {id:'sample3',name:'Meera',status:'Enjoying a walk',style:'violet',x:0,z:-3.1,world:'garden'}
  ];
  let sock=null,selfId='',place='garden',tab='people',peers=new Map(),friends=new Set(),pending=new Map(),requests=[],invites=[],blocked=new Set(),call=null,localStream=null,peerConnection=null,voiceContext=null,analyser=null,lipLoop=0,lastLipAt=0,lastPosAt=0,sceneReady=false,toasting=0;
  const ICE={iceServers:[]}; // No STUN/TURN in LOCAL playtest; add managed, authenticated TURN for launch.
  function notify(message){const box=$('toast');box.textContent=message;box.classList.add('show');clearTimeout(toasting);toasting=setTimeout(()=>box.classList.remove('show'),4200);}
  function isLive(){return !!(sock&&sock.readyState===WebSocket.OPEN&&selfId);}
  function send(type,data={}){if(!isLive())return false;sock.send(JSON.stringify({type,...data}));return true;}
  function scene(){try{return sceneReady?$('scene').contentWindow.TidongSocialScene:null;}catch(_){return null;}}
  function refreshAvatars(){
    const api=scene();if(!api)return;
    const list=isLive()?[...peers.values()].filter(p=>p.world===place&&!blocked.has(p.id)) : sample.filter(p=>p.world===place);
    api.setRemote(list.map(p=>({x:p.x,z:p.z,style:p.style,mouth:p.mouth||0})));
    $('scene-people').textContent=`${list.length} ${isLive()?'connected':'demo'} avatar${list.length===1?'':'s'}`;
  }
  function setPlace(next){if(!['garden','home'].includes(next))return;place=next;
    $('world-heading').textContent=next==='garden'?'GREENWOOD NEIGHBORHOOD':'YOUR VIRTUAL HOME';
    document.querySelectorAll('[data-place]').forEach(b=>b.classList.toggle('active',b.dataset.place===next));
    scene()?.changeWorld(next);refreshAvatars();
    if(isLive())send('place',{world:next});
  }
  $('scene').addEventListener('load',()=>{sceneReady=true;setPlace('garden');});
  document.querySelectorAll('[data-place]').forEach(b=>b.addEventListener('click',()=>setPlace(b.dataset.place)));
  document.querySelectorAll('[data-move]').forEach(b=>b.addEventListener('click',()=>{scene()?.moveLocal(b.dataset.move);$('scene').focus();}));
  document.addEventListener('keydown',e=>{if(e.target.matches('input,textarea,button'))return;const direction={ArrowLeft:'left',ArrowRight:'right',ArrowUp:'forward',ArrowDown:'back',a:'left',d:'right',w:'forward',s:'back'}[e.key];if(direction){e.preventDefault();scene()?.moveLocal(direction);}});
  function contactCard(p){
    const wrap=document.createElement('div');wrap.className='contact';
    const av=document.createElement('div');av.className=`contact-avatar ${colors.includes(p.style)?p.style:'mint'}`;av.textContent=({coral:'👩🏽',sky:'👨🏽',violet:'👩🏻',mint:'🧑🏽'})[p.style]||'🙂';
    const content=document.createElement('div');content.className='contact-main';
    const strong=document.createElement('b');strong.textContent=p.name;
    const sub=document.createElement('small');sub.textContent=isLive()?(p.world==='garden'?'🌿 In the garden':'🏠 At home'):(p.status||'Sample neighbor');
    content.append(strong,sub);
    const buttons=document.createElement('div');buttons.className='contact-buttons';
    const add=(name,fn,variant='')=>{const b=document.createElement('button');b.type='button';b.textContent=name;b.className=variant;b.addEventListener('click',fn);buttons.append(b);};
    if(!isLive()){
      add('View',()=>notify('Preview avatar only. Connect two browser windows to try real requests.'));
    }else if(!blocked.has(p.id)){
      if(!friends.has(p.id))add(pending.has(p.id)?'Requested':'＋ Friend',()=>{if(pending.has(p.id))return;send('friend_request',{target:p.id});pending.set(p.id,'outgoing');render();},'alt');
      else add('✉ Invite',()=>{send('invite',{target:p.id,place});notify(`Invite sent to ${p.name}`);});
      if(friends.has(p.id))add('🎙 Talk',()=>requestVoice(p.id));
      add('Block',()=>{if(!window.confirm(`Block ${p.name}? This stops invites and voice.`))return;send('block',{target:p.id});blocked.add(p.id);if(call?.id===p.id)hangup(true);render();},'danger');
    }
    wrap.append(av,content,buttons);return wrap;
  }
  function simpleActionCard(text,btns=[]){const div=document.createElement('div');div.className='contact';const main=document.createElement('div');main.className='contact-main';const b=document.createElement('b');b.textContent=text;main.append(b);const actions=document.createElement('div');actions.className='contact-buttons';btns.forEach(([label,fn,cls])=>{const button=document.createElement('button');button.type='button';button.textContent=label;button.className=cls||'';button.addEventListener('click',fn);actions.append(button);});div.append(main,actions);return div;}
  function render(){
    const root=$('social-list');root.replaceChildren();
    const visible=[...peers.values()].filter(p=>!blocked.has(p.id));
    if(tab==='people'){
      const list=isLive()?visible:sample;
      if(!list.length)empty('🌳','No neighbors in this playtest lobby yet. Open this page in a second browser window.');
      else list.forEach(p=>root.append(contactCard(p)));
    } else if(tab==='friends'){
      const list=visible.filter(p=>friends.has(p.id));
      if(list.length)list.forEach(p=>root.append(contactCard(p)));
      else empty('🤝',isLive()?'Send a friend request to a connected neighbor.':'Friends will appear when two real demo sessions connect.');
      requests.forEach((r)=>root.append(simpleActionCard(`${r.name} sent you a friend request`,[['Accept',()=>replyFriend(r.id,true)],['Decline',()=>replyFriend(r.id,false),'alt']])));
    } else {
      if(!invites.length)empty('💌','Your neighbor invitations and voice requests will appear here.');
      invites.forEach((r)=>root.append(simpleActionCard(`${r.name} invited you to ${r.place==='home'?'their home':'the garden'}`, [['Accept',()=>replyInvite(r.id,true)],['Decline',()=>replyInvite(r.id,false),'alt']])));
    }
    $('invite-count').textContent=String(invites.length);
    $('player-count').textContent=String(isLive()?visible.length:sample.length).padStart(2,'0');
    function empty(icon,message){const d=document.createElement('div');d.className='empty-list';const sym=document.createElement('span');sym.textContent=icon;d.append(sym,document.createTextNode(message));root.append(d);}
  }
  document.querySelectorAll('[data-tab]').forEach(b=>b.addEventListener('click',()=>{tab=b.dataset.tab;document.querySelectorAll('[data-tab]').forEach(c=>{c.classList.toggle('active',c===b);c.setAttribute('aria-selected',String(c===b));});render();}));
  function replyFriend(id,accept){send('friend_reply',{target:id,accept});requests=requests.filter(r=>r.id!==id);if(accept)friends.add(id);render();}
  function replyInvite(id,accept){send('invite_reply',{target:id,accept});const target=invites.find(i=>i.id===id);invites=invites.filter(i=>i.id!==id);if(accept&&target){setPlace(target.place);notify(`You accepted ${target.name}'s invitation.`);}render();}
  function connect(){
    if(isLive()){sock.close(1000,'Left playtest');return;}
    if(location.protocol==='file:'){notify('Run the local playtest server to connect two browser windows. See README.');return;}
    if(!['localhost','127.0.0.1'].includes(location.hostname)){notify('Local playtest is restricted to localhost. No production identity is connected.');return;}
    const nickname=$('player-name').value.trim().replace(/[^\p{L}\p{N} ._-]/gu,'').slice(0,24);
    if(nickname.length<2){notify('Choose a player name of at least 2 characters.');return;}
    const protocol=location.protocol==='https:'?'wss:':'ws:';
    try{sock=new WebSocket(`${protocol}//${location.host}/socket`);}catch(_){notify('Could not start a local playtest connection.');return;}
    $('connect-btn').disabled=true;
    sock.addEventListener('open',()=>sock.send(JSON.stringify({type:'join',name:nickname,world:place,room:'greenwood'})));
    sock.addEventListener('message',async event=>{let m;try{m=JSON.parse(event.data);}catch(_){return;}await handle(m);});
    sock.addEventListener('close',()=>{const wasLive=!!selfId;selfId='';peers.clear();friends.clear();pending.clear();requests=[];invites=[];hangup(false);$('connect-btn').disabled=false;$('connect-btn').textContent='Join playtest';serverBadge.textContent='GUIDED PREVIEW · OFFLINE';modeTag.textContent='3D WEBGL SCENE';$('connect-notice').textContent='Not connected. Start the local playtest server for two-window multiplayer.';render();refreshAvatars();if(wasLive)notify('Left local playtest lobby.');});
    sock.addEventListener('error',()=>notify('Playtest server unavailable. Run: node social-server.js'));
  }
  $('connect-btn').addEventListener('click',connect);
  async function handle(m){
    if(!m||typeof m.type!=='string')return;
    if(m.type==='welcome'){
      selfId=m.id;peers=new Map((m.players||[]).map(p=>[p.id,p]));
      $('connect-btn').disabled=false;$('connect-btn').textContent='Leave playtest';serverBadge.textContent='CONNECTED · LOCAL PLAYTEST';modeTag.textContent='REAL-TIME LOCAL DEMO';$('connect-notice').textContent='Connected! Open this page in another browser window and choose a different name.';notify('Joined local social lobby. No real game account is attached.');render();refreshAvatars();
    }else if(m.type==='joined'){peers.set(m.player.id,m.player);render();refreshAvatars();notify(`${m.player.name} joined your neighborhood.`);}
    else if(m.type==='left'){peers.delete(m.id);friends.delete(m.id);pending.delete(m.id);if(call?.id===m.id)hangup(false);render();refreshAvatars();}
    else if(m.type==='position' || m.type==='place' || m.type==='lip'){
      const p=peers.get(m.id);if(!p)return;
      if(m.type==='position'){p.x=m.x;p.z=m.z;p.world=m.world;}
      else if(m.type==='place'){p.world=m.world;}
      else p.mouth=Math.max(0,Math.min(1,Number(m.level)||0));
      if(m.type==='place')render();refreshAvatars();
    }else if(m.type==='friend_request'){requests.push({id:m.from,name:m.name});tab='friends';$('tab-friends').click();notify(`${m.name} sent a friend request.`);}
    else if(m.type==='friend_reply'){pending.delete(m.from);if(m.accept){friends.add(m.from);notify(`${m.name} accepted your friend request.`);}else notify(`${m.name} declined the request.`);render();}
    else if(m.type==='friend_confirmed'){friends.add(m.from);render();}
    else if(m.type==='invite'){invites.push({id:m.from,name:m.name,place:m.place});$('tab-invites').click();notify(`Invitation from ${m.name}.`);}
    else if(m.type==='invite_reply'){notify(`${m.name} ${m.accept?'accepted':'declined'} your invitation.`);}
    else if(m.type==='blocked'){blocked.add(m.from);peers.delete(m.from);if(call?.id===m.from)hangup(false);render();refreshAvatars();}
    else if(m.type==='voice_request'){voiceConsent(m.from,m.name);}
    else if(m.type==='voice_reply'){if(!m.accept){notify(`${m.name} declined voice chat.`);hangup(false);return;}if(!call||call.id!==m.from) return; try{await startPeer(true);}catch(err){notify('Microphone or WebRTC unavailable: '+err.message);hangup(true);}}
    else if(m.type==='webrtc_offer'){if(!call||call.id!==m.from||!call.accepted)return;try{await startPeer(false,m.sdp);}catch(err){notify('Unable to start voice: '+err.message);hangup(true);}}
    else if(m.type==='webrtc_answer'){if(peerConnection&&call?.id===m.from)try{await peerConnection.setRemoteDescription(m.sdp);}catch(_) {}}
    else if(m.type==='webrtc_ice'){if(peerConnection&&call?.id===m.from&&m.candidate)try{await peerConnection.addIceCandidate(m.candidate);}catch(_) {}}
    else if(m.type==='voice_end'){if(call?.id===m.from){hangup(false);notify(`${m.name} ended voice chat.`);}}
    else if(m.type==='error'){if(call&&!call.accepted)call=null;notify(m.message||'Playtest request rejected.');}
  }
  function voiceConsent(id,name){if(call){send('voice_reply',{target:id,accept:false});return;}$('modal-description').textContent=`${name} wants to speak with you. Microphone permission is requested only if you accept.`;$('consent-modal').classList.remove('hidden');$('modal-decline').onclick=()=>{$('consent-modal').classList.add('hidden');send('voice_reply',{target:id,accept:false});};$('modal-accept').onclick=async()=>{$('consent-modal').classList.add('hidden');try{await ensureMic();call={id,accepted:true};send('voice_reply',{target:id,accept:true});$('voice-toggle').disabled=false;$('voice-toggle').textContent='⏹ End voice';$('voice-toggle').classList.add('on');notify('Microphone enabled. Waiting for secure peer connection.');}catch(_){send('voice_reply',{target:id,accept:false});notify('Microphone access denied or unsupported.');}};}
  function requestVoice(id){if(!isLive())return;if(call){notify('End the existing voice call before starting another.');return;}const p=peers.get(id);if(!p)return;call={id,accepted:false};send('voice_request',{target:id});notify(`Asking ${p.name} for microphone consent...`);}
  async function ensureMic(){if(localStream)return localStream;if(!navigator.mediaDevices?.getUserMedia)throw Error('Use HTTPS or localhost and a modern browser.');localStream=await navigator.mediaDevices.getUserMedia({audio:{echoCancellation:true,noiseSuppression:true},video:false});setupAnalyzer(localStream);return localStream;}
  function setupAnalyzer(stream){const Context=window.AudioContext||window.webkitAudioContext;if(!Context)return;voiceContext=new Context();const source=voiceContext.createMediaStreamSource(stream);analyser=voiceContext.createAnalyser();analyser.fftSize=256;source.connect(analyser);const bytes=new Uint8Array(analyser.frequencyBinCount);const tick=(at)=>{if(!localStream)return;analyser.getByteFrequencyData(bytes);const avg=bytes.reduce((s,v)=>s+v,0)/bytes.length;const level=Math.min(1,Math.max(0,(avg-8)/90));scene()?.setLocalMouth(level);if(at-lastLipAt>130){send('lip',{level});lastLipAt=at;}lipLoop=requestAnimationFrame(tick);};lipLoop=requestAnimationFrame(tick);}
  async function startPeer(initiator,remoteOffer){await ensureMic();if(peerConnection)return;peerConnection=new RTCPeerConnection(ICE);peerConnection.onicecandidate=e=>{if(e.candidate)send('webrtc_ice',{target:call.id,candidate:e.candidate.toJSON()});};peerConnection.ontrack=e=>{let audio=$('peer-audio');if(!audio){audio=document.createElement('audio');audio.id='peer-audio';audio.autoplay=true;document.body.append(audio);}audio.srcObject=e.streams[0];audio.play().catch(()=>notify('Tap the page to allow incoming audio playback.'));};peerConnection.onconnectionstatechange=()=>{$('voice-status').textContent=`Voice: ${peerConnection?.connectionState||'off'}`;if(peerConnection&&['failed','closed'].includes(peerConnection.connectionState))hangup(true);};localStream.getTracks().forEach(t=>peerConnection.addTrack(t,localStream));call.accepted=true;$('voice-toggle').disabled=false;$('voice-toggle').textContent='⏹ End voice';$('voice-toggle').classList.add('on');if(initiator){const offer=await peerConnection.createOffer();await peerConnection.setLocalDescription(offer);send('webrtc_offer',{target:call.id,sdp:peerConnection.localDescription});}else if(remoteOffer){await peerConnection.setRemoteDescription(remoteOffer);const answer=await peerConnection.createAnswer();await peerConnection.setLocalDescription(answer);send('webrtc_answer',{target:call.id,sdp:peerConnection.localDescription});}}
  function hangup(shouldSignal){const id=call?.id;if(shouldSignal&&id)send('voice_end',{target:id});call=null;if(peerConnection){peerConnection.onconnectionstatechange=null;peerConnection.close();peerConnection=null;}if(localStream){localStream.getTracks().forEach(t=>t.stop());localStream=null;}if(voiceContext){voiceContext.close().catch(()=>{});voiceContext=null;}cancelAnimationFrame(lipLoop);scene()?.setLocalMouth(0);send('lip',{level:0});const a=$('peer-audio');if(a){a.srcObject=null;a.remove();}$('voice-toggle').disabled=true;$('voice-toggle').textContent='🎙 Voice: Off';$('voice-toggle').classList.remove('on');$('voice-status').textContent='Microphone is OFF by default';}
  $('voice-toggle').addEventListener('click',()=>{if(call)hangup(true);});
  window.addEventListener('beforeunload',()=>hangup(true));
  setInterval(()=>{if(!isLive())return;const p=scene()?.getLocal();const at=Date.now();if(p&&at-lastPosAt>=120){send('position',{x:p.x,z:p.z,world:place});lastPosAt=at;}},130);
  render();
})();