<?php

try {

	$config = new Phalcon\Config\Adapter\Ini('../app/config/config.ini');

	//Register an autoloader
	$loader = new Phalcon\Loader();
	$loader->registerDirs( [
		$config->application->controllersDir,
		$config->application->pluginsDir,
		$config->application->libraryDir,
		$config->application->modelsDir,
		] )->register();

	//Create a DI
	$di = new Phalcon\DI\FactoryDefault();

	$di->set('session', function() {
		$session = new Phalcon\Session\Adapter\Files();
		$session->start();
		return $session;
	});

	//Setup the view component
	$di->set('view', function() use ($config) {
		$view = new \Phalcon\Mvc\View();
		$view->setViewsDir($config->application->viewsDir);
		return $view;
	});

	//Route Settings
	include("../app/config/routes.php");

	//DB Settings
	$di->set('mongo', function() use ($config) {
		$mongo = new MongoClient();
		return $mongo->selectDB($config->database->name);
	}, true);

	$di->set('collectionManager', function(){
		return new Phalcon\Mvc\Collection\Manager();
	}, true);

	$di->set('url', function(){
		$url = new \Phalcon\Mvc\Url();
		$url->setBaseUri('/');
		return $url;
	});

	//Handle the request
	$application = new \Phalcon\Mvc\Application($di);

	echo $application->handle()->getContent();

} catch(\Phalcon\Exception $e) {
	echo "PhalconException: ", $e->getMessage();
	echo "<pre>"; var_dump($e->getTrace()); echo "</pre>";
}

