
var movieID = helpers.getMetaContent('property', 'pageId');
if (movieID) console.log('detected movie ID ' + movieID);

// Adding the button
var titleDiv = document.getElementsByClassName('title_wrapper')[0];
var titleH1 = titleDiv.getElementsByTagName('h1')[0];
var newNode = document.createElement('span');
newNode.setAttribute('class', 'cls trigger addToWatchlist');
var newImg = document.createElement('img');
newImg.setAttribute('src',chrome.extension.getURL('btn-add-to-watchlist.png'));
newNode.appendChild(newImg);
titleH1.insertBefore(newNode, titleH1.firstChild)
//TODO: onclick event

