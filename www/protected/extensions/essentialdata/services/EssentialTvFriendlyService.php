<?php

require_once dirname(dirname(__FILE__)).'/EssentialDataServiceBase.php';

class EssentialTvFriendlyService extends EssentialDataServiceBase {
	
	protected $name = 'tvfriendly';
	protected $title = 'Телепрограмма';

	public function checkDriverData ($data)
	{
		return true;
	}
	
}