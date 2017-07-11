<?php

namespace Movies;

/**
 * Class managing the calls to the "The Movie DB" API
 *
 * http://api.themoviedb.org/3/find/tt0137523?api_key=XXXXX&external_source=imdb_id
 * https://developers.themoviedb.org/3/movies/get-upcoming
 * TODO: add credits: https://www.themoviedb.org/assets/static_cache/b32eda2e3812fd459d394791849e7144/images/v4/logos/stacked-blue.svg
 */
class TheMovieDb implements MovieApi
{
    /**
     * @var string
     */
    private static $url = 'http://api.themoviedb.org/3/';

    /**
     * @var string
     */
    private static $apiKey = '';

    public static function init(array $settings)
    {
        if (!empty($settings['baseUrl'])) {
            self::$url = $settings['baseUrl'];
        }
        if (!empty($settings['apiKey'])) {
            self::$apiKey = $settings['apiKey'];
        }
    }

    public static function getByIMDbID(string $IMDbID) //: array | null
    {
        $queryURL = self::_getQueryUrl('find', $IMDbID, ['external_source' => 'imdb_id']);
        $info = file_get_contents($queryURL);
        return self::_normalize($info);
    }

    private static function _getQueryUrl(string $apiFunction, string $pathParam, array $queryParams = []): string
    {
        $queryParams['api_key'] = self::$apiKey;
        return self::$url . $apiFunction . '/' . $pathParam . '?' . http_build_query($queryParams);
    }

    private static function _normalize($TMDbJSON) //: array | null
    {
        $movieInfo = json_decode($TMDbJSON);

        if (empty($movieInfo->movie_results)) {
            return null;
        }

        $movieInfo = $movieInfo->movie_results[0];
        $normalizedInfo = [];

        if (!empty($movieInfo->title)) {
            $normalizedInfo['title'] = $movieInfo->title;
        }

        if (!empty($movieInfo->poster_path)) {
            $normalizedInfo['tmb_url'] = $movieInfo->poster_path;
        }

        return $normalizedInfo;
    }
}

