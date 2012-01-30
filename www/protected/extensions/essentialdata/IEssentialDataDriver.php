<?php
/**
 * IEssentialDataDriver class file.
 *
 * @author Stepanoff Alex <stenlex@gmail.com>
 */

/**
 * EssentialDataServiceBase is a base class for providers. 
 * @package application.extensions.essentialdata
 */
interface IEssentialDataDriver {
	
	/**
	 * Initialize the component. 
	 * @param EssentialDataServiceBase $component the component instance.
	 * @param array $options properties initialization.
	 */
	public function init($component, $options = array());
	
	/**
	 * Returns driver name(id).
	 * @return string the driver name(id).
	 */
	public function getDriverName();
	
	/**
	 * Returns driver title.
	 * @return string the driver title.
	 */
	public function getDriverTitle();
	
	/**
	 * Sets {@link EssentialDataServiceBase} application component
	 * @param EssentialDataServiceBase $component the application component.
	 */
	public function setComponent($component);
	
	/**
	 * Returns the {@link EssentialDataServiceBase} application component.
	 * @return EAuth the {@link EssentialDataServiceBase} application component.
	 */
	public function getComponent();
	
	/**
	 * recived and obtain feed data.
	 * @return boolean whether data was successfuly created.
	 */
	public function run();

	/**
	 * Sets recieved data
	 * @param array
	 */
	public function setData($data);
	
	/**
	 * Returns recieved data.
	 * @return array
	 */
	public function getData();
	
}