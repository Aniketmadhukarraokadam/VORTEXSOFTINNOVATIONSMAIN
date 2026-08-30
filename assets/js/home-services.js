/**
 * VORTEXSOFT — HOMEPAGE SERVICE CARDS CANVAS ANIMATIONS (home-services.js)
 * 6 unique real-time canvas animations per service card.
 */
(function () {
  'use strict';

  function lerp(a, b, t) { return a + (b - a) * t; }
  function rand(min, max) { return Math.random() * (max - min) + min; }
  function randInt(min, max) { return Math.floor(rand(min, max + 1)); }

  function setupCanvas(canvas) {
    const dpr = window.devicePixelRatio || 1;
    const rect = canvas.parentElement.getBoundingClientRect();
    const w = rect.width || 340;
    const h = rect.height || 160;
    canvas.width = w * dpr;
    canvas.height = h * dpr;
    canvas.style.width = w + 'px';
    canvas.style.height = h + 'px';
    const ctx = canvas.getContext('2d');
    ctx.scale(dpr, dpr);
    return { ctx, w, h };
  }

  /* ── 1. HEALTHCARE BPO — ECG Pulse ── */
  function initHealthcareCanvas(canvas) {
    let { ctx, w, h } = setupCanvas(canvas);
    let t = 0, rafId;
    const icons = [
      { x: rand(20, w-20), y: rand(10, h-10), vx: rand(-0.15,0.15), vy: rand(-0.12,0.12), symbol: '+', size: 14, alpha: 0.4, color: '#CC2228' },
      { x: rand(20, w-20), y: rand(10, h-10), vx: rand(-0.15,0.15), vy: rand(-0.12,0.12), symbol: '♥', size: 12, alpha: 0.35, color: '#ff6b6b' },
      { x: rand(20, w-20), y: rand(10, h-10), vx: rand(-0.1,0.1),  vy: rand(-0.1,0.1),   symbol: '✚', size: 16, alpha: 0.3, color: '#CC2228' },
      { x: rand(20, w-20), y: rand(10, h-10), vx: rand(-0.2,0.2),  vy: rand(-0.1,0.1),   symbol: '●', size: 6,  alpha: 0.2, color: '#5BA8D4' },
    ];
    function ecg(x) {
      const period = w * 0.55;
      const phase = ((x + t * 0.6) % period) / period;
      if (phase < 0.25) return Math.sin(phase * Math.PI * 2) * 6;
      if (phase < 0.36) return Math.sin((phase-0.25)/0.11*Math.PI)*10;
      if (phase < 0.48) { const q=(phase-0.36)/0.12; if(q<0.2)return -q*40; if(q<0.5)return (q-0.2)/0.3*55-8; return (1-(q-0.5)/0.5)*30; }
      if (phase < 0.65) return Math.sin((phase-0.48)/0.17*Math.PI)*18;
      return 0;
    }
    function draw() {
      ctx.clearRect(0,0,w,h);
      ctx.strokeStyle='rgba(204,34,40,0.07)'; ctx.lineWidth=0.5;
      for(let gx=0;gx<w;gx+=20){ctx.beginPath();ctx.moveTo(gx,0);ctx.lineTo(gx,h);ctx.stroke();}
      for(let gy=0;gy<h;gy+=20){ctx.beginPath();ctx.moveTo(0,gy);ctx.lineTo(w,gy);ctx.stroke();}
      icons.forEach(ic=>{
        ic.x+=ic.vx; ic.y+=ic.vy;
        if(ic.x<5||ic.x>w-5)ic.vx*=-1; if(ic.y<5||ic.y>h-5)ic.vy*=-1;
        ctx.font=ic.size+'px sans-serif'; ctx.fillStyle=ic.color;
        ctx.globalAlpha=ic.alpha+Math.sin(t*0.03+ic.x)*0.1; ctx.fillText(ic.symbol,ic.x,ic.y);
      });
      ctx.globalAlpha=1;
      const midY=h*0.52;
      const gradient=ctx.createLinearGradient(0,0,w,0);
      gradient.addColorStop(0,'rgba(204,34,40,0)'); gradient.addColorStop(0.2,'rgba(204,34,40,0.9)');
      gradient.addColorStop(0.8,'rgba(91,168,212,0.9)'); gradient.addColorStop(1,'rgba(91,168,212,0)');
      ctx.shadowColor='#CC2228'; ctx.shadowBlur=12;
      ctx.beginPath(); ctx.lineWidth=2.2; ctx.strokeStyle=gradient;
      for(let x=0;x<w;x++){const y=midY-ecg(x); x===0?ctx.moveTo(x,y):ctx.lineTo(x,y);}
      ctx.stroke(); ctx.shadowBlur=0;
      const scanX=(t*0.6)%w, scanY=midY-ecg(scanX);
      ctx.beginPath(); ctx.arc(scanX,scanY,4,0,Math.PI*2);
      ctx.fillStyle='#ff4444'; ctx.shadowColor='#ff4444'; ctx.shadowBlur=12; ctx.fill(); ctx.shadowBlur=0;
      ctx.font='bold 9px Inter,sans-serif'; ctx.fillStyle='rgba(204,34,40,0.7)';
      ctx.fillText('LIVE VITALS MONITOR',10,h-8);
      t++; rafId=requestAnimationFrame(draw);
    }
    draw();
    new ResizeObserver(()=>{ cancelAnimationFrame(rafId); ({ctx,w,h}=setupCanvas(canvas)); draw(); }).observe(canvas.parentElement);
  }

  /* ── 2. AI & AUTOMATION — Neural Network ── */
  function initAICanvas(canvas) {
    let { ctx, w, h } = setupCanvas(canvas);
    let t=0, rafId;
    const layers=[3,5,4,3,2];
    let nodes=[], edges=[];
    function buildNetwork(){
      nodes=[]; edges=[];
      const margin=24, ls=(w-margin*2)/(layers.length-1);
      layers.forEach((count,li)=>{
        const x=margin+li*ls, ns=(h-20)/(count+1);
        for(let ni=0;ni<count;ni++) nodes.push({x,y:10+ns*(ni+1),layer:li,idx:ni,active:Math.random()>0.4});
      });
      for(let li=0;li<layers.length-1;li++){
        const from=nodes.filter(n=>n.layer===li), to=nodes.filter(n=>n.layer===li+1);
        from.forEach(f=>to.forEach(t2=>edges.push({from:f,to:t2,progress:rand(0,1),speed:rand(0.004,0.01),active:Math.random()>0.3})));
      }
    }
    buildNetwork();
    function draw(){
      ctx.clearRect(0,0,w,h);
      edges.forEach(e=>{
        if(!e.active)return;
        e.progress+=e.speed; if(e.progress>1)e.progress=0;
        const grad=ctx.createLinearGradient(e.from.x,e.from.y,e.to.x,e.to.y);
        grad.addColorStop(0,'rgba(28,34,128,0.15)'); grad.addColorStop(0.5,'rgba(91,168,212,0.3)'); grad.addColorStop(1,'rgba(28,34,128,0.15)');
        ctx.beginPath(); ctx.moveTo(e.from.x,e.from.y); ctx.lineTo(e.to.x,e.to.y);
        ctx.strokeStyle=grad; ctx.lineWidth=0.8; ctx.stroke();
        const px=lerp(e.from.x,e.to.x,e.progress), py=lerp(e.from.y,e.to.y,e.progress);
        ctx.beginPath(); ctx.arc(px,py,2,0,Math.PI*2);
        ctx.fillStyle='#5BA8D4'; ctx.shadowColor='#5BA8D4'; ctx.shadowBlur=8; ctx.fill(); ctx.shadowBlur=0;
      });
      nodes.forEach((n,i)=>{
        const pulse=0.5+0.5*Math.sin(t*0.04+i*0.9), r=5+pulse*2;
        const colors=['#1C2280','#5BA8D4','#CC2228','#1C2280','#10b981'];
        const col=colors[n.layer%colors.length];
        ctx.beginPath(); ctx.arc(n.x,n.y,r,0,Math.PI*2);
        ctx.fillStyle=col; ctx.shadowColor=col; ctx.shadowBlur=10; ctx.fill(); ctx.shadowBlur=0;
      });
      ctx.font='bold 9px Inter,sans-serif'; ctx.fillStyle='rgba(91,168,212,0.7)';
      ctx.fillText('NEURAL NET — LIVE INFERENCE',6,h-8);
      t++; rafId=requestAnimationFrame(draw);
    }
    draw();
    new ResizeObserver(()=>{ cancelAnimationFrame(rafId); ({ctx,w,h}=setupCanvas(canvas)); buildNetwork(); draw(); }).observe(canvas.parentElement);
  }

  /* ── 3. CUSTOM SOFTWARE — Code Matrix Terminal ── */
  function initSoftwareCanvas(canvas) {
    let { ctx, w, h } = setupCanvas(canvas);
    let t=0, rafId;
    const snippets=['const api = new VortexClient();','await pipeline.deploy();','db.query("SELECT * FROM ops");','function automate(task) {','  return agent.run(task);','}','BUILD ✓  DEPLOY ✓  LIVE','npm run build:prod','git push origin main','const ai = new LLMPipeline();'];
    const totalLines=Math.floor(h/14)+1;
    const lines=Array.from({length:totalLines},()=>({ text:snippets[randInt(0,snippets.length-1)], alpha:rand(0.15,0.6), speed:rand(0.2,0.5), y:rand(0,h), color:Math.random()>0.7?'#5BA8D4':Math.random()>0.5?'#10b981':'#1C2280' }));
    const cols=Math.floor(w/16);
    const drops=Array.from({length:cols},()=>rand(0,h/14));
    function draw(){
      ctx.fillStyle='rgba(8,11,26,0.15)'; ctx.fillRect(0,0,w,h);
      ctx.font='11px "Courier New",monospace';
      for(let c=0;c<drops.length;c++){
        const ch=String.fromCharCode(randInt(65,90));
        ctx.fillStyle=`rgba(91,168,212,${rand(0.05,0.25)})`; ctx.fillText(ch,c*16,drops[c]*14);
        if(drops[c]*14>h&&Math.random()>0.975)drops[c]=0; drops[c]+=0.18;
      }
      ctx.font='10px "Courier New",monospace';
      lines.forEach(ln=>{ ln.y+=ln.speed; if(ln.y>h+14){ln.y=-14;ln.text=snippets[randInt(0,snippets.length-1)];} ctx.globalAlpha=ln.alpha*(0.7+0.3*Math.sin(t*0.05)); ctx.fillStyle=ln.color; ctx.fillText(ln.text,rand(0,20),ln.y); });
      ctx.globalAlpha=1;
      if(Math.floor(t/30)%2===0){ctx.fillStyle='#10b981'; ctx.fillRect(8,h-22,8,14);}
      ctx.font='bold 9px Inter,sans-serif'; ctx.fillStyle='rgba(91,168,212,0.6)'; ctx.fillText('VORTEX BUILD ENGINE — RUNNING',20,h-8);
      t++; rafId=requestAnimationFrame(draw);
    }
    draw();
    new ResizeObserver(()=>{ cancelAnimationFrame(rafId); ({ctx,w,h}=setupCanvas(canvas)); draw(); }).observe(canvas.parentElement);
  }

  /* ── 4. PUBLISHING — Animated Document Layout ── */
  function initPublishingCanvas(canvas) {
    let { ctx, w, h } = setupCanvas(canvas);
    let t=0, rafId, scanY=0;
    const rows=5,cols2=3,bw=(w-20)/cols2,bh=(h-20)/rows;
    const blocks=[];
    for(let r=0;r<rows;r++) for(let c=0;c<cols2;c++) if(Math.random()>0.25) blocks.push({x:10+c*bw+2,y:10+r*bh+2,w:bw-4+rand(-bw*0.3,0),h:bh-4,type:Math.random()>0.6?'header':Math.random()>0.5?'image':'text',phase:rand(0,Math.PI*2),color:Math.random()>0.7?'rgba(139,92,246,0.5)':Math.random()>0.5?'rgba(28,34,128,0.3)':'rgba(91,168,212,0.25)'});
    function draw(){
      ctx.clearRect(0,0,w,h);
      ctx.fillStyle='rgba(248,250,252,0.05)'; ctx.fillRect(0,0,w,h);
      ctx.strokeStyle='rgba(139,92,246,0.1)'; ctx.lineWidth=0.5; ctx.strokeRect(6,6,w-12,h-12);
      scanY=(scanY+0.5)%h;
      const sg=ctx.createLinearGradient(0,scanY-12,0,scanY+12);
      sg.addColorStop(0,'rgba(139,92,246,0)'); sg.addColorStop(0.5,'rgba(139,92,246,0.12)'); sg.addColorStop(1,'rgba(139,92,246,0)');
      ctx.fillStyle=sg; ctx.fillRect(0,scanY-12,w,24);
      blocks.forEach(b=>{
        const glow=0.5+0.5*Math.sin(t*0.03+b.phase);
        if(b.type==='header'){ctx.fillStyle=`rgba(139,92,246,${0.25+glow*0.15})`; ctx.fillRect(b.x,b.y,b.w,10);}
        else if(b.type==='image'){ctx.strokeStyle=b.color; ctx.lineWidth=1; ctx.strokeRect(b.x,b.y,b.w,b.h); ctx.beginPath(); ctx.moveTo(b.x,b.y); ctx.lineTo(b.x+b.w,b.y+b.h); ctx.moveTo(b.x+b.w,b.y); ctx.lineTo(b.x,b.y+b.h); ctx.strokeStyle=`rgba(91,168,212,${0.15+glow*0.1})`; ctx.stroke();}
        else{const lc=Math.floor(b.h/7); for(let li=0;li<lc;li++){const lw=li===0?b.w:b.w*rand(0.5,0.95); ctx.fillStyle=b.color; ctx.globalAlpha=0.4+glow*0.3; ctx.fillRect(b.x,b.y+li*7,lw,4);} ctx.globalAlpha=1;}
      });
      ctx.font='bold 8px Inter,sans-serif'; ctx.fillStyle='rgba(139,92,246,0.7)'; ctx.fillText('ePUB3 · XML · WCAG 2.1 — LIVE',10,h-8);
      t++; rafId=requestAnimationFrame(draw);
    }
    draw();
    new ResizeObserver(()=>{ cancelAnimationFrame(rafId); ({ctx,w,h}=setupCanvas(canvas)); draw(); }).observe(canvas.parentElement);
  }

  /* ── 5. REAL ESTATE — 3D Rotating Wireframe Building ── */
  function initRealEstateCanvas(canvas) {
    let { ctx, w, h } = setupCanvas(canvas);
    let t=0, rafId, angle=0;
    function project(x,y,z){ const fov=220,p=fov/(fov+z); return {px:w/2+x*p,py:h/2+y*p,scale:p}; }
    const floors=5,floorH=28,bW=60,bD=40;
    function drawBuilding(){
      ctx.clearRect(0,0,w,h);
      angle+=0.008;
      ctx.strokeStyle='rgba(16,185,129,0.1)'; ctx.lineWidth=0.5;
      for(let gi=-3;gi<=3;gi++){
        const ga=gi*24,ca=Math.cos(angle),sa=Math.sin(angle);
        const p1=project(ga*ca+(-72)*sa,30,ga*(-sa)+(-72)*ca), p2=project(ga*ca+72*sa,30,ga*(-sa)+72*ca);
        ctx.beginPath(); ctx.moveTo(p1.px,p1.py); ctx.lineTo(p2.px,p2.py); ctx.stroke();
        const q1=project((-72)*ca+ga*sa,30,(-72)*(-sa)+ga*ca), q2=project(72*ca+ga*sa,30,72*(-sa)+ga*ca);
        ctx.beginPath(); ctx.moveTo(q1.px,q1.py); ctx.lineTo(q2.px,q2.py); ctx.stroke();
      }
      for(let f=0;f<floors;f++){
        const yBase=18+f*20,shrink=1-f*0.04,bw2=bW*shrink,bd2=bD*shrink;
        const c3=[[-bw2,-yBase-floorH,-bd2],[bw2,-yBase-floorH,-bd2],[bw2,-yBase-floorH,bd2],[-bw2,-yBase-floorH,bd2],[-bw2,-yBase,-bd2],[bw2,-yBase,-bd2],[bw2,-yBase,bd2],[-bw2,-yBase,bd2]];
        const ca=Math.cos(angle),sa=Math.sin(angle);
        const pts=c3.map(([x,y,z])=>{ const rx=x*ca-z*sa,rz=x*sa+z*ca; return project(rx,y,rz); });
        const alpha=0.3+(f/floors)*0.4, hue=f%2===0?'16,185,129':'91,168,212';
        [[4,5,6,7],[0,1,5,4],[1,2,6,5],[2,3,7,6],[3,0,4,7]].forEach((face,fi)=>{
          ctx.beginPath(); ctx.moveTo(pts[face[0]].px,pts[face[0]].py);
          for(let i=1;i<face.length;i++)ctx.lineTo(pts[face[i]].px,pts[face[i]].py);
          ctx.closePath(); ctx.strokeStyle=`rgba(${hue},${alpha+0.1})`; ctx.lineWidth=1; ctx.stroke();
          if(fi===0){ctx.fillStyle=`rgba(${hue},${0.04+f*0.015})`; ctx.fill();}
        });
      }
      const beaconY=-18-((t*1.2)%(floors*20));
      const bp=project(0,beaconY,0);
      ctx.beginPath(); ctx.arc(bp.px,bp.py,4,0,Math.PI*2);
      ctx.fillStyle='rgba(16,185,129,0.9)'; ctx.shadowColor='#10b981'; ctx.shadowBlur=12; ctx.fill(); ctx.shadowBlur=0;
      ctx.font='bold 9px Inter,sans-serif'; ctx.fillStyle='rgba(16,185,129,0.7)'; ctx.fillText('PROPERTY WIREFRAME — 3D',8,h-8);
      t++; rafId=requestAnimationFrame(drawBuilding);
    }
    drawBuilding();
    new ResizeObserver(()=>{ cancelAnimationFrame(rafId); ({ctx,w,h}=setupCanvas(canvas)); drawBuilding(); }).observe(canvas.parentElement);
  }

  /* ── 6. ENTERPRISE AI / PAYROLL — Data Pipeline + Gear ── */
  function initEnterpriseCanvas(canvas) {
    let { ctx, w, h } = setupCanvas(canvas);
    let t=0, rafId, gearAngle=0;
    function getStages(){ return [{label:'DATA\nINGEST',x:w*0.12,y:h/2,color:'#f59e0b',icon:'⬇'},{label:'AI\nPROCESS',x:w*0.35,y:h/2,color:'#5BA8D4',icon:'⚙'},{label:'ENRICH\n& SYNC',x:w*0.58,y:h/2,color:'#CC2228',icon:'↔'},{label:'PAYROLL\nOUT',x:w*0.82,y:h/2,color:'#10b981',icon:'✓'}]; }
    let stages=getStages();
    const particles=Array.from({length:14},(_,i)=>({progress:rand(0,1),speed:rand(0.003,0.006),lane:randInt(0,2),size:rand(2,4),color:stages[0].color}));
    function draw(){
      ctx.clearRect(0,0,w,h);
      gearAngle+=0.012;
      for(let i=0;i<20;i++){const bx=((i*37+t*0.2)%w),by=((i*19+t*0.1)%h); ctx.beginPath(); ctx.arc(bx,by,1,0,Math.PI*2); ctx.fillStyle='rgba(245,158,11,0.1)'; ctx.fill();}
      for(let si=0;si<stages.length-1;si++){
        const s=stages[si],e=stages[si+1];
        const grad=ctx.createLinearGradient(s.x,s.y,e.x,e.y);
        grad.addColorStop(0,s.color+'66'); grad.addColorStop(1,e.color+'66');
        ctx.beginPath(); ctx.moveTo(s.x+18,s.y); ctx.lineTo(e.x-18,e.y);
        ctx.strokeStyle=grad; ctx.lineWidth=5; ctx.lineCap='round'; ctx.stroke();
      }
      particles.forEach(p=>{
        p.progress+=p.speed; if(p.progress>1){p.progress=0; p.lane=randInt(0,stages.length-2); p.color=stages[p.lane].color;}
        const s=stages[p.lane],e=stages[p.lane+1];
        const px=lerp(s.x+18,e.x-18,p.progress), py=s.y+Math.sin(p.progress*Math.PI)*8;
        ctx.beginPath(); ctx.arc(px,py,p.size,0,Math.PI*2);
        ctx.fillStyle=p.color; ctx.shadowColor=p.color; ctx.shadowBlur=8; ctx.fill(); ctx.shadowBlur=0;
      });
      stages.forEach((s,si)=>{
        const pulse=0.7+0.3*Math.sin(t*0.05+si*1.2);
        ctx.beginPath(); ctx.arc(s.x,s.y,22*pulse,0,Math.PI*2); ctx.strokeStyle=s.color+'33'; ctx.lineWidth=3; ctx.stroke();
        ctx.beginPath(); ctx.arc(s.x,s.y,18,0,Math.PI*2); ctx.fillStyle=s.color+'22'; ctx.fill(); ctx.strokeStyle=s.color; ctx.lineWidth=2; ctx.stroke();
        if(si===1){
          ctx.save(); ctx.translate(s.x,s.y); ctx.rotate(gearAngle);
          for(let ti=0;ti<8;ti++){ const a=(ti/8)*Math.PI*2,ix=Math.cos(a)*12,iy=Math.sin(a)*12,ox=Math.cos(a)*20,oy=Math.sin(a)*20; ctx.beginPath(); ctx.moveTo(ix,iy); ctx.lineTo(ox,oy); ctx.strokeStyle=s.color+'99'; ctx.lineWidth=3; ctx.stroke(); }
          ctx.restore();
        }
        ctx.font='12px sans-serif'; ctx.fillStyle=s.color; ctx.textAlign='center'; ctx.fillText(s.icon,s.x,s.y+4);
        ctx.font='bold 7px Inter,sans-serif'; ctx.fillStyle=s.color; ctx.textAlign='center';
        s.label.split('\n').forEach((ln,li)=>ctx.fillText(ln,s.x,s.y+28+li*9));
      });
      ctx.textAlign='left'; ctx.font='bold 9px Inter,sans-serif'; ctx.fillStyle='rgba(245,158,11,0.7)';
      ctx.fillText('AI PAYROLL ENGINE — AUTO RUNNING',6,h-8);
      t++; rafId=requestAnimationFrame(draw);
    }
    draw();
    new ResizeObserver(()=>{ cancelAnimationFrame(rafId); ({ctx,w,h}=setupCanvas(canvas)); stages=getStages(); draw(); }).observe(canvas.parentElement);
  }

  /* ── Bootstrap ── */
  function init() {
    const map = { 'svc-canvas-healthcare':initHealthcareCanvas,'svc-canvas-ai':initAICanvas,'svc-canvas-software':initSoftwareCanvas,'svc-canvas-publishing':initPublishingCanvas,'svc-canvas-realestate':initRealEstateCanvas,'svc-canvas-enterprise':initEnterpriseCanvas };
    Object.entries(map).forEach(([id,fn])=>{ const el=document.getElementById(id); if(el)fn(el); });
  }
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',init); else init();
})();
