<?php
/**
 * Драйвер для общения с OpenWeatherMap
 * http://openweathermap.org/weather-conditions
 */

require_once dirname(__FILE__).'/../../EssentialDataDriverBase.php';
require_once dirname(__FILE__).'/../../helpers/WeatherHelper.php';


class OpenWeatherMapDriverBase extends EssentialDataDriverBase
{
    protected $apiKey = '4b93289d392b45b30acf4cfa94f5b6e6';
    protected $cityName = '';
    protected $cityId = false;
    protected $lon = false;
    protected $lat = false;
    protected $cities = array();


    /**
     * Получить данные о погоде с сервиса
     */
    protected function getWeatherData($cityId, $lon=null, $lat=null)
    {
        return $this->getWeatherDataInner('forecast', $cityId, $lon=null, $lat=null);
    }

    /**
     * Получить данные о погоде с сервиса
     */
    protected function getWeatherDailyData($cityId, $lon=null, $lat=null)
    {
        return $this->getWeatherDataInner('forecast/daily', $cityId, $lon=null, $lat=null);
    }

    /**
     * Получить данные о текущей погоде с сервиса
     */
    protected function getCurrentWeatherData($cityId, $lon=null, $lat=null)
    {
        return $this->getWeatherDataInner('weather', $cityId, $lon=null, $lat=null);
    }

    /**
     * Получить данные о погоде с сервиса
     */
    private function getWeatherDataInner($uri, $cityId, $lon=null, $lat=null)
    {
        $queryStr = ($cityId)
            ? 'id='.$cityId
            : 'lon='.$lon.'&lat='.$lat;
        $uri = 'http://api.openweathermap.org/data/2.5/'.$uri.'?'.$queryStr.'&units=metric&APPID='.$this->apiKey;

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
     * Получить текст погодных условий
     */
    protected function condition($weatherData)
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

        $essentialConditions = WeatherHelper::getWeatherConditions();
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
                '13d' => 16, '13n' => 16,   // Снег
                '50d' => 21, '50n' => 21    // Туман
            );
            $iconCode = $weatherData['icon'];
            $c = (isset($arr[$iconCode]))
                ? $arr[$iconCode]
                : 0;
        }
        return $essentialConditions[$c];
    }

    /**
     * Перевод гектопаскалей в мм.рт.ст.
     */
    protected function mmHg($hPa)
    {
        return round($hPa * 0.75006375541921);
    }

    /**
     * Перевод градусов в направление ветра
     */
    protected function windDirection($speed, $deg)
    {
        // Абстрактно считаем, что при маленьком ветре стоит полный штиль
        if ($speed < 0.1)
            return '-';

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
     * Получение типа осадков
     */
    protected function precipitation($weatherData)
    {
        // Сперва берем по коду
        switch (intval($weatherData['id'])) {
            // Град
            case 906:
                return WeatherHelper::WEATHER_STATUS_CODE_HAIL;
            // Снег с дождем
            case 615:
            case 616:
                return WeatherHelper::WEATHER_STATUS_CODE_RAIN_AND_SNOW;
        }

        static $arr = array(
            // Ясно
            '01d' => WeatherHelper::WEATHER_STATUS_CODE_CLEAR,
            '01n' => WeatherHelper::WEATHER_STATUS_CODE_NIGHT_CLEAR,
            // Переменная облачность
            '02d' => WeatherHelper::WEATHER_STATUS_CODE_PARTY_CLOUDY,
            '02n' => WeatherHelper::WEATHER_STATUS_CODE_NIGHT_PARTY_CLOUDY,
            // Облачно
            '03d' => WeatherHelper::WEATHER_STATUS_CODE_STRONG_CLOUDY,
            '03n' => WeatherHelper::WEATHER_STATUS_CODE_NIGHT_STRONG_CLOUDY,
            // Пасмурно
            '04d' => WeatherHelper::WEATHER_STATUS_CODE_CLOUDY,
            '04n' => WeatherHelper::WEATHER_STATUS_CODE_NIGHT_CLOUDY,
            // Ливень
            '09d' => WeatherHelper::WEATHER_STATUS_CODE_SHOWER_RAIN,
            '09n' => WeatherHelper::WEATHER_STATUS_CODE_SHOWER_RAIN,
            // Дождь
            '10d' => WeatherHelper::WEATHER_STATUS_CODE_RAIN,
            '10n' => WeatherHelper::WEATHER_STATUS_CODE_RAIN,
            // Гроза
            '11d' => WeatherHelper::WEATHER_STATUS_CODE_THUNDER,
            '11n' => WeatherHelper::WEATHER_STATUS_CODE_THUNDER,
            // Снег
            '13d' => WeatherHelper::WEATHER_STATUS_CODE_SNOW,
            '13n' => WeatherHelper::WEATHER_STATUS_CODE_SNOW,
            // Туман
            '50d' => WeatherHelper::WEATHER_STATUS_CODE_MIST,
            '50n' => WeatherHelper::WEATHER_STATUS_CODE_MIST
        );
        $iconCode = $weatherData['icon'];
        if (isset($arr[$iconCode]))
            return $arr[$iconCode];

        // В случае, если пришел неизвестный код, возращаем ясную погоду
        return WeatherHelper::WEATHER_STATUS_CODE_CLEAR;
    }


    /**
     * Создает массив детальных данных
     */
    protected function createDetailCityArray($data)
    {
        return array(
            "temperature"       => (isset($data['main']['temp']) ? $data['main']['temp'] : null),
            "humidity"          => $data['main']['humidity'],
            "pressure"          => $this->mmHg($data['main']['pressure']),
            "cloudiness"        => $data['clouds']['all'],
            "precipitationIcon" => $this->precipitation($data['weather'][0]),
            "precipitationText" => $this->condition($data['weather'][0]),
            "windSpeed"         => $data['wind']['speed'],
            "windDirection"     => $this->windDirection($data['wind']['speed'], $data['wind']['deg'])
        );
    }


    /**
     * Создает массив кратких данных
     */
    protected function createDailyCityArray($data)
    {
        return array(
            "morning"           => $data['temp']['morn'],
            "day"               => $data['temp']['day'],
            "evening"           => $data['temp']['eve'],
            "night"             => $data['temp']['night'],
            "humidity"          => $data['humidity'],
            "pressure"          => $this->mmHg($data['pressure']),
            "cloudiness"        => $data['clouds'],
            "precipitationIcon" => $this->precipitation($data['weather'][0]),
            "precipitationText" => $this->condition($data['weather'][0]),
            "windSpeed"         => $data['speed'],
            "windDirection"     => $this->windDirection($data['speed'], $data['deg'])
        );
    }
}