<?php
/**
 * Получение погоды с OpenWeatherMap
 */

require_once dirname(dirname(__FILE__)).'/../EssentialDataServiceBase.php';

class OpenWeatherMapService extends EssentialDataServiceBase
{
    protected $name = 'weather';
    protected $title = 'Прогноз погоды';

    public function checkDriverData($data)
    {
        return true;
    }
}
