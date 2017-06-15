<?php
namespace Watchlist\Models;

class User extends \Phalcore\Models\Mongo
{
    use \Phalcore\Models\Mongo\AutoId, \Phalcore\Models\Mongo\Timestamped;

    public function getSource() { return "users"; }

//Structure:
//name, email, password, apiKey
//watchlist (movies, rating)
//watched (movies, rating, watched date)
//recommended (movies, rating, recommendation date)
//following (one-way friendship)

    /*public function __construct()
    {
        // TODO: move this to the Mongo parent class (cannot use __construct... is there an init function? or should I make a factory?)
        foreach (self::getDefaults() as $field -> $value) {
            $this->{$field} = $value;
        }
    }*/

    public static function getDefaults()
    {
        return [ 'watchlist' => [], 'watched' => [], 'recommended' => [], 'following' => [] ];
    }

    public function getMovieStatus($imdbID)
    {
        if ($movieInfo = $this->getMovieFromList($imdbID, 'watchlist')) {
            return ['imdbid' => $imdbID, 'status' => 'watchlist', 'rating' => $movieInfo['rating']];
        }

        if ($movieInfo = $this->getMovieFromList($imdbID, 'watched')) {
            return ['imdbid' => $imdbID, 'status' => 'watched', 'rating' => $movieInfo['rating'], 'date' => $movieInfo['date']->toDateTime()->format('Y-m-d')];
            //TODO: converstion to / from PHP / Mongo dates should be handled by Phalcore
        }

        return ['status' => 'unknown'];
    }

    // TODO: all functions here should take a Movie object as a param, not a mix of objects and IMDbID.
    public function getMovieFromList($imdbID, $list)
    {
        foreach ($this->{$list} as $it) {
            if ($it['movie']->IMDbID == $imdbID) {
                return $it;
            }
        }

        return null;
    }

    public function removeFromList($movie, $list = 'watchlist')
    {
        $movieId = $movie->_id;

        $userMovies = $this->{$list};
        $filteredUserMovies = array_filter(
            $userMovies,
            function($item) use($movieId) { return $item["movie"]->_id != $movieId; }
        );

        if (count($userMovies) != count($filteredUserMovies)) {
            $this->{$list} = $filteredUserMovies;
        }
        // TODO maybe: use an atomic Mongo array pop maybe? But then I lose the "$user->modify(); $user->save();" pattern... Also runtime loaded classes might get out of sync...
    }

    public function addToList($movie, $listName = 'watchlist', $extraOptions = [])
    {
        // Should I validate $list maybe?
        if (!empty($this->getMovieFromList($movie->IMDbID, $listName))) {
            return; // movie already in list: nothing to do
        }

        if ($listName == 'watched' && !empty($this->getMovieFromList($movie->IMDbID, 'watchlist'))) {
            // just watched it: remove from watchlist, add to watched list
            $this->removeFromList($movie, 'watchlist');
        }

        // TODO: if the user has a Google Cal set up, and the list is 'watched', add an event on the Cal

        $listItem = ['movie' => $movie];
        if (array_key_exists('rating', $extraOptions)) {
            $listItem['rating'] = $extraOptions['rating'];
        }
        if ($listName == 'watched') {
            if (array_key_exists('date', $extraOptions)) {
                $listItem['date'] = new \MongoDB\BSON\UTCDateTime((new \DateTime($extraOptions['date']))->getTimestamp() * 1000);
                // TODO: PHP<->Mongo date conversion should be handled by Phalcore
            } else {
                $listItem['date'] = new \MongoDB\BSON\UTCDateTime((new \DateTime())->getTimestamp() * 1000);
            }
        }

        $moviesList = $this->{$listName};
        array_push($moviesList, $listItem);
        $this->{$listName} = $moviesList;

        // TODO maybe: use an atomic Mongo array push maybe?
    }
}

