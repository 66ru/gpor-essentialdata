<?php
/**
 * Получение погоды с OpenWeatherMap
 */

require_once dirname(dirname(__FILE__)).'/EssentialDataServiceBase.php';

class EssentialOpenWeatherService extends EssentialDataServiceBase
{
    protected $name = 'weather';
    protected $title = 'Прогноз погоды';
    protected $current = false;         // Использовать текущую погоду или за 16 дней

    public function checkDriverData($data)
    {
        return true;
    }
}
