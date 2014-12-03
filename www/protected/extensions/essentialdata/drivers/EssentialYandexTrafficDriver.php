<?php

require_once dirname(dirname(__FILE__)).'/EssentialDataDriverBase.php';

class EssentialYandexTrafficDriver extends EssentialDataDriverBase {
	
	protected $name = 'yandexTraffic';
	protected $title = 'Баллы дорожной ситуации в городе от Яндекса';
	protected $attributes = array();

	protected $url = '';
	protected $cityName = '';

	public function run() {	
		$data = $this->component->loadUrl ($this->url, false);
		$array = false;
		if ($data)
			$array = XML2Array::createArray($data);

		$result = array();

		if ($array && is_array($array)) {
			if (isset($array['info']['traffic']) && isset($array['info']['traffic']['level']) ) {
				$result['cityName'] = $array['info']['region']['title'];

				if ( isset($array['info']['traffic']['level']) ) {
					$result['level'] = $array['info']['traffic']['level'];
				}
				if ( isset($array['info']['traffic']['icon']) ) {
					$result['icon'] = $array['info']['traffic']['icon'];
				}
				if ( isset($array['info']['traffic']['time']) ) {
					$result['actualDate'] =  strtotime( date('Y-m-d') . ' ' . $array['info']['traffic']['time'].':00' );
				}
			}

		}
		$this->setData($result);
		
		return true;
	}

	protected function xmlUnserialize ($xml)
	{
		return XML2Array::createArray ($xml);
	}

}