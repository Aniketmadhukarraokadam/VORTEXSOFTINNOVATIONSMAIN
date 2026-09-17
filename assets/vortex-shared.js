document.addEventListener('DOMContentLoaded',()=>{const loader=document.getElementById('page-loader');if(!loader)return;setTimeout(()=>loader.classList.add('hide'),50);setTimeout(()=>loader.remove(),400);});let scrollTicking=false;window.addEventListener('scroll',()=>{if(!scrollTicking){window.requestAnimationFrame(()=>{const nav=document.getElementById('mainNavbar');if(nav)nav.classList.toggle('scrolled',window.scrollY>50);const st=document.getElementById('scrollTop');if(st)st.classList.toggle('show',window.scrollY>350);scrollTicking=false;});scrollTicking=true;}},{passive:true});const canObserve='IntersectionObserver'in window;const revealObserver=canObserve?new IntersectionObserver((entries)=>{entries.forEach(e=>{if(e.isIntersecting){e.target.classList.add('visible');revealObserver.unobserve(e.target);}});},{threshold:0.1,rootMargin:'0px 0px -30px 0px'}):null;function initReveal(){const elements=document.querySelectorAll('.scroll-reveal, .scroll-reveal-left, .scroll-reveal-right');if(!revealObserver){elements.forEach(el=>el.classList.add('visible'));return;}elements.forEach(el=>revealObserver.observe(el));}
document.readyState==='loading'?document.addEventListener('DOMContentLoaded',initReveal):initReveal();function animateCounter(el,target,duration){const start=performance.now();const startV=0;function step(now){const p=Math.min((now-start)/duration,1);const ease=1-Math.pow(1-p,3);el.textContent=Math.floor(startV+ease*(target-startV)).toLocaleString();if(p<1)requestAnimationFrame(step);else el.textContent=target.toLocaleString();}
requestAnimationFrame(step);}
const counterObserver=canObserve?new IntersectionObserver((entries)=>{entries.forEach(e=>{if(e.isIntersecting&&!e.target.dataset.counted){e.target.dataset.counted='true';const target=parseInt(e.target.dataset.target||e.target.textContent,10);if(!isNaN(target))animateCounter(e.target,target,2200);counterObserver.unobserve(e.target);}});},{threshold:0.5}):null;function initCounters(){document.querySelectorAll('[data-counter]').forEach(el=>{if(counterObserver)counterObserver.observe(el);});}
document.readyState==='loading'?document.addEventListener('DOMContentLoaded',initCounters):initCounters();function toggleFAQ(id){const item=document.getElementById(id);if(!item)return;const isOpen=item.classList.contains('open');document.querySelectorAll('.faq-item').forEach(f=>f.classList.remove('open'));if(!isOpen)item.classList.add('open');}
function _attachContactForm(formId){
    var form = document.getElementById(formId);
    if (!form || form._contactListenerAttached) return;
    form._contactListenerAttached = true;
    form.addEventListener('submit', function(e){
        e.preventDefault();
        var name = form.querySelector('#fullName')?.value.trim();
        var email = form.querySelector('#emailAddr')?.value.trim();
        var msg = form.querySelector('#msgText')?.value.trim();
        var feedback = document.getElementById('form-feedback');
        var btn = document.getElementById('submitBtn');
        if (!name || !email || !msg) {
            if (feedback) {
                feedback.className = 'mt-3 alert alert-danger';
                feedback.textContent = 'Please fill in all required fields.';
                feedback.classList.remove('d-none');
            }
            return;
        }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            if (feedback) {
                feedback.className = 'mt-3 alert alert-danger';
                feedback.textContent = 'Please enter a valid email address.';
                feedback.classList.remove('d-none');
            }
            return;
        }
        if (btn) {
            btn.dataset.original = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            btn.disabled = true;
        }
        var targetUrl = form.getAttribute('action') || 'api/contact.php';
        fetch(targetUrl, { method: 'POST', body: new FormData(form) })
            .then(function(r){ return r.json(); })
            .then(function(res){
                if (res && res.success) {
                    form.reset();
                    var cModal = document.getElementById('contactSuccessModal');
                    if (cModal && window.bootstrap) {
                        new bootstrap.Modal(cModal).show();
                        if (feedback) feedback.classList.add('d-none');
                    } else if (feedback) {
                        feedback.className = 'mt-3 alert alert-success';
                        feedback.innerHTML = '<i class="fas fa-check-circle me-2"></i>' + (res.message || 'Thank you! Your message has been sent.');
                        feedback.classList.remove('d-none');
                    }
                } else {
                    if (feedback) {
                        feedback.className = 'mt-3 alert alert-danger';
                        feedback.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>' + ((res && res.message) ? res.message : 'Error submitting form. Please try again.');
                        feedback.classList.remove('d-none');
                    }
                }
            })
            .catch(function(){
                if (feedback) {
                    feedback.className = 'mt-3 alert alert-danger';
                    feedback.textContent = 'Network error — please check your connection and try again, or email us at contact@vortexsoftinnovations.in';
                    feedback.classList.remove('d-none');
                }
            })
            .finally(function(){
                if (btn) {
                    btn.innerHTML = btn.dataset.original || '<i class="fas fa-paper-plane"></i> Send Message';
                    btn.disabled = false;
                }
            });
    });
}
function initContactForm(formId){if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',function(){_attachContactForm(formId);});}else{_attachContactForm(formId);}}
function initParticleCanvas(canvasId){
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    let W, H;
    let mouseX = 0, mouseY = 0;
    let targetMouseX = 0, targetMouseY = 0;

    function resize(){
        W = canvas.width = canvas.offsetWidth || window.innerWidth;
        H = canvas.height = canvas.offsetHeight || 620;
    }
    resize();
    window.addEventListener('resize', resize, {passive: true});

    window.addEventListener('mousemove', (e) => {
        const rect = canvas.getBoundingClientRect();
        targetMouseX = e.clientX - rect.left;
        targetMouseY = e.clientY - rect.top;
    }, {passive: true});

    // Elegant, ambient network particles across the full hero
    const count = Math.min(Math.floor((window.innerWidth * 500) / 11000), 75);
    const particles = [];
    const colors = ['#1C2280', '#2d35c4', '#5BA8D4', '#CC2228', '#10B981'];

    for (let i = 0; i < count; i++) {
        particles.push({
            x: Math.random() * (W || 1200),
            y: Math.random() * (H || 600),
            vx: (Math.random() - 0.5) * 0.45,
            vy: (Math.random() - 0.5) * 0.45,
            radius: Math.random() * 2.2 + 1.2,
            color: colors[i % colors.length],
            alpha: Math.random() * 0.45 + 0.25
        });
    }

    let lastTime = performance.now();
    function render(now) {
        const dt = Math.min((now - lastTime) / 1000, 0.1);
        lastTime = now;

        ctx.clearRect(0, 0, W, H);

        mouseX += (targetMouseX - mouseX) * 0.05;
        mouseY += (targetMouseY - mouseY) * 0.05;

        // Draw connecting constellation lines
        const maxDist = 110;
        for (let i = 0; i < particles.length; i++) {
            const p = particles[i];

            p.x += p.vx;
            p.y += p.vy;

            // Soft bounce off edges
            if (p.x < 0 || p.x > W) p.vx *= -1;
            if (p.y < 0 || p.y > H) p.vy *= -1;

            // Gentle repulsion from cursor
            const dx = p.x - mouseX;
            const dy = p.y - mouseY;
            const dist = Math.sqrt(dx * dx + dy * dy);
            if (dist < 120 && dist > 0) {
                const force = (120 - dist) / 120 * 0.6;
                p.x += (dx / dist) * force;
                p.y += (dy / dist) * force;
            }

            // Draw line to nearby particles
            for (let j = i + 1; j < particles.length; j++) {
                const p2 = particles[j];
                const dX = p.x - p2.x;
                const dY = p.y - p2.y;
                const d = Math.sqrt(dX * dX + dY * dY);
                if (d < maxDist) {
                    const lineAlpha = (1 - d / maxDist) * 0.18;
                    ctx.strokeStyle = `rgba(91, 168, 212, ${lineAlpha})`;
                    ctx.lineWidth = 0.8;
                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y);
                    ctx.lineTo(p2.x, p2.y);
                    ctx.stroke();
                }
            }

            // Draw glowing particle
            ctx.save();
            ctx.globalAlpha = p.alpha;
            ctx.fillStyle = p.color;
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
            ctx.fill();
            ctx.restore();
        }

        requestAnimationFrame(render);
    }
    requestAnimationFrame(render);
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
        .forEach(function(el) { if (revealObserver) revealObserver.observe(el); else el.classList.add('visible'); });
      initMagneticButtons();
    }
  };
  document.head.appendChild(script);
}
/* 3D Perspective Card Tilt Engine with Specular Glare */
function initTiltCards(){
    if(window.innerWidth < 992) return;
    const cards = document.querySelectorAll('.service-card, .whyus-card, .tilt-card-3d, .hero-card, [data-tilt-3d]');
    cards.forEach(card => {
        card.classList.add('tilt-card-3d');
        card.addEventListener('mousemove', e => {
            const rect = card.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width;
            const y = (e.clientY - rect.top) / rect.height;
            const rotX = (y - 0.5) * -16;
            const rotY = (x - 0.5) * 16;
            card.style.transform = `perspective(1000px) rotateX(${rotX.toFixed(2)}deg) rotateY(${rotY.toFixed(2)}deg) translateZ(12px) translateY(-6px)`;
            card.style.setProperty('--glare-x', `${(x * 100).toFixed(1)}%`);
            card.style.setProperty('--glare-y', `${(y * 100).toFixed(1)}%`);
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
            card.style.transition = 'transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
        });
        card.addEventListener('mouseenter', () => {
            card.style.transition = 'transform 0.1s ease-out';
        });
    });
}
document.readyState==='loading'?document.addEventListener('DOMContentLoaded',initTiltCards):initTiltCards();

