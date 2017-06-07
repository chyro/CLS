var initHelper = {
  detectedProfile: {name: '', email: '', apiKey: ''},
  profileForm: null,

  init: function() {
    initHelper.profileForm = document.getElementsByTagName('form')[0];
    initHelper.detectProfile();
    if (initHelper.detectedProfile.name != ''
        && initHelper.detectedProfile.email != ''
        && initHelper.detectedProfile.apiKey != '') {
      console.log("Detected user account " + initHelper.detectedProfile.name + ", email " + initHelper.detectedProfile.email + ", key " + initHelper.detectedProfile.apiKey);
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
    document.body.insertBefore(pairButton, initHelper.profileForm);

    pairButton.addEventListener("click", initHelper.pairProfile);
    // Should I add an un-pair button here as well? Probably redundant.
  },

  pairProfile: function() {
    credentials.set(initHelper.detectedProfile.name, initHelper.detectedProfile.email, initHelper.detectedProfile.apiKey, function() { alert('Done.'); });
  }
};

console.log("So we're on a profile page apparently...");
//document.addEventListener('watchlist.initialized', function() { // not currently using any watchlist stuff yet, so not needed, maybe later
//document.addEventListener('DOMContentLoaded', function() { // already fired when plugin files are loaded (by default)
//window.addEventListener('load', function() { // not waiting for any of the page's resources
initHelper.init();

