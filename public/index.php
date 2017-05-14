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
		'Watchlist\Controllers' => BASE_DIR . '/' . $config->application->controllersDir,
		'Watchlist\Views' => BASE_DIR . '/' . $config->application->viewsDir,
		'Watchlist\Models' => BASE_DIR . '/' . $config->application->modelsDir,
		'Watchlist' => BASE_DIR . '/' . $config->application->appLibDir,
		'Phalcore' => BASE_DIR . '/' . $config->application->librariesDir . 'phalcore/',
		'Phalcon' => BASE_DIR . '/' . $config->application->vendorsDir . 'incubator/Library/Phalcon/',
		] )->register();

	//Create a DI
	$config['di'] = new \Phalcon\DI\FactoryDefault();

	$config['di']->set('session', function() {
		$session = new \Phalcore\Session(
			//TODO: allow adding a unique ID in the config file, in case separate instances of this app are installed on the same server
			new \Phalcon\Session\Adapter\Files(['uniqueId' => 'cls'])
		);
		$session->start();
		return $session;
	});

	//Setup the view component
	$config['di']->set('view', function() use ($config) {
		$view = new \Phalcon\Mvc\View();
		$view->setViewsDir(BASE_DIR . '/' . $config->application->viewsDir);
		return $view;
	});

	$config['di']->set('dispatcher', function () {
		$dispatcher = new \Phalcon\Mvc\Dispatcher();
		$dispatcher->setDefaultNamespace('Watchlist\Controllers');
		return $dispatcher;
	});

	//Setup the flash service
	$config['di']->set('flash', function () {
	    return new \Phalcon\Flash\Session();
	});

	//Route Settings
	include("../app/config/routes.php");

	//DB Settings
	$config['di']->set('mongo', function() use ($config) {
		/**
		 * Migrating from Phalcon 2, PHP 5, MongoClient to Phalcon 3, PHP 7, MongoDB...
		 * PHP 7 does not support MongoClient, Phalcon does not support MongoDB. Possible workarounds include:
		 * - MongoDB / MongoClient compatibility class: https://github.com/alcaeus/mongo-php-adapter
		 * - Phalcon incubator compatibility class: https://github.com/phalcon/incubator/tree/master/Library/Phalcon/Db/Adapter
		 * Trying the last one. Hopefully the next version of Phalcon will incorporate those into the core
		 * code in a compatible, seamless manner.
		 */
		//$mongo = new MongoClient();
		$mongoClient = new \Phalcon\Db\Adapter\MongoDB\Client();
		$db = $mongoClient->selectDatabase($config->database->name);
		return $db;
	}, true);

	$config['di']->set('collectionManager', function(){
		return new \Phalcon\Mvc\Collection\Manager();
	}, true);

	$config['di']->set('url', function(){
		$url = new \Phalcon\Mvc\Url();
		$base = dirname($_SERVER["SCRIPT_NAME"]); // hoping to handle root and subfolder installs // PHP_SELF seems to work as well
		$base = rtrim($base, '/') . '/'; // making sure there is always a trailing slash
		$url->setBaseUri($base);
		return $url;
	});

	//Handle the request
	$application = new \Phalcon\Mvc\Application($config['di']);

	echo $application->handle()->getContent();

} catch(\Phalcon\Exception $e) {
	echo '\PhalconException: ', $e->getMessage();
	echo '<pre>'; var_dump($e->getTrace()); echo '</pre>';
}

