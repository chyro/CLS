<?php
namespace Phalcore\Helper\Head;

use Phalcore\Env;

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

    public function getCompiler()/*: Compiler*/
    {
        if (empty($this->_compiler)) {
            $this->_compiler = new \Leafo\ScssPhp\Compiler();
            foreach ($this->_settings->getImportPaths('scss') as $importPath) {
                $this->_compiler->addImportPath(Env::getDocroot() . $importPath);
            }
        }
        return $this->_compiler;
    }

    public function getMinifier()/*: Minifier*/
    {
        if (empty($this->_minifier)) {
            //$this->_minifier = ???;
        }
        return $this->_minifier;
    }

    public function get(): string
    {
        // process SCSS files: compile, store, replace
        foreach ($this->_styles as $i => $bit) {
            if ($bit['type'] == 'scss file') {
                // calculate file paths
                $sourceFileRelativePath = $bit['path'];
                $sourceFileFullPath = Env::getDocroot() . $sourceFileRelativePath;
                $sourceFileInfo = pathinfo($sourceFileRelativePath);
                $targetFileName = \Phalcore\Text::slugify($sourceFileInfo['dirname'] . '/' . $sourceFileInfo['filename']) . '.css';
                $targetFileRelativePath = $this->_settings->getCacheDir('css') . $targetFileName;
                $targetFileFullPath = Env::getDocroot() . $targetFileRelativePath;

                // compile if necessary
                if (!file_exists($targetFileFullPath) || filemtime($targetFileFullPath) < filemtime($sourceFileFullPath)) {
                    $scssSnippet = file_get_contents($sourceFileFullPath);
                    $cssSnippet = $this->getCompiler()->compile($scssSnippet);
                    file_put_contents($targetFileFullPath, $cssSnippet);
                }

                // replace SCSS file with the newly compiled CSS file
                $this->_styles[$i]['path'] = $targetFileRelativePath;
                $this->_styles[$i]['type'] = 'css file';
            } elseif ($bit['type'] == 'scss snippet') {
                $scssSnippet = $bit['snippet'];
                $cssSnippet = $this->getCompiler()->compile($scssSnippet);
                $this->_styles[$i]['snippet'] = $cssSnippet;
                $this->_styles[$i]['type'] = 'css snippet';
            }
        }

        //TODO minify and unify (https://github.com/matthiasmullie/minify ?)
        if ($this->_settings->getMerged()) {
            /* do merge */
        }
        if ($this->_settings->getMinified()) {
            /* do minify */
        }

        $HTMLBits = [];
        $url = $this->_settings->getDI()->get('url');
        foreach ($this->_styles as $bit) {
            $bitHTML = '';
            if ($bit['type'] == 'css file') {
                $bitHTML = '<link rel="stylesheet" href="' . $url->get($bit['path']) . '" media="' . $bit['media'] . '" type="text/css" />';
            } else if ($bit['type'] == 'css snippet') {
                $bitHTML = '<style>' . $bit['snippet'] . '</style>';
            }
            $HTMLBits[] = $bitHTML;
        }
        return implode("\n", $HTMLBits) . "\n";
    }

}

