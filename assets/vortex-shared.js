document.addEventListener('DOMContentLoaded',()=>{const loader=document.getElementById('page-loader');if(!loader)return;setTimeout(()=>loader.classList.add('hide'),50);setTimeout(()=>loader.remove(),400);});let scrollTicking=false;window.addEventListener('scroll',()=>{if(!scrollTicking){window.requestAnimationFrame(()=>{const nav=document.getElementById('mainNavbar');if(nav)nav.classList.toggle('scrolled',window.scrollY>50);const st=document.getElementById('scrollTop');if(st)st.classList.toggle('show',window.scrollY>350);scrollTicking=false;});scrollTicking=true;}},{passive:true});const canObserve='IntersectionObserver'in window;const revealObserver=canObserve?new IntersectionObserver((entries)=>{entries.forEach(e=>{if(e.isIntersecting){e.target.classList.add('visible');revealObserver.unobserve(e.target);}});},{threshold:0.1,rootMargin:'0px 0px -30px 0px'}):null;function initReveal(){const elements=document.querySelectorAll('.scroll-reveal, .scroll-reveal-left, .scroll-reveal-right');if(!revealObserver){elements.forEach(el=>el.classList.add('visible'));return;}elements.forEach(el=>revealObserver.observe(el));}
document.readyState==='loading'?document.addEventListener('DOMContentLoaded',initReveal):initReveal();function animateCounter(el,target,duration){const start=performance.now();const startV=0;function step(now){const p=Math.min((now-start)/duration,1);const ease=1-Math.pow(1-p,3);el.textContent=Math.floor(startV+ease*(target-startV)).toLocaleString();if(p<1)requestAnimationFrame(step);else el.textContent=target.toLocaleString();}
requestAnimationFrame(step);}
const counterObserver=canObserve?new IntersectionObserver((entries)=>{entries.forEach(e=>{if(e.isIntersecting&&!e.target.dataset.counted){e.target.dataset.counted='true';const target=parseInt(e.target.dataset.target||e.target.textContent,10);if(!isNaN(target))animateCounter(e.target,target,2200);counterObserver.unobserve(e.target);}});},{threshold:0.5}):null;function initCounters(){document.querySelectorAll('[data-counter]').forEach(el=>{if(counterObserver)counterObserver.observe(el);});}
document.readyState==='loading'?document.addEventListener('DOMContentLoaded',initCounters):initCounters();function toggleFAQ(id){const item=document.getElementById(id);if(!item)return;const isOpen=item.classList.contains('open');document.querySelectorAll('.faq-item').forEach(f=>f.classList.remove('open'));if(!isOpen)item.classList.add('open');}
function _attachContactForm(formId){var form=document.getElementById(formId);if(!form||form._contactListenerAttached)return;form._contactListenerAttached=true;form.addEventListener('submit',function(e){e.preventDefault();var name=form.querySelector('#fullName')?.value.trim();var email=form.querySelector('#emailAddr')?.value.trim();var msg=form.querySelector('#msgText')?.value.trim();var feedback=document.getElementById('form-feedback');var btn=document.getElementById('submitBtn');if(!name||!email||!msg){if(feedback){feedback.className='mt-3 alert alert-danger';feedback.textContent='Please fill in all required fields.';feedback.classList.remove('d-none');}return;}if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){if(feedback){feedback.className='mt-3 alert alert-danger';feedback.textContent='Please enter a valid email address.';feedback.classList.remove('d-none');}return;}if(btn){btn.dataset.original=btn.innerHTML;btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Sending...';btn.disabled=true;}
fetch(form.action,{method:'POST',body:new FormData(form),mode:'no-cors'}).then(function(){form.reset();setTimeout(function(){try{var cModal=document.getElementById('contactSuccessModal');if(cModal&&window.bootstrap){new bootstrap.Modal(cModal).show();if(feedback)feedback.classList.add('d-none');}else if(feedback){feedback.className='mt-3 alert alert-success';feedback.innerHTML='<i class="fas fa-check-circle me-2"></i>Thank you! Your message has been sent. Our team will reply within 24 hours.';feedback.classList.remove('d-none');}}catch(err){if(feedback){feedback.className='mt-3 alert alert-success';feedback.innerHTML='<i class="fas fa-check-circle me-2"></i>Thank you! Your message has been sent. Our team will reply within 24 hours.';feedback.classList.remove('d-none');}}},380);}).catch(function(){if(feedback){feedback.className='mt-3 alert alert-danger';feedback.textContent='Network error — please check your connection and try again, or email us at contact@vortexsoftinnovations.in';feedback.classList.remove('d-none');}}).finally(function(){if(btn){btn.innerHTML=btn.dataset.original;btn.disabled=false;}});})}
function initContactForm(formId){if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',function(){_attachContactForm(formId);});}else{_attachContactForm(formId);}}
function initParticleCanvas(canvasId){
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    let W, H;
    let targetRotX = 0, targetRotY = 0;
    let rotX = 0, rotY = 0;

    function resize(){
        W = canvas.width = canvas.offsetWidth || window.innerWidth;
        H = canvas.height = canvas.offsetHeight || 600;
    }
    resize();
    window.addEventListener('resize', resize, {passive: true});

    // Track mouse for 3D camera rotation and 8D spatial depth
    window.addEventListener('mousemove', (e) => {
        const cx = window.innerWidth / 2;
        const cy = window.innerHeight / 2;
        targetRotY = (e.clientX - cx) / cx * 0.8;
        targetRotX = -(e.clientY - cy) / cy * 0.5;
    }, {passive: true});

    // 3D Particles on Sphere Surface & Orbital Rings
    const POINT_COUNT = Math.min(Math.floor((window.innerWidth * 600) / 7500), 160);
    const points = [];
    const sphereRadius = Math.min(W * 0.28, 240);

    for (let i = 0; i < POINT_COUNT; i++) {
        const phi = Math.acos(-1 + (2 * i) / POINT_COUNT);
        const theta = Math.sqrt(POINT_COUNT * Math.PI) * phi;
        const rad = sphereRadius * (0.8 + Math.random() * 0.4);
        points.push({
            x: rad * Math.cos(theta) * Math.sin(phi),
            y: rad * Math.sin(theta) * Math.sin(phi),
            z: rad * Math.cos(phi),
            color: (i % 3 === 0) ? '#CC2228' : ((i % 2 === 0) ? '#5BA8D4' : '#1C2280'),
            size: Math.random() * 2.5 + 1.2
        });
    }

    // Technology Anchor Nodes in 3D (All 9 Core Service Pillars including Custom Web Dev & Compliance)
    const TECH_NODES = [
        { label: 'AI Agentic Workflows', angle: 0, r: sphereRadius * 1.25, color: '#CC2228' },
        { label: 'Healthcare BPO & RCM', angle: (Math.PI * 2) / 9, r: sphereRadius * 1.34, color: '#0284C7' },
        { label: 'Custom Web Development', angle: (Math.PI * 4) / 9, r: sphereRadius * 1.28, color: '#6366F1' },
        { label: 'Enterprise IT & Cloud', angle: (Math.PI * 6) / 9, r: sphereRadius * 1.22, color: '#1C2280' },
        { label: 'Company Compliance (ISO/HIPAA)', angle: (Math.PI * 8) / 9, r: sphereRadius * 1.32, color: '#10B981' },
        { label: 'Data Annotation & AI', angle: (Math.PI * 10) / 9, r: sphereRadius * 1.38, color: '#F59E0B' },
        { label: 'Publishing Services', angle: (Math.PI * 12) / 9, r: sphereRadius * 1.26, color: '#8B5CF6' },
        { label: 'Real Estate & Title', angle: (Math.PI * 14) / 9, r: sphereRadius * 1.34, color: '#06B6D4' },
        { label: '24/7 Global BPO Pods', angle: (Math.PI * 16) / 9, r: sphereRadius * 1.24, color: '#EC4899' }
    ];

    let autoAngle = 0;
    const FOCAL_LENGTH = 450;

    function project(x, y, z, centerX, centerY) {
        const perspective = FOCAL_LENGTH / (FOCAL_LENGTH + z + sphereRadius * 1.5);
        return {
            px: centerX + x * perspective,
            py: centerY + y * perspective,
            scale: perspective,
            alpha: Math.max(0.12, Math.min(1, (z + sphereRadius) / (sphereRadius * 2) + 0.15))
        };
    }

    function render() {
        ctx.clearRect(0, 0, W, H);
        
        rotX += (targetRotX - rotX) * 0.06;
        rotY += (targetRotY - rotY) * 0.06;
        autoAngle += 0.007;

        const centerX = (W > 991) ? W * 0.72 : W * 0.5;
        const centerY = H * 0.5;

        const cosY = Math.cos(rotY + autoAngle);
        const sinY = Math.sin(rotY + autoAngle);
        const cosX = Math.cos(rotX);
        const sinX = Math.sin(rotX);

        const projectedPoints = [];
        for (let i = 0; i < points.length; i++) {
            const p = points[i];
            const x1 = p.x * cosY - p.z * sinY;
            const z1 = p.z * cosY + p.x * sinY;
            const y2 = p.y * cosX - z1 * sinX;
            const z2 = z1 * cosX + p.y * sinX;

            const proj = project(x1, y2, z2, centerX, centerY);
            projectedPoints.push({
                x: proj.px,
                y: proj.py,
                z: z2,
                scale: proj.scale,
                alpha: proj.alpha,
                color: p.color,
                size: p.size
            });
        }

        ctx.lineWidth = 0.75;
        const maxDist = 65;
        for (let i = 0; i < projectedPoints.length; i++) {
            const pi = projectedPoints[i];
            for (let j = i + 1; j < projectedPoints.length; j++) {
                const pj = projectedPoints[j];
                const dx = pi.x - pj.x;
                const dy = pi.y - pj.y;
                const d = Math.sqrt(dx * dx + dy * dy);
                if (d < maxDist) {
                    const lineAlpha = (1 - d / maxDist) * Math.min(pi.alpha, pj.alpha) * 0.45;
                    ctx.strokeStyle = `rgba(91, 168, 212, ${lineAlpha})`;
                    ctx.beginPath();
                    ctx.moveTo(pi.x, pi.y);
                    ctx.lineTo(pj.x, pj.y);
                    ctx.stroke();
                }
            }
        }

        for (let i = 0; i < projectedPoints.length; i++) {
            const p = projectedPoints[i];
            ctx.save();
            ctx.globalAlpha = p.alpha;
            ctx.fillStyle = p.color;
            ctx.beginPath();
            ctx.arc(p.x, p.y, Math.max(1, p.size * p.scale), 0, Math.PI * 2);
            ctx.fill();
            ctx.restore();
        }

        for (let k = 0; k < TECH_NODES.length; k++) {
            const node = TECH_NODES[k];
            const angle = node.angle + autoAngle * 1.4;
            const nx = node.r * Math.cos(angle);
            const ny = Math.sin(angle * 2) * 40;
            const nz = node.r * Math.sin(angle);

            const x1 = nx * cosY - nz * sinY;
            const z1 = nz * cosY + nx * sinY;
            const y2 = ny * cosX - z1 * sinX;
            const z2 = z1 * cosX + ny * sinX;

            const proj = project(x1, y2, z2, centerX, centerY);

            if (proj.scale > 0.4) {
                ctx.save();
                ctx.globalAlpha = Math.max(0.35, proj.alpha);

                ctx.strokeStyle = node.color;
                ctx.lineWidth = 2 * proj.scale;
                ctx.beginPath();
                ctx.arc(proj.px, proj.py, 10 * proj.scale, 0, Math.PI * 2);
                ctx.stroke();

                ctx.fillStyle = node.color;
                ctx.beginPath();
                ctx.arc(proj.px, proj.py, 4 * proj.scale, 0, Math.PI * 2);
                ctx.fill();

                if (z2 > -50 && W > 768) {
                    ctx.font = `600 ${Math.round(11 * proj.scale)}px 'Poppins', sans-serif`;
                    ctx.fillStyle = '#0D0F2B';
                    const textWidth = ctx.measureText(node.label).width;
                    
                    ctx.fillStyle = 'rgba(255, 255, 255, 0.9)';
                    ctx.shadowColor = 'rgba(28, 34, 128, 0.2)';
                    ctx.shadowBlur = 8;
                    ctx.beginPath();
                    const px = proj.px + 12 * proj.scale;
                    const py = proj.py - 10 * proj.scale;
                    ctx.roundRect(px, py, textWidth + 14, 20 * proj.scale, 6);
                    ctx.fill();

                    ctx.shadowBlur = 0;
                    ctx.fillStyle = node.color;
                    ctx.fillText(node.label, px + 7, py + 14 * proj.scale);
                }
                ctx.restore();
            }
        }

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

    window.addEventListener('mousemove', (e) => {
        const cx = window.innerWidth / 2;
        const cy = window.innerHeight / 2;
        mouseX = (e.clientX - cx) / cx;
        mouseY = (e.clientY - cy) / cy;
    }, { passive: true });

    if (window.DeviceOrientationEvent) {
        window.addEventListener('deviceorientation', (e) => {
            if (e.gamma !== null && e.beta !== null) {
                mouseX = Math.min(Math.max(e.gamma / 25, -1), 1);
                mouseY = Math.min(Math.max(e.beta / 25, -1), 1);
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

        return { el, depthX, depthY, depthZ, phase, freqX, freqY, freqZ, color, currentX: 0, currentY: 0 };
    });

    // Connect left-column service motion chips to right-column 3D nodes
    document.querySelectorAll('.motion-service-chip').forEach(chip => {
        const targetId = chip.getAttribute('data-target-service');
        if (!targetId) return;
        const targetNode = document.querySelector(`.service-node-8d[data-service="${targetId}"]`);
        if (!targetNode) return;
        chip.addEventListener('mouseenter', () => {
            targetNode.classList.add('active-focus');
        });
        chip.addEventListener('mouseleave', () => {
            targetNode.classList.remove('active-focus');
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

        smoothMouseX += (mouseX - smoothMouseX) * 0.07;
        smoothMouseY += (mouseY - smoothMouseY) * 0.07;

        // 3D stage gyroscopic tilt
        if (stage && window.innerWidth >= 992) {
            const stageRotX = -smoothMouseY * 13;
            const stageRotY = smoothMouseX * 17;
            stage.style.transform = `perspective(1200px) rotateX(${stageRotX.toFixed(2)}deg) rotateY(${stageRotY.toFixed(2)}deg)`;
        }

        // Center Vortexsoft core 3D tilt
        if (core) {
            const coreRotX = -smoothMouseY * 18;
            const coreRotY = smoothMouseX * 22;
            const coreScale = 1 + Math.sin(t * 1.5) * 0.03;
            core.style.transform = `rotateX(${coreRotX.toFixed(2)}deg) rotateY(${coreRotY.toFixed(2)}deg) scale(${coreScale.toFixed(3)})`;
        }

        // Stage Energy Canvas: Dynamic laser beams & traveling photons
        if (eCtx && sW > 0 && sH > 0) {
            eCtx.clearRect(0, 0, sW, sH);
            const coreCenterX = sW * 0.5;
            const coreCenterY = sH * 0.5;

            // Draw glowing energy connections between center Vortexsoft core and orbiting nodes
            for (let i = 0; i < nodeState.length; i++) {
                const n = nodeState[i];
                const rect = n.el.getBoundingClientRect();
                const stageRect = stage.getBoundingClientRect();
                const nodeCenterX = rect.left - stageRect.left + rect.width * 0.5;
                const nodeCenterY = rect.top - stageRect.top + rect.height * 0.5;

                const isFocused = n.el.classList.contains('active-focus');
                const lineAlpha = isFocused ? 0.9 : 0.24 + Math.sin(t * 2 + n.phase) * 0.08;

                // Glowing connecting laser line
                eCtx.save();
                eCtx.beginPath();
                eCtx.moveTo(coreCenterX, coreCenterY);
                eCtx.lineTo(nodeCenterX, nodeCenterY);
                eCtx.strokeStyle = isFocused ? n.color : `rgba(91, 168, 212, ${lineAlpha})`;
                eCtx.lineWidth = isFocused ? 2.5 : 1.2;
                if (isFocused) {
                    eCtx.shadowColor = n.color;
                    eCtx.shadowBlur = 14;
                }
                eCtx.stroke();

                // Traveling photon energy pulse
                const pulseProg = (t * 0.75 + (i / nodeState.length)) % 1;
                const photonX = coreCenterX + (nodeCenterX - coreCenterX) * pulseProg;
                const photonY = coreCenterY + (nodeCenterY - coreCenterY) * pulseProg;

                eCtx.beginPath();
                eCtx.arc(photonX, photonY, isFocused ? 4 : 2.5, 0, Math.PI * 2);
                eCtx.fillStyle = isFocused ? '#ffffff' : n.color;
                eCtx.shadowColor = n.color;
                eCtx.shadowBlur = isFocused ? 12 : 8;
                eCtx.fill();
                eCtx.restore();
            }
        }

        // 8D Physics for each Service Node
        for (let i = 0; i < nodeState.length; i++) {
            const n = nodeState[i];
            if (n.el.classList.contains('active-focus')) {
                n.el.style.transform = `translate3d(0, -10px, 60px) scale(1.15)`;
                continue;
            }

            // 8 Dimensions of Freedom
            const posX = Math.cos(t * n.freqX + n.phase) * 12 + (smoothMouseX * n.depthX * 65);
            const posY = Math.sin(t * n.freqY + n.phase) * 14 + (smoothMouseY * n.depthY * 65);
            const posZ = Math.sin(t * n.freqZ + n.phase) * 18 + n.depthZ + (-smoothMouseY * 18);
            const rotX = -smoothMouseY * 10 + Math.sin(t * 0.8 + n.phase) * 5;
            const rotY = smoothMouseX * 12 + Math.cos(t * 0.7 + n.phase) * 5;
            const rotZ = Math.sin(t * 0.5 + n.phase) * 3;
            const scale = 1 + Math.sin(t * 1.2 + n.phase) * 0.03;
            const glareX = 50 + (smoothMouseX * 32);
            const glareY = 50 + (smoothMouseY * 32);

            n.el.style.transform = `translate3d(${posX.toFixed(2)}px, ${posY.toFixed(2)}px, ${posZ.toFixed(1)}px) rotateX(${rotX.toFixed(2)}deg) rotateY(${rotY.toFixed(2)}deg) rotateZ(${rotZ.toFixed(2)}deg) scale(${scale.toFixed(3)})`;
            n.el.style.setProperty('--glare-x', `${glareX.toFixed(1)}%`);
            n.el.style.setProperty('--glare-y', `${glareY.toFixed(1)}%`);
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
