/* TIDONG Virtual Life V5: offline interactive WebGL geometry preview. No real-money or backend operations. */
(() => {
  'use strict';
  const canvas = document.getElementById('world3d');
  const status = document.getElementById('status');
  const fallback = document.getElementById('fallback');
  const gl = canvas.getContext('webgl', { antialias: true, alpha: false, preserveDrawingBuffer: false });
  const cpuCtx = gl ? null : canvas.getContext('2d');
  if (!gl) { status.textContent = 'Lightweight 3D preview active · Drag to rotate, use arrows to walk.'; document.querySelector('.webgl-chip').textContent='SOFTWARE 3D'; }

  const VS = `attribute vec3 aPosition; attribute vec3 aNormal; attribute vec3 aColor;
  uniform mat4 uProjectionView; uniform vec3 uLightDir; uniform float uDaylight;
  varying vec3 vColor; varying float vDepth;
  void main(){ vec4 clip=uProjectionView*vec4(aPosition,1.0); gl_Position=clip;
    float lambert=max(dot(normalize(aNormal),normalize(uLightDir)),0.0);
    float light=0.51+0.42*lambert*uDaylight;
    vColor=aColor*light+vec3(0.025,0.035,0.046); vDepth=clip.z/clip.w;
  }`;
  const FS = `precision mediump float; varying vec3 vColor; varying float vDepth; uniform vec3 uFogColor;
  void main(){float fog=smoothstep(0.89,1.0,vDepth)*0.25;
    gl_FragColor=vec4(mix(vColor,uFogColor,fog),1.0);
  }`;
  function shader(kind, source) {
    const sh=gl.createShader(kind); gl.shaderSource(sh,source); gl.compileShader(sh);
    if (!gl.getShaderParameter(sh,gl.COMPILE_STATUS)) throw Error(gl.getShaderInfoLog(sh));
    return sh;
  }
  let program;
  if (gl) try {
    program=gl.createProgram();gl.attachShader(program,shader(gl.VERTEX_SHADER,VS));gl.attachShader(program,shader(gl.FRAGMENT_SHADER,FS));gl.linkProgram(program);
    if(!gl.getProgramParameter(program,gl.LINK_STATUS)) throw Error(gl.getProgramInfoLog(program));
  } catch (e) { fallback.classList.remove('hidden'); status.textContent='3D shader unavailable.'; return; }
  if (gl) { gl.useProgram(program); gl.enable(gl.DEPTH_TEST); gl.depthFunc(gl.LEQUAL); }
  const attr=gl?{pos:gl.getAttribLocation(program,'aPosition'),normal:gl.getAttribLocation(program,'aNormal'),color:gl.getAttribLocation(program,'aColor')}:null;
  const uniform=gl?{pv:gl.getUniformLocation(program,'uProjectionView'),sun:gl.getUniformLocation(program,'uLightDir'),day:gl.getUniformLocation(program,'uDaylight'),fog:gl.getUniformLocation(program,'uFogColor')}:null;
  const vertexBuffer=gl?gl.createBuffer():null;
  if(gl)for(const a of Object.values(attr))gl.enableVertexAttribArray(a);
  const color=(hex)=>{
    const h=hex.replace('#',''); const a=h.length===3?h.split('').map(c=>c+c):[h.slice(0,2),h.slice(2,4),h.slice(4,6)];
    return a.map(c=>parseInt(c,16)/255);
  };
  const M={v:[]};
  function tri(a,b,c,n,shade){for(const p of [a,b,c]) M.v.push(...p,...n,...shade);}
  function quad(a,b,c,d,n,shade){tri(a,b,c,n,shade);tri(a,c,d,n,shade);}
  function box(x,y,z,w,h,d,base){
    const [r,g,b]=color(base), t=[r,g,b], darker=[r*.88,g*.88,b*.88], brighter=[Math.min(1,r*1.07),Math.min(1,g*1.07),Math.min(1,b*1.07)];
    const x0=x-w/2,x1=x+w/2,y0=y-h/2,y1=y+h/2,z0=z-d/2,z1=z+d/2;
    quad([x0,y0,z1],[x1,y0,z1],[x1,y1,z1],[x0,y1,z1],[0,0,1],t);
    quad([x1,y0,z0],[x0,y0,z0],[x0,y1,z0],[x1,y1,z0],[0,0,-1],darker);
    quad([x1,y0,z1],[x1,y0,z0],[x1,y1,z0],[x1,y1,z1],[1,0,0],darker);
    quad([x0,y0,z0],[x0,y0,z1],[x0,y1,z1],[x0,y1,z0],[-1,0,0],t);
    quad([x0,y1,z1],[x1,y1,z1],[x1,y1,z0],[x0,y1,z0],[0,1,0],brighter);
    quad([x0,y0,z0],[x1,y0,z0],[x1,y0,z1],[x0,y0,z1],[0,-1,0],darker);
  }
  function disc(x,y,z,r,shade){ const N=20, col=color(shade); for(let i=0;i<N;i++){
    const a=i*2*Math.PI/N,b=(i+1)*2*Math.PI/N;
    tri([x,y,z],[x+r*Math.cos(a),y,z+r*Math.sin(a)],[x+r*Math.cos(b),y,z+r*Math.sin(b)],[0,1,0],col);
  }}
  function sphere(x,y,z,r,c){
    const col=color(c);const rings=9,sectors=13;
    function point(phi,theta){return [x+r*Math.sin(phi)*Math.cos(theta),y+r*Math.cos(phi),z+r*Math.sin(phi)*Math.sin(theta)];}
    function normal(p){return [(p[0]-x)/r,(p[1]-y)/r,(p[2]-z)/r];}
    for(let i=0;i<rings;i++)for(let j=0;j<sectors;j++){
      const a=point(i*Math.PI/rings,j*2*Math.PI/sectors);
      const b=point((i+1)*Math.PI/rings,j*2*Math.PI/sectors);
      const d=point((i+1)*Math.PI/rings,(j+1)*2*Math.PI/sectors);
      const e=point(i*Math.PI/rings,(j+1)*2*Math.PI/sectors);
      tri(a,b,e,normal(a),col);tri(b,d,e,normal(b),col);
    }
  }
  function cylinder(x,y,z,r,h,c,segments=16){
    const col=color(c);for(let i=0;i<segments;i++){
      const a=i*2*Math.PI/segments,b=(i+1)*2*Math.PI/segments;
      const p=[x+r*Math.cos(a),y,z+r*Math.sin(a)],q=[x+r*Math.cos(b),y,z+r*Math.sin(b)];
      const p2=[p[0],y+h,p[2]],q2=[q[0],y+h,q[2]];
      quad(p,q,q2,p2,[Math.cos(a),0,Math.sin(a)],col);
      tri([x,y+h,z],p2,q2,[0,1,0],col);
    }
  }
  function rug(x,z,r,c){disc(x,.02,z,r,c);}
  function plant(x,z,scale=1){
    disc(x,.02,z,.52*scale,'#b8b4ac');cylinder(x,0,z,.31*scale,.43*scale,'#d28c68');
    cylinder(x,.35*scale,z,.065*scale,1.15*scale,'#87613f');
    for(let i=0;i<5;i++){const angle=i*2*Math.PI/5;
      sphere(x+.32*Math.cos(angle)*scale,1.18*scale,z+.32*Math.sin(angle)*scale,.36*scale,i%2?'#72b486':'#5d9c7d');}
    sphere(x,1.51*scale,z,.36*scale,'#8aca87');
  }
  function sofa(x,z,c='#d5a78a'){
    rug(x,z,.97,'#445e64');box(x,.32,z,2.65,.4,1.05,c);box(x,.86,z-.49,2.65,.78,.28,'#c89478');
    box(x-1.24,.55,z,.19,.75,1.11,'#bd8c74');box(x+1.24,.55,z,.19,.75,1.11,'#bd8c74');
    box(x-.59,.57,z+.11,1.06,.21,.74,'#e6b89e');box(x+.59,.57,z+.11,1.06,.21,.74,'#d8a486');
    box(x-.56,.13,z,0.13,.25,.13,'#805e55');box(x+.56,.13,z,0.13,.25,.13,'#805e55');
  }
  function table(x,z){rug(x,z,1.1,'#c3bdad');cylinder(x,.12,z,.48,.13,'#a68165');
    cylinder(x,.18,z,.13,.42,'#705249');cylinder(x,.59,z,.82,.10,'#d2b28b');
    cylinder(x,.7,z,.15,.05,'#e9dbbf');sphere(x,.86,z,.2,'#6a936d');
  }
  function floorLamp(x,z){disc(x,.021,z,.37,'#725a49');cylinder(x,.025,z,.10,1.65,'#bca782');
    cylinder(x,1.65,z,.48,.38,'#fff0b8');sphere(x,1.79,z,.19,'#fff5d0');
  }
  function shelf(x,z){box(x,1.05,z,1.6,2.1,.48,'#6b5b58');
    for(let i=0;i<3;i++)box(x,.38+i*.63,z+.15,1.52,.09,.54,'#b99677');
    for(let i=0;i<8;i++)box(x-.58+i*.16,.57,z+.27,.1,.26,.14,i%2?'#86a2a1':'#c88d7a');
  }
  function car(x,z){rug(x,z,1.2,'#555c66');box(x,.39,z,2.0,.43,1.05,'#ae566a');box(x,.75,z-.03,1.24,.42,.8,'#a44b65');
    box(x,.81,z+.385,1.12,.27,.08,'#8fbfcf');
    for(const dx of [-.64,.64])for(const dz of [-.56,.56])sphere(x+dx,.19,z+dz,.23,'#283947');
  }
  function bed(x,z){rug(x,z,1.25,'#cdb89c');box(x,.35,z,2.6,.36,1.7,'#b9c3c7');
    box(x,.55,z-.68,2.52,.43,.22,'#a38d88');box(x,.58,z+.18,2.1,.18,.9,'#d4e4e2');
    box(x-.56,.64,z-.49,.73,.16,.42,'#e8eef1');box(x+.56,.64,z-.49,.73,.16,.42,'#e8eef1');
  }
  function defaultWorld(){
    // An open-sided architectural room is used so the camera can see inside.
    for(let x=-5;x<5;x++)for(let z=-5;z<5;z++){const col=color((x+z)%2?'#d3c6b4':'#ded4c5');quad([x,-.08,z],[x+1,-.08,z],[x+1,-.08,z+1],[x,-.08,z+1],[0,1,0],col);}
    box(-5,1.88,0,.20,3.9,10.2,'#b9c9cf');box(0,1.88,-5,10.2,3.9,.20,'#d9d3c7');
    box(-5,3.84,0,.28,.18,10.3,'#9db0b7');box(0,3.84,-5,10.3,.18,.28,'#d0c7b9');
    box(0,0,-5.01,6.65,.11,.1,'#a1a19b');
    // The window is architectural glass on the left wall, framed and divided into panes.
    box(-4.86,2.2,-.8,.055,1.56,2.45,'#87afbb');
    box(-4.81,2.2,-.8,.06,1.60,.055,'#d3e2dc');
    box(-4.81,2.2,-2.02,.06,1.6,.07,'#f4f2e8');
    box(-4.81,2.2,.42,.06,1.6,.07,'#f4f2e8');
    box(-4.81,3,-.8,.06,.07,2.48,'#f4f2e8');
    box(-4.81,1.4,-.8,.06,.07,2.48,'#f4f2e8');
    box(-4.79,2.2,-.8,.07,1.56,.045,'#e6e4d8');
    for(let i=0;i<4;i++){
      box(-4.75,1.15+i*.58,3.4,.13,.18,.7,'#c6ddd5');
    }
    box(-1.4,1.9,-4.85,1.8,1.13,.13,'#738e8d');box(-1.4,1.9,-4.75,1.55,.88,.1,'#8bb5af');
    box(-1.4,2.46,-4.74,.92,.13,.11,'#e2cd9e');
    box(2.6,2.00,-4.86,1.3,1.6,.16,'#987f75');
    box(2.6,2.00,-4.73,1.05,1.34,.08,'#d4b19e');
    box(2.6,2.00,-4.68,.85,1.16,.07,'#76989a');
    rug(-.5,-.2,2.0,'#dac7ad');sofa(-2.05,-1.45);
    table(-.3,-.55);plant(3.9,-3.66,1.2);
    floorLamp(-3.55,-3.64);shelf(3.55,1.9);
    cylinder(4.1,0,3.8,.27,.33,'#c17d62');sphere(4.1,.66,3.8,.39,'#608b6a');
    box(0,3.73,0,.75,.10,.65,'#f2ecd9');sphere(0,3.49,0,.2,'#f9eed2');
  }
  function gardenWorld(){
    // Low-polygon outdoor geometry; no external textures or networking.
    for(let x=-7;x<7;x++)for(let z=-7;z<7;z++){
      const shade=(x+z)%3===0?'#91bb83':(x+z)%3===1?'#8ab57e':'#9ac18a';
      quad([x,-.11,z],[x+1,-.11,z],[x+1,-.11,z+1],[x,-.11,z+1],[0,1,0],color(shade));
    }
    // Main winding path, with stepping stones and a small pond.
    for(let z=-5.6;z<=5.4;z+=.68){const x=.40*Math.sin(z*.8);box(x,-.055,z,1.65,.07,.62,'#d8c5a3');}
    disc(2.40,-.018,-3.30,1.85,'#98c7b4');
    disc(2.40,.009,-3.30,1.58,'#65afbc');
    disc(2.2,.019,-3.57,.23,'#d9e5bc');disc(2.87,.020,-3.25,.2,'#bbd7a2');
    // Outdoor bench.
    box(2.8,.57,3.22,2.3,.18,.75,'#ac7658');box(2.8,1.07,2.87,2.3,.71,.15,'#bd9072');
    for(const x of [1.87,3.72]){box(x,.3,3.22,.14,.6,.63,'#5c5a53');}
    // Flower patch, including blossoms visible at different hours.
    for(let i=0;i<11;i++){
      const x=-4.25+(i%4)*.62,z=-3.4+Math.floor(i/4)*.62;
      cylinder(x,-.01,z,.055,.48,'#537d4d',8);
      sphere(x,.54,z,.16,['#e4a6af','#edd291','#c9b5e6'][i%3]);
    }
    // Trees are sculpted with reusable low-resolution spherical canopies.
    for(const [x,z,scale] of [[-5,-5,1.1],[-5,4.5,1.15],[5,5,1.0],[5,-.4,.94],[-1.6,5.5,.8]]){
      cylinder(x,.01,z,.23*scale,2.0*scale,'#785b41',10);
      for(const dx of [-.65,.65])for(const dz of [-.5,.5])sphere(x+dx*scale,2.55*scale,z+dz*scale,.86*scale,'#5a9671');
      sphere(x,3.05*scale,z,1.07*scale,'#6aaf7b');
    }
    // Small open gazebo at the end of the pathway.
    disc(-2.8,.01,2.75,1.23,'#cbbb9d');
    for(const x of [-3.8,-1.8])for(const z of [1.75,3.75])cylinder(x,0,z,.10,2.24,'#c4a67b',8);
    box(-2.8,2.31,2.75,2.4,.22,2.38,'#a36f5d');
    for(const x of [-6.7,6.7])for(let z=-6.5;z<7;z+=2){box(x,.45,z,.12,.9,.12,'#d4c3aa');}
    box(-6.7,.65,0,.09,.07,13.6,'#d4c3aa');box(6.7,.65,0,.09,.07,13.6,'#d4c3aa');
    // Butterfly-colored accents, non-interactive decorations.
    sphere(-3.18,1.3,-2.85,.10,'#ffd1a0');sphere(-3.00,1.38,-2.79,.10,'#c7b4ec');
  }
  function addedItems(placed){
    const spots=[[-3.5,1.1],[2.85,1.15],[1.2,2.7],[.8,-3.15],[-3.5,-3.35],[3.6,-.4],[-.2,3.5],[2.2,-2.25]];
    placed.slice(0,8).forEach((id,i)=>{
      const [x,z]=spots[i];
      if(id==='plant')plant(x,z,.65);
      else if(id==='lamp')floorLamp(x,z);
      else if(id==='sofa')sofa(x,z,'#8fb6b1');
      else if(id==='rug')rug(x,z,.87,'#a4c2a5');
      else if(id==='bed')bed(x,z);
      else if(id==='books')shelf(x,z);
      else if(id==='painting')box(x,1.1,z,.85,1.2,.18,'#dcae81');
      else if(id==='table')table(x,z);
      else if(id==='villa')box(x,.7,z,1.4,1.3,1.4,'#cbad9c');
    });
  }
  function avatar(x,z,style,step,mouthLevel=0){
    const bodyColors={mint:'#80daca',coral:'#e9a18f',sky:'#80b5e8',violet:'#b7a6d9'};
    const shade=bodyColors[style]||bodyColors.mint;
    disc(x,.045,z,.53,'#8f9e9f');
    // Small oscillations are visual-only and do not change player balances.
    const walk=Math.sin(step)*.075;
    box(x-.15,.35+walk,z,.24,.64,.22,'#36485c');box(x+.15,.35-walk,z,.24,.64,.22,'#36485c');
    box(x,.98,z,.76,.78,.44,shade);
    box(x-.50,.91+walk,z,.19,.72,.25,shade);box(x+.50,.91-walk,z,.19,.72,.25,shade);
    cylinder(x,1.39,z,.12,.17,'#d7ab90');sphere(x,1.79,z,.34,'#dfb69d');
    sphere(x,1.98,z-.11,.33,'#55484b');sphere(x,1.84,z+.24,.12,'#e5be9f');
    if(mouthLevel>.08)box(x,1.72,z+.354,.12,.025+Math.min(1,mouthLevel)*.16,.035,'#6f3b48');
  }
  const socialVisitors=[];
  let socialLocalMouth=0;
  function meshData(style,placed,time,step,pos){
    M.v=[];
    if(world==='garden')gardenWorld();else {defaultWorld();addedItems(placed);}
    avatar(pos.x,pos.z,style,step,socialLocalMouth);
    for(const peer of socialVisitors)avatar(peer.x,peer.z,peer.style,step+peer.offset,peer.mouth);
    return new Float32Array(M.v);
  }
  function v3(a,b,c){return [a,b,c];}
  function sub(a,b){return a.map((v,i)=>v-b[i]);}
  function dot(a,b){return a.reduce((s,v,i)=>s+v*b[i],0);}
  function norm(a){const n=Math.hypot(...a)||1;return a.map(v=>v/n);}
  function cross(a,b){return [a[1]*b[2]-a[2]*b[1],a[2]*b[0]-a[0]*b[2],a[0]*b[1]-a[1]*b[0]];}
  function lookAt(eye,center){const z=norm(sub(eye,center)),x=norm(cross([0,1,0],z)),y=cross(z,x);
    return [x[0],y[0],z[0],0,x[1],y[1],z[1],0,x[2],y[2],z[2],0,-dot(x,eye),-dot(y,eye),-dot(z,eye),1];
  }
  function perspective(fov,aspect,near,far){const f=1/Math.tan(fov/2),nf=1/(near-far);
    return [f/aspect,0,0,0,0,f,0,0,0,0,(far+near)*nf,-1,0,0,2*far*near*nf,0];
  }
  function multiply(a,b){const out=new Array(16).fill(0);
    for(let c=0;c<4;c++)for(let r=0;r<4;r++)for(let k=0;k<4;k++)out[c*4+r]+=a[k*4+r]*b[c*4+k];
    return out;
  }
  const camera={az:Math.PI*.28,el:.63,dist:16,target:[0,1.2,0]};
  let lighting='day',style='mint',owned=[],placed=[],step=0, savedState=null,world='home',travel=null,trail=[],walking=false;
  const player={x:1.0,z:2.3};
  const studioLogic=window.TidongStudio;
  const worldLogic=window.TidongWorld;
  const GARDEN_SAVE_KEY='tidong.virtual-life.garden.v5';
  let visits={completed:[]};
  try {visits=worldLogic.safeProgress(JSON.parse(localStorage.getItem(GARDEN_SAVE_KEY)));}catch (_) {}
  const gameLogic=window.TidongLogic;
  const STORAGE_KEY='tidong.virtual-life.demo.v2';
  function clamp(v,min,max){return Math.max(min,Math.min(max,v));}
  // Social playtest scene adapter: visual avatars only. No entitlement, wallet, or network trust.
  window.TidongSocialScene=Object.freeze({
    getLocal:()=>({x:player.x,z:player.z,world}),
    setRemote(players){
      socialVisitors.length=0;
      if(!Array.isArray(players))return;
      for(const [index,p] of players.slice(0,15).entries()){
        if(!p || !Number.isFinite(p.x) || !Number.isFinite(p.z))continue;
        socialVisitors.push({x:clamp(p.x,-5.4,5.4),z:clamp(p.z,-5.4,5.4),
          style:['mint','coral','sky','violet'].includes(p.style)?p.style:'sky',
          mouth:clamp(Number(p.mouth)||0,0,1),offset:index*1.8});
      }
    },
    setLocalMouth(v){socialLocalMouth=clamp(Number(v)||0,0,1);},
    changeWorld(name){if(['home','garden'].includes(name))switchWorld(name);},
    moveLocal(dir){if(['left','right','forward','back'].includes(dir))move(dir);}
  });
  function setMessage(message){document.getElementById('studio-hint').textContent=message;}
  function syncFromSave(){
    savedState=null;
    try {
      const candidate=JSON.parse(localStorage.getItem(STORAGE_KEY));
      if(studioLogic.isReady(candidate)) savedState=candidate;
    } catch (_) {}
    owned=savedState?savedState.inventory.slice():[];
    placed=savedState?savedState.placed.filter(id=>studioLogic.HOME_ITEMS.some(item=>item.id===id)).slice(0,8):[];
    style=savedState?savedState.style:'mint';
    lighting=savedState?savedState.timeOfDay:'day';
    document.getElementById('studio-avatar-style').value=style;
    document.getElementById('save-message').textContent=savedState
      ? 'Your room is connected to the original game save on this device. No real wallet is involved.'
      : 'Start the original demo to unlock your virtual collection. This room remains a free local preview.';
    renderFurniture();refreshCounts();
  }
  function refreshCounts(){
    document.getElementById('owned-count').textContent=String(owned.length);
    document.getElementById('decor-count').textContent=String(placed.length);
  }
  function persist(){
    if(!savedState || !gameLogic.isValidState(savedState))return false;
    try {localStorage.setItem(STORAGE_KEY,JSON.stringify(savedState));return true;}
    catch (_) {setMessage('Unable to save changes on this device.');return false;}
  }
  function changeFurniture(action,id,index,delta){
    if (!savedState)return setMessage('Enter the original game to arrange purchased items.');
    let result;
    if(action==='place')result=studioLogic.place(savedState,id);
    else if(action==='remove')result=studioLogic.remove(savedState,index);
    else result=studioLogic.shift(savedState,index,delta);
    if(result.ok && !persist()){syncFromSave();return;}
    setMessage(result.message);
    owned=savedState.inventory.slice();placed=savedState.placed.slice(0,8);
    renderFurniture();refreshCounts();
  }
  function makeBtn(label,callback,disabled=false){
    const btn=document.createElement('button');btn.type='button';btn.textContent=label;btn.disabled=disabled;
    btn.addEventListener('click',callback);return btn;
  }
  function renderFurniture(){
    const panel=document.getElementById('furniture-panel');panel.replaceChildren();
    if(!savedState){
      setMessage('Your 3D home is ready. Enter the original game and buy furniture to decorate it.');
      return;
    }
    const available=studioLogic.inventory(savedState);
    if(!available.length){setMessage('You do not own any virtual home items yet. Browse the original marketplace.');return;}
    const summary=document.createElement('p');summary.className='muted';
    summary.textContent=placed.length+'/8 studio display spots occupied.';
    panel.appendChild(summary);
    for(const item of available){
      const row=document.createElement('div');row.className='furniture-row';
      const title=document.createElement('span');title.className='furniture-title';
      title.textContent=item.icon+' '+item.name+' · '+item.placed+'/'+item.owned+' displayed';
      row.appendChild(title);
      row.appendChild(makeBtn('+ Place',()=>changeFurniture('place',item.id),item.placed>=item.owned||placed.length>=8));
      panel.appendChild(row);
    }
    if(!placed.length)return;
    const headline=document.createElement('p');headline.className='studio-subhead';headline.textContent='Your displayed pieces';
    panel.appendChild(headline);
    placed.forEach((id,index)=>{
      const item=studioLogic.HOME_ITEMS.find(x=>x.id===id);
      const row=document.createElement('div');row.className='furniture-row displayed-row';
      const label=document.createElement('span');label.className='furniture-title';
      label.textContent=(index+1)+'. '+(item?item.name:id);
      row.appendChild(label);
      row.appendChild(makeBtn('←',()=>changeFurniture('shift',null,index,-1),index===0));
      row.appendChild(makeBtn('→',()=>changeFurniture('shift',null,index,1),index===placed.length-1));
      row.appendChild(makeBtn('Remove',()=>changeFurniture('remove',null,index)));
      panel.appendChild(row);
    });
  }
  syncFromSave();
  document.getElementById('studio-avatar-style').addEventListener('change',e=>{
    const chosen=e.target.value;
    if(!['mint','coral','sky','violet'].includes(chosen))return;
    if(savedState){const result=gameLogic.changeStyle(savedState,chosen);if(!result.ok||!persist())return;}
    style=chosen;
    setMessage('Your character style is updated'+(savedState?' and saved.': ' for this preview.'));
  });
  const ACTIVITIES={
    relax:{x:-2.0,z:-.85,message:'Taking a peaceful moment by the sofa.'},
    read:{x:2.95,z:1.75,message:'Spending a little time in the reading corner.'},
    plant:{x:3.45,z:-3.5,message:'Looking after the greenery in your home.'}
  };
  document.querySelectorAll('[data-activity]').forEach(b=>b.addEventListener('click',()=>{
    const activity=ACTIVITIES[b.dataset.activity];if(!activity)return;
    player.x=activity.x;player.z=activity.z;step+=1.2;
    document.querySelectorAll('[data-activity]').forEach(btn=>btn.classList.toggle('active',btn===b));
    document.getElementById('activity-status').textContent=activity.message;
    status.textContent=activity.message+' No T-Coins spent or awarded.';
  }));
  function updateGardenProgress(message){
    document.getElementById('garden-progress').textContent=visits.completed.length+' / 3 moments explored';
    document.querySelectorAll('[data-garden]').forEach(b=>b.classList.toggle('active',visits.completed.includes(b.dataset.garden)));
    if(message) document.getElementById('garden-story').textContent=message;
  }
  function switchWorld(next){
    if(!['home','garden'].includes(next))return;
    world=next;travel=null;trail=[];walking=false;
    Object.assign(player,next==='garden'?{x:0,z:4.45}:{x:1,z:2.3});
    camera.target=[0,1.2,0];viewPreset('wide');
    document.querySelectorAll('[data-world]').forEach(b=>b.classList.toggle('active',b.dataset.world===world));
    status.textContent=next==='garden'?'Explore the Garden: find flowers, pond and bench.':'Welcome back to your 3D home.';
    updateMood();
  }
  function startGardenVisit(id){
    if(!Object.hasOwn(worldLogic.ACTIVITIES,id))return;
    if(world!=='garden')switchWorld('garden');
    const a=worldLogic.ACTIVITIES[id];travel={x:a.x,z:a.z,activity:id};
    document.getElementById('garden-story').textContent='Walking to '+a.title.toLowerCase()+'…';
    status.textContent='Walking to '+a.title.toLowerCase()+'…';
  }
  function completedVisit(id){
    const result=worldLogic.markVisited(visits,id);if(!result.ok)return;
    visits=result.progress;
    try {localStorage.setItem(GARDEN_SAVE_KEY,JSON.stringify(visits));}catch(_) {}
    updateGardenProgress(result.message+(visits.completed.length===3?' Garden trail complete — all three moments discovered!':''));
    status.textContent=result.message+' No T-Coins earned or spent.';
  }
  document.querySelectorAll('[data-world]').forEach(b=>b.addEventListener('click',()=>switchWorld(b.dataset.world)));
  if (location.hash === '#garden') switchWorld('garden');
  document.querySelectorAll('[data-garden]').forEach(b=>b.addEventListener('click',()=>{trail=[];startGardenVisit(b.dataset.garden);}));
  document.getElementById('garden-tour').addEventListener('click',()=>{
    switchWorld('garden');trail=worldLogic.IDS.slice();startGardenVisit(trail.shift());
    document.getElementById('garden-story').textContent='Guided walk started. Explore the flowers, pond and bench.';
  });
  document.getElementById('garden-reset').addEventListener('click',()=>{
    visits={completed:[]};try{localStorage.removeItem(GARDEN_SAVE_KEY);}catch(_){}
    updateGardenProgress('Garden visit markers reset. Your original game save and demo balance are unchanged.');
  });
  updateGardenProgress();
  function updateMood(){
    document.querySelectorAll('[data-light]').forEach(b=>b.classList.toggle('active',b.dataset.light===lighting));
    document.getElementById('view-mode').textContent=(world==='garden'?'3D GARDEN':'3D HOME')+' · '+({day:'SUNNY DAY',sunset:'GOLDEN HOUR',night:'NIGHT LIGHTS'})[lighting];
  }
  updateMood();
  document.querySelectorAll('[data-light]').forEach(b=>b.addEventListener('click',()=>{
    lighting=b.dataset.light;
    if(savedState){gameLogic.setTimeOfDay(savedState,lighting);persist();}
    updateMood();
  }));
  function viewPreset(name){
    if(name==='wide')Object.assign(camera,{az:Math.PI*.28,el:.63,dist:16});
    else if(name==='close')Object.assign(camera,{az:Math.PI*.31,el:.37,dist:10.6});
    else Object.assign(camera,{az:Math.PI*.25,el:1.18,dist:17});
    document.querySelectorAll('[data-camera]').forEach(b=>b.classList.toggle('active',b.dataset.camera===name));
  }
  document.querySelectorAll('[data-camera]').forEach(b=>b.addEventListener('click',()=>viewPreset(b.dataset.camera)));
  document.getElementById('reset-view').addEventListener('click',()=>{travel=null;trail=[];Object.assign(player,world==='garden'?{x:0,z:4.45}:{x:1,z:2.3});viewPreset('wide');status.textContent='Camera and character reset.';});
  document.getElementById('zoom-in').addEventListener('click',()=>{camera.dist=clamp(camera.dist-1.5,7.5,22);});
  document.getElementById('zoom-out').addEventListener('click',()=>{camera.dist=clamp(camera.dist+1.5,7.5,22);});
  function move(dir){
    travel=null;trail=[];walking=true;
    const d=.55;
    const bounds=world==='garden'?[-5.4,5.4]:[-3.7,4.2];
    if(dir==='left')player.x=clamp(player.x-d,...bounds);
    if(dir==='right')player.x=clamp(player.x+d,...bounds);
    if(dir==='forward')player.z=clamp(player.z-d,...(world==='garden'?[-5.3,5.3]:[-3.5,4.1]));
    if(dir==='back')player.z=clamp(player.z+d,...(world==='garden'?[-5.3,5.3]:[-3.5,4.1]));
    step+=1.1;lastStepTime=performance.now();
    status.textContent='Exploring your 3D world · Use arrows or WASD to walk.';
  }
  document.querySelectorAll('[data-move]').forEach(b=>b.addEventListener('click',()=>{move(b.dataset.move);canvas.focus({preventScroll:true});}));
  document.addEventListener('keydown',(e)=>{
    if(['INPUT','SELECT','TEXTAREA','BUTTON'].includes(document.activeElement?.tagName))return;
    const map={ArrowUp:'forward',w:'forward',W:'forward',ArrowDown:'back',s:'back',S:'back',ArrowLeft:'left',a:'left',A:'left',ArrowRight:'right',d:'right',D:'right'};
    if(map[e.key]){e.preventDefault();move(map[e.key]);}
  });
  let drag=null;
  canvas.addEventListener('pointerdown',e=>{drag={id:e.pointerId,x:e.clientX,y:e.clientY};canvas.setPointerCapture(e.pointerId);});
  canvas.addEventListener('pointermove',e=>{if(!drag||drag.id!==e.pointerId)return;
    const dx=e.clientX-drag.x,dy=e.clientY-drag.y;
    camera.az+=dx*.008;camera.el=clamp(camera.el+dy*.005,.14,1.36);
    drag.x=e.clientX;drag.y=e.clientY;
  });
  function release(){drag=null;}
  canvas.addEventListener('pointerup',release);canvas.addEventListener('pointercancel',release);
  canvas.addEventListener('wheel',e=>{e.preventDefault();camera.dist=clamp(camera.dist+e.deltaY*.015,7.5,22);},{passive:false});
  window.addEventListener('storage',(e)=>{if(e.key===STORAGE_KEY){syncFromSave();updateMood();}});
  let last=0, lastSoftwareFrame=0;
  let lastStepTime=0;
  function softwareFrame(vertices,pv,bg,daylight,lightDir){
    const ctx=cpuCtx,w=canvas.width,h=canvas.height;
    ctx.fillStyle='rgb('+bg.map(x=>Math.round(x*255)).join(',')+')';
    ctx.fillRect(0,0,w,h);
    // Project the same actual 3D vertex mesh as the WebGL path into screen coordinates.
    // Back-to-front painter's ordering trades photorealism for offline compatibility.
    const polys=[];
    const light=norm(lightDir);
    for(let i=0;i<vertices.length;i+=27){
      const p=[];let visible=true;
      for(let j=0;j<3;j++){
        const k=i+j*9,x=vertices[k],y=vertices[k+1],z=vertices[k+2];
        const xp=pv[0]*x+pv[4]*y+pv[8]*z+pv[12];
        const yp=pv[1]*x+pv[5]*y+pv[9]*z+pv[13];
        const zp=pv[2]*x+pv[6]*y+pv[10]*z+pv[14];
        const wp=pv[3]*x+pv[7]*y+pv[11]*z+pv[15];
        if(wp<=.1){visible=false;break;}
        p.push({x:(xp/wp*.5+.5)*w,y:(1-(yp/wp*.5+.5))*h,z:zp/wp});
      }
      if(!visible)continue;
      if(p.every(q=>q.x<0)||p.every(q=>q.x>w)||p.every(q=>q.y<0)||p.every(q=>q.y>h))continue;
      const shade=.51+.42*Math.max(0,vertices[i+3]*light[0]+vertices[i+4]*light[1]+vertices[i+5]*light[2])*daylight;
      const r=Math.min(255,Math.round((vertices[i+6]*shade+.025)*255));
      const g=Math.min(255,Math.round((vertices[i+7]*shade+.035)*255));
      const b=Math.min(255,Math.round((vertices[i+8]*shade+.046)*255));
      polys.push({p,d:(p[0].z+p[1].z+p[2].z)/3,c:`rgb(${r},${g},${b})`});
    }
    polys.sort((a,b)=>b.d-a.d);
    for(const a of polys){ctx.beginPath();ctx.moveTo(a.p[0].x,a.p[0].y);ctx.lineTo(a.p[1].x,a.p[1].y);ctx.lineTo(a.p[2].x,a.p[2].y);ctx.closePath();ctx.fillStyle=a.c;ctx.fill();}
  }
  function draw(now){
    if(!gl && now-lastSoftwareFrame<125){requestAnimationFrame(draw);return;}
    lastSoftwareFrame=now;
    const dpr=gl?Math.min(1.5,window.devicePixelRatio||1):1;
    const width=Math.round(canvas.clientWidth*dpr),height=Math.round(canvas.clientHeight*dpr);
    if(canvas.width!==width||canvas.height!==height){canvas.width=width;canvas.height=height;}
    const bg=lighting==='day'?[.47,.67,.69]:lighting==='sunset'?[.64,.46,.44]:[.075,.12,.22];
    const eye=[camera.dist*Math.cos(camera.el)*Math.cos(camera.az),camera.dist*Math.sin(camera.el)+1.2,camera.dist*Math.cos(camera.el)*Math.sin(camera.az)];
    const pv=multiply(perspective(Math.PI/3,canvas.width/canvas.height,.2,90),lookAt(eye,camera.target));
    const sunlight=lighting==='night'?[.1,.9,.2]:lighting==='sunset'?[.7,.4,.8]:[.5,.9,.45];
    const day=lighting==='night'?.31:lighting==='sunset'?.76:1.0;
    const dt=Math.min(Math.max(0,now-last),80)/1000;last=now;
    if(travel){
      const next=worldLogic.moveTowards(player,travel,dt*2.35);
      if(next){player.x=next.x;player.z=next.z;walking=!next.arrived;
        if(next.arrived){const id=travel.activity;travel=null;if(id)completedVisit(id);
          if(trail.length)startGardenVisit(trail.shift());}}
    } else if(now-lastStepTime>350) walking=false;
    if(walking) step+=dt*11;

    const vertices=meshData(style,placed,lighting,step,player);
    if(gl){
      gl.viewport(0,0,canvas.width,canvas.height);gl.clearColor(...bg,1);gl.clear(gl.COLOR_BUFFER_BIT|gl.DEPTH_BUFFER_BIT);
      gl.uniformMatrix4fv(uniform.pv,false,new Float32Array(pv));
      gl.uniform3fv(uniform.sun,new Float32Array(sunlight));
      gl.uniform1f(uniform.day,day);gl.uniform3fv(uniform.fog,new Float32Array(bg));
      gl.bindBuffer(gl.ARRAY_BUFFER,vertexBuffer);gl.bufferData(gl.ARRAY_BUFFER,vertices,gl.DYNAMIC_DRAW);
      const stride=9*4;
      gl.vertexAttribPointer(attr.pos,3,gl.FLOAT,false,stride,0);
      gl.vertexAttribPointer(attr.normal,3,gl.FLOAT,false,stride,12);
      gl.vertexAttribPointer(attr.color,3,gl.FLOAT,false,stride,24);
      gl.drawArrays(gl.TRIANGLES,0,vertices.length/9);
    } else softwareFrame(vertices,pv,bg,day,sunlight);
    requestAnimationFrame(draw);
  }
  requestAnimationFrame(draw);
})();