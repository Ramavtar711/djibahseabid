const CACHE_NAME = 'djibahseabid-pwa-v1';
const OFFLINE_ASSETS = [
  './',
  './manifest.json',
  './pwa-192.png',
  './pwa-512.png',
  './home/assets/css/bootstrap.min.css',
  './home/assets/css/bootstrap-icons.css',
  './home/assets/css/nice-select.css',
  './home/assets/css/style.css',
  './home/assets/js/jquery-3.7.1.min.js',
  './home/assets/js/popper.min.js',
  './home/assets/js/bootstrap.min.js',
  './home/assets/js/jquery.nice-select.min.js',
  './home/assets/js/main.js',
  './home/assets/img/logo.png',
  './home/assets/img/djibah-logo.png'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(OFFLINE_ASSETS))
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(
        keys
          .filter((key) => key !== CACHE_NAME)
          .map((key) => caches.delete(key))
      )
    )
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') {
    return;
  }

  event.respondWith(
    caches.match(event.request).then((cachedResponse) => {
      if (cachedResponse) {
        return cachedResponse;
      }

      return fetch(event.request)
        .then((networkResponse) => {
          if (!networkResponse || networkResponse.status !== 200 || networkResponse.type !== 'basic') {
            return networkResponse;
          }

          const responseClone = networkResponse.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(event.request, responseClone));
          return networkResponse;
        })
        .catch(() => caches.match('./'));
    })
  );
});
