<?php
namespace Watchlist\Models;

use Movies\Info as MovieApi;

class Movie extends \Phalcore\Models\Mongo
{
    public function getSource() { return "movies"; }

//Structure:
//title
//IMDbID
//tmb_url: URL to thumbnail image
//rt_imdb: IMDb rating
//rt_rt: Rotten Tomatoes rating
//last_update

    public static function getMovie($imdbID)
    {
        $movie = Movie::findFirst([['IMDbID' => $imdbID]]);
        if (empty($movie)) {
            $movie = new Movie();
            $movie->IMDbID = $imdbID;
            $movie->save();
        }
        if (empty($movie->last_update) || strtotime($movie->last_update) < strtotime('last month')) {
            $movieInfo = MovieApi::getByIMDbID($imdbID);

            if (!empty($movieInfo)) {
                $movie->setMovieInfo($movieInfo);
            }
        }
        return $movie;
    }

    public function setMovieInfo($movieInfo)
    {
        $updated = false;
        foreach (['title', 'rt_imdb', 'rt_rt', 'tmb_url'] as $field) { // maintain an array of self::getSettableFields()?
            if (!empty($movieInfo[$field])) {
                $updated = true;
                $value = $movieInfo[$field];
                if ($field == 'tmb_url') { $value = 'https://image.tmdb.org/t/p/w500' . $value; } // TODO: call /configuration and check base_url and available sizes
                $this->{$field} = $value;
            }
        }
        if ($updated) {
            $this->last_update = date("Y-m-d H:i:s"); // MongoDate?
            $this->save();
        }
    }

    // When the title is not loaded (e.g. MovieApi failed), it could be nice to have a
    // mechanism displaying a consistent label (e.g. '???') site-wide. Phalcore Model
    // could offer property accessors. That could also be useful to provide calculated
    // properties.
}

