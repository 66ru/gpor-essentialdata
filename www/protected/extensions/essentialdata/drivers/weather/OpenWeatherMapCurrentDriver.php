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

        $weatherArr = array(
            'name'                  => (!empty($this->cityName)) ? $this->cityName : $cityName,
            'temperature'           => $mainCityDetailData['main']['temp'],
            'precipitationIcon'     => $this->precipitation($mainCityDetailData['weather'][0]),
            'precipitationText'     => $this->condition($mainCityDetailData['weather'][0]),
            'other'                 => array()
        );

        foreach ($this->cities as $cityData) {
            $cityDetailData = $this->getCurrentWeatherData($cityData['cityId'], $cityData['lon'], $cityData['lat']);
            
            $cityId     = $cityDetailData['id'];
            $cityName   = $cityDetailData['name'];

            $cityDetailArr = array();

            if (!isset($weatherArr['other'][$cityId])) {
                $weatherArr['other'][$cityId] = array(
                    'name'                  => !empty($cityData['cityName']) ? $cityData['cityName'] : $cityName,
                    'temperature'           => $cityDetailData['main']['temp'],
                    'precipitationIcon'     => $this->precipitation($cityDetailData['weather'][0]),
                    'precipitationText'     => $this->condition($cityDetailData['weather'][0]),
                );
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
