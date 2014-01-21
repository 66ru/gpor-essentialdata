<?php

require_once dirname(dirname(__FILE__)).'/EssentialDataDriverBase.php';

class EssentialYandexTrafficDriver extends EssentialDataDriverBase {
	
	protected $name = 'yandexTraffic';
	protected $title = 'Баллы дорожной ситуации в городе от Яндекса';
	protected $attributes = array();

	protected $url = '';
	protected $cityName = '';

	public function run() {	

		$xmldata = $this->component->loadUrl ($this->url, false);
		$array = $this->xmlUnserialize($xmldata);

		var_dump($array);

		$result = array(
		);

		$this->setData($result);
		
		return true;
	}

	protected function xmlUnserialize ($xml)
	{
		return XML2Array::createArray ($xml);
	}

}