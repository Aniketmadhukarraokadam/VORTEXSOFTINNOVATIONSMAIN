document.addEventListener('DOMContentLoaded',()=>{const loader=document.getElementById('page-loader');if(!loader)return;setTimeout(()=>loader.classList.add('hide'),50);setTimeout(()=>loader.remove(),400);});let scrollTicking=false;window.addEventListener('scroll',()=>{if(!scrollTicking){window.requestAnimationFrame(()=>{const nav=document.getElementById('mainNavbar');if(nav)nav.classList.toggle('scrolled',window.scrollY>50);const st=document.getElementById('scrollTop');if(st)st.classList.toggle('show',window.scrollY>350);scrollTicking=false;});scrollTicking=true;}},{passive:true});const revealObserver=new IntersectionObserver((entries)=>{entries.forEach(e=>{if(e.isIntersecting){e.target.classList.add('visible');revealObserver.unobserve(e.target);}});},{threshold:0.1,rootMargin:'0px 0px -30px 0px'});function initReveal(){document.querySelectorAll('.scroll-reveal, .scroll-reveal-left, .scroll-reveal-right').forEach(el=>revealObserver.observe(el));}
document.readyState==='loading'?document.addEventListener('DOMContentLoaded',initReveal):initReveal();function animateCounter(el,target,duration){const start=performance.now();const startV=0;function step(now){const p=Math.min((now-start)/duration,1);const ease=1-Math.pow(1-p,3);el.textContent=Math.floor(startV+ease*(target-startV)).toLocaleString();if(p<1)requestAnimationFrame(step);else el.textContent=target.toLocaleString();}
requestAnimationFrame(step);}
const counterObserver=new IntersectionObserver((entries)=>{entries.forEach(e=>{if(e.isIntersecting&&!e.target.dataset.counted){e.target.dataset.counted='true';const target=parseInt(e.target.dataset.target||e.target.textContent,10);if(!isNaN(target))animateCounter(e.target,target,2200);counterObserver.unobserve(e.target);}});},{threshold:0.5});function initCounters(){document.querySelectorAll('[data-counter]').forEach(el=>{counterObserver.observe(el);});}
document.readyState==='loading'?document.addEventListener('DOMContentLoaded',initCounters):initCounters();function toggleFAQ(id){const item=document.getElementById(id);if(!item)return;const isOpen=item.classList.contains('open');document.querySelectorAll('.faq-item').forEach(f=>f.classList.remove('open'));if(!isOpen)item.classList.add('open');}
function _attachContactForm(formId){var form=document.getElementById(formId);if(!form||form._contactListenerAttached)return;form._contactListenerAttached=true;form.addEventListener('submit',function(e){e.preventDefault();var name=form.querySelector('#fullName')?.value.trim();var email=form.querySelector('#emailAddr')?.value.trim();var msg=form.querySelector('#msgText')?.value.trim();var feedback=document.getElementById('form-feedback');var btn=document.getElementById('submitBtn');if(!name||!email||!msg){if(feedback){feedback.className='mt-3 alert alert-danger';feedback.textContent='Please fill in all required fields.';feedback.classList.remove('d-none');}return;}if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){if(feedback){feedback.className='mt-3 alert alert-danger';feedback.textContent='Please enter a valid email address.';feedback.classList.remove('d-none');}return;}if(btn){btn.dataset.original=btn.innerHTML;btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Sending...';btn.disabled=true;}
fetch(form.action,{method:'POST',body:new FormData(form),mode:'no-cors'}).then(function(){form.reset();setTimeout(function(){try{var cModal=document.getElementById('contactSuccessModal');if(cModal&&window.bootstrap){new bootstrap.Modal(cModal).show();if(feedback)feedback.classList.add('d-none');}else if(feedback){feedback.className='mt-3 alert alert-success';feedback.innerHTML='<i class="fas fa-check-circle me-2"></i>Thank you! Your message has been sent. Our team will reply within 24 hours.';feedback.classList.remove('d-none');}}catch(err){if(feedback){feedback.className='mt-3 alert alert-success';feedback.innerHTML='<i class="fas fa-check-circle me-2"></i>Thank you! Your message has been sent. Our team will reply within 24 hours.';feedback.classList.remove('d-none');}}},380);}).catch(function(){if(feedback){feedback.className='mt-3 alert alert-danger';feedback.textContent='Network error — please check your connection and try again, or email us at contact@vortexsoftinnovations.in';feedback.classList.remove('d-none');}}).finally(function(){if(btn){btn.innerHTML=btn.dataset.original;btn.disabled=false;}});})}
function initContactForm(formId){if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',function(){_attachContactForm(formId);});}else{_attachContactForm(formId);}}
function initParticleCanvas(canvasId){
    const canvas=document.getElementById(canvasId);
    if(!canvas)return;
    const ctx=canvas.getContext('2d');
    const COLORS=['#1C2280','#2d35c4','#5BA8D4','#87CEEB','#CC2228','#e63940'];
    let W,H,particles=[];
    let cx, cy;

    function resize(){
        W=canvas.width=canvas.offsetWidth || 300;
        H=canvas.height=canvas.offsetHeight || 300;
        // Move cyclone slightly to the right so it balances text on the left
        cx=W*0.65;
        cy=H*0.5;
    }
    resize();
    window.addEventListener('resize',()=>{resize();spawnParticles();},{passive:true});

    class VortexParticle{
        constructor(){this.reset(true);}
        reset(init){
            this.angle=Math.random()*Math.PI*2;
            const maxRad = Math.max(W, H, 100) * 0.8;
            this.radius=init ? Math.random()*maxRad : maxRad;
            // The cyclone creates a tornado shape in 3D
            this.yOffset=(Math.random()-0.5)*H*0.8 * (this.radius / Math.max(maxRad, 1)); 
            this.size=Math.random()*3+1;
            this.baseSpeed=Math.random()*0.02 + 0.005;
            this.color=COLORS[Math.floor(Math.random()*COLORS.length)];
            this.inwardSpeed=Math.random()*1.5+0.5;
            this.pullPeriod=Math.random()*100;
            this.prevX = cx + Math.cos(this.angle) * this.radius;
            this.prevY = cy + Math.sin(this.angle) * this.radius * 0.35 + this.yOffset;
        }
        update(){
            // Save previous pos for trail
            this.prevX = cx + Math.cos(this.angle) * this.radius;
            this.prevY = cy + Math.sin(this.angle) * this.radius * 0.35 + this.yOffset;
            
            // Swirl math: accelerate as radius decreases
            const speedMultiplier = Math.max(0.5, 300 / (this.radius + 10));
            this.angle += this.baseSpeed * speedMultiplier;
            this.radius -= this.inwardSpeed * (speedMultiplier * 0.5);
            
            // Vertical undulating
            this.yOffset += Math.sin(this.pullPeriod) * 1.2;
            this.pullPeriod += 0.03;

            if(this.radius <= 10){
                this.reset(false);
            }
        }
        draw(){
            const tilt = 0.35; // 3D flatten
            const x = cx + Math.cos(this.angle) * this.radius;
            const y = cy + Math.sin(this.angle) * this.radius * tilt + this.yOffset;
            
            // Opacity math
            const distRatio = this.radius / (Math.max(W,H) * 0.5);
            let alpha = 1.2 - distRatio;
            if(this.radius < 60) alpha *= this.radius / 60; // fade into the center hole
            if(alpha < 0) alpha = 0;
            if(alpha > 1) alpha = 1;

            ctx.save();
            ctx.globalAlpha = alpha * 0.9;
            ctx.strokeStyle = this.color;
            ctx.lineWidth = Math.max(0.5, this.size * (1 - distRatio * 0.4));
            ctx.lineCap = 'round';
            ctx.beginPath();
            ctx.moveTo(this.prevX, this.prevY);
            ctx.lineTo(x, y);
            ctx.stroke();
            
            // Add a glowing ball at the tip for a comet effect
            if(Math.random() > 0.9) {
                ctx.fillStyle = this.color;
                ctx.beginPath();
                ctx.arc(x, y, ctx.lineWidth * 0.8, 0, Math.PI*2);
                ctx.fill();
            }
            ctx.restore();
        }
    }

    function spawnParticles(){
        const count=Math.min(Math.floor((W*H)/9000), 120);
        particles=Array.from({length:count},()=>new VortexParticle());
    }
    spawnParticles();

    function render(){
        ctx.clearRect(0,0,W,H);
        
        // ctx.globalCompositeOperation = 'lighter'; // Makes overlapping layers glow like energy
        particles.forEach(p=>{p.update();p.draw();});
        ctx.globalCompositeOperation = 'source-over';
        
        requestAnimationFrame(render);
    }
    render();
}
function initMagneticButtons(){document.querySelectorAll('.magnetic').forEach(btn=>{btn.addEventListener('mousemove',e=>{const rect=btn.getBoundingClientRect();const x=e.clientX-rect.left-rect.width/2;const y=e.clientY-rect.top-rect.height/2;btn.style.transform=`translate(${x * 0.18}px, ${y * 0.18}px)`;});btn.addEventListener('mouseleave',()=>{btn.style.transform='';btn.style.transition='transform 0.5s cubic-bezier(0.4,0,0.2,1)';});btn.addEventListener('mouseenter',()=>{btn.style.transition='transform 0.1s ease';});});}
document.readyState==='loading'?document.addEventListener('DOMContentLoaded',initMagneticButtons):initMagneticButtons();function initTyped(el,words,speed){if(!el)return;let wi=0,ci=0,deleting=false,waiting=false;function tick(){if(waiting)return;const word=words[wi];if(!deleting){el.textContent=word.slice(0,ci+1);ci++;if(ci===word.length){waiting=true;setTimeout(()=>{waiting=false;deleting=true;},2000);setTimeout(tick,2100);return;}}else{el.textContent=word.slice(0,ci-1);ci--;if(ci===0){deleting=false;wi=(wi+1)%words.length;}}
setTimeout(tick,deleting?speed/2.5:speed);}
tick();}
function injectNavbar(rootPrefix) {
  const p = rootPrefix || './';
  const script = document.createElement('script');
  script.src = p + 'assets/partials/header.js';
  script.onload = function() {
    const headerEl = document.getElementById('site-header');
    if (headerEl && typeof VORTEX_HEADER_TEMPLATE !== 'undefined') {
      headerEl.innerHTML = VORTEX_HEADER_TEMPLATE.replace(/\{\{PREFIX\}\}/g, p);
      const path = window.location.pathname;
      headerEl.querySelectorAll('#mainNavbar .nav-link').forEach(function(a) {
        const href = a.getAttribute('href');
        if (href && path.endsWith(href.replace(/^\.\.\//, ''))) {
          a.classList.add('active');
        }
      });
      initMagneticButtons();
    }
  };
  document.head.appendChild(script);
}
function injectFooter(rootPrefix) {
  const p = rootPrefix || './';
  const script = document.createElement('script');
  script.src = p + 'assets/partials/footer.js';
  script.onload = function() {
    const footerEl = document.getElementById('site-footer');
    if (footerEl && typeof VORTEX_FOOTER_TEMPLATE !== 'undefined') {
      footerEl.innerHTML = VORTEX_FOOTER_TEMPLATE.replace(/\{\{PREFIX\}\}/g, p);
      footerEl.querySelectorAll('.scroll-reveal, .scroll-reveal-left, .scroll-reveal-right')
        .forEach(function(el) { revealObserver.observe(el); });
      initMagneticButtons();
    }
  };
  document.head.appendChild(script);
}
function initTiltCards(){if(window.innerWidth<992)return;document.querySelectorAll('.service-card').forEach(card=>{card.addEventListener('mousemove',e=>{const rect=card.getBoundingClientRect();const x=(e.clientX-rect.left)/rect.width-0.5;const y=(e.clientY-rect.top)/rect.height-0.5;card.style.transform=`perspective(600px) rotateY(${x * 8}deg) rotateX(${-y * 8}deg) translateY(-8px)`;});card.addEventListener('mouseleave',()=>{card.style.transform='';card.style.transition='transform 0.5s cubic-bezier(0.4,0,0.2,1)';});card.addEventListener('mouseenter',()=>{card.style.transition='transform 0.12s ease';});});}
document.readyState==='loading'?document.addEventListener('DOMContentLoaded',initTiltCards):initTiltCards();function initStaggeredReveal(){document.querySelectorAll('.row .scroll-reveal, .row .scroll-reveal-left, .row .scroll-reveal-right').forEach((el,i)=>{if(!el.style.transitionDelay){const delay=Math.min((i%4)*0.06,0.24);el.style.transitionDelay=delay+'s';}});}
document.readyState==='loading'?document.addEventListener('DOMContentLoaded',initStaggeredReveal):initStaggeredReveal();const skillObserver=new IntersectionObserver(entries=>{entries.forEach(e=>{if(e.isIntersecting){e.target.querySelectorAll('.skill-fill').forEach(bar=>{bar.classList.add('animated');});skillObserver.unobserve(e.target);}});},{threshold:0.3});document.querySelectorAll('.skill-bar-wrap').forEach(w=>skillObserver.observe(w.closest('section')||w));document.addEventListener('click',e=>{const btn=e.target.closest('.btn-primary-custom, .btn-cta-white, .btn-submit, .nav-cta');if(!btn)return;const circle=document.createElement('span');const diameter=Math.max(btn.clientWidth,btn.clientHeight);const rect=btn.getBoundingClientRect();circle.style.cssText=`position:absolute;border-radius:50%;width:${diameter}px;height:${diameter}px;left:${e.clientX - rect.left - diameter/2}px;top:${e.clientY - rect.top - diameter/2}px;background:rgba(255,255,255,0.28);transform:scale(0);animation:rippleClick 0.55s linear;pointer-events:none;`;btn.style.position='relative';btn.style.overflow='hidden';btn.appendChild(circle);setTimeout(()=>circle.remove(),560);});const rippleStyle=document.createElement('style');rippleStyle.textContent='@keyframes rippleClick{to{transform:scale(4);opacity:0;}}';document.head.appendChild(rippleStyle);
// ── SERVICE WORKER REGISTRATION ────────────────────────────
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    // Determine root path to sw.js regardless of page depth
    const depth = window.location.pathname.split('/').filter(Boolean).length;
    const swPath = depth > 1 ? '../'.repeat(depth - 1) + 'sw.js' : '/sw.js';
    navigator.serviceWorker.register(swPath, { scope: '/' })
      .catch(() => {});
  });
}
