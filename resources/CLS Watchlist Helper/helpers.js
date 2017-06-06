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

  getMetaContent: function(attribute, value) {
    var allMetas = document.getElementsByTagName('meta');
    for (var i = 0; i < allMetas.length; i++) {
      if (allMetas[i].getAttribute(attribute) == value) {
        return allMetas[i].getAttribute('content');
      }
    }
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

// should be in settings.js?
var settings = {
  profileUrl: 'http://202.171.212.225/~guilhem/cls/user/profile'
};

