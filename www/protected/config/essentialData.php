<?php 
return array (
			'class' => 'EssentialDataProvider',
			'services' => array(
				'rates' => array (
					'class' => 'EssentialRatesService',
        			'period' => '* * * * *',
					'drivers' => array (
						'default' => array(
							'class' => 'EssentialCbrDriver',
						),
					),
				),
				'bankindexes' => array (
					'class' => 'EssentialBankIndexesService',
        			'period' => '* * * * *',
					'drivers' => array (
//						'cbref' => array(
//							'class' => 'EssentialCbrefDriver',
//						),

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
        			'period' => '* * * * *',
					'drivers' => array (
//						'default' => array(
//							'class' => 'EssentialHmnDriver',
//						),
						'ekb' => array(
							'class' => 'EssentialHmnDriver',
							'url1' => "c:\\wamp\\www\\gpor-essentialdata\\www\\files\\xmls\\",
							'prefix' => '',
						),
					),
				),
				'weathercurrent' => array (
					'class' => 'EssentialCurrentWeatherService',
        			'period' => '* * * * *',
					'drivers' => array (
						'default' => array(
							'class' => 'EssentialCurrentHmnDriver',
							'url1' => "c:\\wamp\\www\\gpor-essentialdata\\www\\files\\xmls\\",
							'prefix' => '',
						),
						'ekb' => array(
							'class' => 'EssentialCurrentEkburgDriver',
							'url1' => "c:\\wamp\\www\\gpor-essentialdata\\www\\files\\xmls\\",
							'prefix' => '',
						),
					),
				),
				
			),
		);
?>