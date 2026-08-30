/**
 * ═══════════════════════════════════════════════════════════════════
 * VORTEXSOFT INNOVATIONS — HOMEPAGE 3D REAL-TIME INTERACTION ENGINE
 * (home-3d.js)
 * ═══════════════════════════════════════════════════════════════════
 */

document.addEventListener('DOMContentLoaded', function() {

  // 1. ── Real-Time 3D Hero Particle Sphere / Vortex ───────────────────
  const heroCanvas = document.getElementById('hero-canvas');
  if (heroCanvas) {
    const ctx = heroCanvas.getContext('2d');
    let width, height, dpr = window.devicePixelRatio || 1;
    let particles3D = [];
    const count = 180;
    const sphereRadius = 240;
    const focalLength = 380;

    let rotX = 0.2, rotY = 0;
    let targetRotX = 0.2, targetRotY = 0;
    let mouseX = 0, mouseY = 0;
    let isMouseOver = false;

    function resizeHero() {
      width = heroCanvas.width = heroCanvas.parentElement.offsetWidth;
      height = heroCanvas.height = heroCanvas.parentElement.offsetHeight;
      init3DParticles();
    }

    function init3DParticles() {
      particles3D = [];
      const COLORS = ['#1C2280', '#2D35C4', '#5BA8D4', '#CC2228', '#87CEEB'];

      for (let i = 0; i < count; i++) {
        // Fibonacci sphere distribution for perfect 3D geometry
        const phi = Math.acos(1 - 2 * (i + 0.5) / count);
        const theta = Math.PI * (1 + Math.sqrt(5)) * (i + 0.5);

        const r = sphereRadius * (0.8 + Math.random() * 0.4);
        const x = r * Math.sin(phi) * Math.cos(theta);
        const y = r * Math.sin(phi) * Math.sin(theta) * 0.65; // flattened galaxy disc
        const z = r * Math.cos(phi);

        particles3D.push({
          origX: x, origY: y, origZ: z,
          x: x, y: y, z: z,
          size: Math.random() * 2.2 + 1.2,
          color: COLORS[i % COLORS.length],
          orbitSpeed: (Math.random() * 0.003 + 0.002) * (Math.random() > 0.5 ? 1 : -1)
        });
      }
    }

    // Track mouse over hero
    window.addEventListener('mousemove', (e) => {
      const rect = heroCanvas.getBoundingClientRect();
      if (e.clientY >= rect.top && e.clientY <= rect.bottom) {
        const cx = rect.left + rect.width * 0.65;
        const cy = rect.top + rect.height * 0.5;
        targetRotY = (e.clientX - cx) * 0.0012;
        targetRotX = (e.clientY - cy) * 0.0012;
      }
    }, { passive: true });

    function render3DHero() {
      ctx.clearRect(0, 0, width, height);

      // Smooth inertia
      rotX += (targetRotX - rotX) * 0.05;
      rotY += (targetRotY - rotY) * 0.05;

      const cosX = Math.cos(rotX);
      const sinX = Math.sin(rotX);
      const cosY = Math.cos(rotY + performance.now() * 0.0004);
      const sinY = Math.sin(rotY + performance.now() * 0.0004);

      const centerX = width > 991 ? width * 0.70 : width * 0.5;
      const centerY = height * 0.5;

      const projected = [];

      // 3D rotation & projection
      for (let i = 0; i < particles3D.length; i++) {
        const p = particles3D[i];

        // Orbit calculation
        const x1 = p.origX * Math.cos(p.orbitSpeed) - p.origZ * Math.sin(p.orbitSpeed);
        const z1 = p.origX * Math.sin(p.orbitSpeed) + p.origZ * Math.cos(p.orbitSpeed);
        p.origX = x1;
        p.origZ = z1;

        // Rotate around Y
        let x2 = p.origX * cosY - p.origZ * sinY;
        let z2 = p.origX * sinY + p.origZ * cosY;

        // Rotate around X
        let y2 = p.origY * cosX - z2 * sinX;
        let z3 = p.origY * sinX + z2 * cosX;

        // Depth perspective projection
        const scale = focalLength / (focalLength + z3 + 300);
        const projX = centerX + x2 * scale;
        const projY = centerY + y2 * scale;
        const alpha = Math.max(0.1, Math.min(1, (z3 + sphereRadius) / (sphereRadius * 2) * 0.85 + 0.15));

        projected.push({
          x: projX,
          y: projY,
          z: z3,
          scale: scale,
          alpha: alpha,
          size: p.size * scale,
          color: p.color
        });
      }

      // Sort by depth (painters algorithm)
      projected.sort((a, b) => b.z - a.z);

      // Draw 3D connecting lines between close nodes
      for (let i = 0; i < projected.length; i++) {
        for (let j = i + 1; j < projected.length; j++) {
          const p1 = projected[i];
          const p2 = projected[j];

          const dx = p1.x - p2.x;
          const dy = p1.y - p2.y;
          const dist = Math.sqrt(dx * dx + dy * dy);

          if (dist < 65 && Math.abs(p1.z - p2.z) < 80) {
            ctx.strokeStyle = `rgba(91, 168, 212, ${0.2 * (1 - dist / 65) * p1.alpha})`;
            ctx.lineWidth = 0.8 * p1.scale;
            ctx.beginPath();
            ctx.moveTo(p1.x, p1.y);
            ctx.lineTo(p2.x, p2.y);
            ctx.stroke();
          }
        }
      }

      // Draw 3D particle points
      projected.forEach(p => {
        ctx.fillStyle = p.color;
        ctx.globalAlpha = p.alpha;
        ctx.beginPath();
        ctx.arc(p.x, p.y, Math.max(0.5, p.size), 0, Math.PI * 2);
        ctx.fill();

        // Glowing core for prominent front nodes
        if (p.z > 80 && p.scale > 0.8) {
          ctx.fillStyle = '#FFFFFF';
          ctx.beginPath();
          ctx.arc(p.x, p.y, p.size * 0.4, 0, Math.PI * 2);
          ctx.fill();
        }
      });
      ctx.globalAlpha = 1.0;

      requestAnimationFrame(render3DHero);
    }

    window.addEventListener('resize', resizeHero, { passive: true });
    resizeHero();
    render3DHero();
  }

  // 2. ── Interactive 3D Card Tilt with Specular Glare ─────────────────
  if (window.innerWidth > 991) {
    const tiltTargets = document.querySelectorAll(
      '.service-card, .whyus-card, .testimonial-card, .card-3d-wrap, .stat-3d-box'
    );

    tiltTargets.forEach(card => {
      card.classList.add('card-3d-wrap');

      card.addEventListener('mousemove', function(e) {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const centerX = rect.width / 2;
        const centerY = rect.height / 2;

        const rotateX = ((y - centerY) / centerY) * -7;
        const rotateY = ((x - centerX) / centerX) * 7;

        card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-6px) translateZ(8px)`;
        card.style.setProperty('--glare-x', `${x}px`);
        card.style.setProperty('--glare-y', `${y}px`);
      });

      card.addEventListener('mouseleave', function() {
        card.style.transform = '';
        card.style.transition = 'transform 0.5s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.5s ease';
      });

      card.addEventListener('mouseenter', function() {
        card.style.transition = 'transform 0.1s ease, box-shadow 0.1s ease';
      });
    });
  }

  // 3. ── Real-Time 3D Autonomous AI Agent Pipeline Canvas ──────────────
  const aiCanvas = document.getElementById('home-ai-canvas');
  if (aiCanvas) {
    const ctx = aiCanvas.getContext('2d');
    let width, height;
    let layers = [];
    let packets = [];

    function resizeAiCanvas() {
      width = aiCanvas.width = aiCanvas.parentElement.offsetWidth;
      height = aiCanvas.height = aiCanvas.parentElement.offsetHeight;
      initAiPipeline();
    }

    function initAiPipeline() {
      layers = [];
      packets = [];

      const layerCount = 4;
      const nodesPerLayer = [3, 4, 4, 2];

      for (let l = 0; l < layerCount; l++) {
        const layerX = (width / (layerCount + 1)) * (l + 1);
        const count = nodesPerLayer[l];
        const layerNodes = [];

        for (let n = 0; n < count; n++) {
          const nodeY = (height / (count + 1)) * (n + 1);
          layerNodes.push({
            x: layerX,
            y: nodeY,
            radius: l === 1 || l === 2 ? 6 : 4.5,
            color: l === 0 ? '#5BA8D4' : l === 3 ? '#10B981' : '#CC2228',
            pulse: Math.random() * Math.PI * 2
          });
        }
        layers.push(layerNodes);
      }

      // Initial data packets
      for (let i = 0; i < 14; i++) {
        spawnAiPacket();
      }
    }

    function spawnAiPacket() {
      if (layers.length < 2) return;
      const startLayer = Math.floor(Math.random() * (layers.length - 1));
      const fromNode = layers[startLayer][Math.floor(Math.random() * layers[startLayer].length)];
      const toNode = layers[startLayer + 1][Math.floor(Math.random() * layers[startLayer + 1].length)];

      packets.push({
        from: fromNode,
        to: toNode,
        progress: Math.random(),
        speed: Math.random() * 0.012 + 0.008,
        color: '#5BA8D4'
      });
    }

    function renderAiPipeline() {
      ctx.clearRect(0, 0, width, height);

      // Draw connections between layers
      for (let l = 0; l < layers.length - 1; l++) {
        const currLayer = layers[l];
        const nextLayer = layers[l + 1];

        for (let i = 0; i < currLayer.length; i++) {
          for (let j = 0; j < nextLayer.length; j++) {
            ctx.strokeStyle = 'rgba(255, 255, 255, 0.08)';
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(currLayer[i].x, currLayer[i].y);
            ctx.lineTo(nextLayer[j].x, nextLayer[j].y);
            ctx.stroke();
          }
        }
      }

      // Draw Nodes
      layers.forEach(layer => {
        layer.forEach(node => {
          node.pulse += 0.04;
          const glow = Math.sin(node.pulse) * 0.3 + 0.7;

          ctx.fillStyle = node.color;
          ctx.shadowBlur = 10;
          ctx.shadowColor = node.color;
          ctx.beginPath();
          ctx.arc(node.x, node.y, node.radius * glow, 0, Math.PI * 2);
          ctx.fill();
          ctx.shadowBlur = 0;
        });
      });

      // Update & Draw Packets
      for (let i = packets.length - 1; i >= 0; i--) {
        const p = packets[i];
        p.progress += p.speed;

        if (p.progress >= 1) {
          packets.splice(i, 1);
          spawnAiPacket();
          continue;
        }

        const currX = p.from.x + (p.to.x - p.from.x) * p.progress;
        const currY = p.from.y + (p.to.y - p.from.y) * p.progress;

        ctx.fillStyle = '#FFFFFF';
        ctx.shadowBlur = 12;
        ctx.shadowColor = '#5BA8D4';
        ctx.beginPath();
        ctx.arc(currX, currY, 2.5, 0, Math.PI * 2);
        ctx.fill();
        ctx.shadowBlur = 0;
      }

      requestAnimationFrame(renderAiPipeline);
    }

    window.addEventListener('resize', resizeAiCanvas, { passive: true });
    resizeAiCanvas();
    renderAiPipeline();
  }

});
