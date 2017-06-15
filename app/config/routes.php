<?php

//Route Settings
$router = new \Phalcon\Mvc\Router();
//$router->add('/', ['controller' => 'index', 'action' => 'index'])->setname('homepage');
//$router->addPost() // only match POST requests (same with addGet etc)

$router->add('/',
    ['controller' => 'index', 'action' => 'index'])
    ->setname('top');

$router->add('/watchlist/all',
    ['controller' => 'watchlist', 'action' => 'watchlist'])
    ->setname('watchlist all');
$router->add('/watchlist/add/{movie_id}',
    ['controller' => 'watchlist', 'action' => 'add'])
    ->setname('watchlist add');
$router->add('/watchlist/delete/{movie_id}',
    ['controller' => 'watchlist', 'action' => 'delete'])
    ->setname('watchlist delete');

//TODO: delete all those routes, just use GET params on the watchlist route
// more useful routes will be things like '/cal' i.e. use-case driven rather than data-driven
$router->add('/watchlist/watched/',
    ['controller' => 'watchlist', 'action' => 'watched'])
    ->setname('watched all');
$router->add('/watchlist/watched/add/{movie_id}',
    ['controller' => 'watchlist', 'action' => 'watchedAdd'])
    ->setname('watched add');
$router->add('/watchlist/watched/delete/{movie_id}',
    ['controller' => 'watchlist', 'action' => 'watchedDelete'])
    ->setname('watched delete');

$router->add('/watchlist/recommended/',
    ['controller' => 'watchlist', 'action' => 'recommended'])
    ->setname('recommended all');
$router->add('/watchlist/recommended/add/{movie_id}',
    ['controller' => 'watchlist', 'action' => 'recommendedAdd'])
    ->setname('recommended add');
$router->add('/watchlist/recommended/delete/{movie_id}',
    ['controller' => 'watchlist', 'action' => 'recommendedDelete'])
    ->setname('recommended delete');

$router->add('/user',
    ['controller' => 'user', 'action' => 'index'])
    ->setname('user home');
$router->add('/user/login',
    ['controller' => 'user', 'action' => 'login'])
    ->setname('login');
$router->add('/user/logout',
    ['controller' => 'user', 'action' => 'logout'])
    ->setname('logout');
$router->add('/user/register',
    ['controller' => 'user', 'action' => 'register'])
    ->setname('register');
$router->add('/user/profile',
    ['controller' => 'user', 'action' => 'profile'])
    ->setname('user profile');

$router->add('/api/imdb-add',
    ['controller' => 'api', 'action' => 'imdbAdd'])
    ->setname('api imdb add');
$router->add('/api/movie-status',
    ['controller' => 'api', 'action' => 'movieStatus'])
    ->setname('api movie status');

$config['di']->set('router', $router);

