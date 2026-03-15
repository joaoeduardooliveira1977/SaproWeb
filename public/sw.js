const CACHE_NAME = 'sapro-v1';

// Recursos estáticos que ficam em cache
const PRECACHE = [
    '/offline.html',
    '/icons/icon.svg',
    '/favicon.ico',
];

// ── Install ──────────────────────────────────────────────────────────────────
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(PRECACHE))
    );
    self.skipWaiting();
});

// ── Activate ─────────────────────────────────────────────────────────────────
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k))
            )
        )
    );
    self.clients.claim();
});

// ── Fetch ────────────────────────────────────────────────────────────────────
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Ignora: não-GET, outros origins, Livewire wire-requests, API calls
    if (request.method !== 'GET') return;
    if (url.origin !== location.origin) return;
    if (url.pathname.startsWith('/livewire/')) return;
    if (request.headers.get('X-Livewire')) return;

    // Recursos estáticos: cache-first
    const isStatic = /\.(css|js|woff2?|ttf|svg|png|jpg|jpeg|gif|ico|webp)(\?.*)?$/.test(url.pathname);
    if (isStatic) {
        event.respondWith(
            caches.match(request).then(cached => {
                if (cached) return cached;
                return fetch(request).then(response => {
                    if (!response || response.status !== 200 || response.type === 'opaque') {
                        return response;
                    }
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(request, clone));
                    return response;
                });
            })
        );
        return;
    }

    // Páginas HTML: network-first, fallback para offline.html
    if (request.headers.get('Accept')?.includes('text/html')) {
        event.respondWith(
            fetch(request).catch(() => caches.match('/offline.html'))
        );
    }
});
