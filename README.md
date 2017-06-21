Lifestyle
=========

Naive attempt at household management, with movie watchlists,
shopping lists, holiday planning, etc.
Scheduling? E.g. reminders for stuff that expire, like renewing ID papers?

needs:
- Phalcon
- Mongo

Tested with:
- Ubuntu 16.04
- Apache 2.4.18
- PHP 7.0
- Phalcon 3.1
- php-mongodb 1.1.5
- Phalcon Incubator branch 3.1

TODO:
- DB init script (single admin user?)
- Create a webapp that works offline on mobile
- user admin dashboard (modules?)
- move the services out of index.php and into app/config/services.php or app/bootstrap.php
- Phalcore classes for base controller (init function, ACL maybe)? base user (session, ACL)?
- hostname, HTTPS on the server?
- auto-post watched movies to Google Cal
- feeds quick-add button:
- - recent / upcoming
- - movies in "want to watch" list of friends
- - movies highly ranked by friends
- auto-select movies not liked or already seen by friends, for solo viewing
- add friend features
- - from a list of friends, auto-select movies everyone wants to see
- - from a movie, auto-select friends who want to see it as well
- - from no input, auto-suggest friends / movies
- - from no input, auto-suggest potential friends
- Plugin needs to display the lists (tabs?)
- Plugin needs to display notifications e.g. friends requests, movie suggestions
- Replace the movie API - obviously it's not working out
- create an "artisan"-style bin to install / update the vendor dependencies? use composer?

Folders:
- app:       core app code (models, controllers, views, etc)
- library:   internal libraries
- vendor:    external libraries
- public:    HTTP root
- data:      location for the app to store files, e.g. cache or uploaded files
- resources: non-web resources, e.g. plugins, documentation

The data/etc folders are the only ones needing write access. TODO: add a .htaccess rule to block any script from running from any writable folder.

