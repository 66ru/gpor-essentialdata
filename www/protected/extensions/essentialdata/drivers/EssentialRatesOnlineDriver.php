<?php
/**
 * EssentialRatesOnlineDriver class file.
 *
 * @author Stepanoff Alex <stenlex@gmail.com>
 */

require_once dirname(dirname(__FILE__)).'/EssentialDataDriverBase.php';

class EssentialRatesOnlineDriver extends EssentialDataDriverBase {
	
	protected $name = 'currencyonline';
	protected $title = 'Курсы валют с сайта http://currencyconverterapi.com';
	protected $attributes = array();

	protected $url = 'http://free.currencyconverterapi.com/api/v3/convert?q=USD_RUB,EUR_RUB&compact=y';

	public function run() {	
		
		$result = array(
			'USD' => array('value' => null),
			'RUB' => array('value' => null),
			'date' => date('Y-m-d H:i:s'),
		);

		$r = @file_get_contents($this->url);
		if ($r) {
			$r = CJSON::decode($r);
			if ($r) {
				if (isset($r['USD_RUB']) && isset($r['USD_RUB']['val']) && $r['USD_RUB']['val'])
					$result['USD']['value'] = $r['USD_RUB']['val'];
				if (isset($r['EUR_RUB']) && isset($r['EUR_RUB']['val']) && $r['EUR_RUB']['val'])
					$result['EUR']['value'] = $r['EUR_RUB']['val'];
			}
		}
		
		$this->setData(array());
		
		
		$this->setData($result);
		
		return true;
	}
	
}