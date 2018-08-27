/*
TODO:
- include service worker JS
- get cached array of movies
- wrist.watch - display local var of the array of movies
- if internet is available, update the array of movies cache
- add event: update movies
- add service worker event: if internet is not available, store the update query in cache
- add service worker event: if internet becomes available, run the stored queries
*/
var MovieApp = {
  settings: {
    apiURL: undefined,
    apiKey: undefined,
  },
  status: {
    moviesWatched: [],
    moviesWatchlist: [],
    moviesRecommended: [],
    friends: []
  },
  events: {
    // user input handlers
    // push handler
  },
  utils: {
    loadStatus: function() {
      /* initialize all the MovieApp.status variables based on the DB */
    },
    refreshStatus: function() { /*query API to get the movie lists from user and friends, notifications etc, store in DB*/ },
    //runQuery() { if online, run; if fail, or if not online, cache; after any successful query, re-load the status},
    //runQueries() { if any queries are cached, run them; },
    switchToOfflineMode: function() {
      console.log("became offline");
      /*set MovieApp.status.online to false; remove 'status-online' body class; add 'status-offline' body class;*/
    },
    switchToOnlineMode: function() {
      console.log("became online");
      /*set MovieApp.status.online to true; remove 'status-offline' body class; add 'status-online' body class; if any query is stored, run it now; if data is old, update now*/
    },
    showLoginForm: function() {
      // hide everything
      // show login form
      // set form events
    },
    /*bindModels: function() {
      // knockout view model bindings: ko.applyBindings(Page.vm);
      // MovieApp.status.watch('moviesWatched', redraw);
    },*/
    updateDBSchema(db) {
      // var objectStore = db.createObjectStore("name", { keyPath: "myKey" });
      // https://developer.mozilla.org/en-US/docs/Web/API/IndexedDB_API/Using_IndexedDB#Structuring_the_database
    }
  },
  init: function(params) {
    if (params['apiUrl'] == undefined) throw "API URL parameter required and not provided";
    MovieApp.apiUrl = params['apiUrl'];

    // register the service worker if available
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('./worker.js').then(function(reg) {
        console.log('Successfully registered service worker', reg);
      }).catch(function(err) {
        console.warn('Error whilst registering service worker', err);
      });
    }

    window.addEventListener('online', function(e) {
      MovieApp.utils.switchToOnlineMode();
    }, false);

    window.addEventListener('offline', function(e) {
      MovieApp.utils.switchToOffline();
    }, false);

    // open DB
    var request = window.indexedDB.open("MovieAppDB", 1); // 2nd param is version, if the DB version is lower than this onupgradeneeded is fired, which I can catch to update the schema
    request.onsuccess = function(event) { MovieApp.settings.db = event.target.result; }; // set that to a promise? have maybe bootstrap(){open DB as promise, query API as promise} and Promise.all([]).then(init);?
    request.onupgradeneeded = function(event) { MovieApp.utils.updateDBSchema(event.target.result); };

    // load status from storage
    MovieApp.utils.loadStatus();

    if (MovieApp.settings.apiKey == undefined) {
      MovieApp.utils.showLoginForm();
      return;
    }
    // else show the movie lists

    /*
    // check if the user is connected
    if (navigator.onLine) {
      console.log("currently online");
      // use stitchToOnlineMode? Or just set MovieApp.status.online to true?
    } else {
      console.log("currently offline");
      // use stitchToOfflineMode? Or just set MovieApp.status.online to false?
    }
    */

    //TODO: do something with this:
    // https://www.w3.org/TR/ambient-light
    // sensor.onchange = function(event) { set theme class to (sensor.illuminance > 5 ? 'day' : 'night'); }
  }
}

