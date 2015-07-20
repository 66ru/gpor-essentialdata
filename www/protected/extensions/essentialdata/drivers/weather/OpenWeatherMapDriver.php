<?php
/**
 * Драйвер для общения с OpenWeatherMap
 * http://openweathermap.org/weather-conditions
 */

require_once dirname(__FILE__).'/OpenWeatherMapDriverBase.php';


class OpenWeatherMapDriver extends OpenWeatherMapDriverBase
{
    protected $name = 'weather';
    protected $title = 'Прогноз погоды';


    /**
     * Получить информацию о погоде на несколько дней
     */
    private function getWeather()
    {
        $weatherArr = array();

        // Получаем детальную информацию
        $mainCityDetailData = $this->getWeatherData($this->cityId, $this->lon, $this->lat);
        $cityName = $mainCityDetailData['city']['name'];

        foreach ($mainCityDetailData['list'] as $dayData) {
            $currDate = date('Y-m-d', $dayData['dt']);
            $currHour = intval(date('G', $dayData['dt']));
            
            if (!isset($weatherArr[$currDate])) {
                $weatherArr[$currDate] = array(
                    'name'  => (!empty($this->cityName)) ? $this->cityName : $cityName,
                    'other' => array()
                );
            }

            if ($currHour >= 0  &&  $currHour < 6) {
                // Night
                if (!isset($weatherArr[$currDate]['night'])) {
                    $weatherArr[$currDate]['night'] = $this->createDetailCityArray($dayData);
                }
            } else
            if ($currHour < 12) {
                // Morning
                if (!isset($weatherArr[$currDate]['morning'])) {
                    $weatherArr[$currDate]['morning'] = $this->createDetailCityArray($dayData);
                }
            } else
            if ($currHour < 18) {
                // Day
                if (!isset($weatherArr[$currDate]['day'])) {
                    $weatherArr[$currDate]['day'] = $this->createDetailCityArray($dayData);
                }
            } else {
                // Evening
                if (!isset($weatherArr[$currDate]['evening'])) {
                    $weatherArr[$currDate]['evening'] = $this->createDetailCityArray($dayData);
                }
            }
        }

        foreach ($this->cities as $cityData) {
            $cityDetailData = $this->getWeatherData($cityData['cityId'], $cityData['lon'], $cityData['lat']);
            
            $cityId     = $cityDetailData['city']['id'];
            $cityName   = $cityDetailData['city']['name'];

            $cityDetailArr = array();
            foreach ($cityDetailData['list'] as $dayData) {
                $currDate = date('Y-m-d', $dayData['dt']);
                $currHour = intval(date('G', $dayData['dt']));
                
                // Если для главного города такой даты нет, то и записывать некуда
                if (!isset($weatherArr[$currDate]))
                    continue;

                if (!isset($weatherArr[$currDate]['other'][$cityId])) {
                    $weatherArr[$currDate]['other'][$cityId] = array(
                        'name' => !empty($cityData['cityName']) ? $cityData['cityName'] : $cityName,
                    );
                }
                if ($currHour >= 0  &&  $currHour < 6) {
                    // Night
                    if (!isset($weatherArr[$currDate]['other'][$cityId]['night'])) {
                        $weatherArr[$currDate]['other'][$cityId]['night'] = $this->createDetailCityArray($dayData);
                    }
                } else
                if ($currHour < 12) {
                    // Morning
                    if (!isset($weatherArr[$currDate]['other'][$cityId]['morning'])) {
                        $weatherArr[$currDate]['other'][$cityId]['morning'] = $this->createDetailCityArray($dayData);
                    }
                } else
                if ($currHour < 18) {
                    // Day
                    if (!isset($weatherArr[$currDate]['other'][$cityId]['day'])) {
                        $weatherArr[$currDate]['other'][$cityId]['day'] = $this->createDetailCityArray($dayData);
                    }
                } else {
                    // Evening
                    if (!isset($weatherArr[$currDate]['other'][$cityId]['evening'])) {
                        $weatherArr[$currDate]['other'][$cityId]['evening'] = $this->createDetailCityArray($dayData);
                    }
                }
            }

            // Если не допришли нужные данные, выкидываем неполные данные нафиг
            if (!isset($weatherArr[$currDate]['other'][$cityId]['day']))
                unset($weatherArr[$currDate]['other'][$cityId]);
        }

        // Если не допришли нужные данные, выкидываем неполные данные нафиг
        if (!isset($weatherArr[$currDate]['day']))
            unset($weatherArr[$currDate]);

        return $weatherArr;
    }

    public function run()
    {
        $isGeoEmpty = !$this->lon || !$this->lat;
        if (!$this->cityId && $isGeoEmpty)
            throw new EssentialDataException(Yii::t('essentialdata', get_class($this).': cityId or coordinates attributes required', array()), 500);

        $weatherArr = $this->getWeather();
        $this->setData($weatherArr);

        if (!sizeof($weatherArr))
            Yii::app()->essentialData->report(get_class($this).': data empty');

        return true;
    }
}
