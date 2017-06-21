<?php
namespace Phalcore\Helper\Head;

/**
 * Dependency helper, assisting with the CSS files.
 * Supports SCSS files, using Leafo's compiler.
 * Also minifies and merges if needed.
 */
class Style
{
    private $_compiler; // Compiler
    private $_minifier; // Minifier
    private $_styles = []; // array
    private $_settings; // Head helper

    public function __construct($settings)
    {
        $this->_settings = $settings;
    }

    public function appendFile(string $filePath, string $media = "screen")
    {
        $type = (substr($filePath, -5) === '.scss') ? 'scss' : 'css';
        array_push($this->_styles, ['type' => "$type file", 'path' => $filePath, 'media' => $media]);
    }

    public function prependFile(string $filePath, string $media = "screen")
    {
        $type = (substr($filePath, -5) === '.scss') ? 'scss' : 'css';
        array_unshift($this->_styles, ['type' => "$type file", 'path' => $filePath, 'media' => $media]);
    }

    public function appendCSS(string $style)
    {
        array_push($this->_styles, ['type' => 'css snippet', 'snippet' => $style]);
    }

    public function prependCSS(string $style)
    {
        array_unshift($this->_styles, ['type' => 'css snippet', 'snippet' => $style]);
    }

    public function appendSCSS(string $style)
    {
        array_push($this->_styles, ['type' => 'scss snippet', 'snippet' => $style]);
    }

    public function prependSCSS(string $style)
    {
        array_unshift($this->_styles, ['type' => 'scss snippet', 'snippet' => $style]);
    }

    public function getCompiler(): Compiler
    {
        if (empty($this->_compiler)) {
            $this->_compiler = new \Leafo\ScssPhp\Compiler();
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
        //TODO compile SCSS files, store the compiled CSS in a folder, and replace the _styles scss file with the new css file

        //TODO minify and unify (https://github.com/matthiasmullie/minify ?)
        if ($this->_settings->getMerge()) {
            /* do merge */
        }
        if ($this->_settings->getMinify()) {
            /* do minify */
        }

        $HTMLBits = [];
        foreach ($this->_styles as $bit) {
            $bitHTML = '';
            if ($bit['type'] == 'css file') {
                $bitHTML = '<link rel="stylesheet" href="' . $bit['path'] . '" media="' . $bit['media'] . '" type="text/css" />';
            } else if ($bit['type'] == 'css snippet') {
                $bitHTML = '<style>' . $bit['snippet'] . '</style>';
            }
            $HTMLBits[] = $bitHTML;
        }
        return implode("\n", $HTMLBits) . "\n";
    }

}

