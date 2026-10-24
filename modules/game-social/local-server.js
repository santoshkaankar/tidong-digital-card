/** TIDONG social local playtest server, zero dependencies. NOT a production/authenticated service.
 * Launch from this folder: node social-server.js. Binds loopback by default.
 * Includes a small RFC6455 text-frame transport only for localhost demonstrations.
 * Real deployment MUST replace this with reviewed WebSocket infrastructure, TLS,
 * authenticated game session, server-side service gate, Redis presence and TURN credentials.
 */
'use strict';
const http=require('node:http');
const crypto=require('node:crypto');
const fs=require('node:fs');
const path=require('node:path');
const ROOT=path.resolve(__dirname,'../../public/play/virtual-life');
const HOST='127.0.0.1';
const PORT=Number(process.env.PORT||8743);
const peers=new Map(), pendingFriend=new Set(), pendingInvites=new Set(), pendingVoice=new Set(), grants=new Set(), friends=new Set();
const colors=['mint','coral','sky','violet'];
const types={'.html':'text/html; charset=utf-8','.css':'text/css; charset=utf-8','.js':'text/javascript; charset=utf-8','.jpeg':'image/jpeg','.jpg':'image/jpeg','.png':'image/png','.svg':'image/svg+xml'};
const key=(a,b)=>[a,b].sort().join(':');
const requestKey=(a,b)=>a+':'+b;
function bounded(value,min,max){return typeof value==='number'&&Number.isFinite(value)?Math.max(min,Math.min(max,value)):null;}
function transmit(peer,obj){if(peer?.alive){const data=Buffer.from(JSON.stringify(obj));let head;if(data.length<126){head=Buffer.from([129,data.length]);}else{head=Buffer.alloc(4);head[0]=129;head[1]=126;head.writeUInt16BE(data.length,2);}try{peer.sock.write(Buffer.concat([head,data]));}catch(_){peer.alive=false;}}}
function roomPeers(p){return [...peers.values()].filter(q=>q!==p&&q.alive&&q.joined&&q.room===p.room);}
function sendRoom(p,event){for(const q of roomPeers(p))if(!q.blocked.has(p.id)&&!p.blocked.has(q.id))transmit(q,event);}
function targetOf(p,id){const q=peers.get(id);return q?.joined&&q.alive&&q.room===p.room&&q!==p&&!p.blocked.has(q.id)&&!q.blocked.has(p.id)?q:null;}
function safeView(p){return {id:p.id,name:p.name,style:p.style,x:p.x,z:p.z,world:p.world,mouth:0};}
function error(p,message){transmit(p,{type:'error',message});}
function onMessage(p,raw){
  let m;try{m=JSON.parse(raw);}catch(_){return error(p,'Invalid message');}
  if(!m||typeof m.type!=='string')return;
  const now=Date.now();if(now-p.burstAt>1000){p.burstAt=now;p.burst=0;}if(++p.burst>42)return error(p,'Too many events');
  if(m.type==='join'){
    if(p.joined)return;
    const name=typeof m.name==='string'?m.name.trim().replace(/[^\p{L}\p{N} ._-]/gu,'').slice(0,24):'';
    if(name.length<2)return error(p,'Choose a valid player name');
    const room='greenwood';if(roomPeers({room,alive:true,joined:true,id:''}).length>=15)return error(p,'Playtest room is full');
    p.joined=true;p.name=name;p.room=room;p.world=m.world==='home'?'home':'garden';p.style=colors[(peers.size-1)%colors.length];
    const available=roomPeers(p).filter(q=>!q.blocked.has(p.id)).map(safeView);
    transmit(p,{type:'welcome',id:p.id,players:available});sendRoom(p,{type:'joined',player:safeView(p)});return;
  }
  if(!p.joined)return error(p,'Join the local lobby first');
  if(m.type==='position'){
    const x=bounded(m.x,-5.4,5.4),z=bounded(m.z,-5.4,5.4);
    if(x===null||z===null||now-p.lastPosition<90)return;
    p.x=x;p.z=z;p.lastPosition=now;
    sendRoom(p,{type:'position',id:p.id,x,z,world:p.world});return;
  }
  if(m.type==='place'){
    if(!['garden','home'].includes(m.world))return;
    p.world=m.world;sendRoom(p,{type:'place',id:p.id,world:p.world});return;
  }
  if(m.type==='lip'){
    const level=bounded(m.level,0,1);if(level===null||now-p.lastLip<90)return;
    p.lastLip=now;for(const q of roomPeers(p)){if(grants.has(key(p.id,q.id))&&!p.blocked.has(q.id)&&!q.blocked.has(p.id))transmit(q,{type:'lip',id:p.id,level});}return;
  }
  if(m.type==='block'){
    const other=targetOf(p,m.target);if(!other)return;
    p.blocked.add(other.id);pendingVoice.delete(requestKey(other.id,p.id));pendingVoice.delete(requestKey(p.id,other.id));
    pendingFriend.delete(requestKey(other.id,p.id));pendingFriend.delete(requestKey(p.id,other.id));
    friends.delete(key(p.id,other.id));grants.delete(key(p.id,other.id));pendingInvites.delete(requestKey(p.id,other.id));pendingInvites.delete(requestKey(other.id,p.id));
    transmit(other,{type:'blocked',from:p.id,name:p.name});return;
  }
  if(m.type==='friend_request'){
    const other=targetOf(p,m.target);if(!other||friends.has(key(p.id,other.id)))return;
    const req=requestKey(p.id,other.id);if(pendingFriend.has(req))return;
    pendingFriend.add(req);transmit(other,{type:'friend_request',from:p.id,name:p.name});return;
  }
  if(m.type==='friend_reply'){
    const other=targetOf(p,m.target);if(!other||!pendingFriend.delete(requestKey(other.id,p.id)))return;
    const accept=m.accept===true;if(accept)friends.add(key(p.id,other.id));
    transmit(other,{type:'friend_reply',from:p.id,name:p.name,accept});
    if(accept)transmit(p,{type:'friend_confirmed',from:other.id,name:other.name});return;
  }
  if(m.type==='invite'){
    const other=targetOf(p,m.target);if(!other)return;
    if(!friends.has(key(p.id,other.id)))return error(p,'Invites need accepted friendship');
    pendingInvites.add(requestKey(p.id,other.id));transmit(other,{type:'invite',from:p.id,name:p.name,place:m.place==='home'?'home':'garden'});return;
  }
  if(m.type==='invite_reply'){
    const other=targetOf(p,m.target);if(other&&pendingInvites.delete(requestKey(other.id,p.id)))transmit(other,{type:'invite_reply',from:p.id,name:p.name,accept:m.accept===true});return;
  }
  if(m.type==='voice_request'){
    const other=targetOf(p,m.target);if(!other)return;
    if(!friends.has(key(p.id,other.id)))return error(p,'Voice requires accepted friendship');
    if(pendingVoice.has(requestKey(p.id,other.id)))return;
    pendingVoice.add(requestKey(p.id,other.id));
    transmit(other,{type:'voice_request',from:p.id,name:p.name});return;
  }
  if(m.type==='voice_reply'){
    const other=targetOf(p,m.target);if(!other||!pendingVoice.delete(requestKey(other.id,p.id)))return;
    const accept=m.accept===true;if(accept)grants.add(key(p.id,other.id));
    transmit(other,{type:'voice_reply',from:p.id,name:p.name,accept});return;
  }
  if(['webrtc_offer','webrtc_answer','webrtc_ice','voice_end'].includes(m.type)){
    const other=targetOf(p,m.target);if(!other)return;
    const approved=grants.has(key(p.id,other.id));
    if(m.type==='voice_end'){grants.delete(key(p.id,other.id));transmit(other,{type:'voice_end',from:p.id,name:p.name});return;}
    if(!approved)return error(p,'Voice requires mutual consent');
    if(m.type==='webrtc_ice'){
      if(!m.candidate||typeof m.candidate.candidate!=='string'||m.candidate.candidate.length>2400)return;
      transmit(other,{type:m.type,from:p.id,candidate:m.candidate});
    }else{
      const want=m.type==='webrtc_offer'?'offer':'answer';
      if(!m.sdp||m.sdp.type!==want||typeof m.sdp.sdp!=='string'||m.sdp.sdp.length>10000)return;
      transmit(other,{type:m.type,from:p.id,sdp:{type:want,sdp:m.sdp.sdp}});
    }return;
  }
}
function goodbye(p){if(!p.alive)return;p.alive=false;peers.delete(p.id);if(p.joined)sendRoom(p,{type:'left',id:p.id});for(const k of [pendingFriend,pendingInvites,pendingVoice,grants,friends])for(const str of [...k])if(str.includes(p.id))k.delete(str);try{p.sock.destroy();}catch(_) {}}
function parseFrames(p,chunk){
  if(!p.alive)return;p.input=Buffer.concat([p.input,chunk]);if(p.input.length>131072)return goodbye(p);
  while(p.input.length>=2){const a=p.input[0],b=p.input[1];const op=a&15,final=!!(a&128),masked=!!(b&128);let len=b&127,offset=2;
    if(!final||!masked)return goodbye(p);
    if(len===127)return goodbye(p);
    if(len===126){if(p.input.length<4)return;len=p.input.readUInt16BE(2);offset=4;}
    if(len>16000)return goodbye(p);
    if(p.input.length<offset+4+len)return;
    const mask=p.input.subarray(offset,offset+4),payload=Buffer.from(p.input.subarray(offset+4,offset+4+len));p.input=p.input.subarray(offset+4+len);
    for(let i=0;i<len;i++)payload[i]^=mask[i%4];
    if(op===8)return goodbye(p);
    if(op===9){const frame=Buffer.concat([Buffer.from([138,payload.length]),payload]);p.sock.write(frame);continue;}
    if(op!==1)continue;
    let txt;try{txt=new TextDecoder('utf-8',{fatal:true}).decode(payload);}catch(_){return goodbye(p);}
    onMessage(p,txt);
  }
}
const server=http.createServer((req,res)=>{
  if(req.method!=='GET'){res.writeHead(405);res.end('Method Not Allowed');return;}
  let name;try{name=decodeURIComponent(new URL(req.url,'http://localhost').pathname);}catch(_){res.writeHead(400);res.end();return;}
  if(name==='/'||name==='/index.html')name='/social-preview.html';
  const full=path.resolve(ROOT,'.'+name);if(!full.startsWith(ROOT+path.sep)){res.writeHead(403);res.end('Forbidden');return;}
  fs.stat(full,(err,stat)=>{if(err||!stat.isFile()){res.writeHead(404);res.end('Not Found');return;}
    res.writeHead(200,{'Content-Type':types[path.extname(full)]||'application/octet-stream','X-Content-Type-Options':'nosniff','Cache-Control':'no-store','Referrer-Policy':'no-referrer','Permissions-Policy':'camera=(), geolocation=()'});fs.createReadStream(full).pipe(res);
  });
});
server.on('upgrade',(req,sock,head)=>{
  let pathname;try{pathname=new URL(req.url,'http://localhost').pathname;}catch(_){sock.destroy();return;}
  const keyHeader=req.headers['sec-websocket-key'];const origin=req.headers.origin;
  if(pathname!=='/socket'||req.headers.upgrade?.toLowerCase()!=='websocket'||!keyHeader||!/^([A-Za-z0-9+/]{22}==)$/.test(keyHeader)|| (origin&&!/^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?$/.test(origin))){sock.destroy();return;}
  const accept=crypto.createHash('sha1').update(keyHeader+'258EAFA5-E914-47DA-95CA-C5AB0DC85B11').digest('base64');
  sock.write('HTTP/1.1 101 Switching Protocols\r\nUpgrade: websocket\r\nConnection: Upgrade\r\nSec-WebSocket-Accept: '+accept+'\r\n\r\n');
  const id=crypto.randomUUID();const p={id,sock,input:Buffer.alloc(0),alive:true,joined:false,room:'',name:'',x:0,z:0,style:'mint',world:'garden',blocked:new Set(),burstAt:0,burst:0,lastPosition:0,lastLip:0};peers.set(id,p);
  sock.on('data',chunk=>parseFrames(p,chunk));sock.on('error',()=>goodbye(p));sock.on('close',()=>goodbye(p));if(head.length)parseFrames(p,head);
});
server.listen(PORT,HOST,()=>console.log(`TIDONG SOCIAL LOCAL PLAYTEST: http://${HOST}:${server.address().port}/`));