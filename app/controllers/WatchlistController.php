<?php
namespace Watchlist\Controllers;

use Watchlist\Models\Movie;
use Watchlist\Models\User;

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
		$user = User::find(["name"=>"Guilhem"]);
		$watchcal = $user->watched;
		$watchlist = $user->watchlist;
		$recommended = $user->recommended;
		/*
		//{watched:{$elemMatch: {name: "Guilhem"}}}
		$watchcal = Movie::find([["watched" => ['$elemMatch' => ["name" => "Guilhem"]]], 'limit' => 5]);
		//{watchlist:{$elemMatch: {name: "Guilhem"}}}
		$watchlist = Movie::find([["watchlist" => ['$elemMatch' => ["name" => "Guilhem"]]], 'limit' => 5]);
		//{recommended:{$elemMatch: {to: "Guilhem"}}}
		$recommended = Movie::find([["recommended" => ['$elemMatch' => ["to" => "Guilhem"]]], 'limit' => 5]);
		*/

		$this->view->watchcal = $watchcal;
		$this->view->watchlist = $watchlist;
		$this->view->recommended = $recommended;
	}

	/** Watchlist list
	*
	* Display the movies on the watchlist, sorted
	* "most want to watch" first
	*/
	public function watchlistAction()
	{
		$user = User::find(["name"=>"Guilhem"]);
		$watchlist = $user->watchlist;
		if (!is_array($watchlist)) $watchlist = [];

		// sort by "most want to watch"
		usort($watchlist, function($a, $b) { return $a->rating - $b->rating; });

		$this->view->watchlist = $watchlist;
	}

	/** Watchlist add
	* 
	* Add a movie to the watchlist
	* TODO: add some JS to help insert the movie,
	* looking up in the local DB and other sources
	* (imdb?) for matches on the title
	*/
	public function addAction()
	{
		if ($this->request->isPost()) {
			$user = User::find(["name"=>"Guilhem"]);

			$movie = null;
			if ($imdb_id = $this->request->getPost('imdb'))
				$movie = Movie::find(["imdb" => $imdb_id]);
			if ($title = $this->request->getPost('title'))
				$movie = Movie::find(["title" => $title]);
			if (!$movie)
				throw New Exception("Can't add to watchlist: movie not specified");

			//should check the movie is not in the list yet?

			$item = [
					"movie" => $movie,
				];
			if ($rating = $this->request->getPost('rating', 'int'))
				$item["rating"] = $this->request->getPost('rating', 'int');

			$user->watchlist[] = $item;
			// Warning: I don't know the details of Phalcon Mongo libraries,
			//but this seriously looks non-atomic. Can't I just $push?

			if (!$user->save()) {
				$this->flash->error($user->getMessages());
			} else {
				$this->flash->success("Movie was added to watchlist. Add another?");
				//Tag::resetInput();
			}
		}
	}

	public function deleteAction()
	{
		if ($this->request->isPost()) {
			$user = User::find(["name"=>"Guilhem"]);

			$movie = null;
			if ($imdb_id = $this->request->getPost('imdb'))
				$movie = Movie::find(["imdb" => $imdb_id]);
			if ($title = $this->request->getPost('title'))
				$movie = Movie::find(["title" => $title]);
			if (!$movie)
				throw New Exception("Can't remove from watchlist: movie not specified");

			$user->watchlist = array_filter($user->watchlist, function() use($movie) { return $item->movie != $movie; });

			if (!$user->save()) {
				$this->flash->error($user->getMessages());
			} else {
				$this->flash->success("Movie was removed from watchlist.");
			}
		}
	}

	/** Recommended list
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
		//1. add to watched
		//2. if in watchlist, remove from watchlist
		//$this->view->watched = Movie::find([["watched" => ['$elemMatch' => ["by" => "Guilhem"]]]]);
	}

	//recommendedAddAction, recommendedDeleteAction, watched, watchedAdd, watchDelete

	// divide this into add suggestion, add watch, add to watch
	//public function addAction($linkparam1, $linkparam2)
	//{
		//$movie = Movie::find($id);
		//$watched = $movie->watched->new();
		//$watched->rating = 5;
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

	//public function someAjaxStuff()
	//{
		//$this->view->setRenderLevel(View::LEVEL_NO_RENDER);
	//}
}

