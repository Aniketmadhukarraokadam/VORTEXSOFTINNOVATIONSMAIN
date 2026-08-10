/* Vortexsoft Group — Service Worker v10 */
const CACHE = 'vortexsoft-v10';
const CRITICAL = [
  '/',
  '/index.php',
  '/about.php',
  '/service.php',
  '/contact.php',
  '/careers.php',
  '/blog.php',
  '/assets/vortex-shared.css',
  '/assets/vortex-shared.js',
  '/assets/vendor/bootstrap.min.css',
  '/assets/vendor/bootstrap.bundle.min.js',
  '/assets/vendor/fonts.css',
  '/logo-header.png',
  '/logo-footer-new.png',
  '/icon.jpg',
];

// Install: cache critical assets
self.addEventListener('install', e => {
  self.skipWaiting();
  e.waitUntil(
    caches.open(CACHE).then(cache => cache.addAll(CRITICAL))
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

// Fetch: cache-first for assets, network-first for HTML
self.addEventListener('fetch', e => {
  const url = new URL(e.request.url);
  if (e.request.method !== 'GET') return;

  // Network-first for HTML
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

  // Cache-first for assets (CSS, JS, fonts, images)
  e.respondWith(
    caches.match(e.request).then(cached => {
      if (cached) return cached;
      return fetch(e.request).then(res => {
        if (!res || res.status !== 200 || res.type !== 'basic') return res;
        const clone = res.clone();
        caches.open(CACHE).then(c => c.put(e.request, clone));
        return res;
      });
    })
  );
});