/* 8D Multi-Axis Spatial Parallax Engine */
function initSpatialParallax(){
    const elements = document.querySelectorAll('[data-depth]');
    if(!elements.length || window.innerWidth < 768) return;
    let mouseX = 0, mouseY = 0, currentX = 0, currentY = 0;
    window.addEventListener('mousemove', (e) => {
        mouseX = (e.clientX - window.innerWidth / 2) / (window.innerWidth / 2);
        mouseY = (e.clientY - window.innerHeight / 2) / (window.innerHeight / 2);
    }, {passive: true});
    function tick(){
        currentX += (mouseX - currentX) * 0.08;
        currentY += (mouseY - currentY) * 0.08;
        elements.forEach(el => {
            const depth = parseFloat(el.getAttribute('data-depth') || '0.05');
            const moveX = currentX * depth * 55;
            const moveY = currentY * depth * 55;
            const rotate = currentX * depth * 12;
            el.style.transform = `translate3d(${moveX.toFixed(2)}px, ${moveY.toFixed(2)}px, 0) rotate(${rotate.toFixed(2)}deg)`;
        });
        requestAnimationFrame(tick);
    }
    tick();
}
document.readyState==='loading'?document.addEventListener('DOMContentLoaded',initSpatialParallax):initSpatialParallax();

