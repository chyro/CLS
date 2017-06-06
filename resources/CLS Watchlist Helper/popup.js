
document.addEventListener('DOMContentLoaded', function() {
  // Checking current credentials, adjusting the display
  chrome.storage.sync.get({ name :'', email: '', apiKey: '' }, function(credentials) {
    if (credentials.name != '' || credentials.email != '' || credentials.apiKey != '') {
      helpers.setFieldValue('name', credentials.name);
      helpers.setFieldValue('email', credentials.email);
      helpers.setFieldValue('apiKey', credentials.apiKey);
      document.getElementById('account-info').classList.add('initialized');
    }
  });

  // Set the unpair button handler
  var unpairButton = document.querySelector('button.control.unpair');
  unpairButton.addEventListener('click', function(){
    chrome.storage.sync.set({ name: '', email: '', apiKey: '' }, function() {
      alert('Done.');
      window.location.href = window.location.href;
    });
  });

  /*
  // Getting the data from the tab requires extra permissions. It is simpler to use code directly in the page.

  // Checking current tab, adjusting the display
  helpers.getCurrentTabUrl(function(tabUrl){
    if (tabUrl == settings.profileUrl) {
      var name = "name", email = "email", apiKey = "apiKey"; // TODO: GET THOSE FROM THE TAB

      document.getElementById('account-init').classList.add('active');
      helpers.setFieldValue('accountLabel', name + " (" + email + ")");
    }
  });

  // TODO: add a click handler to button.pair, storing the user info and key from the current page
  // chrome.storage.sync.set({ name: name, email: email, apiKey: apiKey });
  */
});

