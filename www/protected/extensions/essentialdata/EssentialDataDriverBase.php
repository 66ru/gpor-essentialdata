<?php
/**
 * EssentialDataDriverBase class file.
 *
 * @author Stepanoff Alex <stenlex@gmail.com>
 */

require_once 'IEssentialDataDriver.php';

/**
 * EssentialDataServiceBase is a base class for providers. 
 * @package application.extensions.essentialdata
 */
abstract class EssentialDataDriverBase extends CComponent implements IEssentialDataDriver {
	
	/**
	 * @var string the driver name.
	 */
	protected $name;
	
	/**
	 *
	 * @var string the driver title to display in views. 
	 */
	protected $title;
	
	/**
	 * @var array attributes.
	 * @see getAttribute
	 * @see getItem
	 */
	protected $attributes = array();
	
	/**
	 * @var EssentialDataServiceBase the {@link EssentialDataServiceBase} application component.
	 */
	protected $component;
	
	/**
	 * @var array recieved data
	 */
	protected $data;
	
	/**
	 * Initialize the component. 
	 * @param EssentialDataServiceBase $component the component instance.
	 * @param array $options properties initialization.
	 */
	public function init($component, $options = array()) {
		if (isset($component))
			$this->setComponent($component);
	
		foreach ($options as $key => $val)
			$this->$key = $val;
	}
	
	/**
	 * Returns driver name(id).
	 * @return string the driver name(id).
	 */
	public function getDriverName() {
		return $this->name;
	}
	
	/**
	 * Returns driver title.
	 * @return string the driver title.
	 */
	public function getDriverTitle() {
		return $this->title;
	}
	
	/**
	 * Sets {@link EssentialDataServiceBase} application component
	 * @param EssentialDataServiceBase $component the application component.
	 */
	public function setComponent($component) {
		$this->component = $component;
	}
	
	/**
	 * Returns the {@link EssentialDataServiceBase} application component.
	 * @return EAuth the {@link EssentialDataServiceBase} application component.
	 */
	public function getComponent() {
		return $this->component;
	}
	
	/**
	 * Sets recieved data
	 * @param array
	 */
	public function setData($data) {
		$this->data = $data;
	}
	
	/**
	 * Returns recieved data.
	 * @return array
	 */
	public function getData() {
		return $this->data;
	}
	
	/**
	 * recived and obtain feed data.
	 * @return boolean whether data was successfuly created.
	 */
	public function run() {	
		return true;
	}

}