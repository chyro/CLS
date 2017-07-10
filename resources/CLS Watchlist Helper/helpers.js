/**
 * This file contains utility code for use in the various other files.
 *
 * In particular, it initializes the credentials, which are used throughout the plugin. In
 * order to ensure the plugin is initialized before running the page code, every page code
 * should use to the 'CLS.loaded' promise as a base before running anything.
 */

var helpers = {
  // querries the Chrome API for the tab URL
  getCurrentTabUrl: function(callback) {
    chrome.tabs.query({ active: true, currentWindow: true }, function(tabs) {
      var url = tabs[0].url;
      //console.assert(typeof url == 'string', 'tab.url should be a string');
      callback(url);
    });
  },

  // helper to assign value to field spans
  // example use: <a class="field articleUrl"><span class="field articleTitle"></span></a>
  setFieldValue: function(field, value) {
    spans = document.querySelectorAll('span.field.' + field);
    for (var i = 0; i < spans.length; i++) {
      spans[i].textContent = value;
    }
    links = document.querySelectorAll('a.field.' + field);
    for (var i = 0; i < links.length; i++) {
      links[i].href = value;
    }
  },

  // helper to get values from the HTML meta tags
  getMetaContent: function(attribute, value) {
    var allMetas = document.getElementsByTagName('meta');
    for (var i = 0; i < allMetas.length; i++) {
      if (allMetas[i].getAttribute(attribute) == value) {
        return allMetas[i].getAttribute('content');
      }
    }
  },

  // create an identical but separate instance of an associative array i.e. object
  clone: function (obj) {
    return JSON.parse(JSON.stringify(obj));
  },

  // merge associative arrays i.e. objects
  arrayMerge: function (obj1, obj2) {
    newObj = helpers.clone(obj1);
    for (var property in obj2) {
        if (obj2.hasOwnProperty(property)) {
            newObj[property] = obj2[property];
        }
    }
    return newObj;
  },

  // always resolve the promise, whether it succeeded or not.
  optionalPromise: function (promise) {
    return promise.then(
      function(v){ return {v:v, status: "resolved" }},
      function(e){ return {e:e, status: "rejected" }}
    );
  },

  // convert an associative array into a URL query (e.g. {a:1,b:2} => a=1&b=2)
  getQueryString: function(obj) {
    var str = [];
    for(var p in obj)
      if (obj.hasOwnProperty(p)) {
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
      }
    return str.join("&");
  },

  // helper to query the CLS API
  // This could return a Promise maybe?
  apiQuery: function(apiFunction, parameters, callback) {
    if (!parameters) parameters = {};
    authParams = {email: CLS.credentials.current.email, apiKey: CLS.credentials.current.apiKey};
    queryParams = helpers.arrayMerge(authParams, parameters);
    queryUrl = CLS.credentials.current.baseUrl + 'api/' + apiFunction;

    var request = new XMLHttpRequest();
    request.open('POST', queryUrl, true);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    request.onload = function() { if (request.status >= 200 && request.status < 400) {
      if (callback) callback(JSON.parse(request.responseText));
    } }; // ignoring the "else" and the onError because we handle errors in the returned JSON

    request.send(helpers.getQueryString(queryParams));
  },

  // helper to output debug info - particularly useful for plugin popups, where console.log is harder to access
  log: function(comment, level) {
    if (level < settings.loglevel) return;

    console.log(comment);

    // displaying the message on the page if the page expects it
    if (document.getElementById('status')) {
      document.getElementById('status').innerHTML += comment + "<br/>";
    }
  }
};

// should be in settings.js?
var settings = {
  loglevel: 0 // 0: verbose, 1: notice, 2: critical
};

var CLS = {
  initialized: null, // useful for pages that should not run unless everything is configured
  loaded: null, // useful for configuration pages, that should run regardless

  credentials: {
    loaded: null,
    current: null,
    init: function() {
      helpers.log('Initializing CLS credentials');

      CLS.credentials.loaded = new Promise((resolve, reject) => {
        chrome.storage.sync.get({ name : '', email: '', apiKey: '', baseUrl: ''}, function(storedCreds) {
          CLS.credentials.current = storedCreds;
          if (!storedCreds.name || !storedCreds.email || !storedCreds.apiKey) {
            reject('Credentials not initialized.');
          } else {
            resolve(storedCreds);
          }
        });
      });

      CLS.credentials.loaded.then(res => { helpers.log('CLS credentials initialization complete.'); });
    },
    set: function(name, email, apiKey, baseUrl, callback) {
      chrome.storage.sync.set({name: name, email: email, apiKey: apiKey, baseUrl: baseUrl}, callback);
    },
    unset: function(callback) {
      chrome.storage.sync.set({name: '', email: '', apiKey: '', baseUrl: ''}, callback);
    }
  },

  // Nothing else for now, but if ever any it would look like this:
  otherStuff: {
    loaded: null,
    init: function() {
      CLS.otherStuff.loaded = new Promise((resolve, reject) => { setTimeout(function(){ resolve(true); }, 500); });
    }
  },

  DOMLoaded: new Promise((resolve, reject) => { if (document.readyState == 'complete') resolve(true); else window.addEventListener('load', function() { resolve(true); })}),

  init: function() {
    helpers.log('Initializing CLS');
    CLS.credentials.init();
    CLS.otherStuff.init();

    // Pages can run directly if they don't need anything, or run based on components e.g. CLS.credential.loaded.then(),
    // or to be really conservative wait for full load e.g. CLS.loaded.then(). Pages that required the plugin to be
    // initialized should wait for CLS.initialized.
    var required = [CLS.otherStuff.loaded, CLS.DOMLoaded];
    var optional = [CLS.credentials.loaded];

    CLS.loaded = Promise.all(required.concat(optional.map(helpers.optionalPromise)));
    CLS.initialized = Promise.all(required.concat(optional));

    CLS.loaded.then(res => { helpers.log('CLS initialization complete.'); });
    CLS.initialized.then(res => { helpers.log('CLS fully configured.'); });
  }
}

//DOMContentLoaded is already gone, running the init right away might trigger the other plugin files before the DOM is ready => waiting for window.load
//window.addEventListener('load', function() {
  CLS.init();
//});
