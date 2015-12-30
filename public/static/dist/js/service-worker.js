var VERSION = 1,
    CACHE_NAME = 'page-cache-' + VERSION,
    urlsToCache = [
        '/',
        '/bakerloo-line',
        '/all.json',
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

self.addEventListener('fetch', function(event) {
    event.respondWith(
        caches.match(event.request)
            .then(function(response) {
                // Cache hit - return response
                if (response) {
                    return response;
                }
                var fetchRequest = event.request.clone();

                return fetch(fetchRequest).then(
                    function(response) {
                        // Check if we received a valid response
                        if(!response || response.status !== 200 || response.type !== 'basic') {
                            return response;
                        }
                        var responseToCache = response.clone();

                        caches.open(CACHE_NAME)
                            .then(function(cache) {
                                cache.put(event.request, responseToCache);
                            });

                        return response;
                    }
                );
            })
    );
});

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