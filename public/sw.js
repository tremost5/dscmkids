const CACHE_NAME = 'dscmkids-pwa-v1';
const URLS = ['/', '/berita', '/materi', '/favicon.ico', '/manifest.webmanifest'];

self.addEventListener('install', (event) => {
    event.waitUntil(caches.open(CACHE_NAME).then((cache) => cache.addAll(URLS)));
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key)))
        )
    );
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') {
        return;
    }

    const isNavigate = event.request.mode === 'navigate';

    event.respondWith(
        caches.match(event.request).then((cached) => {
            if (cached) return cached;
            return fetch(event.request)
                .then((response) => {
                    if (response.ok && event.request.url.startsWith(self.location.origin)) {
                        const cloned = response.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(event.request, cloned));
                    }
                    return response;
                })
                .catch(() => {
                    if (isNavigate) {
                        return caches.match('/');
                    }
                    return new Response('', { status: 504, statusText: 'Offline' });
                });
        })
    );
});
