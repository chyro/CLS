<?php
namespace Phalcore\Helper\Head;

//TODO: ES6 compiler
//TODO: minifier
//TODO: prevent duplicates?

/**
 * Dependency helper, assisting with the JS files.
 * Supports ES6 files, using ???
 * Also minifies and merges if needed.
 */
class Script
{
    private $_compiler; // Compiler
    private $_minifier; // Minifier
    private $_scripts = []; // array
    private $_settings; // Head helper

    public function __construct($settings)
    {
        $this->_settings = $settings;
    }

    public function appendFile(string $filePath)
    {
        $type = (substr($filePath, -4) === '.es6') ? 'es6' : 'js';
        array_push($this->_scripts, ['type' => "$type file", 'path' => $filePath]);
    }

    public function prependFile(string $filePath)
    {
        $type = (substr($filePath, -4) === '.es6') ? 'es6' : 'js';
        array_unshift($this->_scripts, ['type' => "$type file", 'path' => $filePath]);
    }

    public function appendJS(string $script)
    {
        array_push($this->_scripts, ['type' => 'js snippet', 'snippet' => $script]);
    }

    public function prependJS(string $script)
    {
        array_unshift($this->_scripts, ['type' => 'js snippet', 'snippet' => $script]);
    }

    //TODO: appendES6, prependES6

    public function getCompiler(): Compiler
    {
        if (empty($this->_compiler)) {
            //$this->_compiler = ???;
        }
        return $this->_compiler;
    }

    public function getMinifier(): Minifier
    {
        if (empty($this->_minifier)) {
            //$this->_minifier = ???;
        }
        return $this->_minifier;
    }

    public function get(): string
    {
        //TODO compile ES6 files, store the compiled JS in a folder, and replace the added file with that
        //foreach ($this->_scripts as $bit) { if ($bit['type'] == 'es6 file') { } }

        //TODO minify and unify (https://github.com/matthiasmullie/minify ?)
        if ($this->_settings->getMerged()) {
            /* do merge */
        }
        if ($this->_settings->getMinified()) {
            /* do minify */
        }

        $HTMLBits = [];
        foreach ($this->_scripts as $bit) {
            $bitHTML = '';
            if ($bit['type'] == 'js file') {
                $bitHTML = '<script type="text/javascript" src="' . $bit['path'] . '"></script>';
            } else if ($bit['type'] == 'js snippet') {
                $bitHTML = '<script type="text/javascript">' . $bit['snippet'] . '</script>';
            }
            $HTMLBits[] = $bitHTML;
        }
        return implode("\n", $HTMLBits) . "\n";
    }
}

