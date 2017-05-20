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
- browser add-on (or monkey grease script) to add movie from IMDB website
- feeds quick-add button:
- - recent / upcoming
- - movies in "want to watch" list of friends
- - movies highly ranked by friends
- auto-select movies not liked or already seen by friends, for solo viewing
- from a list of friends, auto-select movies everyone wants to see
- from a movie, auto-select friends who want to see it as well

