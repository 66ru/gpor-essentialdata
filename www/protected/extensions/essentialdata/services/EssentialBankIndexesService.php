<?php
/**
 * EssentialBankIndexesService class file.
 *
 * @author Stepanoff Alex <stenlex@gmail.com>
 */

require_once dirname(dirname(__FILE__)).'/EssentialDataServiceBase.php';

class EssentialBankIndexesService extends EssentialDataServiceBase {	
	
	protected $name = 'bankindexes';
	protected $title = 'Значения кредитных индексов';

	public function checkDriverData ($data)
	{
		return true;
	}
	
}