const CACHE_NAME = 'maternidade-plus-v1';
const ASSETS_TO_CACHE = [
    '/',
    '/dashboard',
    '/alertas',
    '/alertas/metricas',
    '/manifest.json',
    '/js/offline-alerts.js',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css',
    'https://cdn.jsdelivr.net/npm/chart.js',
    'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap'
];

// Instalação do Service Worker e pre-caching do App Shell
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('[Service Worker] Pré-carregando App Shell');
            return cache.addAll(ASSETS_TO_CACHE).catch((err) => {
                console.warn('[Service Worker] Alguns assets não puderam ser cacheados imediatamente:', err);
            });
        }).then(() => self.skipWaiting())
    );
});

// Ativação e limpeza de caches antigos
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cache) => {
                    if (cache !== CACHE_NAME) {
                        console.log('[Service Worker] Limpando cache antigo:', cache);
                        return caches.delete(cache);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Estratégia de Network-First com Fallback para Cache
self.addEventListener('fetch', (event) => {
    // Ignorar requisições não-GET ou de métodos POST/PUT/DELETE
    if (event.request.method !== 'GET') {
        return;
    }

    event.respondWith(
        fetch(event.request)
            .then((networkResponse) => {
                if (networkResponse && networkResponse.status === 200) {
                    const responseToCache = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseToCache);
                    });
                }
                return networkResponse;
            })
            .catch(() => {
                return caches.match(event.request).then((cachedResponse) => {
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    // Fallback para página offline se for navegação HTML
                    if (event.request.headers.get('accept')?.includes('text/html')) {
                        return caches.match('/dashboard');
                    }
                });
            })
    );
});
