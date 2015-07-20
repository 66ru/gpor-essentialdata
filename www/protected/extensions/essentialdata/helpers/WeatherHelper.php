<?php

class WeatherHelper
{
    // Статусы погоды
    const WEATHER_STATUS_CODE_CLEAR                 = 0;        // ясно
    const WEATHER_STATUS_CODE_PARTY_CLOUDY          = 1;        // переменная облачность
    const WEATHER_STATUS_CODE_STRONG_CLOUDY         = 2;        // облачно
    const WEATHER_STATUS_CODE_CLOUDY                = 3;        // пасмурно
    const WEATHER_STATUS_CODE_RAIN                  = 4;        // дождь
    const WEATHER_STATUS_CODE_SHOWER_RAIN           = 5;        // ливень
    const WEATHER_STATUS_CODE_SNOW                  = 6;        // снег
    const WEATHER_STATUS_CODE_RAIN_AND_SNOW         = 7;        // снег с дождем
    const WEATHER_STATUS_CODE_THUNDER               = 8;        // гроза
    const WEATHER_STATUS_CODE_HAIL                  = 9;        // град
    const WEATHER_STATUS_CODE_NIGHT_CLEAR           = 10;       // ночь, ясно
    const WEATHER_STATUS_CODE_NIGHT_PARTY_CLOUDY    = 11;       // ночь, переменная облачность
    const WEATHER_STATUS_CODE_NIGHT_STRONG_CLOUDY   = 12;       // ночь, облачно
    const WEATHER_STATUS_CODE_NIGHT_CLOUDY          = 13;       // ночь, пасмурно
    const WEATHER_STATUS_CODE_MIST                  = 20;       // туман

    private static $weatherConditions = array(
        0 => "Ясно",
        1 => "Переменная облачность",
        2 => "Облачно",
        3 => "Пасмурно",
        4 => "Морось",
        5 => "Слабый дождь",
        6 => "Временами дождь",
        7 => "Дождь",
        8 => "Ливень",
        9 => "Гроза",
        10 => "Град",
        11 => "Гроза, град",
        12 => "Дождь со снегом",
        13 => "Ледяной дождь",
        14 => "Слабый снег",
        15 => "Временами снег",
        16 => "Снег",
        17 => "Сильный снег",
        18 => "Пыль в воздухе",
        19 => "Пыль с ветром",
        20 => "Пыльная буря",
        21 => "Туман",
        22 => "Смерчь",
        23 => "Метель",
        24 => "Поземок",

        100 => "",
    );

    public static function getWeatherConditions()
    {
        return self::$weatherConditions;
    }
}
