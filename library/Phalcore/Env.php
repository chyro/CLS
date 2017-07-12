<?php

namespace Phalcore;

class Env
{
    static public function getDocroot()
    {
        return dirname($_SERVER['SCRIPT_FILENAME']) . '/'; // DOCUMENT_ROOT was not too good with mod_userdir...
    }

    static public function getAppDir()
    {
        return realpath(dirname(realpath($_SERVER['SCRIPT_FILENAME'])) . '/..') . '/';
    }
}

