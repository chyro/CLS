<?php
namespace Phalcore\Helper;

use \Phalcore\Helper\Head\Script as HeadScript;
use \Phalcore\Helper\Head\Style as HeadStyle;

/**
 * Dependency helper, assisting with the CSS / JS files
 */
class Head extends \Phalcon\Di\Injectable
{
    private $_script; // HeadScript
    private $_style; // HeadStyle

    private $cacheDirs = []; // array
    private $doMinify = false; // bool
    private $doMerge = false; // bool

    /* // lazy loading
    public function __construct()
    {
        $this->_script = new \Phalcore\Helper\Head\Script();
        $this->_style  = new \Phalcore\Helper\Head\Style();
    }*/

    public function setCacheDir(/*array|string*/ $paths, string $for = '')
    {
        if (is_array($paths)) {
            foreach ($paths as $for => $path) {
                $this->setCacheDir($path, $for);
            }
        } else {
            $this->cacheDirs[$for] = $paths;
        }
    }

    public function getCacheDir(/*array|string*/ $paths, string $for = '')/*: array|string*/
    {
        if (!empty($for)) {
            return empth($this->cacheDirs[$for]) ? '' : $this->cacheDirs[$for];
        } // else
        return $this->cacheDirs;
    }

    public function setMinify(bool $switch)
    {
        $this->doMinify = $switch;
    }

    public function getMinify(): bool
    {
        return $this->doMinify;
    }

    public function setMerge(bool $switch)
    {
        $this->doMerge = $switch;
    }

    public function getMerge(): bool
    {
        return $this->doMerge;
    }

    public function scripts(): HeadScript
    {
        if (empty($this->_script)) {
            $this->_script = new HeadScript($this);
            /*$this->_script->setMinify($this->doMinify);
            $this->_script->setMerge($this->doMerge);
            if (!empty($this->cacheDirs['js'])) {
                $this->_script->setCacheDir($this->cacheDirs['js']);
            }*/
        }
        return $this->_script;
    }

    public function styles(): HeadStyle
    {
        if (empty($this->_style)) {
            $this->_style = new HeadStyle($this);
            /*$this->_style->setMinify($this->doMinify);
            $this->_style->setMerge($this->doMerge);
            if (!empty($this->cacheDirs['css'])) {
                $this->_style->setCacheDir($this->cacheDirs['css']);
            }*/
        }
        return $this->_style;
    }
}

