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


    public function run()
    {
    	$time = time();
    	$timeDiff = time() - $time;
    	while ($timeDiff <= 55) {
    		$res = parent::run();
    		sleep(5);
	    	$timeDiff = time() - $time;
    	}
        return $res;
    }	
}