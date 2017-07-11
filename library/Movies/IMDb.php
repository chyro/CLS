<?php

namespace Movies;

/**
 * Class managing the calls to the IMDb API
 */
class IMDb implements MovieApi
{
    // http://stackoverflow.com/questions/1966503/does-imdb-provide-an-api/7744369#7744369
    // http://sg.media-imdb.com/suggests/u/$approximateTitle.json
    static public function getByID(string $IMDbID)
    {
    }
}

