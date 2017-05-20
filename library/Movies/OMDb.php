<?php

namespace Movies;

/**
 * Class managing the calls to the OMDb API
 *
 * http://www.omdbapi.com/
 */
class OMDb {
    /**
     * @var string
     */
    private static $url = 'http://www.omdbapi.com/';
    /**
     * @var array
     */
    private static $defaultParams = [];//['plot' => 'short', 'r' => 'json'];

    public static function setBaseUrl(string $url)
    {
        self::$url = $url;
    }

    /**
     * OMDb accepts IMDb ID as a query criteria. Let's keep that as a standard.
     * Example returned string: {"Title":"On the Other Side of the Tracks","Year":"2012", etc }
     * Example error string:    {"Response":"False","Error":"Movie not found!"}
     */
    public function getByID(string $IMDbID)
    {
        $params = array_merge(self::$defaultParams, ['i' => $IMDbID]);
        $queryURL = self::$url . '?' . http_build_query($params);
        $info = file_get_contents($queryURL);
        $info = json_decode($info);
        if (!empty($info->error)) {
            return null;
        }
        // TODO: normalize the fields? create a Movies\Movie based on this array?
        return $info;
    }

    //TODO: search by title, search by actor, search by director... get posted by IMDBID...
}

