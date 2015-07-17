<?php
/**
 * Драйвер для общения с OpenWeatherMap
 * http://openweathermap.org/weather-conditions
 */

require_once dirname(dirname(__FILE__)).'/EssentialDataDriverBase.php';

class EssentialOpenWeatherDriver extends EssentialDataDriverBase
{
    protected $name = 'weather';
    protected $title = 'Прогноз погоды';

    protected $cityId = false;
    protected $lon = false;
    protected $lat = false;
    protected $cities = array();
    protected $current = false;     // Получать текущую погоду или за 16 дней

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
                    'name'  => $cityName,
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
                        'name' => $cityName,
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
        }
        return $weatherArr;
    }

    /**
     * Получить код погодных условий
     */
    private function condition($weatherData)
    {
        $conditionArr = array(
            200 => 9,       // гроза
            201 => 9,       // гроза
            202 => 9,       // гроза
            210 => 9,       // гроза
            211 => 9,       // гроза
            212 => 9,       // гроза
            221 => 9,       // гроза
            230 => 9,       // гроза
            231 => 9,       // гроза
            232 => 9,       // гроза

            300 => 4,       // морось
            301 => 4,       // морось
            302 => 5,       // слабый дождь
            310 => 5,       // слабый дождь
            311 => 7,       // дождь
            312 => 7,       // дождь
            313 => 8,       // ливень
            314 => 8,       // ливень
            321 => 8,       // ливень

            500 => 5,       // слабый дождь
            501 => 5,       // слабый дождь
            502 => 7,       // дождь
            503 => 7,       // дождь
            504 => 7,       // дождь
            511 => 13,      // ледяной дождь
            520 => 8,       // ливень
            521 => 8,       // ливень
            522 => 8,       // ливень
            531 => 8,       // ливень

            600 => 14,      // слабый снег
            601 => 16,      // снег
            602 => 17,      // сильный снег
            611 => 12,      // дождь со снегом
            612 => 12,      // дождь со снегом
            615 => 12,      // дождь со снегом
            616 => 12,      // дождь со снегом
            620 => 12,      // дождь со снегом
            621 => 12,      // дождь со снегом
            622 => 12,      // дождь со снегом

            701 => 21,      // туман
            711 => 21,      // туман
            721 => 21,      // туман
            731 => 18,      // пыль в воздухе
            741 => 21,      // туман
            751 => 19,      // пыль с ветром
            761 => 20,      // пыльная буря
            762 => 20,      // пыльная буря
            771 => 22,      // смерчь
            781 => 22,      // смерчь

            800 => 0,       // ясно
            801 => 1,       // переменная облачность
            802 => 1,       // переменная облачность
            803 => 2,       // облачно
            804 => 3,       // пасмурно

            900 => 22,      // смерчь
            901 => 22,      // смерчь
            902 => 22,      // смерчь
            903 => -1,
            904 => -1,
            905 => -1,
            906 => 10,      // град

            951 => -1,
            952 => -1,
            953 => -1,
            954 => -1,
            955 => -1,
            956 => -1,
            957 => -1,
            958 => -1,
            959 => -1,
            960 => -1,
            961 => -1,
            962 => -1
        );
        $essentialConditions = EssentialCurrentWeatherHelper::getWeatherConditions();
        $id = $weatherData['id'];

        $c = -1;
        if (isset($conditionArr[$id]))
            $c = $conditionArr[$id];

        if ($c == -1) {
            // Берем по иконке

            static $arr = array(
                '01d' => 0, '01n' => 0,     // Ясно
                '02d' => 1, '02n' => 1,     // Переменная облачность
                '03d' => 2, '03n' => 2,     // Облачно
                '04d' => 3, '04n' => 3,     // Пасмурно
                '09d' => 7, '09n' => 7,     // Дождь
                '10d' => 8, '10n' => 8,     // Ливень
                '11d' => 9, '11n' => 9,     // Гроза
                '13d' => 16, '13n' => 16,     // Снег
                '50d' => 21, '50n' => 21      // Туман
            );
            $iconCode = $weatherData['icon'];
            $c = (isset($arr[$iconCode]))
                ? $arr[$iconCode]
                : 0;
        }
        return $c;
    }

    /**
     * Получить текущую погоду
     */
    private function getCurrentWeather()
    {
        // Получаем текущую погоду
        $mainCityDetailData = $this->getWeatherData($this->cityId, $this->lon, $this->lat);

        $weatherArr = array(
            'name'          => $mainCityDetailData['name'],
            'temperature'   => $mainCityDetailData['main']['temp'],
            'condition'     => $this->condition($mainCityDetailData['weather'][0]),
            'other'         => array()
        );

        return $weatherArr;
    }

    /**
     * Получить данные о погоде с сервиса
     */
    private function getWeatherData($cityId, $lon=null, $lat=null)
    {
        $queryStr = ($cityId)
            ? 'id='.$cityId
            : 'lon='.$lon.'&lat='.$lat;
        $uri = ($this->current)
            ? 'weather'
            : 'forecast';
        $uri = 'http://api.openweathermap.org/data/2.5/'.$uri.'?'.$queryStr.'&units=metric';

        // Данные не загружены
        $data = $this->component->loadUrl($uri, false);
        if (!$data) {
            Yii::app()->essentialData->report(get_class($this).': url='.$uri.' data didnt load');
            return null;
        }

        // Данные не декодированы
        $data = CJSON::decode($data);
        if (!$data) {
            Yii::app()->essentialData->report(get_class($this).': url='.$uri.' json is wrong');
            return null;
        }

        // Ошибка (например, не найден город)        
        if ($data['cod'] != 200) {
            Yii::app()->essentialData->report(get_class($this).': url='.$uri.' code='.$data['code'].' message='.$data['message']);
            return null;
        }

        return $data;
    }

    /**
     * Перевод гектопаскалей в мм.рт.ст.
     */
    private function mmHg($hPa)
    {
        return round($hPa * 0.75006375541921);
    }

    /**
     * Перевод градусов в направление ветра
     */
    private function windDirection($windData)
    {
        // Абстрактно считаем, что при маленьком ветре стоит полный штиль
        if ($windData['speed'] < 0.1)
            return '-';

        $deg = $windData['deg'];

        // На всякий случай нормируем до 360 градусов
        while ($deg < 0)
            $deg += 360;

        if ($deg <= 33)     return 'n';
        if ($deg <= 78)     return 'ne';
        if ($deg <= 123)    return 'e';
        if ($deg <= 168)    return 'se';
        if ($deg <= 214)    return 's';
        if ($deg <= 258)    return 'sw';
        if ($deg <= 303)    return 'w';
        if ($deg <= 348)    return 'nw';

        // Все остальные углы считаем севером 
        return 'n';
    }

    /**
     * Перевод облачности в % в коды
     */
    private function cloudiness($weatherData)
    {
        /*
         * 0 - ясно
         * 1 - переменная облачность
         * 2 - облачно
         * 3 - пасмурно
         * 4 - дождь
         * 5 - ливень
         * 6 - снег
         * 7 - град
         * 8 - гроза
         * 9 - вечером ясно
         * 10 - вечером переменная облачность
         * 11 - вечером облачно
         */
        static $arr = array(
            '01d' => 0, '01n' => 0,     // Ясно
            '02d' => 1, '02n' => 1,     // Переменная облачность
            '03d' => 2, '03n' => 2,     // Облачно
            '04d' => 3, '04n' => 3,     // Пасмурно
            '09d' => 4, '09n' => 4,     // Дождь
            '10d' => 5, '10n' => 5,     // Ливень
            '11d' => 8, '11n' => 8,     // Гроза
            '13d' => 6, '13n' => 6,     // Снег
            '50d' => 3, '50n' => 3      // Туман
        );
        $iconCode = $weatherData['icon'];
        if (isset($arr[$iconCode]))
            return $arr[$iconCode];

        // В случае, если пришел неизвестный код, возращаем переменную облачность
        return 1;
    }

    /**
     * Получение типа осадков
     */
    private function precipitation($weatherData)
    {
        /*
         * 0 - нет осадков
         * 4 - дождь
         * 5 - ливень
         * 6 - снег
         * 7 - град
         * 8 - гроза
         */
        switch ($weatherData['icon']) {
            case '09d':
            case '09n':
                return 4;

            case '10d':
            case '10n':
                return 5;

            case '13d':
            case '13n':
                return 6;

            case '11d':
            case '11n':
                return 8;
        }

        // Град берется только по коду
        if (intval($weatherData['id']) == 906)
            return 7;

        return 0;
    }

    /**
     * Создает массив детальных данных
     */
    private function createDetailCityArray($data)
    {
        return array(
            "temperature"   => $data['main']['temp'],
            "relwet"        => $data['main']['humidity'],
            "pressure"      => $this->mmHg($data['main']['pressure']),
            "wind"          => $data['wind']['speed'],
            "cloudiness"    => $this->cloudiness($data['weather'][0]),
            "precipitation" => $this->precipitation($data['weather'][0]),
            "windDirection" => $this->windDirection($data['wind'])
        );
    }

    public function run()
    {
        $isGeoEmpty = !$this->lon || !$this->lat;
        if (!$this->cityId && $isGeoEmpty)
            throw new EssentialDataException(Yii::t('essentialdata', get_class($this).': cityId or coordinates attributes required', array()), 500);

        $weatherArr = ($this->current)
            ? $this->getCurrentWeather()
            : $this->getWeather();

        print_r($weatherArr);

        return true;




        if ($this->prefix === false || !$this->cityId)
            throw new EssentialDataException(Yii::t('essentialdata', get_class($this).': cityId and prefix attributes required', array()), 500);

        if (!$this->url1 && !$this->url2)
            throw new EssentialDataException(Yii::t('essentialdata', get_class($this).': url1 and/or url2 attributes required', array()), 500);

        $feedItems = false;
        $result = array();

        $files_days = array(
            $this->prefix.'/0day_forecast.xml',
            $this->prefix.'/1day_forecast.xml',
            $this->prefix.'/2day_forecast.xml',
            $this->prefix.'/3day_forecast.xml',
            $this->prefix.'/4day_forecast.xml',
            $this->prefix.'/5day_forecast.xml',
            $this->prefix.'/6day_forecast.xml',
            $this->prefix.'/7day_forecast.xml',
            $this->prefix.'/8day_forecast.xml',
            $this->prefix.'/9day_forecast.xml',
            $this->prefix.'/10day_forecast.xml',
        );

        for ($if=0; $if<sizeof($files_days); $if++) {
            $file1 = $this->url1.$files_days[$if];
            $file2 = $this->url2.$files_days[$if];

            $xmldata = '';
            if (!$xmldata = $this->component->loadUrl ($file1, false)) {
                $xmldata = $this->component->loadUrl ($file2, false);
            }

            if (!$xmldata) {
                Yii::app()->essentialData->report(get_class($this).': url '.$file1.' return empty result');
                continue;
            }
            $array = $this->xmlUnserialize($xmldata);
            $array = $array['forecast'];

            $currDate = date('Y-m-d', strtotime(implode('-',array_values($array['f_provider']['forecast_to_date']['@attributes']))));
            $tmp = $array['c'];
            for ($i=0; $i<sizeof($tmp); $i++) {
                $city_id = $tmp[$i]['@attributes']['id'];

                $feedItems[$city_id] = $tmp[$i];
            }

            if (!$feedItems)
                throw new EssentialDataException(Yii::t('essentialdata', get_class($this).': cityId '.$this->cityId.' not found in source', array()), 500);

            foreach ($feedItems as $city_id=>$feedItem) {

                // День
                $c = $this->getCloudiness($feedItem['dw']);
                $p = $this->getPrecipitation($feedItem['dw']);

                $wind_direct = $this->windDirect($feedItem['dwd']);

                $cityItemDay = array(
                    'temperature' => (string) round($feedItem['td']),
                    'relwet' => isset($feedItem['hum_d'])?$feedItem['hum_d']:0,
                    'pressure' => $feedItem['pd'],
                    'wind' => $feedItem['dws'],
                    'cloudiness' => $c,
                    'precipitation' => $p,
                    'windDirection' => $wind_direct,
                );

                // Ночь
                $c = $this->getCloudiness($feedItem['nw']);
                $p = $this->getPrecipitation($feedItem['nw']);

                $wind_direct = $this->windDirect($feedItem['nwd']);

                $cityItemNight = array(
                    'temperature' => (string) round($feedItem['tn']),
                    'relwet' => $feedItem['hum_n'],
                    'pressure' => $feedItem['pn'],
                    'wind' => $feedItem['nws'],
                    'cloudiness' => $c,
                    'precipitation' => $p,
                    'windDirection' => $wind_direct,
                );
                if ($this->cityId == $city_id) {
                    $result[$currDate]['day'] = $cityItemDay;
                    $result[$currDate]['night'] = $cityItemNight;
                    $result[$currDate]['name'] = $feedItem['t'];
                } else
                    $result[$currDate]['other'][$city_id] = array('day' => $cityItemDay, 'night' => $cityItemNight, 'name'=>$feedItem['t']);
            }
        }


        /**
         *
         * Тут берется погод полная на 4 дня текущих
         *
         */
        $files_3 = array(
            $this->prefix.'/0day_d_forecast.xml',
            $this->prefix.'/1day_d_forecast.xml',
            $this->prefix.'/2day_d_forecast.xml',
            $this->prefix.'/3day_d_forecast.xml',
        );

        for ($if=0; $if<sizeof($files_3); $if++) {
            $file1 = $this->url1.$files_3[$if];
            $file2 = $this->url2.$files_3[$if];

            $xmldata = '';
            if (!$xmldata = $this->component->loadUrl ($file1, false)) {
                $xmldata = $this->component->loadUrl ($file2, false);
            }

            if (!$xmldata) {
                Yii::app()->essentialData->report(get_class($this).': url '.$file1.' return empty result');
                continue;
            }
            $array = $this->xmlUnserialize($xmldata);
            $array = $array['forecast'];


            $currDate = date('Y-m-d', strtotime(implode('-',array_values($array['f_provider']['forecast_to_date']['@attributes']))));


            $feedItems = false;
            $tmp = $array['c'];
            for ($i=0; $i<sizeof($tmp); $i++) {
                $feedItem = false;

                $city_id = $tmp[$i]['@attributes']['id'];
                $feedItem = $tmp[$i];

                $new = array();
                $tmp_ft = $feedItem['ft'];
                for ($i2=0; $i2<sizeof($tmp_ft); $i2++) {
                    $t = $tmp_ft[$i2]['@attributes']['t'];
                    $new[$t]= $tmp_ft[$i2];
                }

                $feedItem['ft'] = $new;
                $feedItems[$city_id]=$feedItem;
            }

            if (!$feedItems)
                throw new EssentialDataException(Yii::t('essentialdata', get_class($this).': cityId '.$this->cityId.' not found in source', array()), 500);

            foreach ($feedItems as $city_id=>$feedItem) {
                foreach ($feedItem['ft'] as $t => $itemtime) {
                    $t = $t==24 ? 0 : $t;
                    $dtime = $currDate.' '.((int) $t<10 ? '0'.$t : $t).':00:00';
                    $time = strtotime($dtime);
                    $weekday = date('w',$time)+1;
                    $wind_direct = $this->windDirect($itemtime['wd']);

                    $c = $this->getCloudiness($itemtime['w']);
                    $p = $this->getPrecipitation($itemtime['w']);

                    if (in_array(self::hourToDayPeriod($t),array('morning','day','evening'))) {

                        if(round($itemtime['tf'])>round($itemtime['tt']))
                            $temp = (string) round($itemtime['tf']);
                        else
                            $temp = (string) round($itemtime['tt']);
                    } else
                        if(round($itemtime['tf'])>round($itemtime['tt']))
                            $temp = (string) round($itemtime['tt']);
                        else
                            $temp = (string) round($itemtime['tf']);



                    $cityItem = array(
                        'temperature' => $temp,
                        'relwet' => $itemtime['hum'],
                        'pressure' => $itemtime['p'],
                        'wind' => $itemtime['ws'],
                        'cloudiness' => $c,
                        'precipitation' => $p,
                        'windDirection' => $wind_direct,
                    );
                    if ($this->cityId==$city_id) {
                        $result[$currDate][self::hourToDayPeriod($t)] = $cityItem;
                        $result[$currDate]['name'] = $feedItem['t'];
                    } else {
                        $result[$currDate]['other'][$city_id]['name'] = $feedItem['t'];
                        $result[$currDate]['other'][$city_id][self::hourToDayPeriod($t)] = $cityItem;
                    }
                }
            }
        }

        $this->setData($result);

        if (!sizeof($result))
            Yii::app()->essentialData->report(get_class($this).': data empty');

        return true;
    }

    public static function hourToDayPeriod($hour)
    {
        $hour = (int) $hour;
        if ($hour>0 && $hour<7)
            return 'night';
        if ($hour>=7 && $hour<13)
            return 'morning';
        if ($hour>=13 && $hour<19)
            return 'day';
        if ($hour>=19 || $hour==0)
            return 'evening';
    }

    protected function xmlUnserialize($xml)
    {
        return XML2Array::createArray ($xml);
    }

}
