
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
    //helpers.setFieldValue('profileUrl', settings.profileUrl); // sadly I do not know the hostname until it is paired...
  }

  // Set the unpair button handler
  var unpairButton = document.querySelector('button.control.unpair');
  unpairButton.addEventListener('click', function(){
    CLS.credentials.unset(function() {
      alert('Done.');
      window.location.href = window.location.href; // reloading to reflect the new status
    });
  });

  helpers.log("CLS popup initialization complete.");
});

