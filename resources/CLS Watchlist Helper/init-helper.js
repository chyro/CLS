/**
 * This code is added to the CLS profile page. It automatically detects the credentials
 * on the page, and adds a button offering to store them in the plugin, to use for API
 * calls.
 */

var initHelper = {
  detectedProfile: {name: '', email: '', apiKey: ''},
  profileForm: null,

  init: function() {
    initHelper.profileForm = document.getElementsByTagName('form')[0];
    initHelper.detectProfile();
    if (initHelper.detectedProfile.name != ''
        && initHelper.detectedProfile.email != ''
        && initHelper.detectedProfile.apiKey != '') {
      console.log("Detected user account " + initHelper.detectedProfile.name + ", email " + initHelper.detectedProfile.email + ", key " + initHelper.detectedProfile.apiKey, 1);
      initHelper.addPairButton();
    }
  },

  detectProfile: function() {
    var fields = initHelper.profileForm.getElementsByTagName('input');
    for (var i = 0; i < fields.length; i++) {
      var field = fields[i];
      var fieldName = field.getAttribute('name')
      if (fieldName == 'name') {
        initHelper.detectedProfile.name = field.getAttribute('value');
      } else if (fieldName == 'email') {
        initHelper.detectedProfile.email = field.getAttribute('value');
      } else if (fieldName == 'apiKey') {
        initHelper.detectedProfile.apiKey = field.getAttribute('value');
      }
    }
  },

  addPairButton: function() {
    var pairButton = document.createElement('button');
    pairButton.setAttribute('id', 'pair-profile');
    pairButton.innerText = 'Pair this account';
    initHelper.profileForm.parentNode.insertBefore(pairButton, initHelper.profileForm);

    pairButton.addEventListener("click", initHelper.pairProfile);
    // Should I add an un-pair button here as well? Probably redundant.
  },

  pairProfile: function() {
    CLS.credentials.set(initHelper.detectedProfile.name, initHelper.detectedProfile.email, initHelper.detectedProfile.apiKey, function() { alert('Done.'); });
  }
};

console.log("So we're on a profile page apparently...");

//https://stackoverflow.com/a/43245774
//document.addEventListener('DOMContentLoaded', function() { // already fired when plugin files are loaded (by default)
//window.addEventListener('load', function() { // not waiting for any of the page's resources
//CLS.initialized.then(function() { // not currently using any watchlist stuff yet, so not needed, maybe later
CLS.loaded.then(res => { // not using any watchlist stuff yet, but it would  be a shame if we store data before they are loaded.
  console.log('Initializing CLS Profile Initialization Helper');
  initHelper.init();
  console.log('CLS Profile Initialization Helper initialization complete.');
});
