<?php

/*
TODO:
\Phalcon shell:
- bootstrap?
- healcheck? => check \Phalcon is loaded, check Mongo is loaded
- Layouts
- add CSS / JS based on controller / action
*/

define('BASE_DIR', rtrim(dirname(__DIR__), '/') . '/');
define('APP_DIR', BASE_DIR . 'app/');
define('BASE_URL', rtrim(dirname($_SERVER["SCRIPT_NAME"]), '/') . '/'); // hoping to handle root and subfolder installs // PHP_SELF seems to work as well
//TODO: make sure BASE_DIR and APP_DIR always have a trailing slash

try {

    $config = new \Phalcon\Config\Adapter\Ini(APP_DIR . 'config/config.ini');
    $localConfig = new \Phalcon\Config\Adapter\Ini(APP_DIR . 'config/config.local.ini');

    //Register an autoloader
    $loader = new \Phalcon\Loader();
    $loader->registerNamespaces( [
        'Watchlist\Controllers' => BASE_DIR . $config->application->controllersDir,
        'Watchlist\Views' => BASE_DIR . $config->application->viewsDir,
        'Watchlist\Models' => BASE_DIR . $config->application->modelsDir,
        'Watchlist' => BASE_DIR . $config->application->appLibDir,
        'Phalcore' => BASE_DIR . $config->application->librariesDir . 'Phalcore/',
        'Movies' => BASE_DIR . $config->application->librariesDir . 'Movies/',
        'Phalcon' => BASE_DIR . $config->application->vendorsDir . 'phalcon/incubator/Library/Phalcon/',
        'Leafo\ScssPhp' => BASE_DIR . $config->application->vendorsDir . 'leafo/scssphp/src/',
        'Erusev\Parsedown' => BASE_DIR . $config->application->vendorsDir . 'erusev/parsedown/',
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
        $view->setViewsDir(BASE_DIR . $config->application->viewsDir);
        //$view->setLayoutsDir(BASE_DIR . $config->application->viewsDir . 'layout/'); // TODO: make sure BASE_DIR, and every other dir, always has a final /
        //$view->setLayout('index');
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
    include(APP_DIR . "config/routes.php");

    //DB Settings
    $config['di']->set('mongo', function() use ($localConfig) {
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
        $db = $mongoClient->selectDatabase($localConfig->database->name);
        return $db;
    }, true);

    $config['di']->set('collectionManager', function() {
        return new \Phalcon\Mvc\Collection\Manager();
    }, true);

    $config['di']->set('url', function() {
        $url = new \Phalcon\Mvc\Url();
        $url->setBaseUri(BASE_URL);
        return $url;
    });

    $config['di']->set('head', function() use ($config) {
        $headHelper = new \Phalcore\Helper\Head();
        $headHelper->setCacheDir(['css' => $config->application->CSSCacheDir, 'js' => $config->application->JSCacheDir]);
        if ($config->application->minifyScripts) {
            $headHelper->setMinified(true);
        }
        if ($config->application->mergeScripts) {
            $headHelper->setMerged(true);
        }
        $headHelper->addImportPath('scss', 'scss');

        // Adding Kickstart resources (should be in a saner place, e.g. base controller)
        // http://www.adamgrant.me/development/kickstart/
        // http://getkickstart.com/
        $headHelper->addImportPath('kickstart/scss', 'scss');
        $headHelper->scripts()->appendFile('kickstart/kickstart.min.js');
        return $headHelper;
    }, true);

    // TODO: INITIALIZE THE MOVIE API library, including the third party APIS WITH THEIR KEYS, IF REQUIRED / AVAILABLE
    \Movies\TheMovieDb::init(['apiKey' => $localConfig->MovieAPIs->TheMovieDBAPIKey]);

    //Handle the request
    $application = new \Phalcon\Mvc\Application($config['di']);

    echo $application->handle()->getContent();

} catch(\Phalcon\Exception $e) {
    echo '\PhalconException: ', $e->getMessage();
    echo '<pre>'; var_dump($e->getTrace()); echo '</pre>';
}

