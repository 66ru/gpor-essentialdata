<?php
/**
 * Essential3daysWeatherDriver class file.
 *
 * @author Stepanoff Alex <stenlex@gmail.com>
 */

require_once dirname(dirname(__FILE__)).'/EssentialDataDriverBase.php';

class Essential3daysWeatherDriver extends EssentialDataDriverBase {
	
	protected $name = 'weather3days';
	protected $title = 'Прогноз погоды на сегодня, завтра и послезавтра';

	protected $serviceCurrent = 'weathercurrent';
	protected $serviceWeather = 'weather';
	protected $driverCurrent = false;
	protected $driverWeather = false;
	protected $cityId = false;

	public function run() {
		$result = array();
		
		if (!$this->cityId || !$this->driverCurrent || !$this->driverWeather )
			throw new EssentialDataException(Yii::t('essentialdata', get_class($this).': cityId, driverCurrent, driverWeather attributes required', array()), 500);
		
		$service = Yii::app()->essentialData->getServiceClass($this->serviceCurrent);
		$currentData = $service->readDriverData ($this->driverCurrent);
		if (!$currentData || !isset($currentData['data']))
			throw new EssentialDataException(Yii::t('essentialdata', get_class($this).': '.$this->serviceCurrent.' '.$this->driverCurrent.' data empty', array()), 500);
		if (!isset($currentData['data'][$this->cityId]))
			throw new EssentialDataException(Yii::t('essentialdata', get_class($this).': '.$this->serviceCurrent.' '.$this->driverCurrent.' city '.$this->cityId.' not found', array()), 500);
		$result['today'] = $currentData['data'][$this->cityId];
			
		$service = Yii::app()->essentialData->getServiceClass($this->serviceWeather);
		$currentData = $service->readDriverData ($this->driverWeather);
		$date1 = date('Y-m-d', (time()+(60*60*24)));
		$date2 = date('Y-m-d', (time()+(60*60*24*2)));
		if (!$currentData || !isset($currentData['data']))
			throw new EssentialDataException(Yii::t('essentialdata', get_class($this).': '.$this->serviceWeather.' '.$this->driverWeather.' data empty', array()), 500);
		if (!isset($currentData['data'][$date1][$this->cityId]))
			throw new EssentialDataException(Yii::t('essentialdata', get_class($this).': '.$this->serviceWeather.' '.$this->driverWeather.' city '.$this->cityId.' not found', array()), 500);
		if (!isset($currentData['data'][$date2][$this->cityId]))
			throw new EssentialDataException(Yii::t('essentialdata', get_class($this).': '.$this->serviceWeather.' '.$this->driverWeather.' city '.$this->cityId.' not found', array()), 500);
		
		$result['tomorrow'] = $currentData['data'][$date1][$this->cityId]['day'];
		$result['aftertomorrow'] = $currentData['data'][$date2][$this->cityId]['day'];
		
		$this->setData($result);
		
		return true;
	}
	
	protected function xmlUnserialize ($xml)
	{
		return XML2Array::createArray ($xml);
	}

}