<?php
namespace Watchlist\Controllers;

class IndexController extends \Phalcore\Controller
{

    public function indexAction()
    {
        //Early tests... (basic files are now automatically appended, using the Phalcore controller as a base)
        //$this->head->styles()->appendFile('css/default.css');
        //$this->head->styles()->appendFile('scss/default.scss');
        //$this->head->styles()->appendCSS('body p { display: block; }');
        //$this->head->styles()->appendSCSS('body { p { display: block; } }');
        //$this->head->scripts()->appendFile('js/default.js');
        //$this->head->scripts()->appendJS('console.log("snippet");');
    }

}
