<?php

class OpenWeatherMapDrivers
{
    public static function getConfig($current=false)
    {
        /**
         * NOTE: В конфиге можно указывать или cityId или lon/lat
         *       Приоритетным будет cityId
         */

        $driverName = 'ext.essentialdata.drivers.weather.' . ($current ? 'OpenWeatherMapCurrentDriver' : 'OpenWeatherMapDriver');
        return array(
            'ekb' => array(
                'class'     => $driverName,
                'title'     => 'Прогноз погоды в Екатеринбурге',
                'cityName'  => 'Екатеринбург',
                'cityId'    => 1486209,
                'lon'       => 60.612499,
                'lat'       => 56.857498,
                'cities'    => array(
                    array('cityName'=>'Нижний Тагил', 'cityId'=>520494, 'lon'=>null, 'lat'=>null),
                    array('cityName'=>'Каменск-Уральский', 'cityId'=>1504826, 'lon'=>null, 'lat'=>null)
                )
            ),
            'perm' => array(
                'class'     => $driverName,
                'title'     => 'Прогноз погоды в Перми',
                'cityName'  => 'Пермь',
                'cityId'    => 511196,
                'lon'       => 56.285519,
                'lat'       => 58.01741,
                'cities'    => array()
            ),
            'tambov' => array(
                'class'     => $driverName,
                'title'     => 'Прогноз погоды в Тамбове',
                'cityName'  => 'Тамбов',
                'cityId'    => 484646,
                'lon'       => 41.433891,
                'lat'       => 52.73167,
                'cities'    => array()
            ),
            'almaty' => array(
                'class'     => $driverName,
                'title'     => 'Прогноз погоды в Алматы',
                'cityName'  => 'Алматы',
                'cityId'    => 1526384,
                'lon'       => 76.949997,
                'lat'       => 43.25,
                'cities'    => array()
            ),
            'omsk' => array(
                'class'     => $driverName,
                'title'     => 'Прогноз погоды в Омске',
                'cityName'  => 'Омск',
                'cityId'    => 1496153,
                'lon'       => 73.400002,
                'lat'       => 55,
                'cities'    => array()
            ),
            'gorodche' => array(
                'class'     => $driverName,
                'title'     => 'Прогноз погоды в Череповце',
                'cityName'  => 'Череповец',
                'cityId'    => 569223,
                'lon'       => 37.900002,
                'lat'       => 59.133331,
                'cities'    => array()
            ),
            'engels' => array(
                'class'     => $driverName,
                'title'     => 'Прогноз погоды в Энгельсе',
                'cityName'  => 'Энгельс',
                'cityId'    => 563464,
                'lon'       => 46.116669,
                'lat'       => 51.5,
                'cities'    => array()
            )
        );

    }
}

return array(
    'weather' => array (
        'class' => 'ext.essentialdata.services.weather.OpenWeatherMapService',
        'period' => '/5 * * * *',
        'drivers' => OpenWeatherMapDrivers::getConfig(),
    ),
    'weathercurrent' => array (
        'class' => 'ext.essentialdata.services.weather.OpenWeatherMapCurrentService',
        'period' => '/5 * * * *',
        'drivers' => OpenWeatherMapDrivers::getConfig(true),
    )
);