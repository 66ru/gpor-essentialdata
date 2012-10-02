<?php
/**
 * EssentialDataServiceBase class file.
 *
 * @author Stepanoff Alex <stenlex@gmail.com>
 */

require_once 'IEssentialDataService.php';

/**
 * EssentialDataServiceBase is a base class for providers. 
 * @package application.extensions.essentialdata
 */
abstract class EssentialDataServiceBase extends CComponent implements IEssentialDataService {

	/**
	 * @var string cron period.
	 */
	protected $period;
	
	/**
	 * @var string the service name.
	 */
	protected $name;
	
	/**
	 *
	 * @var string the service title to display in views. 
	 */
	protected $title;
	
	/**
	 *
	 * @var string drivers that recieved and obtain data from eternal sources. 
	 */
	protected $drivers;
	
	/**
	 * @var array attributes.
	 * @see getAttribute
	 * @see getItem
	 */
	protected $attributes = array();
	
	/**
	 * @var EssentialDataProvider the {@link EssentialDataProvider} application component.
	 */
	private $component;
	
	/**
	 * PHP getter magic method.
	 * This method is overridden so that service attributes can be accessed like properties.
	 * @param string $name property name.
	 * @return mixed property value.
	 * @see getAttribute
	 */
	public function __get($name) {
		if ($this->hasAttribute($name))
			return $this->getAttribute($name);
		else
			return parent::__get($name);
	}

	/**
	 * Checks if a attribute value is null.
	 * This method overrides the parent implementation by checking
	 * if the attribute is null or not.
	 * @param string $name the attribute name.
	 * @return boolean whether the attribute value is null.
	 */
	public function __isset($name) {
		if ($this->hasAttribute($name))
			return true;
		else
			return parent::__isset($name);
	}

	/**
	 * Initialize the component. 
	 * @param EssentialDataProvider $component the component instance.
	 * @param array $options properties initialization.
	 */
	public function init($component, $options = array()) {
		if (isset($component))
			$this->setComponent($component);
	
		foreach ($options as $key => $val)
			$this->$key = $val;
	}
	
	/**
	 * Returns period.
	 */
	public function getServicePeriod() {
		return $this->period;
	}
	
	/**
	 * Returns service name(id).
	 * @return string the service name(id).
	 */
	public function getServiceName() {
		return $this->name;
	}
	
	/**
	 * Returns service title.
	 * @return string the service title.
	 */
	public function getServiceTitle() {
		return $this->title;
	}
	
	/**
	 * Sets {@link EssentialDataProvider} application component
	 * @param EssentialDataProvider $component the application component.
	 */
	public function setComponent($component) {
		$this->component = $component;
	}
	
	/**
	 * Returns the {@link EssentialDataProvider} application component.
	 * @return EAuth the {@link EssentialDataProvider} application component.
	 */
	public function getComponent() {
		return $this->component;
	}
	
	/**
	 * Returns declared drivers settings
	 * @return array drivers settings.
	 */
	public function getDrivers() {
		$drivers = array();
		foreach ($this->drivers as $driver => $options) {
				$class = $this->getDriverClass($driver);
				$drivers[$driver] = (object) array(
					'id' => $class->getDriverName(),
					'title' => $class->getDriverTitle(),
				);
		}
		return $drivers;
	}
	
	/**
	 * Returns the driver class.
	 * @param string $driver the driver name.
	 * @return IEssentialDataDriver the identity class.
	 */
	public function getDriverClass($driver) {
		$driver = strtolower($driver);
		if (!isset($this->drivers[$driver]))
			throw new CHttpException(404, 'Страница не найдена');
		$driver = $this->drivers[$driver];
		
		$class = $driver['class'];
		$point = strrpos($class, '.');
		// if it is yii path alias
		if ($point > 0) {
			Yii::import($class);
			$class = substr($class, $point + 1);
		}
		unset($driver['class']);
		$driverClass = new $class();
		$driverClass->init($this, $driver);
		return $driverClass;
	}
	
	/**
	 * driver data validation
	 * @param array $data data recived from driver
	 * @return boolean whether validation successfuly passed.
	 */
	public function checkDriverData ($data)
	{
		return true;
	}
	
	/**
	 * driver data validation
	 * @param string $driver driver name
	 * @return file path where data stored
	 */
	public function getDriverPath ($driver)
	{
		if (!isset($this->drivers[$driver]))
			throw new CHttpException(404, 'Страница не найдена');

		return Yii::app()->params['essentialDataFilePath'] . DS . $this->getServiceName() . DS . $driver . '.json';
	}
	
