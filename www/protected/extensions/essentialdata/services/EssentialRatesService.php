<?php
/**
 * EssentialRatesService class file.
 *
 * @author Stepanoff Alex <stenlex@gmail.com>
 */

require_once dirname(dirname(__FILE__)).'/EssentialDataServiceBase.php';

/**
 * Currency rates provider
 * @package application.extensions.essentialdata.services
 */
class EssentialRatesService extends EssentialDataServiceBase {	
	
	protected $name = 'currency';
	protected $title = 'Курсы валют ЦБ РФ';

	protected function fetchAttributes() {
		/*
		$info = (object) $this->makeSignedRequest('https://graph.facebook.com/me');

		$this->attributes['id'] = $info->id;
		$this->attributes['name'] = $info->name;
		$this->attributes['url'] = $info->link;
		*/
	}
	
	public function checkDriverData ($data)
	{
		return true;
	}
	
}