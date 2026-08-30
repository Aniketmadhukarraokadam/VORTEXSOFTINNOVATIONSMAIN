/**
 * ═══════════════════════════════════════════════════════════════════
 * VORTEXSOFT INNOVATIONS — SERVICES PAGE INTERACTIVITY (services.js)
 * Real-Time Canvas Animations, Category Sync & Cursor Spotlight
 * ═══════════════════════════════════════════════════════════════════
 */

document.addEventListener('DOMContentLoaded', function() {

  // 1. ── Hero Ambient Canvas ──────────────────────────────────────────
  const heroCanvas = document.getElementById('services-hero-canvas');
  if (heroCanvas) {
    const ctx = heroCanvas.getContext('2d');
    let width, height, particles = [];
    let mouse = { x: null, y: null, radius: 140 };

    function resizeHeroCanvas() {
      width = heroCanvas.width = heroCanvas.parentElement.offsetWidth;
      height = heroCanvas.height = heroCanvas.parentElement.offsetHeight;
      initParticles();
    }

    function initParticles() {
      particles = [];
      const count = Math.min(Math.floor((width * height) / 14000), 65);
      for (let i = 0; i < count; i++) {
        particles.push({
          x: Math.random() * width,
          y: Math.random() * height,
          vx: (Math.random() - 0.5) * 0.45,
          vy: (Math.random() - 0.5) * 0.45,
          size: Math.random() * 1.8 + 0.8,
          alpha: Math.random() * 0.5 + 0.2
        });
      }
    }

    function renderHeroCanvas() {
      ctx.clearRect(0, 0, width, height);

      // Draw connections
      for (let i = 0; i < particles.length; i++) {
        for (let j = i + 1; j < particles.length; j++) {
          const dx = particles[i].x - particles[j].x;
          const dy = particles[i].y - particles[j].y;
          const dist = Math.sqrt(dx * dx + dy * dy);

          if (dist < 110) {
            ctx.strokeStyle = `rgba(91, 168, 212, ${0.15 * (1 - dist / 110)})`;
            ctx.lineWidth = 0.8;
            ctx.beginPath();
            ctx.moveTo(particles[i].x, particles[i].y);
            ctx.lineTo(particles[j].x, particles[j].y);
            ctx.stroke();
          }
        }
      }

      // Update and draw particles
      particles.forEach(p => {
        p.x += p.vx;
        p.y += p.vy;

        if (p.x < 0) p.x = width;
        if (p.x > width) p.x = 0;
        if (p.y < 0) p.y = height;
        if (p.y > height) p.y = 0;

        ctx.fillStyle = `rgba(255, 255, 255, ${p.alpha})`;
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
        ctx.fill();
      });

      requestAnimationFrame(renderHeroCanvas);
    }

    window.addEventListener('resize', resizeHeroCanvas);
    resizeHeroCanvas();
    renderHeroCanvas();
  }

  // 2. ── Healthcare Real-Time Clinical ECG Waveform ───────────────────
  const ecgCanvas = document.getElementById('healthcare-telemetry-canvas');
  if (ecgCanvas) {
    const ctx = ecgCanvas.getContext('2d');
    let width, height, step = 0;

    function resizeEcg() {
      width = ecgCanvas.width = ecgCanvas.offsetWidth || 400;
      height = ecgCanvas.height = ecgCanvas.offsetHeight || 300;
    }

    function drawEcg() {
      ctx.clearRect(0, 0, width, height);

      ctx.strokeStyle = 'rgba(204, 34, 40, 0.45)';
      ctx.lineWidth = 2;
      ctx.shadowBlur = 8;
      ctx.shadowColor = 'rgba(204, 34, 40, 0.6)';
      ctx.beginPath();

      const centerY = height * 0.82;
      const points = [];

      for (let x = 0; x < width; x += 3) {
        const offset = (x + step) % width;
        let y = centerY;

        // ECG spike calculation
        const cycle = offset % 180;
        if (cycle > 40 && cycle < 50) {
          y = centerY - 12; // P wave
        } else if (cycle >= 50 && cycle < 56) {
          y = centerY + 8; // Q wave
        } else if (cycle >= 56 && cycle < 66) {
          y = centerY - 54; // R peak
        } else if (cycle >= 66 && cycle < 72) {
          y = centerY + 16; // S wave
        } else if (cycle >= 80 && cycle < 100) {
          y = centerY - 18; // T wave
        }

        if (x === 0) {
          ctx.moveTo(x, y);
        } else {
          ctx.lineTo(x, y);
        }
      }

      ctx.stroke();
      ctx.shadowBlur = 0;

      step += 2;
      requestAnimationFrame(drawEcg);
    }

    window.addEventListener('resize', resizeEcg);
    resizeEcg();
    drawEcg();
  }

  // 3. ── AI Enterprise Real-Time Routing Network Canvas ───────────────
  const aiCanvas = document.getElementById('ai-routing-canvas');
  if (aiCanvas) {
    const ctx = aiCanvas.getContext('2d');
    let width, height;
    let nodes = [], packets = [];

    function resizeAiCanvas() {
      width = aiCanvas.width = aiCanvas.parentElement.offsetWidth;
      height = aiCanvas.height = aiCanvas.parentElement.offsetHeight;
      initAiNetwork();
    }

    function initAiNetwork() {
      nodes = [];
      packets = [];
      const cols = 5;
      const rows = 3;
      const cellW = width / cols;
      const cellH = height / rows;

      for (let r = 0; r < rows; r++) {
        for (let c = 0; c < cols; c++) {
          nodes.push({
            x: c * cellW + cellW * 0.5 + (Math.random() - 0.5) * (cellW * 0.4),
            y: r * cellH + cellH * 0.5 + (Math.random() - 0.5) * (cellH * 0.4),
            radius: Math.random() * 2.5 + 2.5,
            pulse: Math.random() * Math.PI
          });
        }
      }

      // Initialize moving data packets
      for (let i = 0; i < 18; i++) {
        spawnPacket();
      }
    }

    function spawnPacket() {
      if (nodes.length < 2) return;
      const fromIdx = Math.floor(Math.random() * nodes.length);
      let toIdx = Math.floor(Math.random() * nodes.length);
      while (toIdx === fromIdx) {
        toIdx = Math.floor(Math.random() * nodes.length);
      }

      packets.push({
        from: nodes[fromIdx],
        to: nodes[toIdx],
        progress: Math.random(),
        speed: Math.random() * 0.008 + 0.004,
        size: Math.random() * 2 + 1.5
      });
    }

    function renderAiNetwork() {
      ctx.clearRect(0, 0, width, height);

      // Draw node connections
      ctx.strokeStyle = 'rgba(91, 168, 212, 0.12)';
      ctx.lineWidth = 1;
      for (let i = 0; i < nodes.length; i++) {
        for (let j = i + 1; j < nodes.length; j++) {
          const dx = nodes[i].x - nodes[j].x;
          const dy = nodes[i].y - nodes[j].y;
          const dist = Math.sqrt(dx * dx + dy * dy);
          if (dist < 220) {
            ctx.beginPath();
            ctx.moveTo(nodes[i].x, nodes[i].y);
            ctx.lineTo(nodes[j].x, nodes[j].y);
            ctx.stroke();
          }
        }
      }

      // Draw nodes
      nodes.forEach(n => {
        n.pulse += 0.03;
        const glow = Math.sin(n.pulse) * 0.3 + 0.7;
        ctx.fillStyle = `rgba(91, 168, 212, ${0.4 * glow})`;
        ctx.beginPath();
        ctx.arc(n.x, n.y, n.radius + (1 - glow) * 3, 0, Math.PI * 2);
        ctx.fill();

        ctx.fillStyle = '#5BA8D4';
        ctx.beginPath();
        ctx.arc(n.x, n.y, n.radius * 0.7, 0, Math.PI * 2);
        ctx.fill();
      });

      // Update & Draw Packets
      for (let i = packets.length - 1; i >= 0; i--) {
        const p = packets[i];
        p.progress += p.speed;

        if (p.progress >= 1) {
          packets.splice(i, 1);
          spawnPacket();
          continue;
        }

        const currX = p.from.x + (p.to.x - p.from.x) * p.progress;
        const currY = p.from.y + (p.to.y - p.from.y) * p.progress;

        ctx.fillStyle = '#5BA8D4';
        ctx.shadowBlur = 10;
        ctx.shadowColor = '#5BA8D4';
        ctx.beginPath();
        ctx.arc(currX, currY, p.size, 0, Math.PI * 2);
        ctx.fill();
        ctx.shadowBlur = 0;
      }

      requestAnimationFrame(renderAiNetwork);
    }

    window.addEventListener('resize', resizeAiCanvas);
    resizeAiCanvas();
    renderAiNetwork();
  }

  // 4. ── Interactive Card Cursor Spotlight ────────────────────────────
  if (window.innerWidth > 991) {
    const cards = document.querySelectorAll('.svc-card, .svc-featured-ai-card');
    cards.forEach(card => {
      card.addEventListener('mousemove', function(e) {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        card.style.setProperty('--mouse-x', `${x}px`);
        card.style.setProperty('--mouse-y', `${y}px`);
      });
    });
  }

  // 5. ── Category Navigation Observer & Smooth Scroll ─────────────────
  const navPills = document.querySelectorAll('.svc-nav-pill');
  const serviceCards = document.querySelectorAll('.svc-card, .svc-featured-ai-card');

  // Smooth scroll handler
  navPills.forEach(pill => {
    pill.addEventListener('click', function(e) {
      const targetId = this.getAttribute('href');
      if (targetId && targetId.startsWith('#')) {
        const targetElem = document.querySelector(targetId);
        if (targetElem) {
          e.preventDefault();
          targetElem.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      }
    });
  });

  // IntersectionObserver to sync active category
  if ('IntersectionObserver' in window && serviceCards.length > 0) {
    const observerOptions = {
      root: null,
      rootMargin: '-20% 0px -60% 0px',
      threshold: 0
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const id = entry.target.getAttribute('id');
          if (id) {
            navPills.forEach(p => {
              if (p.getAttribute('href') === `#${id}`) {
                p.classList.add('active');
                // Scroll navigation into view on mobile
                p.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
              } else {
                p.classList.remove('active');
              }
            });
          }
        }
      });
    }, observerOptions);

    serviceCards.forEach(card => observer.observe(card));
  }

});
