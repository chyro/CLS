<?php
namespace Watchlist\Models;

class Movie extends \Phalcore\Models\Mongo {
    public function getSource() { return "movies"; }

//Structure:
//title
//imdb ID
//imdb rating

    /**
     * possible sources.....
     * http://stackoverflow.com/a/7744369
     * http://sg.media-imdb.com/suggests/$titleFirstLetter/$title.json
     * http://www.omdbapi.com/?t=$title&y=&plot=short&r=json
     * https://www.themoviedb.org/faq/api
     * others?
     */
    public static function getMovie($imdbID)
    {
        $movie = Movie::findFirst([['IMDbID' => $imdbID]]);
        if (empty($movie)) {
            $movieInfo = MovieApi::getByID($imdbID); // TODO: failover in case the API is not helping

            $movie = new Movie();
            // the normalization of Movie API fields into local convention should be made by the Movie API library!
            $movie->IMDbID = $movieInfo->imdbID;
            $movie->title = $movieInfo->Title;
            foreach ($movieInfo->Ratings as $rating) {
                if ($rating->Source == 'Internet Movie Database') {
                    $movie->ratingIMDb = reset(explode('/', $rating->Value));
                } else if ($rating->Source == 'Rotten Tomatoes') {
                    $movie->ratingRotten = reset(explode('%', $rating->Value));
                }
            }
            $movie->save();
        }
        return $movie;
    }
}

