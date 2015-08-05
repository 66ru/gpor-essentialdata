<?php
/**
 * Получение погоды с OpenWeatherMap
 */

require_once dirname(dirname(__FILE__)).'/../EssentialDataServiceBase.php';

class OpenWeatherMapCurrentService extends EssentialDataServiceBase
{
    protected $name = 'currentweather';
    protected $title = 'Текущая погода';

    public function checkDriverData($data)
    {
        return count($data) > 0 ? true : false;
    }
}