/* ==========================================================================
   8D SPATIAL MOTION ENGINE (Service Nodes & 8D Gyroscopic Stage)
   ========================================================================== */
/* ==========================================================================
   8D SPATIAL MOTION ENGINE (Dynamic Laser Web & Service Constellation)
   ========================================================================== */
function init8DMotionEngine() {
    const stage = document.querySelector('.hero-stage-container');
    const core = document.querySelector('.vortex-8d-core');
    const nodes = document.querySelectorAll('.service-node-8d');
    const energyCanvas = document.getElementById('stageEnergyCanvas');
    if (!stage || !nodes.length) return;

    const eCtx = energyCanvas ? energyCanvas.getContext('2d') : null;
    let sW = 0, sH = 0;

    function resizeEnergyCanvas() {
        if (!energyCanvas || !stage) return;
        sW = energyCanvas.width = stage.offsetWidth || 500;
        sH = energyCanvas.height = stage.offsetHeight || 520;
    }
    resizeEnergyCanvas();
    window.addEventListener('resize', resizeEnergyCanvas, { passive: true });

    let mouseX = 0, mouseY = 0;
    let smoothMouseX = 0, smoothMouseY = 0;
    let lastMouseMoveTime = 0;
    let idleAutoAngle = 0;

    window.addEventListener('mousemove', (e) => {
        const cx = window.innerWidth / 2;
        const cy = window.innerHeight / 2;
        mouseX = (e.clientX - cx) / cx;
        mouseY = (e.clientY - cy) / cy;
        lastMouseMoveTime = Date.now();
    }, { passive: true });

    if (window.DeviceOrientationEvent) {
        window.addEventListener('deviceorientation', (e) => {
            if (e.gamma !== null && e.beta !== null) {
                mouseX = Math.min(Math.max(e.gamma / 25, -1), 1);
                mouseY = Math.min(Math.max(e.beta / 25, -1), 1);
                lastMouseMoveTime = Date.now();
            }
        }, { passive: true });
    }

    const nodeState = Array.from(nodes).map((el, i) => {
        const depthX = parseFloat(el.getAttribute('data-depth-x') || (0.05 + (i % 4) * 0.02).toFixed(2));
        const depthY = parseFloat(el.getAttribute('data-depth-y') || (0.05 + ((i + 2) % 4) * 0.02).toFixed(2));
        const depthZ = parseFloat(el.getAttribute('data-depth-z') || (15 + (i % 3) * 12));
        const phase = parseFloat(el.getAttribute('data-phase') || (i * (Math.PI * 2 / nodes.length)).toFixed(2));
        const freqX = 0.65 + (i % 3) * 0.2;
        const freqY = 0.55 + ((i + 1) % 3) * 0.18;
        const freqZ = 0.45 + ((i + 2) % 3) * 0.22;
        const color = el.style.getPropertyValue('--node-color') || '#1C2280';
        const isCentered = (el.style.left && el.style.left.includes('50%')) || el.style.transform.includes('translateX(-50%)');

        el.addEventListener('mouseenter', () => { el.classList.add('active-focus'); });
        el.addEventListener('mouseleave', () => { el.classList.remove('active-focus'); });

        el.addEventListener('click', () => {
            const svc = el.getAttribute('data-service');
            if (svc === 'hrms') {
                const aiProducts = document.getElementById('ai-products');
                if (aiProducts) {
                    aiProducts.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    return;
                }
            }
            const targetSection = document.getElementById('services') || document.getElementById('positioning');
            if (targetSection) {
                targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });

        return { el, depthX, depthY, depthZ, phase, freqX, freqY, freqZ, color, isCentered, currentX: 0, currentY: 0, baseCenterX: 0, baseCenterY: 0 };
    });

    function updateNodeAnchors() {
        if (!stage) return;
        const stageRect = stage.getBoundingClientRect();
        for (let i = 0; i < nodeState.length; i++) {
            const n = nodeState[i];
            const r = n.el.getBoundingClientRect();
            n.baseCenterX = (r.left - stageRect.left) + r.width * 0.5;
            n.baseCenterY = (r.top - stageRect.top) + r.height * 0.5;
        }
    }
    setTimeout(updateNodeAnchors, 100);
    window.addEventListener('resize', updateNodeAnchors, { passive: true });

    // Connect left-column service motion chips to right-column 3D nodes
    document.querySelectorAll('.motion-service-chip').forEach(chip => {
        const targetId = chip.getAttribute('data-target-service');
        if (!targetId) return;

        chip.addEventListener('mouseenter', () => {
            if (targetId === 'compliance') {
                if (core) {
                    core.style.boxShadow = '0 0 70px rgba(16,185,129,0.5), 0 0 110px rgba(28,34,128,0.4)';
                    core.style.borderColor = '#10B981';
                }
                return;
            }
            const targetNode = document.querySelector(`.service-node-8d[data-service="${targetId}"]`);
            if (targetNode) targetNode.classList.add('active-focus');
        });

        chip.addEventListener('mouseleave', () => {
            if (targetId === 'compliance') {
                if (core) {
                    core.style.boxShadow = '';
                    core.style.borderColor = '';
                }
                return;
            }
            const targetNode = document.querySelector(`.service-node-8d[data-service="${targetId}"]`);
            if (targetNode) targetNode.classList.remove('active-focus');
        });

        chip.addEventListener('click', () => {
            if (targetId === 'hrms') {
                const aiProducts = document.getElementById('ai-products');
                if (aiProducts) {
                    aiProducts.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    return;
                }
            }
            const targetSection = document.getElementById('services');
            if (targetSection) targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    let startTime = performance.now();

    function animate8D(now) {
        const t = (now - startTime) * 0.0015;

        // Detect idle (no mouse movement for 2 seconds) — gentle auto-rotation
        const isIdle = (Date.now() - lastMouseMoveTime) > 2000;
        if (isIdle) {
            idleAutoAngle += 0.005;
            const idleX = Math.sin(idleAutoAngle * 0.7) * 0.16;
            const idleY = Math.cos(idleAutoAngle * 0.5) * 0.11;
            mouseX += (idleX - mouseX) * 0.02;
            mouseY += (idleY - mouseY) * 0.02;
        }

        smoothMouseX += (mouseX - smoothMouseX) * 0.055;
        smoothMouseY += (mouseY - smoothMouseY) * 0.055;

        // 3D stage gyroscopic tilt
        if (stage && window.innerWidth >= 992) {
            const stageRotX = -smoothMouseY * 8;
            const stageRotY = smoothMouseX * 10;
            stage.style.transform = `perspective(1200px) rotateX(${stageRotX.toFixed(2)}deg) rotateY(${stageRotY.toFixed(2)}deg)`;
        }

        // Center Vortexsoft core 3D tilt
        if (core) {
            const coreRotX = -smoothMouseY * 14;
            const coreRotY = smoothMouseX * 18;
            const coreScale = 1 + Math.sin(t * 1.5) * 0.025;
            core.style.transform = `rotateX(${coreRotX.toFixed(2)}deg) rotateY(${coreRotY.toFixed(2)}deg) scale(${coreScale.toFixed(3)})`;
        }

        // Stage Energy Canvas: Refined laser beams & traveling photons
        if (eCtx && sW > 0 && sH > 0) {
            eCtx.clearRect(0, 0, sW, sH);
            const coreCenterX = sW * 0.5;
            const coreCenterY = sH * 0.5;
            const hasAnyFocus = nodeState.some(n => n.el.classList.contains('active-focus'));

            for (let i = 0; i < nodeState.length; i++) {
                const n = nodeState[i];
                const nodeCenterX = (n.baseCenterX || (sW * 0.5)) + (n.currentX || 0);
                const nodeCenterY = (n.baseCenterY || (sH * 0.5)) + (n.currentY || 0);

                const isFocused = n.el.classList.contains('active-focus');
                // When one node is focused, dim the others for clean visual focus
                const lineAlpha = isFocused ? 0.95 : (hasAnyFocus ? 0.05 : 0.10 + Math.sin(t * 1.6 + n.phase) * 0.04);

                // Parse hex color for rgba
                const rgb = n.color.startsWith('#') ?
                    [parseInt(n.color.slice(1,3),16), parseInt(n.color.slice(3,5),16), parseInt(n.color.slice(5,7),16)] :
                    [91, 168, 212];

                eCtx.save();
                eCtx.beginPath();
                eCtx.moveTo(coreCenterX, coreCenterY);
                eCtx.lineTo(nodeCenterX, nodeCenterY);
                eCtx.strokeStyle = `rgba(${rgb[0]},${rgb[1]},${rgb[2]},${lineAlpha})`;
                eCtx.lineWidth = isFocused ? 2.6 : 1.0;
                if (isFocused) {
                    eCtx.shadowColor = n.color;
                    eCtx.shadowBlur = 14;
                }
                eCtx.stroke();

                // Photon energy packet
                const pulseSpeed = isFocused ? 1.4 : 0.45;
                const rawProg = (t * pulseSpeed + (i / nodeState.length)) % 1;
                const pulseProg = 0.5 - Math.cos(rawProg * Math.PI) * 0.5;
                const photonX = coreCenterX + (nodeCenterX - coreCenterX) * pulseProg;
                const photonY = coreCenterY + (nodeCenterY - coreCenterY) * pulseProg;

                eCtx.beginPath();
                eCtx.arc(photonX, photonY, isFocused ? 4 : 2, 0, Math.PI * 2);
                eCtx.fillStyle = isFocused ? '#ffffff' : `rgba(${rgb[0]},${rgb[1]},${rgb[2]},${hasAnyFocus && !isFocused ? 0.2 : 0.85})`;
                if (isFocused) {
                    eCtx.shadowColor = '#ffffff';
                    eCtx.shadowBlur = 12;
                }
                eCtx.fill();
                eCtx.restore();
            }
        }

        // 8D Physics for each Service Node
        for (let i = 0; i < nodeState.length; i++) {
            const n = nodeState[i];
            const baseOffset = n.isCentered ? 'translateX(-50%) ' : '';

            if (n.el.classList.contains('active-focus')) {
                n.el.style.transform = `${baseOffset}translate3d(0, -6px, 45px) scale(1.06)`;
                continue;
            }

            // Gentle 8D floating motion clamped to ±12px
            const rawPosX = Math.cos(t * n.freqX + n.phase) * 7 + (smoothMouseX * n.depthX * 35);
            const rawPosY = Math.sin(t * n.freqY + n.phase) * 8 + (smoothMouseY * n.depthY * 35);
            const posX = Math.max(-14, Math.min(14, rawPosX));
            const posY = Math.max(-14, Math.min(14, rawPosY));
            n.currentX = posX;
            n.currentY = posY;
            const posZ = Math.sin(t * n.freqZ + n.phase) * 12 + n.depthZ + (-smoothMouseY * 10);
            const rotX = -smoothMouseY * 6 + Math.sin(t * 0.8 + n.phase) * 3;
            const rotY = smoothMouseX * 7 + Math.cos(t * 0.7 + n.phase) * 3;
            const rotZ = Math.sin(t * 0.5 + n.phase) * 1.8;
            const scale = 1 + Math.sin(t * 1.2 + n.phase) * 0.015;

            n.el.style.transform = `${baseOffset}translate3d(${posX.toFixed(2)}px, ${posY.toFixed(2)}px, ${posZ.toFixed(1)}px) rotateX(${rotX.toFixed(2)}deg) rotateY(${rotY.toFixed(2)}deg) rotateZ(${rotZ.toFixed(2)}deg) scale(${scale.toFixed(3)})`;
        }

        requestAnimationFrame(animate8D);
    }

    requestAnimationFrame(animate8D);
}
document.readyState==='loading'?document.addEventListener('DOMContentLoaded',init8DMotionEngine):init8DMotionEngine();
function initStaggeredReveal(){document.querySelectorAll('.row .scroll-reveal, .row .scroll-reveal-left, .row .scroll-reveal-right').forEach((el,i)=>{if(!el.style.transitionDelay){const delay=Math.min((i%4)*0.06,0.24);el.style.transitionDelay=delay+'s';}});}
document.readyState==='loading'?document.addEventListener('DOMContentLoaded',initStaggeredReveal):initStaggeredReveal();const skillObserver=canObserve?new IntersectionObserver(entries=>{entries.forEach(e=>{if(e.isIntersecting){e.target.querySelectorAll('.skill-fill').forEach(bar=>{bar.classList.add('animated');});skillObserver.unobserve(e.target);}});},{threshold:0.3}):null;document.querySelectorAll('.skill-bar-wrap').forEach(w=>{if(skillObserver)skillObserver.observe(w.closest('section')||w);else w.querySelectorAll('.skill-fill').forEach(bar=>bar.classList.add('animated'));});document.addEventListener('click',e=>{const btn=e.target.closest('.btn-primary-custom, .btn-cta-white, .btn-submit, .nav-cta');if(!btn)return;const circle=document.createElement('span');const diameter=Math.max(btn.clientWidth,btn.clientHeight);const rect=btn.getBoundingClientRect();circle.style.cssText=`position:absolute;border-radius:50%;width:${diameter}px;height:${diameter}px;left:${e.clientX - rect.left - diameter/2}px;top:${e.clientY - rect.top - diameter/2}px;background:rgba(255,255,255,0.28);transform:scale(0);animation:rippleClick 0.55s linear;pointer-events:none;`;btn.style.position='relative';btn.style.overflow='hidden';btn.appendChild(circle);setTimeout(()=>circle.remove(),560);});const rippleStyle=document.createElement('style');rippleStyle.textContent='@keyframes rippleClick{to{transform:scale(4);opacity:0;}}';document.head.appendChild(rippleStyle);
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
