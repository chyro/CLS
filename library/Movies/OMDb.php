<?php

namespace Movies;

/**
 * Class managing the calls to the OMDb API
 *
 * http://www.omdbapi.com/
 */
class OMDb implements MovieApi
{
    /**
     * @var string
     */
    private static $url = 'http://www.omdbapi.com/';

    /**
     * @var array
     */
    private static $defaultParams = [];//['plot' => 'short', 'r' => 'json'];

    public static function init(array $settings)
    {
        if (!empty($settings['baseUrl'])) {
            self::$url = $settings['baseUrl'];
        }
        if (!empty($settings['defaultParams'])) {
            self::$defaultParams = $settings['defaultParams'];
        }
    }

    /**
     * OMDb accepts IMDb ID as a query criteria. Let's keep that as a standard.
     * Example returned string: {"Title":"On the Other Side of the Tracks","Year":"2012", etc }
     * Example error string:    {"Response":"False","Error":"Movie not found!"}
     */
    public static function getByIMDbID(string $IMDbID)
    {
        $params = array_merge(self::$defaultParams, ['i' => $IMDbID]);
        $queryURL = self::$url . '?' . http_build_query($params);
        $info = file_get_contents($queryURL);
        return self::_normalize($info);
    }

    private static function _normalize(string $OMDbJSON)
    {
        $movieInfo = json_decode($OMDbJSON);
        if (!empty($movieInfo->error)) {
            return null;
        }

        $normalizedInfo = [];

        if (!empty($movieInfo->Title)) {
            $normalizedInfo['title'] = $movieInfo->Title;
        }

        foreach ($movieInfo->Ratings as $rating) {
            if ($rating->Source == 'Internet Movie Database') {
                $normalizedInfo['rt_imdb'] = reset(explode('/', $rating->Value));
            } else if ($rating->Source == 'Rotten Tomatoes') {
                $normalizedInfo['rt_rt'] = reset(explode('%', $rating->Value));
            }
        }

        return $normalizedInfo;
    }

}

