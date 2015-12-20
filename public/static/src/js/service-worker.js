self.addEventListener('install', function(event) {
    var VERSION = 1;
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