	/**
	 * save driver data
	 * @param string $path file path where data will be save
	 * @param array $data data recived from driver
	 * @return boolean whether data was successfuly saved.
	 */
	public function saveData ($path, $data)
	{
		$pathinfo = pathinfo($path);
		$tmp = explode(DS, $pathinfo['dirname']);
		$tmpPath = '';
		foreach ($tmp as $part)
		{
			if (empty($part))
			{
				$tmpPath = DS;
				continue;
			}
			$tmpPath .= $part . DS;
			if (!is_dir($tmpPath))
			{
				echo $tmpPath;
				if (!mkdir($tmpPath, 0755))
					throw new EssentialDataException(Yii::t('essentialdata', 'Can\'t create dir {dir}', array('{dir}' => $tmpPath)), 500);
				else
					chmod($tmpPath, 0755);
			}
		}
		
		$tmpFile = $path.'.tmp';
		if(!$handle = fopen($tmpFile, 'w+'))
		{
			throw new EssentialDataException(Yii::t('essentialdata', 'Can\'t create file {file}', array('{file}' => $tmpFile)), 500);
			return false;
		}
		fwrite($handle, CJSON::encode($data));
		fclose($handle);
    	if (file_exists($tmpFile)){
			if (file_exists($path))
				unlink($path);
			copy($tmpFile, $path);
		}
		unlink($tmpFile);
		
		return true;
	}
	
	/**
	 * read driver data from file
	 * @param string $driver driver name
	 * @return array saved driver data
	 */
	public function readDriverData ($driver)
	{
		$fileName = $this->getDriverPath ($driver);
		$result = null;
		if (file_exists($fileName))
		{
			$content = @file_get_contents($fileName);
			if ($content)
			{
				$content = CJSON::decode($content);
				if ($content)
				{
					return $content;
				}
			}
		}
		return $result;
	}
	
	
	/**
	 * create feed data.
	 * @return boolean whether data was successfuly created.
	 */
	public function run() {
		$myPid = getmypid();
		$path = Yii::app()->params['essentialDataFilePath'] . DS . $this->getServiceName();
		$lastLaunchFile = $path . DS . 'lastLaunch.txt';
		$lastLaunchTime = 0;
		if (file_exists($lastLaunchFile))
			$lastLaunchTime = file_get_contents($lastLaunchFile) + 1;

		$lockFile = $path . DS . 'lock.txt';
		if (file_exists($lockFile))
		{
			$pid = file_get_contents($lockFile);
			if (posix_getsid($pid))
			{
				return false;
			}
		}
		$this->saveData($lockFile, $myPid);
		$lastLaunchTime = time();
		
		foreach ($this->drivers as $driver => $options)
		{
			$driverClass = $this->getDriverClass($driver);
			$driverClass->setData($this->readDriverData($driver));
			if ($driverClass->run())
			{
				$data = $driverClass->getData();
				if ($this->checkDriverData($data))
				{
					$path = $this->getDriverPath($driver);
					if ($this->saveData($path, $data))
					{}
					else
						throw new EssentialDataException(Yii::t('essentialdata', 'Can\'t write data file: {path}', array('{path}' => $path)), 500);
				}
				else
					throw new EssentialDataException(Yii::t('essentialdata', 'Driver {driver} data validation failed', array('{driver}' => $driver)), 500);
			}
			else
				throw new EssentialDataException(Yii::t('essentialdata', 'Driver {driver} failed', array('{driver}' => $driver)), 500);
		}
		$this->saveData($lastLaunchFile, $lastLaunchTime);
		unlink($lockFile);
		return true;
	}


	public function loadXml ($url)
	{
		$c = @file_get_contents($url);
		if (!$c)
			throw new EssentialDataException(Yii::t('essentialdata', 'Error reading url: {url}', array('{url}' => $url)), 500);
		$xml = @simplexml_load_string($c);
		if (!$xml)
			throw new EssentialDataException(Yii::t('essentialdata', 'Error parsiong url: {url} data', array('{url}' => $url)), 500);
		return $xml;
	}
	
	public function loadUrl ($url, $dropException = true)
	{
		$c = @file_get_contents($url);
		if (!$c && $dropException)
			throw new EssentialDataException(Yii::t('essentialdata', 'Error reading url: {url}', array('{url}' => $url)), 500);
		return $c;
	}
	
