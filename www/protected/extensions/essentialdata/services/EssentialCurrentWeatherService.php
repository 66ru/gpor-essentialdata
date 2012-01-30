<?php
/**
 * EssentialCurrentWeatherService class file.
 *
 * @author Stepanoff Alex <stenlex@gmail.com>
 */

require_once dirname(dirname(__FILE__)).'/EssentialDataServiceBase.php';

class EssentialCurrentWeatherService extends EssentialDataServiceBase {	
	
	protected $name = 'currentweather';
	protected $title = 'Текущая погода';

	public function checkDriverData ($data)
	{
		return true;
	}
	
}