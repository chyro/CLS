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
}
