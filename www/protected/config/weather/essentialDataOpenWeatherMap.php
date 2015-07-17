<?php

class OpenWeatherMapDrivers
{
    public static function getConfig($current=false)
    {
        return array(
            'ekb' => array(
                'class' => 'EssentialOpenWeatherDriver',
                'title' => 'Прогноз погоды в Екатеринбурге на 16 дней',
                'cityId' => 1486209,
                'lon' => 60.612499,
                'lat' => 56.857498,
                'cities' => array(
                    array('cityId'=>520494, 'lon'=>null, 'lat'=>null),      // Нижний Тагил
                    array('cityId'=>1504826, 'lon'=>null, 'lat'=>null)      // Каменск-Уральский
                ),
                'current' => $current
            ),
/*            'perm' => array(
                'class' => 'EssentialOpenWeatherDriver',
                'title' => 'Прогноз погоды в Перми на 16 дней',
                'cityId' => 511196,
                'lon' => 56.285519,
                'lat' => 58.01741,
                'cities' => array(),
                'current' => $current
            ),
            'tambov' => array(
                'class' => 'EssentialOpenWeatherDriver',
                'title' => 'Прогноз погоды в Тамбове на 16 дней',
                'cityId' => 484646,
                'lon' => 41.433891,
                'lat' => 52.73167,
                'cities' => array(),
                'current' => $current
            ),
            'almaty' => array(
                'class' => 'EssentialOpenWeatherDriver',
                'title' => 'Прогноз погоды в Алматы на 16 дней',
                'cityId' => 1526384,
                'lon' => 76.949997,
                'lat' => 43.25,
                'cities' => array(),
                'current' => $current
            ),
            'omsk' => array(
                'class' => 'EssentialOpenWeatherDriver',
                'title' => 'Прогноз погоды в Омске на 16 дней',
                'cityId' => 1496153,
                'lon' => 73.400002,
                'lat' => 55,
                'cities' => array(),
                'current' => $current
            ),
            'gorodche' => array(
                'class' => 'EssentialOpenWeatherDriver',
                'title' => 'Прогноз погоды в Череповце на 16 дней',
                'cityId' => 569223,
                'lon' => 37.900002,
                'lat' => 59.133331,
                'cities' => array(),
                'current' => $current
            ),
            'engels' => array(
                'class' => 'EssentialOpenWeatherDriver',
                'title' => 'Прогноз погоды в Энгельсе на 16 дней',
                'cityId' => 563464,
                'lon' => 46.116669,
                'lat' => 51.5,
                'cities' => array(),
                'current' => $current
            )*/
        );

    }
}

return array(
    'weather' => array (
        'class' => 'EssentialOpenWeatherService',
        'period' => '/5 * * * *',
        'current' => false,
        'drivers' => OpenWeatherMapDrivers::getConfig(),
    ),
    'weathercurrent' => array (
        'class' => 'EssentialOpenWeatherService',
        'period' => '/5 * * * *',
        'current' => true,
        'drivers' => OpenWeatherMapDrivers::getConfig(true),
    )
);