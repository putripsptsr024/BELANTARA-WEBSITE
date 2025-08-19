const CACHE_NAME = 'toko-cache-v1';
const urlsToCache = [
  '/',
  '/index.html',
  '/assets/image/logo.png',
  '/assets/image/icon-192.png',
  '/assets/image/icon-512.png',
  '/assets/certificates/perizinan_thumbnail.jpg',
  '/assets/certificates/halal_thumbnail.jpg'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        return cache.addAll(urlsToCache);
      })
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        return response || fetch(event.request);
      })
  );
});