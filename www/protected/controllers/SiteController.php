<?php

class SiteController extends Controller
{
	public $layout='column1';

	public function actionService()
	{
		$service = isset($_GET['service']) ? $_GET['service'] : false;
		
		if (!$service)
			throw new CHttpException(404, 'Страница не найдена');
			
		$serviceClass = Yii::app()->essentialData->getServiceClass($service);
		
		if ($serviceClass)
		{
			$drivers = $serviceClass->getDrivers();
			$res = array('feeds' => array() );
			foreach ($drivers as $k => $driver)
			{
				$res['feeds'][] = array (
					'name' => $k,
					'title' => $driver->title,
					'url' => Yii::app()->params['domain']. '/'. CHtml::normalizeUrl(array('/site/feed/', 'service' => $service, 'driver' => $k)),
				);
			}
			echo CJSON::encode($res);
			Yii::app()->end();
		}
		
		throw new CHttpException(404, 'Страница не найдена');
	}

	public function actionFeed()
	{
		$service = isset($_GET['service']) ? $_GET['service'] : false;
		
		if (!$service)
			throw new CHttpException(404, 'Страница не найдена');
			
		$serviceClass = Yii::app()->essentialData->getServiceClass($service);
		
		if ($serviceClass)
		{
			$driver = isset($_GET['driver']) ? $_GET['driver'] : false;
			if (!$driver)
				throw new CHttpException(404, 'Страница не найдена');
			
			$res = $serviceClass->readDriverData($driver);
			if ($res === null)
				throw new EssentialDataException(Yii::t('essentialdata', 'Undefined$driver name: {driver}', array('{driver}' => $driver)), 500);
			echo CJSON::encode($res);
			Yii::app()->end();
		}
		
		throw new CHttpException(404, 'Страница не найдена');
	}
	
	
	public function actionIndex()
	{
		$services = Yii::app()->essentialData->getServices();
		foreach ($services as $k => $service)
		{
			$res[] = array (
				'name' => $k,
				'title' => $service->title,
				'url' => Yii::app()->params['domain']. '/'. CHtml::normalizeUrl(array('/site/service/', 'service' => $k)),
			);
		}
		echo CJSON::encode($res);
		Yii::app()->end();
	}
	
}