<?php
$res = array(
    'class' => 'EssentialDataProvider',
    'services' => array(
        'rates' => array (
            'class' => 'EssentialRatesService',
            'period' => '/30 * * * *',
            'drivers' => array (
                'default' => array(
                    'class' => 'EssentialCbrDriver',
                ),
                'kzr' => array(
                    'class' => 'EssentialKzrDriver',
                ),
            ),
        ),
		'ratesonline' => array (
			'class' => 'EssentialRatesOnlineService',
        	'period' => '/30 * * * *',
			'drivers' => array (
				'default' => array(
					'class' => 'EssentialRatesOnlineWixiQuotesDriver',
				),
			),
		),
        'bankcurrency' => array(
            'class' => 'EssentialBankCurrencyService',
            'period' => '/5 * * * *',
            'drivers' => array(
                'bankinform' => array(
                    'class' => 'EssentialBankinformDriver',
                    'title' => 'Курсы валют банков с сайта http://bankinform.ru',
                    'url' => 'http://bankinform.ru/services/rates/xml.aspx'
                )
            )
        ),
        'bankindexes' => array (
            'class' => 'EssentialBankIndexesService',
            'period' => '/30 * * * *',
            'drivers' => array (
                'cbref' => array(
                    'class' => 'EssentialCbrefDriver',
                ),
                'kzrf' => array(
                    'class' => 'EssentialKzrfDriver',
                ),
                'mosprime3m' => array(
                    'class' => 'EssentialMosprimeDriver',
                    'title' => 'Значение индекса mosprime3m',
                    'name' => 'mosprime3m',
                    'indicatorTypeId' => 'P',
                    'indexId' => 'MosPrime3M',
                ),
                'mosprime6m' => array(
                    'class' => 'EssentialMosprimeDriver',
                    'title' => 'Значение индекса mosprime6m',
                    'name' => 'mosprime6m',
                    'indicatorTypeId' => 'P',
                    'indexId' => 'MosPrime6M',
                ),
                'libor3m' => array(
                    'class' => 'EssentialLiborDriver',
                    'name' => 'libor3m',
                    'title' => 'Значение индекса libor3m',
                    'url' => 'http://www.bankrate.com/rates/interest-rates/3-month-libor.aspx',
                    'checkName' => '3 Month LIBOR',
                ),
                'libor6m' => array(
                    'class' => 'EssentialLiborDriver',
                    'name' => 'libor6m',
                    'title' => 'Значение индекса libor6m',
                    'url' => 'http://www.bankrate.com/rates/interest-rates/6-month-libor.aspx',
                    'checkName' => '6 Month LIBOR',
                ),
            ),
        ),
        'tv' => array(
            'class' => 'EssentialTvService',
            'period' => '1 * * * *',
            'drivers' => array(
                'ekb' => array(
                    'class' => 'EssentialTvDriver',
                    'login' => 'tv6035',
                    'pass' => 'YdFbBDsbB1',
                    'GMT' => 6,
                ),
                /*
                'almaty' => array(
                    'class' => 'EssentialTvDriver',
                    'login' => 'tv6403',
                    'pass' => 'AZAZympvjt',
                    'GMT' => 6,
                ),
                'omsk' => array(
                    'class' => 'EssentialTvDriver',
                    'login' => 'tv6653',
                    'pass' => 'IKjVewvZvl',
                    'GMT' => 7,
                ),
                'gorodche' => array(
                    'class' => 'EssentialTvDriver',
                    'login' => 'tv6480',
                    'pass' => 'k07pqHh0lJ',
                    'GMT' => 4,
                ),
                */
            )
        ),
        'auto' => array(
            'class' => 'EssentialAutoService',
            'period' => '1 * * * *',
            'drivers' => array(
                '66' => array(
                    'class' => 'EssentialAutoDriver',
                    'url' => $params['autoUrl'],
                ),
            )
        ),
        'traffic' => array(
            'class' => 'EssentialTrafficService',
            'period' => '/5 * * * *',
            'drivers' => array(
                'ekb' => array(
                    'class' => 'EssentialYandexTrafficDriver',
//                          'url' => 'http://api-maps.yandex.ru/services/traffic-info/1.0/?format=json&lang=ru-RU',
                    'url' => 'http://export.yandex.ru/bar/reginfo.xml?region=54',
                    'cityName' => 'Екатеринбург',
                ),
                /*
                'perm' => array(
                    'class' => 'EssentialYandexTrafficDriver',
                    'url' => 'http://api-maps.yandex.ru/services/traffic-info/1.0/?format=json&lang=ru-RU',
                    'cityName' => 'Пермь',
                ),
                'omsk' => array(
                    'class' => 'EssentialYandexTrafficDriver',
                    'url' => 'http://api-maps.yandex.ru/services/traffic-info/1.0/?format=json&lang=ru-RU',
                    'cityName' => 'Омск',
                ),
                'cherepovec' => array(
                    'class' => 'EssentialYandexTrafficDriver',
                    'url' => 'http://api-maps.yandex.ru/services/traffic-info/1.0/?format=json&lang=ru-RU',
                    'cityName' => 'Череповец',
                ),
                */
            )
        ),
    ),
);

// Подключаем погоду с OpenWeatherMap
$res['services'] = array_merge($res['services'], require('weather/essentialDataOpenWeatherMap.php'));

return $res;