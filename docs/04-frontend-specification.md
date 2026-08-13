# 04 — Frontend & UI Specification
**Vortexsoft Innovations Pvt. Ltd.**
*Confirmed from codebase — August 2026*

---

## Brand Colors (Confirmed from CSS / source)

| Token | Hex | Usage |
|---|---|---|
| Primary Dark (Navy) | `#1C2280` | Primary brand, headings, buttons, links |
| Primary Deep Dark | `#080B1A` | Page backgrounds, header, dark sections |
| Accent Red | `#CC2228` | CTAs, badges, accent icons, hover states |
| Bright Red | `#DE252A` | Loader animation gradient endpoint |
| Accent Blue | `#5BA8D4` | Secondary gradient, highlights |
| Purple | `#943BA8` | Gradient mid-point (loader) |
| Success Green | `#10b981` | Status badges, success messages |
| Warning Amber | `#f59e0b` | Gold trust icons |
| Text Body | `#475569` | Body text |
| Text Dark | `#1e293b` | Dark body text |
| Text Muted | `#94a3b8` | Placeholder, muted text |
| Text Light | `#64748b` | Subtle text |
| Background Light | `#f0f2ff` | Admin panel background |
| White | `#ffffff` | Card backgrounds, forms |

---

## Typography (Confirmed from `assets/vendor/fonts.css`)

| Element | Font | Weight | Size |
|---|---|---|---|
| Headings (h1–h6) | Poppins | 700 | `clamp(32px, 6vw, 56px)` for hero |
| Body | Inter | 400 | 15px default |
| Navigation text | Poppins | 500–600 | 13–14px |
| Buttons | Poppins | 600 | 14–15px |
| Admin labels | Inter | 600 | 13px |
| Loader brand | Poppins | 900 | `clamp(32px, 6vw, 56px)` |

---

## Layout (Confirmed)

| Property | Value |
|---|---|
| Max container width | Bootstrap 5.3 default (~1320px) |
| Sticky navbar height | ~115px desktop, ~70px mobile |
| Content padding (sections) | `py-5` (48px top/bottom) |
| Grid | Bootstrap 5.3 12-column |
| Breakpoints | Bootstrap 5.3: xs <576, sm 576, md 768, lg 992, xl 1200, xxl 1400 |

---

## Component Inventory (Confirmed)

### Navbar
- Sticky (`position: sticky; top: 0; z-index: 1030`)
- Background: `rgba(255,255,255,0.98)` + `backdrop-filter: blur(20px)`
- Logo height: 52px
- Has mega-menu for services (AI mega-menu)
- Mobile hamburger behavior

### Footer
- Dark background (`#080B1A`)
- 4-column layout: Brand+Social, Quick Links, Services, HQ+Contact+Newsletter
- Trust marquee strip above footer (animated scroll)
- Footer bottom bar: Copyright, FAQ, Privacy Policy, Terms & Conditions
- CTA banner before footer

### Page Hero
- Dark gradient: `linear-gradient(135deg, #080B1A, #1C2280, #0D1035)`
- Padding: `90px 0 80px`

### Buttons
- `.btn-cta-white` — white button for dark hero CTAs
- `.btn-hero-secondary` — border button for secondary actions
- `.btn-login` — gradient blue for admin login
- Bootstrap `.btn` classes used throughout

### Cards
- Service cards with icon, title, description
- Team/stats cards with gradient accents
- Blog post cards
- Trust/certification cards

### Modals (Bootstrap)
- `#contactSuccessModal` — contact form success
- `#applySuccessModal` — application submitted success
- Job apply modal (inline on careers page)

### Forms
- Contact form: `fullName`, `emailAddr`, `phone`, `service`, `company`, `msgText` + `website_hp` (honeypot)
- Apply form: full candidate form with file upload
- Newsletter form: email input + subscribe button (in footer)
- Admin login form: username + password

### Page Loader
- Full-screen overlay on page load: `#080B1A` background
- Animated gradient progress bar
- Brand name with color-split (blue + red)
- Dismissed after 50ms + `window.load` event

### Trust Marquee
- Infinite CSS scroll animation (40s linear)
- 8 trust items (ISO 27001, HIPAA, Startup India, etc.)
- Fade edges with `::before`/`::after` gradient overlays

### WhatsApp Button
- Fixed floating position
- Links to WhatsApp API with pre-filled message

### Scroll-to-Top Button
- `#scrollTop` fixed bottom right

---

## Responsive Design (Confirmed)

| Breakpoint | Navbar | Footer | Service Cards | Hero |
|---|---|---|---|---|
| Mobile (<768px) | Hamburger | 1 column | 1 column | Reduced padding |
| Tablet (768–992px) | Hamburger | 2 columns | 2 columns | — |
| Desktop (>992px) | Full nav | 4 columns | 3–4 columns | Full hero |

### Known Responsive Issues (REQUIRES VERIFICATION on live device)
- Mega-menu on mobile: complex, needs browser testing
- Trust marquee: may overflow on very small screens
- Admin panel sidebar: collapses on mobile (toggle mechanism REQUIRES VERIFICATION)

---

## Animations & Micro-interactions (Confirmed)

- Page load progress bar animation (1s cubic-bezier)
- `.scroll-reveal` class on elements with `transition-delay` staggering
- Hover transform on cards: `translateY(-2px)`
- `.magnetic` button class (referenced in CTA buttons)
- Loader color gradient animation
- Trust marquee: 40s linear scroll

---

## Assets (Confirmed)

| File | Purpose |
|---|---|
| `/logo-header.png` | Navbar logo (1018KB — very large) |
| `/logo-footer.png` | Footer logo (1018KB — very large) |
| `/logo-footer-new.png` | Updated footer logo (1278KB — very large) |
| `/logo-header.jpg` | JPG fallback (102KB) |
| `/logo-footer-new.jpg` | JPG fallback (61KB) |
| `/icon.jpg` | PWA icon / favicon (61KB) |
| `vortex-shared.css` | Main stylesheet (30KB) |
| `vortex-shared.js` | Main JS (14KB) |
| `assets/vendor/bootstrap.min.css` | Bootstrap 5.3 |
| `assets/vendor/bootstrap.bundle.min.js` | Bootstrap JS |
| `assets/vendor/fontawesome/all.min.css` | FontAwesome 6 |
| `assets/vendor/fonts.css` | Google Fonts local loader |

> ⚠️ **PERFORMANCE ISSUE**: Logo PNG files are 1MB+. These should be compressed and/or served as WebP. See ticket WEB-PERF-001.

> ⚠️ **PWA ISSUE**: `manifest.json` shortcuts use `.html` URLs (`/contact.html`, `/careers.html`, `/service.html`). These are .htaccess redirected to `.php` but may cause extra redirect round trips.
