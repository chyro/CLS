<?php
namespace Watchlist\Controllers;

use Watchlist\Models\Movie;
use Watchlist\Models\User;

use Movies\OMDb as MovieApi;

/**
 * API Controller
 *
 * Keeping all the API functions in this controller for now. A separate module would
 * be nice, but doesn't really fit in my current subfolder installation. We'll see
 * how many functions we get.
 */
class ApiController extends \Phalcon\Mvc\Controller
{
    public function initialize()
    {
        // all API responses will be JSON
        $this->view->disable();

        // all API functions will require authentification
        $userEmail = $this->request->get('email');
        $user = User::findFirst([['email' => $userEmail]]);
        $userApiKey = $this->request->get('apiKey');
        if (empty($user) || $user->apiKey != $userApiKey) {
            echo '{"result": "error", "message": "wrong credentials"}';
            // API error management should be unified somehow
            die();
        }

        $this->user = $user;
    }

/*
<form action="http://202.171.212.225/~guilhem/cls/api/imdb-add" method="post">
  <input name="email" value="1234">
  <input name="apiKey" value="xjwrfa60kfjrq787550obagrcw44n8erac6bo3zo">
  <input name="imdbid" value="tt0287839">
  <!-- optional: list=watchlist|watched -->
  <input type="submit">
</form>
*/
    /**
     * Add a movie to the watchlist (or watched list)
     *
     * Intended to use from the IMDb website directly, "add this to watchlist".
     */
    public function imdbAddAction()
    {
        $user = $this->user;

        $imdbID = $this->request->getPost('imdbid');
        $targetList = $this->request->getPost('list', null, 'watchlist');
        if (!in_array($targetList, ['watchlist', 'watched'])) $targetList = 'watchlist';

        $extraOptions = [];
        if (!empty($watchRating = $this->request->getPost('rating'))) {
            $extraOptions['rating'] = $watchRating;
        }
        if (!empty($watchDate = $this->request->getPost('date'))) {
            $extraOptions['date'] = $watchDate;
        }

        $movie = Movie::getMovie($imdbID);

        $user->addToList($movie, $targetList, $extraOptions);
        $user->save();

        echo '{"result": "OK"}';
    }

    /**
     * Get the status of a movie, if it is currently in a list
     */
    public function movieStatusAction()
    {
        $user = $this->user;

        $imdbID = $this->request->getPost('imdbid');

        $status = $user->getMovieStatus($imdbID);

        echo json_encode($status);
    }

    //automatically-generated quizz of movies/actors/directors from IMDb
}

