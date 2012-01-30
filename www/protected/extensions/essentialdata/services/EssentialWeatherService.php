<?php
/**
 * EssentialWeatherService class file.
 *
 * @author Stepanoff Alex <stenlex@gmail.com>
 */

require_once dirname(dirname(__FILE__)).'/EssentialDataServiceBase.php';

class EssentialWeatherService extends EssentialDataServiceBase {	
	
	protected $name = 'weather';
	protected $title = 'Прогноз погоды';

	public function checkDriverData ($data)
	{
		return true;
	}
	
}