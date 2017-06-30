<?php
namespace Watchlist\Controllers;

use Watchlist\Models\Movie;
use Watchlist\Models\User;

/**
 * Watchlist Controller
 *
 * Controller handling actions related to watchlists:
 * - movies to watch
 * - movies watched
 * - recommended movies
 * - movie ratings
 * etc
 *
 * TODO:
 * - features
 * - design
 * - all the stuff
 * ... ... ...
 * - "my movies" view, similar to GoodRead's "my bookshelf" view, listing watched + to watch
 * - "my feed" view, listing "recommended to me" and friends activity
 * - addMovie action... we need to add stuff to the movie collection at some stage obviously
 */
class WatchlistController extends \Phalcore\Controller
{
    /**
     * Watchlist Index
     *
     * Display recently watched movies, upcoming movies,
     * recommended to me, etc
     */
    public function indexAction()
    {
        $user = User::findFirst([["name"=>"Guilhem"]]);
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

    /**
     * Watchlist list
     *
     * Display the movies on the watchlist, sorted
     * "most want to watch" first
     */
    public function watchlistAction()
    {
        $user = User::findFirst([["name"=>"Guilhem"]]);
        $watchlist = $user->watchlist;
        if (!is_array($watchlist)) $watchlist = [];

        // sort by "most want to watch"
        usort($watchlist, function($a, $b) { return $a->rating - $b->rating; });

        $this->view->watchlist = $watchlist;
    }

    /**
     * Watchlist add
     *
     * Add a movie (from the "movie" collection)  to the user's watchlist
     * TODO: add some JS to help insert the movie,
     * looking up in the local DB and other sources
     * (imdb?) for matches on the title
     * http://stackoverflow.com/a/7744369
     * http://sg.media-imdb.com/suggests/$titleFirstLetter/$title.json
     * http://www.omdbapi.com/?t=$title&y=&plot=short&r=json
     * https://www.themoviedb.org/faq/api
     */
    public function addAction()
    {
        if ($this->request->isPost()) {
            $user = User::findFirst([["name"=>"Guilhem"]]);
            $watchlist = $user->watchlist;
            if (!is_array($watchlist)) $watchlist = [];

            $movie = null;
            if (!$this->request->getPost('imdb') && !$this->request->getPost('title'))
                throw new \Exception("Can't add to watchlist: movie not specified");

            if ($imdb_id = $this->request->getPost('imdb'))
                $movie = Movie::findFirst([["imdb" => $imdb_id]]);
            if (!$movie && $title = $this->request->getPost('title'))
                $movie = Movie::findFirst([["title" => $title]]);
            if (!$movie && $movieId = $this->dispatcher->getParam('movie_id'))
                $movie = Movie::findFirst([["_id" => $movieId]]);

            //if (!$movie)
                //TODO: add movie

            //should check the movie is not in the list yet, and skip

            $item = [
                    "movie" => $movie,
                ];
            if ($rating = $this->request->getPost('rating', 'int'))
                $item["rating"] = $this->request->getPost('rating', 'int');

            $watchlist[] = $item;
            $user->watchlist = $watchlist;
            //Warning: I don't know the details of Phalcon Mongo libraries,
            //but this seriously looks non-atomic. Can't I just $push?
            //Of course you can push, moron! Redo this thing!

            $success = $user->save();
            if ($success) {
                $message = "Added <span class='movie_title'>" . $movie->title . "</span>. Add another?";
                // "Movie was added to watchlist. Add another?";
                $this->view->addedMovie = $movie;
            } else {
                $message = "Error (" . $user->getMessages() . "). Try again?";
            }

            if ($success) {
                $this->flash->success($message);
            } else {
                $this->flash->error($message);
            }

            $this->view->watchlist = $watchlist;
        }
    }

    /**
     * Watchlist delete
     *
     * Remove a movie from the watchlist
     */
//TODO: MAKE THAT A POST! Get URL params should be idempotent.
    public function deleteAction()
    {
        if (!$movieId = $this->dispatcher->getParam('movie_id'))
            throw new \Exception("Can't remove from watchlist: movie not specified");

        $user = User::findFirst([["name"=>"Guilhem"]]);

        $watchlist = $user->watchlist;
        $filteredWatchlist = array_filter($user->watchlist,
            function($item) use($movieId) { return $item["movie"]->_id != $movieId; });

        if (count($filteredWatchlist) == count($watchlist))
            throw new \Exception("Can't remove from watchlist: movie not found in watchlist");

        $user->watchlist = $filteredWatchlist;

        if (!$user->save()) {
            $this->flash->error("Error (" . $user->getMessages() . "). Try again?");
        } else {
            $this->flash->success("Movie was removed from watchlist.");
        }
    }

    /**
     * Recommended list
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

    /**
     * Watched Movies
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

