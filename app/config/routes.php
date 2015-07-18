<?php

//Route Settings
$router = new \Phalcon\Mvc\Router();
//$router->add("/", ['controller' => 'index', 'action' => 'index'])->setname('homepage');
//$router->addPost() // only match POST requests (same with addGet etc)

$router->add("/watchlist/all/:params",
		['controller' => 'watchlist', 'action' => 'watchlist', 'params' => 1])
		->setname('watchlist all');
$router->add("/watchlist/add/:params",
		['controller' => 'watchlist', 'action' => 'add', 'params' => 1])
		->setname('watchlist add');
$router->add("/watchlist/delete/{movie_id}",
		['controller' => 'watchlist', 'action' => 'delete'])
		->setname('watchlist delete');

$router->add("/watchlist/watched/",
		['controller' => 'watchlist', 'action' => 'watched'])
		->setname('watched all');
$router->add("/watchlist/watched/add/:params",
		['controller' => 'watchlist', 'action' => 'watchedAdd', 'params' => 1])
		->setname('watched add');
$router->add("/watchlist/watched/{movie_id}",
		['controller' => 'watchlist', 'action' => 'watched'])
		->setname('watched');

$router->add("/watchlist/recommended/",
		['controller' => 'watchlist', 'action' => 'recommended'])
		->setname('recommended all');
$router->add("/watchlist/recommended/add/:params",
		['controller' => 'watchlist', 'action' => 'recommendedAdd', 'params' => 1])
		->setname('recommended add');

$config["di"]->set('router', $router);

