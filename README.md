Lifestyle
=========

This project is a naive attempt at household management, e.g. things like:
- movie watchlists
- shopping lists
- holiday planning, etc.
- scheduling? E.g. reminders for stuff that expire, like renewing ID papers?

#####Current features:
- Register, login
- Manage watched, to watch movies
- API functions to manage movies
- Chrome plugin to quick-add movies to lists while browsing IMDb

#####Coming ~~soon~~ eventually:
- Clean up the website:
 - watched movies should be displayed as a calendar
 - watched movies should have a control to set watched date, rating, add to Cal
 - adding / removing from lists should work
 - fix the flash-messages (Kickstart growls?)
- auto-post watched movies to Google Cal
- Create a webapp that works offline on mobile
 - hourly, download the last notifications, display on status bar / icon
 - on launch, display notifications, with response actions
 - if online, store response actions for later processing
- feeds quick-add button:
 - recent / upcoming
 - movies in "want to watch" list of friends
 - movies highly ranked by friends
- add friend features
 - from a list of friends, auto-select movies everyone wants to see
 - from a movie, auto-select friends who want to see it as well
 - from no input, auto-suggest friends / movies
 - from no input, auto-suggest potential friends
 - from no input, auto-suggest movies not liked or already seen by friends, for solo viewing
- Browser plugin:
 - needs to display the lists (tabs?)
 - needs to display notifications e.g. friends requests, movie suggestions
 - add "recommend to a friend" action
- user admin dashboard (modules?)
- DB init script (single admin user?)
- move the services out of index.php and into app/config/services.php or app/bootstrap.php
- Phalcore base controller: ACL maybe?
- create an "artisan"-style bin to install / update the vendor dependencies? use composer?
- If I keep using Kickstart - move it to vendor, symlink in public, keep changes in a versioned file extending it.

#####Folders:
- app:       core app code (models, controllers, views, etc)
- library:   internal libraries
- vendor:    external libraries
- public:    HTTP root
- data:      location for the app to store files, e.g. cache or uploaded files
- resources: non-web resources, e.g. plugins, documentation

The data/etc folders are the only ones needing write access. TODO: add a .htaccess rule to block any script from running from any writable folder.

#####Notes on APIs:
It seems there are numerous APIs available. However, they are all closed. Apparently freely
available APIs are quickly overrun, and become non-free. IMDb is unofficial, incomplete and
undocumented. OMDb is temporarily unavailable. Rotten Tomatoes requires registration.
An option would be to get the data from websites, e.g. IMDb itself, via the plugin. When a user
navigates IMDb, the plugin could scrape the visited pages, and store the info. Excessive
load could be avoided by having the "status" query include a "last updated" date, so the plugin
can run a "update movie" query if required.
Let's try without for now. TheMovieDB seems fine, OMDb claims it will be back to free.

#####Requirements:
- Phalcon
- Mongo

#####Tested with:
- Ubuntu 16.04
- Apache 2.4.18
- PHP 7.0
- Phalcon 3.1
- php-mongodb 1.1.5
- Phalcon Incubator branch 3.1

