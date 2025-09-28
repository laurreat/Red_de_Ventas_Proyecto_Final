const CACHE_NAME = 'arepa-llanerita-v1.0.2';
const urlsToCache = [
  '/manifest.json',
  '/build/assets/app-HM29PgAA.js',
  '/build/assets/app-CvM0rU53.css',
  '/images/favicon.svg',
  '/images/icons/icon-192x192.png',
  '/images/icons/icon-512x512.png',
  '/images/icons/icon-144x144.png'
];

// Eventos principales del service worker
self.addEventListener('install', event => {
  console.log('[SW] Install');
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('[SW] Precaching App Shell');
        return cache.addAll(urlsToCache);
      })
      .catch(err => {
        console.error('[SW] Failed to cache', err);
      })
  );
});

self.addEventListener('activate', event => {
  console.log('[SW] Activate');
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            console.log('[SW] Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

// Estrategia de cache: Network First, fallback to Cache
self.addEventListener('fetch', event => {
  const url = new URL(event.request.url);

  // Solo cachear requests del mismo origen
  if (url.origin !== location.origin) {
    return;
  }

  // Estrategia especial para páginas HTML
  if (event.request.destination === 'document') {
    event.respondWith(
      fetch(event.request)
        .then(response => {
          // Si la respuesta es exitosa, clona y guarda en cache (solo GET)
          if (response.status === 200 && event.request.method === 'GET') {
            const responseClone = response.clone();
            caches.open(CACHE_NAME).then(cache => {
              cache.put(event.request, responseClone);
            });
          }
          return response;
        })
        .catch(() => {
          // Si falla la red, intenta desde cache
          return caches.match(event.request)
            .then(cachedResponse => {
              if (cachedResponse) {
                return cachedResponse;
              }
              // Página offline de fallback
              return caches.match('/offline.html');
            });
        })
    );
    return;
  }

  // Para assets estáticos: Cache First
  if (event.request.destination === 'style' ||
      event.request.destination === 'script' ||
      event.request.destination === 'image') {

    event.respondWith(
      caches.match(event.request)
        .then(cachedResponse => {
          if (cachedResponse) {
            return cachedResponse;
          }
          return fetch(event.request)
            .then(response => {
              // Solo cachear respuestas exitosas de métodos GET
              if (response.status === 200 && event.request.method === 'GET') {
                const responseClone = response.clone();
                caches.open(CACHE_NAME).then(cache => {
                  cache.put(event.request, responseClone);
                });
              }
              return response;
            });
        })
    );
    return;
  }

  // Para API calls: Network First con timeout
  if (event.request.url.includes('/api/')) {
    event.respondWith(
      Promise.race([
        fetch(event.request),
        new Promise((_, reject) =>
          setTimeout(() => reject(new Error('timeout')), 5000)
        )
      ])
      .then(response => {
        // Cachear solo respuestas GET exitosas
        if (event.request.method === 'GET' && response.status === 200) {
          const responseClone = response.clone();
          caches.open(CACHE_NAME).then(cache => {
            cache.put(event.request, responseClone);
          });
        }
        return response;
      })
      .catch(() => {
        if (event.request.method === 'GET') {
          return caches.match(event.request);
        }
        return new Response(
          JSON.stringify({ error: 'Offline', message: 'No hay conexión disponible' }),
          {
            status: 503,
            statusText: 'Service Unavailable',
            headers: new Headers({
              'Content-Type': 'application/json'
            })
          }
        );
      })
    );
    return;
  }

  // Para todo lo demás: Network First con fallback a cache
  event.respondWith(
    fetch(event.request)
      .then(response => {
        if (response.status === 200 && event.request.method === 'GET') {
          const responseClone = response.clone();
          caches.open(CACHE_NAME).then(cache => {
            cache.put(event.request, responseClone);
          });
        }
        return response;
      })
      .catch(() => {
        return caches.match(event.request);
      })
  );
});

// Manejo de mensajes desde la aplicación
self.addEventListener('message', event => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});

// Sincronización en segundo plano
self.addEventListener('sync', event => {
  if (event.tag === 'background-sync') {
    console.log('[SW] Background sync');
    event.waitUntil(doBackgroundSync());
  }
});

async function doBackgroundSync() {
  try {
    // Aquí puedes implementar sincronización de datos pendientes
    console.log('[SW] Performing background sync...');

    // Ejemplo: sincronizar pedidos offline
    const pendingRequests = await getPendingRequests();
    for (const request of pendingRequests) {
      try {
        await fetch(request.url, request.options);
        await removePendingRequest(request.id);
      } catch (error) {
        console.error('[SW] Failed to sync request:', error);
      }
    }
  } catch (error) {
    console.error('[SW] Background sync failed:', error);
  }
}

async function getPendingRequests() {
  // Implementar lógica para obtener requests pendientes desde IndexedDB
  return [];
}

async function removePendingRequest(id) {
  // Implementar lógica para remover request completado desde IndexedDB
}

// Push notifications
self.addEventListener('push', event => {
  if (event.data) {
    const options = {
      body: event.data.text(),
      icon: '/images/icons/icon-192x192.png',
      badge: '/images/icons/badge-72x72.png',
      vibrate: [200, 100, 200],
      data: {
        dateOfArrival: Date.now(),
        primaryKey: 1
      },
      actions: [
        {
          action: 'explore',
          title: 'Ver detalles',
          icon: '/images/icons/checkmark.png'
        },
        {
          action: 'close',
          title: 'Cerrar',
          icon: '/images/icons/xmark.png'
        }
      ]
    };

    event.waitUntil(
      self.registration.showNotification('Arepa la Llanerita', options)
    );
  }
});

// Manejo de clicks en notificaciones
self.addEventListener('notificationclick', event => {
  event.notification.close();

  if (event.action === 'explore') {
    event.waitUntil(
      clients.openWindow('/dashboard')
    );
  } else if (event.action === 'close') {
    // Simplemente cierra la notificación
  } else {
    event.waitUntil(
      clients.openWindow('/')
    );
  }
});