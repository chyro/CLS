<?php

//Route Settings
$router = new \Phalcon\Mvc\Router();
//$router->add('/', ['controller' => 'index', 'action' => 'index'])->setname('homepage');
//$router->addPost() // only match POST requests (same with addGet etc)

$router->add('/',
		['controller' => 'index', 'action' => 'index'])
		->setname('site top');

$router->add('/watchlist/all',
		['controller' => 'watchlist', 'action' => 'watchlist'])
		->setname('watchlist all');
$router->add('/watchlist/add/{movie_id}',
		['controller' => 'watchlist', 'action' => 'add'])
		->setname('watchlist add');
$router->add('/watchlist/delete/{movie_id}',
		['controller' => 'watchlist', 'action' => 'delete'])
		->setname('watchlist delete');

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

$config['di']->set('router', $router);

