<?php
return array(
    'weather' => array (
        'class' => 'EssentialOpenWeatherService',
        'period' => '/5 * * * *',
        'drivers' => array (
            'ekb' => array(
                'class' => 'EssentialOpenWeatherDriver',
                'title' => 'Прогноз погоды в Екатеринбурге на 10 дней',
                'prefix' => '66_ru',
                'cityId' => 28440,
            ),
            'perm' => array(
                'class' => 'EssentialOpenWeatherDriver',
                'title' => 'Прогноз погоды в Перми на 10 дней',
                'prefix' => 'properm_ru',
                'cityId' => 28224,
            ),
            'tambov' => array(
                'class' => 'EssentialOpenWeatherDriver',
                'title' => 'Прогноз погоды в Тамбове на 10 дней',
                'prefix' => 'vtambove_ru',
                'cityId' => 27947,
            ),
            'almaty' => array(
                    'class' => 'EssentialOpenWeatherDriver',
                    'title' => 'Прогноз погоды в Алматы на 10 дней',
                    'prefix' => 'k1_kz',
                    'cityId' => 36870,
            ),
            'omsk' => array(
                    'class' => 'EssentialOpenWeatherDriver',
                    'title' => 'Прогноз погоды в Омске на 10 дней',
                    'prefix' => 'om1_ru',
                    'cityId' => 28698,
            ),
            'gorodche' => array(
                    'class' => 'EssentialOpenWeatherDriver',
                    'title' => 'Прогноз погоды в Череповце на 10 дней',
                    'prefix' => 'gorodche_ru',
                    'cityId' => 27113,
            ),
            'engels' => array(
                    'class' => 'EssentialOpenWeatherDriver',
                    'title' => 'Прогноз погоды в Энгельсе на 10 дней',
                    'prefix' => 'los-engels_ru',
                    'cityId' => 34778,
            )
        ),
    ),
    'weathercurrent' => array (
        'class' => 'EssentialOpenWeatherCurrentService',
        'period' => '/5 * * * *',
        'drivers' => array (
            'ekb' => array(
                'class' => 'EssentialOpenWeatherDriver',
                'prefix' => '66_ru',
                'cityId' => 28440,
            ),
            'perm' => array(
                'class' => 'EssentialOpenWeatherDriver',
                'title' => 'Текущая погода в Перми',
                'prefix' => 'properm_ru',
                'cityId' => 28224,
            ),
            'tambov' => array(
                'class' => 'EssentialOpenWeatherDriver',
                'title' => 'Текущая погода в Тамбове',
                'prefix' => 'vtambove_ru',
                'cityId' => 27947,
            ),
            'almaty' => array(
                'class' => 'EssentialOpenWeatherDriver',
                'title' => 'Текущая погода в Алматы',
                'prefix' => 'k1_kz',
                'cityId' => 36870,
            ),
            'omsk' => array(
                'class' => 'EssentialOpenWeatherDriver',
                'title' => 'Текущая погода в Омске',
                'prefix' => 'om1_ru',
                'cityId' => 28698,
            ),
            'gorodche' => array(
                'class' => 'EssentialOpenWeatherDriver',
                'title' => 'Текущая погода в Череповце',
                'prefix' => 'gorodche_ru',
                'cityId' => 27113,
            ),
            'engels' => array(
                'class' => 'EssentialOpenWeatherDriver',
                'title' => 'Текущая погода в Энгельсе',
                'prefix' => 'los-engels_ru',
                'cityId' => 34778,
            )
        ),
    )
);