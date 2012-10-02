<?php
/**
 * EssentialDataProvider class file.
 *
 * @author Stepanoff Alex <stenlex@gmail.com>
 */

/**
 * @package application.extensions.essentialdata
 */
class EssentialDataProvider extends CApplicationComponent {
	
	/**
	 * @var array essentialdata services and their settings.
	 */
	public $services = array();
	
	/**
	 * Returns declared services settings
	 * @return array services settings.
	 */
	public function getServices() {
		$services = array();
		foreach ($this->services as $service => $options) {
			$class = $this->getServiceClass($service);
			$services[$service] = (object) array(
				'id' => $class->getServiceName(),
				'title' => $class->getServiceTitle(),
			);
		}
		return $services;
	}
	
	/**
	 * Returns the settings of the service.
	 * @param string $service the service name.
	 * @return array the service settings.
	 */
	protected function getService($service) {
		$service = strtolower($service);
		$services = $this->getServices();
		if (!isset($services[$service]))
			throw new CHttpException(404, 'Страница не найдена');
		return $services[$service];
	}
	
	/**
	 * Returns the service class.
	 * @param string $service the service name.
	 * @return IEssentialDataService the identity class.
	 */
	public function getServiceClass($service) {
		$service = strtolower($service);
		if (!isset($this->services[$service]))
			throw new CHttpException(404, 'Страница не найдена');
		$service = $this->services[$service];
		
		$class = $service['class'];
		$point = strrpos($class, '.');
		// if it is yii path alias
		if ($point > 0) {
			Yii::import($class);
			$class = substr($class, $point + 1);
		}
		unset($service['class']);
		$serviceClass = new $class();
		$serviceClass->init($this, $service);
		return $serviceClass;
	}
	
	public function runService($service)
	{
		$serviceClass = $this->getServiceClass($service);
		$serviceClass->run();
	}
	
	public function initService($service)
	{
		$serviceClass = $this->getServiceClass($service);
		$period = $serviceClass->getServicePeriod();
		if (!$period)
			return false;

		list($minute, $hour, $day, $month, $dayOfWeek) = preg_split('/\s+/', $period) + array('*','*','*','*','*');

		$run = $this->parseTimeArgument($minute, date('i'));
		$run = $run && $this->parseTimeArgument($hour, date('G'));
		$run = $run && $this->parseTimeArgument($day, date('j'));
		$run = $run && $this->parseTimeArgument($month, date('n'));
		$run = $run && $this->parseTimeArgument($dayOfWeek, date('N'));

		if ( $run )
		{
        	return $run;
        }
			
		return false;
	}
	
	public function report ($message)
	{
		$data = array (
			'html' => $message,
			'text' => '',
			'subject' => Yii::app()->name . ': report',
		);
		
		return MailHelper::sendMailToAdmin($data);
		
	}
	
	
    /**
     * Проверка, что наступило время из текстового поля периода запуска :-)
     * Функция рекурсивна!
     * Допустимый формат строки: 1 или 2-5 или 6,7 или 8,9-12
     *
     * @param string $string Строка для сравнения
     * @param mixed $compare Значение, с которым сравниваем
     * @return boolean Подходит или нет
     */
    public function parseTimeArgument($string, $compare)
    {
        if ( $string === '*' )
        {
            return true;
        }

        if ( strpos($string, ',') )
        {
            $string = explode(',', $string);
            foreach ( $string as $element )
            {
                if ( $this->parseTimeArgument($element, $compare) )
                {
                    return true;
                }
            }
            return false;
        }
        else
        {
            if ( strpos($string, '-') )
            {
                list($min, $max) = explode('-', $string);
                return ($compare >= $min) && ($compare <= $max);
            }
            elseif ( substr($string, 0, 1) == '/' )
            {
                return !($compare % substr($string, 1));
            }
            else
            {
                return $compare == $string;
            }
        }
    }

}

/**
 * The EssentialDataException exception class.
 * 
 * @author Stepanoff Alex <stenlex@gmail.com>
 * @package application.extensions.essentialdata
 * @version 1.0
 */
class EssentialDataException extends CException {}