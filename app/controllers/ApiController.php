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
/*
<form action="http://202.171.212.225/~guilhem/cls/api/imdb-add" method="post">
  <input name="email" value="1234">
  <input name="apiKey" value="xjwrfa60kfjrq787550obagrcw44n8erac6bo3zo">
  <input name="imdbid" value="tt0287839">
  <!-- optional: list=watchlist|watched -->
  <input type="submit">
</form>
*/
    public function imdbAddAction()
    {
        //This should be in an init() common for the whole API!!!
        $userEmail = $this->request->get('email');
        $user = User::findFirst([['email' => $userEmail]]);
        $userApiKey = $this->request->get('apiKey');
        if (empty($user) || $user->apiKey != $userApiKey) {
            echo '{"result": "error", "message": "wrong credentials"}'; // this too should be common somehow
            die();
        }

        // the actual Add function:
        // TODO: this is a routine task, might be done e.g. from web interface, should be in User::something instead
        $imdbID = $this->request->getPost('imdbid');
        $movie = Movie::find([['IMDbID' => $imdbID]]);
        if (empty($movie)) {
            $movieInfo = MovieApi::getByID($imdbID);
            $movie = new Movie();
            $movie->IMDbID = $movieInfo->imdbID;
            $movie->title = $movieInfo->Title;
            foreach ($movieInfo->Ratings as $rating) {
                if ($rating->Source == 'Internet Movie Database') {
                    $movie->ratingIMDb = reset(explode('/', $rating->Value));
                } else if ($rating->Source == 'Rotten Tomatoes') {
                    $movie->ratingRotten = reset(explode('%', $rating->Value));
                }
            }
            $movie->save();
        }
        $targetList = $this->request->getPost('list', null, 'watchlist');
        array_push($user->{$targetList}, $movie);
        $user->save();

        echo '{"result": "OK"}';
    }

    //Watchlist -> Watched
    // google Cal

    //automatically-generated quizz of movies/actors/directors from IMDB
}

