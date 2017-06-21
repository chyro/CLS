<?php
namespace Watchlist\Controllers;

class IndexController extends \Phalcon\Mvc\Controller
{

    public function indexAction()
    {
//TODO: in appendFile / prependFile, adjust the file path to the project path, so it will work in a subdomain or folder alike
//TODO: append css/default.css, scss/default.scss, js/default.js, es6/default.es6 in the base controller (coming soon); also append controller / action files
        $this->head->styles()->appendFile('css/default.css');
        $this->head->styles()->appendFile('scss/default.scss');
        $this->head->styles()->appendCSS('body p { display: block; }');
        $this->head->styles()->appendSCSS('body { p { display: block; } }');
        $this->head->scripts()->appendFile('js/default.js');
        $this->head->scripts()->appendJS('console.log("snippet");');
    }

}
