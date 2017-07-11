<?php

namespace Movies;

/**
 * Interface for third party movie APIs
 *
 * to make sure we can add any number of third party APIs and they will be
 * interchangeable
 *
 * This could be an abstract class, alternatively, if we have any common code.
 */
interface MovieApi
{
    //private static $url = 'http://www.omdbapi.com/';
    //public static function setBaseUrl(string $url);

    /**
     * optional: set base URL, credentials, etc
     */
    public static function init(array $settings);

    /**
     * query a movie by IMDb ID (standard in many APIs)
     */
    public static function getByIMDbID(string $IMDbID); // array | null

    //TODO: search by title, search by actor, search by director... get posted by IMDBID...
}