	/**
	 * Makes the curl request to the url.
	 * @param string $url url to request.
	 * @param array $options HTTP request options. Keys: query, data, referer.
	 * @param boolean $parseJson Whether to parse response in json format.
	 * @return string the response.
	 */
	public function makeRequest($url, $options = array(), $parseJson = true) {
		$ch = $this->initRequest($url, $options);
		
		if (isset($options['referer']))
			curl_setopt($ch, CURLOPT_REFERER, $options['referer']);
		
		if (isset($options['query'])) {
			$url_parts = parse_url($url);
			if (isset($url_parts['query'])) {
				$old_query = http_build_query($url_parts['query']);
				$url_parts['query'] = array_merge($url_parts['query'], $options['query']);
				$new_query = http_build_query($url_parts['query']);
				$url = str_replace($old_query, $new_query, $url);
			}
			else {
				$url_parts['query'] = $options['query'];
				$new_query = http_build_query($url_parts['query']);
				$url .= '?'.$new_query;
			}					
		}
		
		if (isset($options['data'])) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $options['data']);
		}
		
		if (isset($options['headers']))
		{
			curl_setopt ($ch, CURLOPT_HTTPHEADER, $options['headers']); 			
		}
				
		curl_setopt($ch, CURLOPT_URL, $url);

		$result = curl_exec($ch);
		$headers = curl_getinfo($ch);

		if (curl_errno($ch) > 0)
			throw new EssentialDataException(curl_error($ch), curl_errno($ch));
		
		if ($headers['http_code'] != 200) {
			Yii::log(
				'Invalid response http code: '.$headers['http_code'].'.'.PHP_EOL.
				'URL: '.$url.PHP_EOL.
				'Options: '.var_export($options, true).PHP_EOL.
				'Result: '.$result,
				CLogger::LEVEL_ERROR, 'application.extensions.essentialdata'
			);
			throw new EssentialDataException('Invalid response http code: '.$headers['http_code'].'.', $headers['http_code']);
		}
		
		curl_close($ch);
				
		if ($parseJson)
			$result = $this->parseJson($result);
		
		return $result;
	}
	
	/**
	 * Initializes a new session and return a cURL handle.
	 * @param string $url url to request.
	 * @param array $options HTTP request options. Keys: query, data, referer.
	 * @param boolean $parseJson Whether to parse response in json format.
	 * @return cURL handle.
	 */
	protected function initRequest($url, $options = array()) {
		$ch = curl_init();		
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // error with open_basedir or safe mode
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		return $ch;
	}
		
	/**
	 * Parse response from {@link makeRequest} in json format and check errors.
	 * @param string $response Json string.
	 * @return object result.
	 */
	protected function parseJson($response) {
		try {
			$result = json_decode($response);
			$error = $this->fetchJsonError($result);
			if (!isset($result)) {
				throw new EssentialDataException('Invalid response format.', 500);
			}
			else if (isset($error)) {
				throw new EssentialDataException($error['message'], $error['code']);
			}
			else
				return $result;
		}
		catch(Exception $e) {
			throw new EssentialDataException($e->getMessage(), $e->getCode());
		}
	}
	
	/**
	 * Returns the error info from json.
	 * @param stdClass $json the json response.
	 * @return array the error array with 2 keys: code and message. Should be null if no errors.
	 */
	protected function fetchJsonError($json) {
		if (isset($json->error)) {
			return array(
				'code' => 500,
				'message' => 'Unknown error occurred.',
			);
		}
		else
			return null;
	}
	
	/**
	 * Fetch attributes array.
	 * @return boolean whether the attributes was successfully fetched.
	 */
	protected function fetchAttributes() {
		return true;
	}
	
	/**
	 * Fetch attributes array.
	 * This function is internally used to handle fetched state.
	 */
	protected function _fetchAttributes() {
		if (!$this->fetched) {
			$this->fetched = true;
			$result = $this->fetchAttributes();
			if (isset($result))
				$this->fetched = $result;
		}
	}
	
	/**
	 * Returns the array that contains all available attributes.
	 * @return array the attributes.
	 */
	public function getAttributes() {
		$this->_fetchAttributes();
		$attributes = array();
		foreach ($this->attributes as $key => $val) {
			$attributes[$key] = $this->getAttribute($key);
		}
		return $attributes;
	}
	
	/**
	 * Returns the attribute value.
	 * @param string $key the attribute name.
	 * @param mixed $default the default value.
	 * @return mixed the attribute value.
	 */
	public function getAttribute($key, $default = null) {
		$this->_fetchAttributes();
		$getter = 'get'.$key;
		if (method_exists($this, $getter))
			return $this->$getter();
		else
			return isset($this->attributes[$key]) ? $this->attributes[$key] : $default;
	}
	
	/**
	 * Whether the attribute exists.
	 * @param string $key the attribute name.
	 * @return boolean true if attribute exists, false otherwise.
	 */
	public function hasAttribute($key) {
		return isset($this->attributes[$key]);
	}

}