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
        // TODO: ACL, redirect to login if not logged in for pretty much any action in this controller
        $user = $this->session->getUser();

        $watchcal = $user->watched;
        $watchlist = $user->watchlist;
        $recommended = $user->recommended;

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
        // TODO: remove the list-specific action, or merge them. Don't create actions based on lists, but based on meaningful use cases.

        $user = $this->session->getUser();

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
     */
    public function addAction()
    {
        $user = $this->session->getUser();

        if ($this->request->isPost()) {

            $movie = null;
            if ($imdbID = $this->request->getPost('imdb')) {
                $movie = Movie::getMovie($imdb_id);
            }

            if (!$movie && $movieId = $this->dispatcher->getParam('movie_id')) {
                $movie = Movie::findFirst([["_id" => $movieId]]);
            }

            if (!$movie && $title = $this->request->getPost('title')) {
                $movie = Movie::findFirst([["title" => $title]]);
                //TODO: search IMDb for movies by approximate title
            }

            if (empty($movie)) {
                throw new \Exception("Can't add to watchlist: movie not specified");
            }


            $extraOptions = [];
            if ($rating = $this->request->getPost('rating', 'int')) {
                $extraOptions["rating"] = $rating;
            }

            $user->addMovie($movie, 'watchlist', $extraOptions);

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

        $user = $this->session->getUser();

        $watchlist = $user->watchlist;
        $filteredWatchlist = array_filter($user->watchlist,
            function($item) use($movieId) { return $item["movie"]->_id != $movieId; });

        if (count($filteredWatchlist) == count($watchlist))
            throw new \Exception("Can't remove from watchlist: movie not found in watchlist");

        // TODO: move this to $user->removeMovie($movie, $list);
        // TODO: make atomic
        $user->watchlist = $filteredWatchlist;

        if (!$user->save()) {
            $this->flash->error("Error (" . $user->getMessages() . "). Try again?");
        } else {
            $this->flash->success("Movie was removed from watchlist.");
        }
    }

    public function recommendedAction()
    {
    }

    public function recommendedAddAction()
    {
    }

    public function watchedAction()
    {
    }

    public function watchedAddAction()
    {
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

    //public function someAjaxStuff()
    //{
        //$this->view->setRenderLevel(View::LEVEL_NO_RENDER);
    //}
}

