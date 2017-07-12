<?php

namespace Movies;

/*
This API should have a normalized entry point. Which third-party API to use
should be a setting, and the caller should not know either way. This API could
also be handling the failover from one API to the next if necessary.

There is a need for some helpers... e.g. find by approximate title... might be querying various APIs...
but what would it return... might need a Movies\Movie model to standardize the fields?
For now, although it is not as good as an interface, I'll make sure all the
classes here return movies as an associative array with the following fields:
  $normalizedInfo = [
    'title' => 'etc',
    'rt_imdb' => 1-10,
    'rt_rt' => 1-100,
    'tmb_url' => 'http://etc',
  ];

I guess the movie APIs need a ::getCredits function?
Also, I should create an interface for each external API...

possible sources.....
- http://stackoverflow.com/a/7744369
- http://sg.media-imdb.com/suggests/$titleFirstLetter/$title.json
- http://www.omdbapi.com/?t=$title&y=&plot=short&r=json
- https://www.themoviedb.org/faq/api
- others?

*/

class Info
{
    // TODO at some stage: settings in config file, init in bootstrap, and in this file something like:
    // private static $backend; private static function init($backend) { self::$backend = $backend; }

    public static function searchByName(string $name)
    {
        // one good search option is to use Google's search API for "site:imdb.org $searchTerms", let the user click on the results, and get the IMDb ID from the URL.
    }

    public static function getByIMDbID(string $IMDbID) //: array | null
    {
        return TheMovieDb::getByIMDbID($IMDbID);
    }
}

