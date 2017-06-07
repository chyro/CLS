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
  setFieldValue: function(field, value) {
    spans = document.querySelectorAll('.field.' + field);
    for (var i = 0; i < spans.length; i++) {
      spans[i].textContent = value;
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

  getQueryString: function(obj) {
    var str = [];
    for(var p in obj)
      if (obj.hasOwnProperty(p)) {
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
      }
    return str.join("&");
  },

  // helper to query the CLS API
  apiQuery: function(apiFunction, parameters, callback) {
    if (!parameters) parameters = {};
    authParams = {email: credentials.current.email, apiKey: credentials.current.apiKey};
    queryParams = helpers.arrayMerge(authParams, parameters);
    queryUrl = settings.apiUrl + apiFunction;

    var request = new XMLHttpRequest();
    request.open('POST', queryUrl, true);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    request.onload = function() { if (request.status >= 200 && request.status < 400) {
      if (callback) callback(JSON.parse(request.responseText));
    } }; // ignoring the "else" and the onError because we handle errors in the returned JSON

    request.send(helpers.getQueryString(queryParams));
  },

  // helper to output debug info - particularly useful for plugin popups, where console.log is not usable
  log: function(comment) {
    // if there is no "#status" div in the document, add one:
    if (!document.getElementById('status')) {
      var statusDiv = document.createElement('div');
      statusDiv.setAttribute('id', 'status');
      document.body.insertBefore(statusDiv, document.body.firstChild);
    }
    document.getElementById('status').innerHTML += comment + "<br/>";
  }
};

var credentials = {
  current: null,
  init: function() {
    chrome.storage.sync.get({ name :'', email: '', apiKey: '' }, function(storedCreds) {
      credentials.current = storedCreds;
      document.dispatchEvent(new Event('watchlist.initialized'));
    });
  },
  set: function(name, email, apiKey, callback) {
    chrome.storage.sync.set({name: name, email: email, apiKey: apiKey}, callback);
  },
  unset: function(callback) {
    chrome.storage.sync.set({name: '', email: '', apiKey: ''}, callback);
  },
  get: function(callback) {
    if (current) {
      callback(current);
    } else {
      chrome.storage.sync.get({ name :'', email: '', apiKey: '' }, callback);
    }
  }
};

// should be in settings.js?
var settings = {
  apiUrl: 'http://202.171.212.225/~guilhem/cls/api/',
  profileUrl: 'http://202.171.212.225/~guilhem/cls/user/profile'
};

//https://stackoverflow.com/a/43245774
//DOMContentLoaded is already gone, running the init right away might fire watchlist.initialized before the other plugin files are loaded => waiting for window.load
window.addEventListener('load', function() {
  credentials.init();
  //TODO: inits = { credentials: false, etc }; credentials.init(callback); callback = { set inits.credentials = true; if all inits are true, throw event watchlist.initialized; }; all page scripts will init based on that event. This mechanism will be useful when more than one thing needs to be initialized.
  //TODO: if there are no credentials available, do NOT add icons on IMDb (don't even dispatch the "initialized" event maybe?)
});

