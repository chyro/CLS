<?php

namespace Phalcore;

class Controller extends \Phalcon\Mvc\Controller
{

    public function initialize()
    {
        // Automatically add CSS and JS files: $ext/default.$ext, $ext/$controller.$ext, $ext/$controller/$action.$ext
        $controller = $this->router->getControllerName();
        $action = $this->router->getActionName();
        $autoload = ['default', $controller, $controller . '/' . $action];
        foreach ($autoload as $filebase) {
            if (file_exists(Env::getDocroot() . 'css/' . $filebase . '.css')) {
                $this->head->styles()->appendFile('css/' . $filebase . '.css');
            }
            if (file_exists(Env::getDocroot() . 'scss/' . $filebase . '.scss')) {
                $this->head->styles()->appendFile('scss/' . $filebase . '.scss');
            }
            if (file_exists(Env::getDocroot() . 'js/' . $filebase . '.js')) {
                $this->head->scripts()->appendFile('js/' . $filebase. '.js');
            }
            // TODO: add ES6 files too
        }
    }

    /**
     * Redirect helper
     *
     * It's really nothing much, but redirects are not something I want to worry about. Now I can
     * simply use "return $this->redirect($url);" and forget about it.
     */
    public function redirect(string $url) // would it be convenient to allow a message here? e.g. (string $url, string $message = '', string $type = ERROR|NOTICE|WARNING)
    {
        //maybe: if $url is an array, $url = $this->url->get($url)?
        $this->response->redirect($url, true);
        $this->view->disable();
        return;
    }

}

