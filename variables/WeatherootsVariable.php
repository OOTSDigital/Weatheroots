<?php

namespace Craft;

/**
 * Weatheroots Variable provides access to database objects from templates ie. from Admin CP
 */
class WeatherootsVariable
{
    /**
     * Get all available towns
     *
     * @return array
     */
    public function getAll()
    {
        return craft()->weatheroots->getAll();
    }

    //get town by id
    public function getTownById($id)
    {
        return craft()->weatheroots->getTownById($id);
    }

    //get all towns RSS feed
    public function getAllTownsFeed()
    {
        return craft()->weatheroots->allTownsFeed();
    }

    //get all towns Weather <li>
    public function getAllTownsWeather()
    {
        return craft()->weatheroots->allTownsWeather();
    }

}