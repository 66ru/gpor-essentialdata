<?php
/**
 * EssentialMosprimeDriver class file.
 *
 * @author Stepanoff Alex <stenlex@gmail.com>
 */

require_once dirname(dirname(__FILE__)).'/EssentialDataDriverBase.php';

class EssentialMosprimeDriver extends EssentialDataDriverBase {
	
	protected $name = '';
	protected $title = '';
	protected $attributes = array();

	public $days = 30;
	public $indicatorTypeId = false;
	public $indexId = false;
	
	protected $url = 'http://www.nva.ru/nva/indicators/archive/by_date/get_xml_py?ind_type=-&trdate=';

	public function run() {	
		if (!$this->indicatorTypeId || !$this->indexId)
			throw new EssentialDataException(Yii::t('essentialdata', get_class($this).': indicatorTypeId and indexId attributes required', array()), 500);

		$result = array();
		
		$oldData = $this->getData();
		
		$max = $this->days;
		for($i=$max; $i>=0; $i--)
		{
			$date = date('Y-m-d', time()-(60*60*24*($i)) );
			if ($i <= 1 || !isset($oldData[$date]))
			{
				$res = $this->getDataOnDate($date);
				if ($res !== false)
					$result[$date] = $res;
			}
			else
			{
				$result[$date] = $oldData[$date];
			}
		}
		$this->setData(array());
		$this->setData($result);
		
		return true;
	}
	
	
	protected function getDataOnDate ($date)
	{
		$result = false;
		$foundType = false;
		$foundIndex = false;
		$data = $this->component->loadXml($this->url.$date);
		
		$key = 'indicators';
		$key2 = 'indicator_type';
		if ($data->$key->$key2)
		{
			foreach ($data->$key->$key2 as $indicator)
			{
				$tmp = array();
				foreach ($indicator->attributes() as $k=>$v)
					$tmp[$k] = (string)$v;
				if ($tmp['id'] == $this->indicatorTypeId)
				{
					$foundType = true;
					if ($indicator->indicator)
					{
						foreach ($indicator->indicator as $index)
						{
							$_tmp = array();
							foreach ($index->attributes() as $k=>$v)
								$_tmp[$k] = (string)$v;
							if ($_tmp['id'] == $this->indexId)
							{
								$values = $index->values;
								$__tmp = array();
								foreach ($values->attributes() as $k=>$v)
									$__tmp[$k] = (string)$v;
								
								$foundIndex = true;
								$result = $__tmp['value'];
							}
						}
					}
				}
			}
			
			if (!$foundType)
			{
				Yii::app()->essentialData->report(get_class($this).' '.$this->name.': indicatorTypeId not found in xml');
			}
			if (!$foundIndex)
			{
				$oldData = $this->getData();
				$time = strtotime($date.' 00:00:00');
				$previosDate = date('Y-m-d', ($time-(60*60*24)));
				if (isset($oldData[$previosDate]))
					$result = $oldData[$previosDate];
				else
				{}
			}
		}
		else
			throw new EssentialDataException(Yii::t('essentialdata', 'EssentialCbrDriver error: data file empty', array()), 500);

		return $result;
		
	}
	
}