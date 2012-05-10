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
					),
				),
			),
		);
?>