<?php
namespace Watchlist\Models;

class User extends \Phalcore\Models\Mongo
{
    use \Phalcore\Models\Mongo\AutoId, \Phalcore\Models\Mongo\Timestamped;

    public function getSource() { return "users"; }

//Structure:
//name
//watchlist (movies, rating)
//watched (movies, rating, watched date)
//recommended (movies, rating, recommendation date)

    /*public function __construct()
    {
        // TODO: move this to the Mongo parent class (cannot use __construct... is there an init function? or should I make a factory?)
        foreach (self::getDefaults() as $field -> $value) {
            $this->{$field} = $value;
        }
    }*/

    public function getDefaults()
    {
        return [ 'watchlist' => [], 'watched' => [] ];
    }

    public function getMovieStatus($imdbID)
    {
        foreach ($this->watchlist as $it) {
            $movie = $it['movie'];
            $rating = $it['rating'];
            if ($movie->IMDbID == $imdbID) {
                return ['imdbid' => $imdbID, 'status' => 'watchlist', 'rating' => $rating];
            }
        }

        foreach ($this->watched as $it) {
            $movie = $it['movie'];
            $rating = $it['rating'];
            $date = $it['date'];
            if ($movie->IMDbID == $imdbID) {
                return ['imdbid' => $imdbID, 'status' => 'watched', 'rating' => $rating, 'date' => $date];
            }
        }

        return ['status' => 'unknown'];
    }
}
