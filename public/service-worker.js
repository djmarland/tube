var VERSION = 1,
    CACHE_NAME = 'page-cache-' + VERSION,
    urlsToCache = [
        '/',
        '/settings'
    ];

self.addEventListener('install', function(event) {
    // Perform install steps
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function(cache) {
                return cache.addAll(urlsToCache);
            })
    );
});

self.addEventListener('message', function(event) {
    var data = event.data;
    switch (data.type) {
        case 'cacheUrl':
            addToCache(data.value);
    }
});

self.addEventListener('push', function(event) {
    console.log('Received a push message', event);

    var title = 'Yay a message.';
    var body = 'We have received a push message.';
    var icon = '/images/icon-192x192.png';
    var tag = 'simple-push-demo-notification-tag';

    event.waitUntil(
        self.registration.showNotification(title, {
            body: body,
            icon: icon,
            tag: tag
        })
    );
});

function addToCache(url) {
    caches.open(CACHE_NAME)
        .then(function(cache) {
            cache.add(url);
        });
}

//self.addEventListener('install', function(event) {
//    // Perform install steps
//    event.waitUntil(
//        caches.open(CACHE_NAME)
//            .then(function(cache) {
//                return cache.addAll(urlsToCache);
//            })
//    );
//});
//
//self.addEventListener('fetch', function(event) {
//    event.respondWith(
//        caches.match(event.request)
//            .then(function(response) {
//                // Cache hit - return response
//                if (response) {
//                    return response;
//                }
//                var fetchRequest = event.request.clone();
//
//                return fetch(fetchRequest).then(
//                    function(response) {
//                        // Check if we received a valid response
//                        if(!response || response.status !== 200 || response.type !== 'basic') {
//                            return response;
//                        }
//
//                        if (fetchRequest.path) {
//
//                        }
//                        var responseToCache = response.clone();
//
//                        caches.open(CACHE_NAME)
//                            .then(function(cache) {
//                                cache.put(event.request, responseToCache);
//                            });
//
//                        return response;
//                    }
//                );
//            })
//    );
//});

//self.addEventListener('activate', function(event) {
//    console.log('active!!');
//
//});
//
//self.addEventListener('push', function(event, data) {
//    console.log('Received a push', event);
//
//    fetch('/clip-push-hack/latest.json?cache=' + Math.random())
//        .then(function(response) {
//            response.json().then(function(object) {
//                var synopsis = object.synopsis;
//                if (object.context.length) {
//                    synopsis += ' (' + object.context + ')';
//                }
//
//                self.registration.showNotification(object.title, {
//                    body: synopsis,
//                    icon: object.image,
//                    tag: object.url
//                });
//
//                self.addEventListener('notificationclick', function(event) {
//                    event.notification.close();
//                    clients.openWindow(object.url);
//                }, false);
//            });
//        });
//});