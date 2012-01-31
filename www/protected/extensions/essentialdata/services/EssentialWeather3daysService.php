<?php
/**
 * EssentialWeather3daysService class file.
 *
 * @author Stepanoff Alex <stenlex@gmail.com>
 */

require_once dirname(dirname(__FILE__)).'/EssentialDataServiceBase.php';

class EssentialWeather3daysService extends EssentialDataServiceBase {	
	
	protected $name = 'weather3days';
	protected $title = 'Прогноз погоды на сегдня, завтра, послезавтра';

	public function checkDriverData ($data)
	{
		return true;
	}
	
}