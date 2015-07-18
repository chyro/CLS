<?php
namespace Watchlist\Models;

class User extends \Phalcore\Models\Mongo {
	public function getSource() { return "users"; }

//Structure:
//name
//watchlist (movies, rating)
//watched (movies, rating, watched date)
//recommended (movies, rating, recommendation date)
}

