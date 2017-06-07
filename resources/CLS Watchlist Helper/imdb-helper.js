var imdbHelper = {
  movieID: null,
  movieStatus: null,
  multibuttonContainer: null,

  addMultiButton: function() {
    // Adding the multi-button
    var titleDiv = document.getElementsByClassName('title_wrapper')[0];
    var titleH1 = titleDiv.getElementsByTagName('h1')[0];
    var multibuttonBase = document.createElement('div');
    multibuttonBase.setAttribute('class', 'cls multibutton');
    titleH1.insertBefore(multibuttonBase, titleH1.firstChild);

    imdbHelper.multibuttonContainer = document.createElement('div');
    imdbHelper.multibuttonContainer.setAttribute('class', 'container');
    multibuttonBase.appendChild(imdbHelper.multibuttonContainer);

    //TODO: "loading" wheel placeholder if empty
  },

  addSubButton: function(iconUrl, title, handler) {
    var newImg = document.createElement('img');
    newImg.setAttribute('title', title);
    newImg.setAttribute('src', chrome.extension.getURL(iconUrl));
    imdbHelper.multibuttonContainer.appendChild(newImg);

    if (handler) {
      newImg.classList.add('trigger'); // class for the role?
      newImg.addEventListener('click', handler);
    }
  },

  initIcons: function(status) {
      // TODO: handle errors
      imdbHelper.movieStatus = status;

      if (status.status == "watchlist") {
        imdbHelper.addSubButton('icon-in-watchlist.png', 'currently in watchlist');
//TODO maybe: add glasses in the icon to differentiate it better from the btn?
//TODO maybe: differentiate icons vs buttons using... bg? colors? gloss?
        imdbHelper.addSubButton('btn-add-to-watched.png', 'set to watched', function(){
          helpers.apiQuery('imdb-add', {imdbid: imdbHelper.movieID, list: 'watched'}, function(response){alert(response.result);});
        });
        //TODO: rating, date
      } else if (status.status == "watched") {
        imdbHelper.addSubButton('icon-in-watched.png', 'watched (' + response.date + ')');
        // could add buttons for "recommend" or "rewatch"
      } else if (status.status == "unknown") {
        imdbHelper.addSubButton('btn-add-to-watchlist.png', 'add to watchlist', function(){
          helpers.apiQuery('imdb-add', {imdbid: imdbHelper.movieID}, function(response){alert(response.result);});
        });
        imdbHelper.addSubButton('btn-add-to-watched.png', 'add to watched', function(){
          helpers.apiQuery('imdb-add', {imdbid: imdbHelper.movieID, list: 'watched'}, function(response){alert(response.result);});
        });
        //TODO: rating, date
      } // else { display some error sign? }

    //TODO: ANY OF THOSE API CALLS SHOULD RELOAD THE PAGE, OR AT LEAST REFRESH THE SECTION
  },

  init: function() {
    imdbHelper.movieID = helpers.getMetaContent('property', 'pageId');
    if (imdbHelper.movieID) console.log('detected movie ID ' + imdbHelper.movieID);

    imdbHelper.addMultiButton();
    helpers.apiQuery("movie-status", {imdbid: imdbHelper.movieID}, imdbHelper.initIcons);
  }
};

document.addEventListener('watchlist.initialized', function() {
  imdbHelper.init();
});

