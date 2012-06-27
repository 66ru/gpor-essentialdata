<?php

require_once dirname(dirname(__FILE__)).'/EssentialDataServiceBase.php';

class EssentialTvService extends EssentialDataServiceBase {
	
	protected $name = 'tv';
	protected $title = 'Телепрограмма';

	public function checkDriverData ($data)
	{
		return true;
	}
	
}