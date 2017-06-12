
//CLS.initialized.then(function() { // must run even if we have no credentials
CLS.loaded.then(res => {
//document.addEventListener('watchlist.initialized', function() {
  helpers.log("initializing CLS popup");

  // Checking current credentials, adjusting the display
  if (CLS.credentials.current.name != ''
      || CLS.credentials.current.email != ''
      || CLS.credentials.current.apiKey != '') {
    helpers.setFieldValue('name', CLS.credentials.current.name);
    helpers.setFieldValue('email', CLS.credentials.current.email);
    helpers.setFieldValue('apiKey', CLS.credentials.current.apiKey);
    document.getElementById('account-info').classList.add('initialized');
  } else {
    helpers.setFieldValue('profileUrl', settings.profileUrl);
  }

  // Set the unpair button handler
  var unpairButton = document.querySelector('button.control.unpair');
  unpairButton.addEventListener('click', function(){
    CLS.credentials.unset(function() {
      alert('Done.');
      window.location.href = window.location.href; // reloading to reflect the new status
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
  helpers.log("CLS popup initialization complete.");
});

