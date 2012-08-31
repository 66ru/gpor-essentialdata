<?php
/**
 * EssentialCurrentEkburgDriver class file.
 *
 * @author Stepanoff Alex <stenlex@gmail.com>
 */

require_once dirname(dirname(__FILE__)).'/EssentialDataDriverBase.php';

class EssentialCurrentEkburgDriver extends EssentialCurrentHmnDriver {
	
	protected $name = 'weatherCurrentEkb';
	protected $title = 'Текущее значение погоды в Екатеринбурге';

	protected $ekburgUrl = 'http://www.ekburg.ru/.out/weatherSite/weather66.php';
	
	public function run() {
		parent::run();
		$data = $this->getData();
		if (!empty($data))
		{
			$c = $this->component->loadUrl($this->ekburgUrl);
			$ekburgData = CJSON::decode($c);
			if (is_array($ekburgData) && isset($ekburgData['weather']) && isset($ekburgData['weather']['deg']))
			{
				$data['temperature'] = (string)round($ekburgData['weather']['deg']);
				$this->setData($data);
				return true;
			}
			else
			{
				throw new EssentialDataException(Yii::t('essentialdata', get_class($this).' '.$this->name.': data structure wrong', array()), 500);
			}
		}
		else
			Yii::app()->essentialData->report(get_class($this).' '.$this->name.': cityId '.$this->cityId.' not found in data');
		
		return true;
	}
	
}