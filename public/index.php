<?php

/*
TODO:
\Phalcon shell:
- bootstrap?
- healcheck? => check \Phalcon is loaded, check Mongo is loaded
- Layouts
- add CSS / JS based on controller / action
- if Dev, if file.LESS newer than file.css, compile LESS
- headlink / headscript, with optional merger / minifier
*/

define('BASE_DIR', dirname(__DIR__));
define('APP_DIR', BASE_DIR . '/app');

try {

	$config = new \Phalcon\Config\Adapter\Ini(APP_DIR . '/config/config.ini');

	//Register an autoloader
	$loader = new \Phalcon\Loader();
	$loader->registerNamespaces( [
		"Watchlist\Controllers" => BASE_DIR . '/' . $config->application->controllersDir,
		"Watchlist\Views" => BASE_DIR . '/' . $config->application->viewsDir,
		"Watchlist\Models" => BASE_DIR . '/' . $config->application->modelsDir,
		"Watchlist" => BASE_DIR . '/' . $config->application->appLibDir,
		"Phalcore" => BASE_DIR . '/' . $config->application->librariesDir . "phalcore/"
		] )->register();

	//Create a DI
	$config["di"] = new \Phalcon\DI\FactoryDefault();

	$config["di"]->set('session', function() {
		$session = new \Phalcon\Session\Adapter\Files();
		$session->start();
		return $session;
	});

	//Setup the view component
	$config["di"]->set('view', function() use ($config) {
		$view = new \Phalcon\Mvc\View();
		$view->setViewsDir(BASE_DIR . '/' . $config->application->viewsDir);
		return $view;
	});

	$config["di"]->set('dispatcher', function () {
		$dispatcher = new \Phalcon\Mvc\Dispatcher();
		$dispatcher->setDefaultNamespace('Watchlist\Controllers');
		return $dispatcher;
	});

	//Route Settings
	include("../app/config/routes.php");

	//DB Settings
	$config["di"]->set('mongo', function() use ($config) {
		$mongo = new MongoClient();
		return $mongo->selectDB($config->database->name);
	}, true);

	$config["di"]->set('collectionManager', function(){
		return new \Phalcon\Mvc\Collection\Manager();
	}, true);

	$config["di"]->set('url', function(){
		$url = new \Phalcon\Mvc\Url();
		$url->setBaseUri('/');
		return $url;
	});

	//Handle the request
	$application = new \Phalcon\Mvc\Application($config["di"]);

	echo $application->handle()->getContent();

} catch(\Phalcon\Exception $e) {
	echo "\PhalconException: ", $e->getMessage();
	echo "<pre>"; var_dump($e->getTrace()); echo "</pre>";
}

