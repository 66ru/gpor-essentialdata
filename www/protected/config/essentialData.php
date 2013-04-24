<?php 
return array (
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
				'weather' => array (
					'class' => 'EssentialWeatherService',
        			'period' => '/5 * * * *',
					'drivers' => array (
						'ekb' => array(
							'class' => 'EssentialHmnDriver',
							'title' => 'Прогноз погоды в Екатеринбурге на 10 дней',
							'url1' => $params['hmnUrl'],
							'url2' => $params['hmnUrl2'],
							'prefix' => '66_ru',
							'cityId' => 28440,
						),
						'perm' => array(
							'class' => 'EssentialHmnDriver',
							'title' => 'Прогноз погоды в Перми на 10 дней',
							'url1' => $params['hmnUrl'],
							'url2' => $params['hmnUrl2'],
							'prefix' => 'properm_ru',
							'cityId' => 28224,
						),
						'tambov' => array(
							'class' => 'EssentialHmnDriver',
							'title' => 'Прогноз погоды в Тамбове на 10 дней',
							'url1' => $params['hmnUrl'],
							'url2' => $params['hmnUrl2'],
							'prefix' => 'vtambove_ru',
							'cityId' => 27947,
						),
						'almaty' => array(
								'class' => 'EssentialHmnDriver',
								'title' => 'Прогноз погоды в Алматы на 10 дней',
								'url1' => $params['hmnUrl'],
								'url2' => $params['hmnUrl2'],
								'prefix' => 'k1_kz',
								'cityId' => 36870,
						),
						'omsk' => array(
								'class' => 'EssentialHmnDriver',
								'title' => 'Прогноз погоды в Омске на 10 дней',
								'url1' => $params['hmnUrl'],
								'url2' => $params['hmnUrl2'],
								'prefix' => 'om1_ru',
								'cityId' => 28698,
						),
                        'gorodche' => array(
                                'class' => 'EssentialHmnDriver',
                                'title' => 'Прогноз погоды в Череповце на 10 дней',
                                'url1' => $params['hmnUrl'],
                                'url2' => $params['hmnUrl2'],
                                'prefix' => 'gorodche_ru',
                                'cityId' => 27113,
                        ),
                        'engels' => array(
                                'class' => 'EssentialHmnDriver',
                                'title' => 'Прогноз погоды в Энгельсе на 10 дней',
                                'url1' => $params['hmnUrl'],
                                'url2' => $params['hmnUrl2'],
                                'prefix' => 'los-engels_ru',
                                'cityId' => 34778,
                        )
					),
				),
				'weathercurrent' => array (
					'class' => 'EssentialCurrentWeatherService',
        			'period' => '/5 * * * *',
					'drivers' => array (
						'ekb' => array(
							'class' => 'EssentialCurrentEkburgDriver',
							'url1' => $params['hmnUrl'],
							'url2' => $params['hmnUrl2'],
							'prefix' => '66_ru',
							'cityId' => 28440,
						),
						'perm' => array(
							'class' => 'EssentialCurrentHmnDriver',
							'title' => 'Текущая погода в Перми',
							'url1' => $params['hmnUrl'],
							'url2' => $params['hmnUrl2'],
							'prefix' => 'properm_ru',
							'cityId' => 28224,
						),
						'tambov' => array(
							'class' => 'EssentialCurrentHmnDriver',
							'title' => 'Текущая погода в Тамбове',
							'url1' => $params['hmnUrl'],
							'url2' => $params['hmnUrl2'],
							'prefix' => 'vtambove_ru',
							'cityId' => 27947,
						),
						'almaty' => array(
							'class' => 'EssentialCurrentHmnDriver',
							'title' => 'Текущая погода в Алматы',
							'url1' => $params['hmnUrl'],
							'url2' => $params['hmnUrl2'],
							'prefix' => 'k1_kz',
							'cityId' => 36870,
						),
						'omsk' => array(
							'class' => 'EssentialCurrentHmnDriver',
							'title' => 'Текущая погода в Омске',
							'url1' => $params['hmnUrl'],
							'url2' => $params['hmnUrl2'],
							'prefix' => 'om1_ru',
							'cityId' => 28698,
						),
                        'gorodche' => array(
                            'class' => 'EssentialCurrentHmnDriver',
                            'title' => 'Текущая погода в Череповце',
                            'url1' => $params['hmnUrl'],
                            'url2' => $params['hmnUrl2'],
                            'prefix' => 'gorodche_ru',
                            'cityId' => 27113,
                        ),
                        'engels' => array(
                            'class' => 'EssentialCurrentHmnDriver',
                            'title' => 'Текущая погода в Энгельсе',
                            'url1' => $params['hmnUrl'],
                            'url2' => $params['hmnUrl2'],
                            'prefix' => 'los-engels_ru',
                            'cityId' => 34778,
                        )
					),
				),
				'tv' => array(
					'class' => 'EssentialTvService',
					'period' => '1 * * * *',
					'drivers' => array(
                        'gorodche' => array(
                            'class' => 'EssentialTvDriver',
                            'login' => 'tv6480',
                            'pass' => 'k07pqHh0lJ',
                            'GMT' => 4,
                        ),
						'ekb' => array(
							'class' => 'EssentialTvDriver',
							'login' => 'tv6035',
							'pass' => 'YdFbBDsbB1',
                            'GMT' => 6,
						),
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
			),
		);
?>