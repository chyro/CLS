<?php
namespace Watchlist\Models;

class Movie extends \Phalcore\Models\Mongo {
    public function getSource() { return "movies"; }

//Structure:
//title
//imdb ID
//imdb rating
}

