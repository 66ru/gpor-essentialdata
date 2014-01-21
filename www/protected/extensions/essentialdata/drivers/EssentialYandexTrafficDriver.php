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
		$array = CJSON::decode($data);

		$result = array();

		if ($array && is_array($array)) {
			if (isset($array['GeoObjectCollection']) && isset($array['GeoObjectCollection']['features']) && is_array($array['GeoObjectCollection']['features']) ) {
				foreach ($array['GeoObjectCollection']['features'] as $item) {
					if (isset($item['properties']) && is_array($item['properties']) && isset($item['properties']['name'])) {
						if ($item['properties']['name'] == $this->cityName) {

							$result['cityName'] = $item['properties']['name'];

							if (isset($item['properties']) && isset($item['properties']['JamsMetaData']) && isset($item['properties']['JamsMetaData']['level']) ) {
								$result['level'] = $item['properties']['JamsMetaData']['level'];
							}
							if (isset($item['properties']) && isset($item['properties']['JamsMetaData']) && isset($item['properties']['JamsMetaData']['timestamp']) ) {
								$result['actualDate'] = $item['properties']['JamsMetaData']['timestamp'];
							}
							if (isset($item['properties']) && isset($item['properties']['JamsMetaData']) && isset($item['properties']['JamsMetaData']['icon']) ) {
								$result['icon'] = $item['properties']['JamsMetaData']['icon'];
							}

							break;
						}
					}
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