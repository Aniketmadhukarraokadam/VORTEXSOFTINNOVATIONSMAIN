/* Vortexsoft Innovations Private Limited — Service Worker v14-opt */
const CACHE = 'vortexsoft-v14-opt';
const CRITICAL = [
  '/',
  '/index.php',
  '/about.php',
  '/service.php',
  '/contact.php',
  '/careers.php',
  '/blog.php',
  '/faq.php',
  '/privacy.php',
  '/terms.php',
  '/title-settlement/index.php',
  '/assets/vortex-shared.css?v=20261029opt',
  '/assets/vortex-shared.js?v=20261029opt',
  '/assets/usability-audit-fixes.css?v=20261029opt',
  '/assets/usability-audit-fixes.js?v=20261029opt',
  '/assets/vortex-extensions.css?v=20261029opt',
  '/assets/vortex-extensions.js?v=20261029opt',
  '/assets/vendor/bootstrap.min.css',
  '/assets/vendor/bootstrap.bundle.min.js',
  '/assets/vendor/fonts.css',
  '/logo-header.png',
  '/logo-footer.png',
  '/favicon.ico',
  '/favicon.png',
  '/apple-touch-icon.png',
  '/icon.jpg',
  '/assets/images/vortexsoft-share-banner.png',
  '/assets/images/vortexsoft-careers-share.png',
];

// Install: cache critical assets without blocking entire installation on minor failures
self.addEventListener('install', e => {
  self.skipWaiting();
  e.waitUntil(
    caches.open(CACHE).then(cache => {
      return Promise.allSettled(
        CRITICAL.map(url => cache.add(url).catch(() => {}))
      );
    })
  );
});

// Activate: clean old caches
self.addEventListener('activate', e => {
  e.waitUntil(
    caches.keys().then(keys =>
      Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k)))
    )
  );
  self.clients.claim();
});

// Fetch: cache-first with background revalidation for assets, network-first for HTML
self.addEventListener('fetch', e => {
  const url = new URL(e.request.url);
  if (e.request.method !== 'GET') return;

  // Network-first for HTML pages
  if (e.request.headers.get('accept')?.includes('text/html')) {
    e.respondWith(
      fetch(e.request)
        .then(res => {
          const clone = res.clone();
          caches.open(CACHE).then(c => c.put(e.request, clone));
          return res;
        })
        .catch(() => caches.match(e.request).then(r => r || caches.match('/404.php')))
    );
    return;
  }

  // Stale-while-revalidate / cache-first for static assets
  e.respondWith(
    caches.match(e.request).then(cached => {
      const fetchPromise = fetch(e.request).then(res => {
        if (res && res.status === 200 && (res.type === 'basic' || res.type === 'cors')) {
          const clone = res.clone();
          caches.open(CACHE).then(c => c.put(e.request, clone));
        }
        return res;
      }).catch(() => {});

      return cached || fetchPromise;
    })
  );
});
