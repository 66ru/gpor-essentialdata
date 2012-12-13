<?php

require_once dirname(dirname(__FILE__)).'/EssentialDataServiceBase.php';

class EssentialAutoService extends EssentialDataServiceBase {
	
	protected $name = 'auto';
	protected $title = 'База марок и моделей авто';

	public function checkDriverData ($data)
	{
		return true;
	}
	
}