// Service worker minimal pour rendre le panel admin installable (PWA).
// Stratégie : réseau d'abord pour tout (l'admin doit toujours voir les
// données à jour), avec un repli sur le cache uniquement pour les fichiers
// statiques (icônes, CSS, JS) en cas de coupure réseau ponctuelle.
const CACHE_NAME = 'daoukro-admin-v1';
const ASSETS_STATIQUES = [
  '/images/logo.png',
  '/images/icon-192.png',
  '/images/icon-512.png',
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(ASSETS_STATIQUES))
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((clefs) =>
      Promise.all(clefs.filter((c) => c !== CACHE_NAME).map((c) => caches.delete(c)))
    )
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  const { request } = event;

  if (request.method !== 'GET') return;

  const estAssetStatique = /\.(png|jpg|jpeg|svg|webp|ico|css|js|woff2?)$/.test(
    new URL(request.url).pathname
  );

  if (estAssetStatique) {
    // Cache d'abord pour les fichiers statiques, réseau en repli + mise à jour du cache.
    event.respondWith(
      caches.match(request).then((reponseEnCache) => {
        const fetchPromise = fetch(request).then((reponseReseau) => {
          caches.open(CACHE_NAME).then((cache) => cache.put(request, reponseReseau.clone()));
          return reponseReseau;
        }).catch(() => reponseEnCache);
        return reponseEnCache || fetchPromise;
      })
    );
    return;
  }

  // Pages/API : réseau d'abord, jamais de données admin périmées servies silencieusement.
  event.respondWith(
    fetch(request).catch(() => caches.match(request))
  );
});
