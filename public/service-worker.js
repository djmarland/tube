var VERSION = 4,
    CACHE_NAME = 'page-cache-' + VERSION,
    urlsToCache = [
        '/',
        '/settings',
        '/bakerloo-line',
        '/central-line',
        '/circle-line',
        '/district-line',
        '/hammersmith-city-line',
        '/jubilee-line',
        '/metropolitan-line',
        '/northern-line',
        '/piccadilly-line',
        '/victoria-line',
        '/waterloo-city-line',
        '/dlr',
        '/london-overground',
        '/tfl-rail'
    ];

self.addEventListener('install', function(event) {
    // Perform install steps
    console.log(VERSION);
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function(cache) {
                return cache.addAll(urlsToCache);
            })
    );
});

self.addEventListener('push', function(event) {
    return event.waitUntil(
        self.registration.pushManager.getSubscription().then(function(subscription) {
            if (!subscription) {
                return null;
            }
            return subscription.endpoint;
        }).then(function(endpoint) {
            return fetch('/notifications/latest?endpoint=' + endpoint)
                .then(function(response) {
                    return response.json();
                });
        }).then(function(notification) {
            return self.registration.showNotification(notification.title, {
                body: notification.description,
                icon: notification.image,
                tag: notification.url
            });
        })
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    // This looks to see if the current is already open and
    // focuses if it is
    event.waitUntil(
        clients.matchAll({
                type: "window"
            })
            .then(function(clientList) {
                for (var i = 0; i < clientList.length; i++) {
                    var client = clientList[i],
                        url = client.url,
                        parts = url.split('/'),
                        path = '/' + parts[parts.length-1];
                    if (path == event.notification.tag && 'focus' in client) {
                        return client.focus();
                    }
                }
                if (clients.openWindow) {
                    return clients.openWindow(event.notification.tag);
                }
            })
    );
});

self.addEventListener('fetch', function(event) {
    event.respondWith(
        caches.open(CACHE_NAME).then(function(cache) {
            return cache.match(event.request).then(function (response) {
                return response || fetch(event.request).then(function(response) {
                        return response;
                    });
            });
        })
    );
});

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