<?php
/**
 * EssentialCbrefDriver class file.
 *
 * @author Stepanoff Alex <stenlex@gmail.com>
 */

require_once 'EssentialMosprime3mDriver.php';

class EssentialCbrefDriver extends EssentialMosprime3mDriver {
	
	protected $name = 'cbref';
	protected $title = 'Значение ставки рефинансирования ЦБ РФ';

	public $indexTypeId = 'C';
	public $indexId = 'CBRF';
	
}