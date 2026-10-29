/**
 * Vortexsoft Innovations — Advanced Enterprise Extensions & Plugins Suite
 * - 0ms Instant Page Hover Prefetcher
 * - Scroll Reading Progress Bar
 * - Spotlight Command Palette (Ctrl+K / ⌘K)
 * - Live Social Proof Trust Notification Engine
 * - 1-Click Social Sharing & Clipboard Feedback
 */

(function () {
  'use strict';

  // ══════════════════════════════════════════════════════════════════
  // 1. TOP SCROLL READING PROGRESS BAR
  // ══════════════════════════════════════════════════════════════════
  function initScrollProgress() {
    var bar = document.createElement('div');
    bar.id = 'vortexScrollProgress';
    document.body.appendChild(bar);

    function updateProgress() {
      var scrollY = window.pageYOffset || document.documentElement.scrollTop;
      var docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
      if (docHeight > 0) {
        var pct = (scrollY / docHeight) * 100;
        bar.style.width = Math.min(100, Math.max(0, pct)) + '%';
      }
    }
    window.addEventListener('scroll', updateProgress, { passive: true });
    updateProgress();
  }

  // ══════════════════════════════════════════════════════════════════
  // 2. INSTANT-PAGE HOVER PREFETCHER (0ms Page Loading)
  // ══════════════════════════════════════════════════════════════════
  function initInstantPrefetch() {
    var prefetched = {};
    var prefetchTimer = null;

    function prefetchUrl(url) {
      if (!url || prefetched[url]) return;
      prefetched[url] = true;
      var link = document.createElement('link');
      link.rel = 'prefetch';
      link.href = url;
      document.head.appendChild(link);
    }

    function isInternalLink(a) {
      if (!a || a.tagName !== 'A') return false;
      var href = a.getAttribute('href');
      if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:') || href.startsWith('javascript:')) return false;
      try {
        var url = new URL(href, window.location.href);
        return url.origin === window.location.origin;
      } catch (e) {
        return false;
      }
    }

    document.addEventListener('mouseover', function (e) {
      var a = e.target.closest('a');
      if (isInternalLink(a)) {
        prefetchTimer = setTimeout(function () {
          prefetchUrl(a.href);
        }, 65);
      }
    }, { passive: true });

    document.addEventListener('mouseout', function (e) {
      if (prefetchTimer) {
        clearTimeout(prefetchTimer);
        prefetchTimer = null;
      }
    }, { passive: true });

    document.addEventListener('touchstart', function (e) {
      var a = e.target.closest('a');
      if (isInternalLink(a)) {
        prefetchUrl(a.href);
      }
    }, { passive: true });
  }

  // ══════════════════════════════════════════════════════════════════
  // 3. COMMAND PALETTE SEARCH (Ctrl+K / ⌘K)
  // ══════════════════════════════════════════════════════════════════
  var searchIndex = [
    // Highlighted Title Abstracting
    { title: "India's Best Title Abstracting Company", desc: "40+ happy clients, 50 US States, 40-year & 2-owner searches, 99.8% accuracy", url: "title-settlement/index.php", cat: "Title Abstracting", badge: "#1 IN INDIA", icon: "fa-crown" },
    { title: "Title & Settlement Services", desc: "Title search, commitment preparation, closing packages, lien verification", url: "title-settlement/index.php", cat: "Title Abstracting", badge: "Services", icon: "fa-landmark" },
    { title: "Mortgage & Escrow Support", desc: "Post-closing auditing, pay-off requests, deed recording", url: "mortgage-escrow/index.php", cat: "Title Abstracting", badge: "Real Estate", icon: "fa-file-invoice" },
    { title: "Lease Abstraction Services", desc: "Commercial lease data extraction, CAM audits, critical dates tracking", url: "lease-abstraction-services/index.php", cat: "Real Estate", badge: "BPO", icon: "fa-scroll" },
    { title: "CAM Audit & Reconciliation", desc: "Common Area Maintenance audit for commercial portfolios", url: "cam-audit-services/index.php", cat: "Real Estate", badge: "Accounting", icon: "fa-calculator" },

    // AI & Automations
    { title: "AI Automations for Enterprise & SMB", desc: "Custom LLM pipelines, autonomous multi-agent workflows & RPA", url: "index.php#enterprise-capabilities", cat: "AI Automations", badge: "Popular", icon: "fa-brain" },
    { title: "vortexsofthrms — Enterprise AI HRMS", desc: "Autonomous payroll calculation, biometric sync & predictive HRMS", url: "index.php#ai-products", cat: "AI Products", badge: "AI Platform", icon: "fa-users-cog" },
    { title: "VortexEXHO — Workforce OS", desc: "Unified enterprise operating system combining ATS, HRMS, LMS & AI copilot", url: "index.php#ai-products", cat: "AI Products", badge: "Enterprise", icon: "fa-microchip" },
    { title: "vortexHire — AI Recruiting Engine", desc: "Automated candidate sourcing, resume parsing and qualification scoring", url: "index.php#ai-products", cat: "AI Products", badge: "AI Platform", icon: "fa-user-check" },
    { title: "vortexKonnect — Call Center Analytics", desc: "Real-time speech analytics, acoustic sentiment intelligence & QA", url: "index.php#ai-products", cat: "AI Products", badge: "Call Center", icon: "fa-headset" },
    { title: "Vortexreach — Autonomous Sales Outreach", desc: "Automated B2B prospect research and personalized sequencing", url: "index.php#ai-products", cat: "AI Products", badge: "Sales AI", icon: "fa-paper-plane" },
    { title: "vortexsoftpublishing — Publishing Automation", desc: "ePUB3 conversion, JATS/BITS/S1000D restructuring, WCAG 2.1 AA", url: "index.php#ai-products", cat: "AI Products", badge: "Publishing", icon: "fa-book" },
    { title: "AI Data Annotation Services", desc: "2D/3D bounding boxes, polygon, LiDAR, video tracking & RLHF labeling", url: "data-annotation-services/index.php", cat: "AI Services", badge: "Computer Vision", icon: "fa-tags" },

    // Healthcare BPO & RCM
    { title: "Healthcare BPO & Revenue Cycle Management", desc: "HIPAA compliant medical coding, billing, denial management & AR", url: "service.php#healthcare", cat: "Healthcare", badge: "HIPAA", icon: "fa-heartbeat" },
    { title: "Medical Coding Services", desc: "Certified AAPC/AHIMA coders (ICD-10, CPT, HCPCS, inpatient/outpatient)", url: "medical-coding-services/index.php", cat: "Healthcare", badge: "Medical", icon: "fa-code" },
    { title: "Medical Billing Services", desc: "End-to-end insurance billing with 98%+ clean claim pass rates", url: "medical-billing-services/index.php", cat: "Healthcare", badge: "Medical", icon: "fa-file-invoice-dollar" },
    { title: "Denial Management Services", desc: "Root cause denial tracking, appeals filing and payer dispute recovery", url: "denial-management-services/index.php", cat: "Healthcare", badge: "RCM", icon: "fa-ban" },
    { title: "Accounts Receivable (AR) Recovery", desc: "Aggressive aged claim follow-ups and provider revenue maximization", url: "ar-recovery-services/index.php", cat: "Healthcare", badge: "RCM", icon: "fa-undo" },

    // Software & Digital
    { title: "Custom Software Solutions", desc: "Full-stack React, Next.js, Node.js, PHP, Python & cloud DevOps", url: "software-solutions/index.php", cat: "IT & Software", badge: "Engineering", icon: "fa-laptop-code" },
    { title: "Digital Marketing & SEO", desc: "Performance marketing, technical SEO, AEO and brand authority", url: "digital-marketing-service/index.php", cat: "Marketing", badge: "Growth", icon: "fa-bullhorn" },
    { title: "Digital Accessibility Services", desc: "WCAG 2.1 AA, Section 508, PDF/UA remediation & audit compliance", url: "digital-accessibility-services/index.php", cat: "Compliance", badge: "Accessibility", icon: "fa-universal-access" },

    // Company & Information
    { title: "Frequently Asked Questions (FAQ)", desc: "Authoritative answers about Title abstracting, AI, BPO, and ISO 27001", url: "faq.php", cat: "Company", badge: "Help", icon: "fa-question-circle" },
    { title: "About Vortexsoft Innovations", desc: "ISO 27001 certified company profile, Pune HQ, Bengaluru hub, USA branch", url: "about.php", cat: "Company", badge: "Corporate", icon: "fa-building" },
    { title: "Careers & Open Job Vacancies", desc: "Explore 13+ open roles in IT, AI, Title Abstracting, Medical Coding", url: "careers.php", cat: "Company", badge: "We're Hiring", icon: "fa-user-graduate" },
    { title: "Contact Us & Project Inquiry", desc: "Request a custom proposal, free discovery call, or pilot engagement", url: "contact.php", cat: "Company", badge: "Support", icon: "fa-phone-alt" },
    { title: "Privacy Policy", desc: "ISO 27001:2013 data security standards and privacy practices", url: "privacy.php", cat: "Legal", badge: "Security", icon: "fa-shield-alt" },
    { title: "Terms of Service", desc: "Master service agreement, deliverables, SLAs, and commercial terms", url: "terms.php", cat: "Legal", badge: "Legal", icon: "fa-file-contract" }
  ];

  function initCommandPalette() {
    var paletteHtml = [
      '<div id="vortexCommandPalette" role="dialog" aria-modal="true" aria-label="Quick Search">',
      '  <div class="vcp-modal">',
      '    <div class="vcp-header">',
      '      <i class="fas fa-search vcp-search-icon"></i>',
      '      <input type="text" id="vcpSearchInput" class="vcp-input" placeholder="Search 75+ IT & BPO services, AI platforms, Title abstracting..." autocomplete="off">',
      '      <span class="vcp-badge-esc" onclick="window.vortexClosePalette()">ESC</span>',
      '    </div>',
      '    <div class="vcp-body" id="vcpResults"></div>',
      '    <div class="vcp-footer">',
      '      <span>Vortexsoft Innovations Global Search</span>',
      '      <div class="vcp-footer-keys">',
      '        <span><span class="vcp-key">↑</span><span class="vcp-key">↓</span> Navigate</span>',
      '        <span><span class="vcp-key">↵</span> Select</span>',
      '        <span><span class="vcp-key">ESC</span> Close</span>',
      '      </div>',
      '    </div>',
      '  </div>',
      '</div>'
    ].join('\n');

    document.body.insertAdjacentHTML('beforeend', paletteHtml);

    var palette = document.getElementById('vortexCommandPalette');
    var input = document.getElementById('vcpSearchInput');
    var resultsBox = document.getElementById('vcpResults');
    var selectedIndex = 0;
    var currentFiltered = [];

    window.vortexOpenPalette = function () {
      palette.classList.add('active');
      input.value = '';
      input.focus();
      renderResults('');
    };

    window.vortexClosePalette = function () {
      palette.classList.remove('active');
    };

    palette.addEventListener('click', function (e) {
      if (e.target === palette) {
        window.vortexClosePalette();
      }
    });

    document.addEventListener('keydown', function (e) {
      // Toggle on Ctrl+K or Cmd+K
      if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) {
        e.preventDefault();
        if (palette.classList.contains('active')) {
          window.vortexClosePalette();
        } else {
          window.vortexOpenPalette();
        }
      } else if (e.key === 'Escape' && palette.classList.contains('active')) {
        window.vortexClosePalette();
      } else if (palette.classList.contains('active')) {
        if (e.key === 'ArrowDown') {
          e.preventDefault();
          if (currentFiltered.length > 0) {
            selectedIndex = (selectedIndex + 1) % currentFiltered.length;
            updateSelection();
          }
        } else if (e.key === 'ArrowUp') {
          e.preventDefault();
          if (currentFiltered.length > 0) {
            selectedIndex = (selectedIndex - 1 + currentFiltered.length) % currentFiltered.length;
            updateSelection();
          }
        } else if (e.key === 'Enter') {
          e.preventDefault();
          if (currentFiltered[selectedIndex]) {
            window.location.href = currentFiltered[selectedIndex].url;
          }
        }
      }
    });

    input.addEventListener('input', function () {
      renderResults(this.value.trim());
    });

    function renderResults(query) {
      var q = query.toLowerCase();
      currentFiltered = searchIndex.filter(function (item) {
        if (!q) return true;
        return item.title.toLowerCase().indexOf(q) !== -1 ||
               item.desc.toLowerCase().indexOf(q) !== -1 ||
               item.cat.toLowerCase().indexOf(q) !== -1;
      });

      selectedIndex = 0;

      if (currentFiltered.length === 0) {
        resultsBox.innerHTML = '<div style="padding:32px 16px;text-align:center;color:#64748b;font-size:14px;"><i class="fas fa-search fa-2x mb-2" style="opacity:0.4;"></i><p>No results found for "' + query + '"</p></div>';
        return;
      }

      var html = '';
      var lastCat = '';
      currentFiltered.forEach(function (item, idx) {
        if (item.cat !== lastCat && !query) {
          html += '<div class="vcp-category-label">' + item.cat + '</div>';
          lastCat = item.cat;
        }
        var isSelected = (idx === selectedIndex) ? ' selected' : '';
        html += [
          '<a href="' + item.url + '" class="vcp-item' + isSelected + '" data-idx="' + idx + '">',
          '  <div class="vcp-item-icon"><i class="fas ' + item.icon + '"></i></div>',
          '  <div class="vcp-item-info">',
          '    <div class="vcp-item-title">' + item.title + '</div>',
          '    <div class="vcp-item-desc">' + item.desc + '</div>',
          '  </div>',
          '  <span class="vcp-item-badge">' + item.badge + '</span>',
          '</a>'
        ].join('\n');
      });

      resultsBox.innerHTML = html;
    }

    function updateSelection() {
      var items = resultsBox.querySelectorAll('.vcp-item');
      items.forEach(function (el, idx) {
        if (idx === selectedIndex) {
          el.classList.add('selected');
          el.scrollIntoView({ block: 'nearest' });
        } else {
          el.classList.remove('selected');
        }
      });
    }

    // Attach to any search button with .nav-search-trigger
    document.querySelectorAll('.nav-search-trigger').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        window.vortexOpenPalette();
      });
    });
  }

  // ══════════════════════════════════════════════════════════════════
  // 4. LIVE SOCIAL PROOF TRUST NOTIFICATION TOAST
  // ══════════════════════════════════════════════════════════════════
  function initSocialProofToast() {
    var proofEvents = [
      { icon: 'fa-landmark', title: 'US Title Agency (Texas)', action: 'Requested 40-Year Abstracting SLA proposal', time: '12 mins ago', url: 'title-settlement/index.php' },
      { icon: 'fa-user-md', title: 'Healthcare Network (Florida)', action: 'Onboarded 8 FTEs for Medical Coding & RCM', time: '28 mins ago', url: 'service.php#healthcare' },
      { icon: 'fa-brain', title: 'Enterprise Logistics Group', action: 'Deployed custom AI automation bot', time: '45 mins ago', url: 'index.php#enterprise-capabilities' },
      { icon: 'fa-crown', title: 'Title Settlement Firm (California)', action: 'Ordered 2-Owner Title Search package', time: '1 hour ago', url: 'title-settlement/index.php' },
      { icon: 'fa-briefcase', title: 'Senior Medical Coder Candidate', action: 'Submitted application for Bengaluru center', time: '1 hour ago', url: 'careers.php' },
      { icon: 'fa-book', title: 'Academic Publisher (UK)', action: 'Contracted for S1000D XML conversion', time: '2 hours ago', url: 'publishing-services/index.php' }
    ];

    var toastHtml = [
      '<div id="vortexSocialProofToast" role="alert" aria-live="polite">',
      '  <div class="spt-icon-wrap" id="sptIcon"><i class="fas fa-landmark"></i></div>',
      '  <div class="spt-content" id="sptContent">',
      '    <div class="spt-title" id="sptTitle">US Title Agency (Texas)</div>',
      '    <div class="spt-meta">',
      '      <span class="spt-verified"><i class="fas fa-check-circle"></i> Verified</span> &bull;',
      '      <span id="sptTime">12 mins ago</span>',
      '    </div>',
      '  </div>',
      '  <button type="button" class="spt-close" onclick="window.vortexDismissToast(event)">&times;</button>',
      '</div>'
    ].join('\n');

    document.body.insertAdjacentHTML('beforeend', toastHtml);

    var toast = document.getElementById('vortexSocialProofToast');
    var iconEl = document.getElementById('sptIcon');
    var titleEl = document.getElementById('sptTitle');
    var timeEl = document.getElementById('sptTime');

    var currentIndex = 0;
    var isPaused = false;
    var toastTimeout = null;

    window.vortexDismissToast = function (e) {
      if (e) e.stopPropagation();
      toast.classList.remove('show');
      sessionStorage.setItem('vortex_toast_dismissed', 'true');
    };

    if (sessionStorage.getItem('vortex_toast_dismissed') === 'true') {
      return; // Respect user dismissal for this session
    }

    toast.addEventListener('mouseenter', function () { isPaused = true; });
    toast.addEventListener('mouseleave', function () { isPaused = false; });
    toast.addEventListener('click', function () {
      var item = proofEvents[currentIndex];
      if (item && item.url) {
        window.location.href = item.url;
      }
    });

    function showNextToast() {
      if (isPaused) {
        toastTimeout = setTimeout(showNextToast, 5000);
        return;
      }

      var item = proofEvents[currentIndex];
      iconEl.innerHTML = '<i class="fas ' + item.icon + '"></i>';
      titleEl.innerHTML = '<strong>' + item.title + '</strong>: ' + item.action;
      timeEl.textContent = item.time;

      toast.classList.add('show');

      // Auto-hide after 6.5s
      setTimeout(function () {
        toast.classList.remove('show');
      }, 6500);

      currentIndex = (currentIndex + 1) % proofEvents.length;
      toastTimeout = setTimeout(showNextToast, 28000); // Trigger next every 28s
    }

    // First display after 8 seconds of browsing
    setTimeout(showNextToast, 8000);
  }

  // ══════════════════════════════════════════════════════════════════
  // 5. 1-CLICK SOCIAL SHARE & CLIPBOARD HELPER
  // ══════════════════════════════════════════════════════════════════
  function initShareToast() {
    var toast = document.createElement('div');
    toast.className = 'vortex-copied-toast';
    toast.id = 'vortexCopiedToast';
    toast.innerHTML = '<i class="fas fa-check-circle text-success"></i> Link copied to clipboard!';
    document.body.appendChild(toast);

    window.vortexShowCopied = function (msg) {
      if (msg) toast.innerHTML = '<i class="fas fa-check-circle text-success"></i> ' + msg;
      toast.classList.add('show');
      setTimeout(function () {
        toast.classList.remove('show');
      }, 2500);
    };

    window.vortexShare = function (title, text, url) {
      url = url || window.location.href;
      title = title || document.title;
      text = text || '';

      if (navigator.share) {
        navigator.share({ title: title, text: text, url: url }).catch(function () {});
      } else {
        // Fallback: Copy to clipboard
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(url).then(function () {
            window.vortexShowCopied('Link copied to clipboard!');
          });
        } else {
          var input = document.createElement('input');
          input.value = url;
          document.body.appendChild(input);
          input.select();
          document.execCommand('copy');
          document.body.removeChild(input);
          window.vortexShowCopied('Link copied to clipboard!');
        }
      }
    };
  }

  // ══════════════════════════════════════════════════════════════════
  // 6. CORE WEB VITALS & REAL-USER PERFORMANCE OBSERVER (Free Beacon)
  // ══════════════════════════════════════════════════════════════════
  function initPerformanceObserver() {
    if (!('performance' in window) || !('PerformanceObserver' in window)) return;
    try {
      var vitals = { LCP: 0, FID: 0, CLS: 0, TTFB: 0 };

      // TTFB
      var navEntry = performance.getEntriesByType('navigation')[0];
      if (navEntry) {
        vitals.TTFB = Math.round(navEntry.responseStart - navEntry.requestStart);
      }

      // LCP (Largest Contentful Paint)
      var lcpObserver = new PerformanceObserver(function (entryList) {
        var entries = entryList.getEntries();
        if (entries.length > 0) {
          var last = entries[entries.length - 1];
          vitals.LCP = Math.round(last.startTime);
        }
      });
      lcpObserver.observe({ type: 'largest-contentful-paint', buffered: true });

      // CLS (Cumulative Layout Shift)
      var clsObserver = new PerformanceObserver(function (entryList) {
        for (var i = 0; i < entryList.getEntries().length; i++) {
          var entry = entryList.getEntries()[i];
          if (!entry.hadRecentInput) {
            vitals.CLS += entry.value;
          }
        }
        vitals.CLS = Number(vitals.CLS.toFixed(3));
      });
      clsObserver.observe({ type: 'layout-shift', buffered: true });

      // Save to window for inspection
      window.__vortexVitals = vitals;
    } catch (e) {
      // Graceful fallback on older browsers
    }
  }

  // ══════════════════════════════════════════════════════════════════
  // 7. NETWORK OFFLINE / ONLINE GUARDIAN
  // ══════════════════════════════════════════════════════════════════
  function initNetworkGuardian() {
    var netToast = document.createElement('div');
    netToast.id = 'vortexNetToast';
    netToast.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#0F172A;color:#fff;padding:12px 20px;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,0.25);font-size:13px;font-weight:600;display:none;z-index:999999;align-items:center;gap:10px;font-family:Inter,sans-serif;transition:all .3s ease;';
    document.body.appendChild(netToast);

    window.addEventListener('offline', function () {
      netToast.style.background = '#e11d48';
      netToast.innerHTML = '<i class="fas fa-wifi-slash"></i> You are currently offline. Pages are served via offline cache.';
      netToast.style.display = 'flex';
    });

    window.addEventListener('online', function () {
      netToast.style.background = '#10b981';
      netToast.innerHTML = '<i class="fas fa-check-circle"></i> Connection restored! You are back online.';
      netToast.style.display = 'flex';
      setTimeout(function () {
        netToast.style.display = 'none';
      }, 3500);
    });
  }

  // ══════════════════════════════════════════════════════════════════
  // 8. IMAGE ERROR SELF-HEALING & LAZY-LOAD RECOVERY
  // ══════════════════════════════════════════════════════════════════
  function initImageSelfHealing() {
    document.addEventListener('error', function (e) {
      if (e.target && e.target.tagName === 'IMG') {
        var img = e.target;
        if (!img.dataset.failed) {
          img.dataset.failed = 'true';
          // Fallback to high-res brand banner if an image fails to load
          if (!img.src.includes('logo')) {
            img.src = '/assets/images/vortexsoft-share-banner.png';
            img.style.objectFit = 'cover';
          }
        }
      }
    }, true);
  }

  // ══════════════════════════════════════════════════════════════════
  // 9. 3D PERSPECTIVE TILT PHYSICS & DYNAMIC SPECULAR GLARE (Stripe/Linear)
  // ══════════════════════════════════════════════════════════════════
  function init3DTiltPhysics() {
    var cards = document.querySelectorAll('.motion-tilt, .service-card, .platform-card, .contact-card, .faq-item, .hero-stat-card');
    if (!cards.length) return;

    cards.forEach(function (card) {
      if (!card.querySelector('.motion-glare-overlay')) {
        var glare = document.createElement('div');
        glare.className = 'motion-glare-overlay';
        card.appendChild(glare);
      }

      card.addEventListener('mousemove', function (e) {
        var rect = card.getBoundingClientRect();
        var x = e.clientX - rect.left;
        var y = e.clientY - rect.top;
        var centerX = rect.width / 2;
        var centerY = rect.height / 2;

        var rotateX = ((y - centerY) / centerY) * -6; // Max 6 deg
        var rotateY = ((x - centerX) / centerX) * 6;  // Max 6 deg

        card.style.transform = 'perspective(1000px) rotateX(' + rotateX.toFixed(2) + 'deg) rotateY(' + rotateY.toFixed(2) + 'deg) translateY(-4px)';
        card.style.setProperty('--mouse-x', Math.round((x / rect.width) * 100) + '%');
        card.style.setProperty('--mouse-y', Math.round((y / rect.height) * 100) + '%');
      });

      card.addEventListener('mouseleave', function () {
        card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateY(0px)';
        card.style.transition = 'transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease';
      });

      card.addEventListener('mouseenter', function () {
        card.style.transition = 'transform 0.08s ease-out';
      });
    });
  }

  // ══════════════════════════════════════════════════════════════════
  // 10. AMBIENT DARK SPOTLIGHT MESH (Linear/Vercel Ethereal Glow)
  // ══════════════════════════════════════════════════════════════════
  function initAmbientSpotlightMesh() {
    var darkSections = document.querySelectorAll('.page-hero, .cta-banner, .footer, .contact-info-card');
    darkSections.forEach(function (sec) {
      sec.classList.add('spotlight-dark-mesh');
      sec.addEventListener('mousemove', function (e) {
        var rect = sec.getBoundingClientRect();
        var x = Math.round(e.clientX - rect.left);
        var y = Math.round(e.clientY - rect.top);
        sec.style.setProperty('--spot-x', x + 'px');
        sec.style.setProperty('--spot-y', y + 'px');
      }, { passive: true });
    });
  }

  // ══════════════════════════════════════════════════════════════════
  // 11. KINETIC VIEWPORT COUNTER ENGINE
  // ══════════════════════════════════════════════════════════════════
  function initKineticCounters() {
    var statElements = document.querySelectorAll('.counter-num, .stat-num, [data-counter]');
    if (!statElements.length) return;

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          var el = entry.target;
          observer.unobserve(el);

          var rawText = el.getAttribute('data-counter') || el.innerText.trim();
          var match = rawText.match(/([0-9.,]+)/);
          if (!match) return;

          var targetNum = parseFloat(match[1].replace(/,/g, ''));
          var prefix = rawText.slice(0, match.index);
          var suffix = rawText.slice(match.index + match[0].length);
          var isDecimal = match[1].includes('.');
          var startTime = performance.now();
          var duration = 1800; // 1.8s smooth count

          function updateCounter(now) {
            var progress = Math.min((now - startTime) / duration, 1);
            // EaseOutExpo
            var ease = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
            var current = targetNum * ease;

            el.innerText = prefix + (isDecimal ? current.toFixed(1) : Math.floor(current)) + suffix;

            if (progress < 1) {
              requestAnimationFrame(updateCounter);
            } else {
              el.innerText = rawText;
            }
          }
          requestAnimationFrame(updateCounter);
        }
      });
    }, { threshold: 0.25 });

    statElements.forEach(function (el) {
      observer.observe(el);
    });
  }

  // ══════════════════════════════════════════════════════════════════
  // 12. ADVANCED MARKETING ATTRIBUTION & CONVERSION ENGINE
  // ══════════════════════════════════════════════════════════════════
  function initMarketingOptimization() {
    window.dataLayer = window.dataLayer || [];

    // 1. Capture UTM and ad platform click IDs
    var urlParams = new URLSearchParams(window.location.search);
    var trackingKeys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'fbclid', 'msclkid', 'ttclid'];
    var currentAttribution = {};
    var hasNewParams = false;

    trackingKeys.forEach(function (key) {
      if (urlParams.has(key)) {
        currentAttribution[key] = urlParams.get(key);
        hasNewParams = true;
      }
    });

    try {
      if (hasNewParams) {
        currentAttribution.captured_at = new Date().toISOString();
        currentAttribution.landing_page = window.location.pathname;
        currentAttribution.referrer = document.referrer || 'direct';
        localStorage.setItem('vortex_mkt_attrib', JSON.stringify(currentAttribution));
        sessionStorage.setItem('vortex_mkt_session', JSON.stringify(currentAttribution));
      }
    } catch (e) {}

    // Retrieve active attribution
    var storedAttrib = {};
    try {
      storedAttrib = JSON.parse(sessionStorage.getItem('vortex_mkt_session') || localStorage.getItem('vortex_mkt_attrib') || '{}');
    } catch (e) {}

    // 2. Auto-inject attribution into all forms on submit
    document.addEventListener('submit', function (e) {
      var form = e.target;
      if (!form || form.tagName !== 'FORM') return;

      var formKeys = ['utm_source', 'utm_medium', 'utm_campaign', 'gclid', 'fbclid'];
      formKeys.forEach(function (k) {
        if (storedAttrib[k] && !form.querySelector('input[name="' + k + '"]')) {
          var input = document.createElement('input');
          input.type = 'hidden';
          input.name = k;
          input.value = storedAttrib[k];
          form.appendChild(input);
        }
      });

      // Push form submission conversion event
      window.dataLayer.push({
        event: 'form_lead_submission',
        form_id: form.id || 'lead_form',
        form_action: form.getAttribute('action') || window.location.pathname,
        utm_source: storedAttrib.utm_source || 'direct',
        utm_campaign: storedAttrib.utm_campaign || 'organic'
      });
    }, true);

    // 3. Dynamic WhatsApp attribution appending
    document.addEventListener('click', function (e) {
      var a = e.target.closest('a[href*="api.whatsapp.com"], a[href*="wa.me"]');
      if (a) {
        window.dataLayer.push({
          event: 'whatsapp_conversion_click',
          link_url: a.href,
          utm_source: storedAttrib.utm_source || 'direct',
          utm_campaign: storedAttrib.utm_campaign || 'direct'
        });

        if (storedAttrib.utm_campaign || storedAttrib.utm_source) {
          try {
            var url = new URL(a.href);
            var textParam = url.searchParams.get('text') || '';
            var tag = ' [Ref: ' + (storedAttrib.utm_campaign || storedAttrib.utm_source) + ']';
            if (!textParam.includes(tag)) {
              url.searchParams.set('text', textParam + tag);
              a.href = url.toString();
            }
          } catch (err) {}
        }
      }

      // Track phone calls
      var tel = e.target.closest('a[href^="tel:"]');
      if (tel) {
        window.dataLayer.push({
          event: 'phone_call_click',
          phone_number: tel.getAttribute('href').replace('tel:', ''),
          click_location: window.location.pathname
        });
      }

      // Track email clicks
      var mail = e.target.closest('a[href^="mailto:"]');
      if (mail) {
        window.dataLayer.push({
          event: 'email_inquiry_click',
          email_address: mail.getAttribute('href').replace('mailto:', ''),
          click_location: window.location.pathname
        });
      }
    });

    // 4. Scroll Depth Tracking (SEO & Marketing Engagement Signals)
    var scrollMilestones = { '25%': false, '50%': false, '75%': false, '90%': false };
    var scrollThrottle = false;

    window.addEventListener('scroll', function () {
      if (scrollThrottle) return;
      scrollThrottle = true;
      setTimeout(function () { scrollThrottle = false; }, 250);

      var scrollY = window.pageYOffset || document.documentElement.scrollTop;
      var docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
      if (docHeight <= 0) return;

      var pct = (scrollY / docHeight) * 100;
      ['25%', '50%', '75%', '90%'].forEach(function (mark) {
        var threshold = parseInt(mark, 10);
        if (pct >= threshold && !scrollMilestones[mark]) {
          scrollMilestones[mark] = true;
          window.dataLayer.push({
            event: 'scroll_depth_milestone',
            scroll_depth: mark,
            page_path: window.location.pathname
          });
        }
      });
    }, { passive: true });

    // 5. High-Intent Time on Page Signals (30s and 60s)
    setTimeout(function () {
      window.dataLayer.push({ event: 'engaged_user_30s', page_path: window.location.pathname });
    }, 30000);
    setTimeout(function () {
      window.dataLayer.push({ event: 'engaged_user_60s', page_path: window.location.pathname });
    }, 60000);
  }

  // ══════════════════════════════════════════════════════════════════
  // INITIALIZATION ON DOM READY
  // ══════════════════════════════════════════════════════════════════
  function initAllExtensions() {
    initScrollProgress();
    initInstantPrefetch();
    initCommandPalette();
    // initSocialProofToast disabled to keep bottom left clean and free of popups
    initShareToast();
    initPerformanceObserver();
    initNetworkGuardian();
    initImageSelfHealing();
    init3DTiltPhysics();
    initAmbientSpotlightMesh();
    initKineticCounters();
    initMarketingOptimization();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAllExtensions);
  } else {
    initAllExtensions();
  }

})();
