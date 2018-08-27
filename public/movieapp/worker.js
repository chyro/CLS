var version = '0.01';
var cacheName = 'movieapp-v' + version;

var appFiles = [
  'index.php',
  'main.js'
  //,'' any others? CSS... fonts... font-awesome? wrist.watch?
];

/*
Since main.js can check the connection status and store things in the cache, the only
reason left for this file to exist is registering as a web app. For that, the only
thing required is caching all the relevant files.
Although catching failed queries could help I guess.
*/

self.addEventListener('install', function(e) {
    // once the SW is installed, go ahead and fetch the resources to make this work offline
    e.waitUntil(
        caches.open(cacheName).then(function(cache) {
            return cache.addAll(appFiles).then(function() {
                self.skipWaiting();
            });
        })
    );
});

self.addEventListener('fetch', function(event) {
    // performance boost: if the item queried is cached, use the cache version instead
    event.respondWith(
        caches.match(event.request).then(function(response) {
            if (response) {
                console.log("cached: " + event.request.url);
                return response; // retrieve from cache
            }
            console.log("not cached: " + event.request.url);
            return fetch(event.request); // fetch as normal
        }) // .catch(error => display 'not connected' message; if API call, store it)
    );
});

