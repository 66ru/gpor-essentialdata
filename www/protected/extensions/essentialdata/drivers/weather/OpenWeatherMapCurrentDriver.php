<?php
/**
 * Драйвер для общения с OpenWeatherMap
 * http://openweathermap.org/weather-conditions
 */

require_once dirname(__FILE__).'/OpenWeatherMapDriverBase.php';


class OpenWeatherMapCurrentDriver extends OpenWeatherMapDriverBase
{
    protected $name = 'weatherCurrent';
    protected $title = 'Прогноз погоды';


    /**
     * Получить текущую погоду
     */
    private function getCurrentWeather()
    {
        // Получаем текущую погоду
        $mainCityDetailData = $this->getCurrentWeatherData($this->cityId, $this->lon, $this->lat);
        $cityName = $mainCityDetailData['name'];

        $weatherArr = $this->createDetailCityArray($mainCityDetailData);
        $weatherArr['name'] = (!empty($this->cityName)) ? $this->cityName : $cityName;
        $weatherArr['other'] = array();

        foreach ($this->cities as $cityData) {
            $cityDetailData = $this->getCurrentWeatherData($cityData['cityId'], $cityData['lon'], $cityData['lat']);
            
            $cityId     = $cityDetailData['id'];
            $cityName   = $cityDetailData['name'];

            $cityDetailArr = array();

            if (!isset($weatherArr['other'][$cityId])) {
                $weatherArr['other'][$cityId] = $this->createDetailCityArray($cityDetailData);
                $weatherArr['other'][$cityId]['name'] = !empty($cityData['cityName']) ? $cityData['cityName'] : $cityName;
            }
        }
        return $weatherArr;
    }


    public function run()
    {
        $isGeoEmpty = !$this->lon || !$this->lat;
        if (!$this->cityId && $isGeoEmpty)
            throw new EssentialDataException(Yii::t('essentialdata', get_class($this).': cityId or coordinates attributes required', array()), 500);

        $weatherArr = $this->getCurrentWeather();
        $this->setData($weatherArr);

        if (!sizeof($weatherArr))
            Yii::app()->essentialData->report(get_class($this).': data empty');

        return true;
    }
}
