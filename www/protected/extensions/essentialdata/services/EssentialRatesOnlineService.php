<?php
/**
 * EssentialRatesOnlineService class file.
 *
 * @author Stepanoff Alex <stenlex@gmail.com>
 */

require_once dirname(dirname(__FILE__)).'/EssentialDataServiceBase.php';

class EssentialRatesOnlineService extends EssentialDataServiceBase {	
	
	protected $name = 'ratesonline';
	protected $title = 'Курсы валют в реальном времени';

	protected function fetchAttributes() {
	}
	
	public function checkDriverData ($data)
	{
		return true;
	}
	
}