<?php

require_once dirname(dirname(__FILE__)).'/EssentialDataServiceBase.php';

class EssentialTrafficService extends EssentialDataServiceBase {
	
	protected $name = 'traffic';
	protected $title = 'Баллы дорожной ситуации в городе';

	public function checkDriverData ($data)
	{
		return true;
	}
	
}