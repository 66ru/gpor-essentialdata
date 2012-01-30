<?php
/**
 * IEssentialDataService interface file.
 *
 * @author Stepanoff Alex <stenlex@gmail.com>
 */

/**
 * @package application.extensions.essentialdata
 */
interface IEssentialDataService {
	
	/**
	 * Initizlize the component.
	 * @param EssentialDataProvider $component the component instance.
	 * @param array $options properties initialization.
	 */
	public function init($component, $options = array());
	
	/**
	 * Returns period.
	 */
	public function getServicePeriod();

	
	/**
	 * Returns service name(id).
	 */
	public function getServiceName();
	
	/**
	 * Returns service title.
	 */
	public function getServiceTitle();
	
	/**
	 * Sets {@link EssentialDataProvider} application component
	 * @param EssentialDataProvider $component the application component.
	 */
	public function setComponent($component);
	
	/**
	 * Returns the {@link EssentialDataProvider} application component.
	 */
	public function getComponent();
	
	/**
	 * Returns declared drivers settings
	 * @return array drivers settings.
	 */
	public function getDrivers();
	
	/**
	 * Returns the driver class.
	 * @param string $driver the driver name.
	 * @return IEssentialDataDriver the identity class.
	 */
	public function getDriverClass($driver);
	
	/**
	 * driver data validation
	 * @param array $data data recived from driver
	 * @return boolean whether validation successfuly passed.
	 */
	public function checkDriverData ($data);
	
	/**
	 * driver data validation
	 * @param string $driver driver name
	 * @return file path where data stored
	 */
	public function getDriverPath ($driver);
	
	/**
	 * save driver data
	 * @param string $path file path where data will be save
	 * @param array $data data recived from driver
	 * @return boolean whether data was successfuly saved.
	 */
	public function saveData ($path, $data);
	
	/**
	 * read driver data from file
	 * @param string $driver driver name
	 * @return array saved driver data
	 */
	public function readDriverData ($driver);

	/**
	 * create feed data.
	 * @return boolean whether data was successfuly created.
	 */
	public function run();
		
	
	/**
	 * Returns the array that contains all available attributes.
	 */
	public function getAttributes();
	
	/**
	 * Returns the attribute value.
	 * @param string $key the attribute name.
	 * @param mixed $default the default value.
	 */
	public function getAttribute($key, $default = null);
	
	/**
	 * Whether the attribute exists.
	 * @param string $key the attribute name.
	 */
	public function hasAttribute($key);
	
	
}