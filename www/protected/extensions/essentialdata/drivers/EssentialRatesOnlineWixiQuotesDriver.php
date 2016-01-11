<?php
/**
 * EssentialRatesOnlineDriver class file.
 *
 * @author Stepanoff Alex <stenlex@gmail.com>
 */

require_once dirname(dirname(__FILE__)).'/EssentialDataDriverBase.php';

class EssentialRatesOnlineWixiQuotesDriver extends EssentialDataDriverBase {
	
	protected $name = 'currencyonlineWixiQuotes';
	protected $title = 'Курсы валют с сайта wixiquotes.ru';
	protected $attributes = array();

	protected $url = 'http://wixiquotes.ru/upload/newQuotes.txt';

	public function run() {	
		
		$result = array(
			'USD' => array('value' => null),
			'EUR' => array('value' => null),
			'date' => date('Y-m-d H:i:s'),
		);

		$r = @file_get_contents($this->url);
		if ($r) {
			$r = explode("\n", $r);
			foreach ($r as $row) {
				$row = explode(',', $row);
				switch ($row[0]) {
					case 'USDRUR':
						$result['USD']['value'] = $row[2];
						$result['date'] = date('Y-m-d H:i:s', $row[1]);
						break;
					
					case 'EURRUR':
						$result['EUR']['value'] = $row[2];
						$result['date'] = date('Y-m-d H:i:s', $row[1]);
						break;

					default:
						break;
				}
			}
			var_dump($result);
			die();
		}
		
		$this->setData(array());
		
		
		$this->setData($result);
		
		return true;
	}
	
}