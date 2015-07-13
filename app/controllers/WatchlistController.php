<?php
//namespace Watchlist\Controller;

//use Watchlist\Model\Movie;

/** Watchlist Controller
* 
* Controller handling actions related to watchlists:
* - movies to watch
* - movies watched
* - recommended movies
* - movie ratings
* etc
* 
* TODO:
* - replace "Guilhem" with logged in user
* - loging in, now that I mention it
* - inserting content, etc
*/
class WatchlistController extends \Phalcon\Mvc\Controller
{
	/** Watchlist Index
	* 
	* Display recently watched movies, upcoming movies,
	* recommended to me, etc
	*/
	public function indexAction()
	{
		//$movie = \Watchlist\Model\Movie::find('546b46e56862bcadc7f99134'); //ObjectId("546b46e56862bcadc7f99134")
		//{watched:{$elemMatch: {name: "Guilhem"}}}
		//$watchcal = Movie::all(array("watched" => array("\$elemMatch"=>array("name"=>"Guilhem"))));
		$watchcal = Movie::find([["watched" => ['$elemMatch' => ["name" => "Guilhem"]]], 'limit' => 5]);
		//$watchcal = Movie::all(array("title" => "Avengers"));
		//echo json_encode( $qry, JSON_PRETTY_PRINT ) ."\n";
		//{watchlist:{$elemMatch: {name: "Guilhem"}}}
		//$watchlist = Movie::find([['title'=>'Avengers']]);
		//$watchlist = Movie::find(['sort'=>['title'=>1]]);
		//$watchlist = Movie::find([['title'=>'Avengers'], 'sort'=>['title'=>1]]);
		$watchlist = Movie::find([["watchlist" => ['$elemMatch' => ["name" => "Guilhem"]]], 'limit' => 5]);
		//{recommended:{$elemMatch: {to: "Guilhem"}}}
		$recommended = Movie::find([["recommended" => ['$elemMatch' => ["to" => "Guilhem"]]], 'limit' => 5]);
		$this->view->watchcal = $watchcal;
		$this->view->watchlist = $watchlist;
		$this->view->recommended = $recommended;
	}

	/** Watchlist Watchlist
	* 
	* Display the movies on the watchlist, sorted
	* "most want to watch" first
	*/
	public function watchlistAction()
	{
		$this->view->watchlist = Movie::find([["watchlist" => ['$elemMatch' => ["name" => "Guilhem"]]]]);
	}

	public function addAction()
	{
		//$this->view->watchlist = Movie::find([["watchlist" => ['$elemMatch' => ["name" => "Guilhem"]]]]);
	}

	public function deleteAction()
	{
		//$this->view->watchlist = Movie::find([["watchlist" => ['$elemMatch' => ["name" => "Guilhem"]]]]);
	}

	/** Watchlist Recommended
	* 
	* Display the movies recommended to me, with
	* quick-link to add it to the watchlist.
	*/
	public function recommendedAction()
	{
		$this->view->recommended = Movie::find([["recommended" => ['$elemMatch' => ["to" => "Guilhem"]]]]);
	}

	public function recommendedAddAction()
	{
		//$this->view->recommended = Movie::find([["recommended" => ['$elemMatch' => ["to" => "Guilhem"]]]]);
	}

	/** Watched Movies
	* 
	* Display the movie specified, or if not specified
	* display all the watched movies.
	*/
	public function watchedAction()
	{
		//$this->view->watched = Movie::find([["watched" => ['$elemMatch' => ["by" => "Guilhem"]]]]);
	}

	public function watchedAddAction()
	{
		//$this->view->watched = Movie::find([["watched" => ['$elemMatch' => ["by" => "Guilhem"]]]]);
	}

	//recommendedAddAction, recommendedDeleteAction, watched, watchedAdd, watchDelete

	// divide this into add suggestion, add watch, add to watch
	//public function addAction($linkparam1, $linkparam2)
	//{
		//$movie = Movie::find($id);
		//$watched = $movie->watched->new();
		//$watched->ranking = 5;
		//$movie->watched->addDocument($watched);
		//or $movie->watched[] = $watched;
		//$movie->save();
	//}

	// modify existing, i.e. add rating or convert (to watch -> watched,
	// suggestion -> to watch)
	//public function editAction()
	//{
	//}

	//public function deleteAction()
	//{
	//}
}

