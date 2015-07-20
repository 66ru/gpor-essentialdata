<?php
/**
 * EssentialCurrentWeatherService class file.
 *
 * @author Stepanoff Alex <stenlex@gmail.com>
 */

require_once dirname(dirname(__FILE__)).'/EssentialDataServiceBase.php';

class EssentialCurrentWeatherService extends EssentialDataServiceBase
{
    protected $name = 'currentweather';
    protected $title = 'Текущая погода';

    public function checkDriverData($data)
    {
        return true;
    }

    public function readDriverData($driver)
    {
        $data = parent::readDriverData($driver);

        if($data == null)
            return $data;

        if(!$this->checkWeatherCondition($data))
            throw new EssentialDataException(Yii::t('essentialdata', get_class($this).': weather condition "'.$data['condition'].'" not found or incorrect', array()), 500);

        return $data;
    }

    public function checkWeatherCondition($data)
    {
        // todo: надо не падать, а слать сообщение, что найдено новое погодное условие
        return true;
    }
}
