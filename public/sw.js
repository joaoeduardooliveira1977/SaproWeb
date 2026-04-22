const CACHE_NAME = 'saproweb-v3';

const PRECACHE = [
    '/offline.html',
    '/icons/icon.svg',
    '/icons/icon-maskable.svg',
    '/favicon.ico',
    '/manifest.json',
];

// ── Install ───────────────────────────────────────────────────────────────────
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(PRECACHE))
    );
    self.skipWaiting();
});

// ── Activate ──────────────────────────────────────────────────────────────────
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
        )
    );
    self.clients.claim();
});

// ── Fetch ─────────────────────────────────────────────────────────────────────
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Ignora: não-GET, outros origins, Livewire, portal
    if (request.method !== 'GET') return;
    if (url.origin !== location.origin) return;
    if (url.pathname.startsWith('/livewire/')) return;
    if (url.pathname.startsWith('/portal/')) return;
    if (request.headers.get('X-Livewire')) return;

    // Estáticos: cache-first
    const isStatic = /\.(css|js|woff2?|ttf|svg|png|jpg|jpeg|gif|ico|webp)(\?.*)?$/.test(url.pathname);
    if (isStatic) {
        event.respondWith(
            caches.match(request).then(cached => {
                if (cached) return cached;
                return fetch(request).then(response => {
                    if (response && response.status === 200 && response.type !== 'opaque') {
                        caches.open(CACHE_NAME).then(c => c.put(request, response.clone()));
                    }
                    return response;
                }).catch(() => new Response('', { status: 408 }));
            })
        );
        return;
    }

    // Páginas HTML: network-first, fallback cache, fallback offline
    if (request.headers.get('Accept')?.includes('text/html')) {
        event.respondWith(
            fetch(request, { credentials: 'include' })
                .then(response => {
                    if (response && response.status === 200) {
                        caches.open(CACHE_NAME).then(c => c.put(request, response.clone()));
                    }
                    return response;
                })
                .catch(() =>
                    caches.match(request).then(cached => cached || caches.match('/offline.html'))
                )
        );
    }
});
