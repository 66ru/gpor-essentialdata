<?php
/**
 * EssentialCbrDriver class file.
 *
 * @author Stepanoff Alex <stenlex@gmail.com>
 */

require_once dirname(dirname(__FILE__)).'/EssentialDataDriverBase.php';

class EssentialCbrDriver extends EssentialDataDriverBase {
	
	protected $name = 'cbr';
	protected $title = 'Курсы валют с сайта http://www.cbr.ru';
	protected $attributes = array();

	public $days = 30;
	
	protected $url = 'http://www.cbr.ru/scripts/XML_daily.asp?date_req=';

	public function run() {	
		
		$result = array();
		
		$oldData = $this->getData();
		$this->setData(array());
		
		$max = $this->days;
		for($i=$max; $i>=0; $i--)
		{
			$date = date('Y-m-d', time()-(60*60*24*($i)) );

			$res = $this->getDataOnDate ($date);
				
			if ($res['date'] != $res['xmlDate'])
			{
				$date1 = strtotime($res['date'].' 00:00:00');
				$date2 = strtotime($res['xmlDate'].' 00:00:00');
				if ($date2 < $date1)
				{
					$previosDate = date('Y-m-d', ($date1 - (60*60*24)));
					if (isset($result[$previosDate]))
						unset($result[$previosDate]);
				}
			}
			$result[$date] = $res['data'];
		}
		// курс на завтра
		$date = date('Y-m-d', time()+(60*60*24) );
		$res = $this->getDataOnDate ($date);
		if ($res['date'] == $res['xmlDate'])
			$result[$date] = $res['data'];
		
		$this->setData($result);
		
		return true;
	}
	
	
	protected function getDataOnDate ($date)
	{
		$result = array();
		$tmp = explode('-', $date);
		$data = $this->component->loadXml($this->url.$tmp[2].'/'.$tmp[1].'/'.$tmp[0]);
		
		if (!$data || !is_object($data))
			throw new EssentialDataException(Yii::t('essentialdata', get_class($this) . ': result data empty'), 500);
		
		$tmp = array();
		foreach ($data->attributes() as $k=>$v)
			$tmp[$k] = (string)$v;
		$xmlDate = isset($tmp['Date']) ? $tmp['Date'] : false;
		if (!$xmlDate)
			throw new EssentialDataException(Yii::t('essentialdata', get_class($this) . ': result data wrong'), 500);
		
		$tmp = array();
		if (strstr($xmlDate, '/'))
			$tmp = explode('/', $xmlDate);
		if (strstr($xmlDate, '.'))
			$tmp = explode('.', $xmlDate);
		if (count($tmp) != 3)
			throw new EssentialDataException(Yii::t('essentialdata', get_class($this) . ': result data wrong'), 500);
		
		$result['xmlDate'] = $tmp[2].'-'.$tmp[1].'-'.$tmp[0];
		$result['date'] = $date;
		
		$key = 'Valute';
		if ($data->$key)
		{
			$result['data'] = array();
			foreach ($data->$key as $item)
			{
				$result['data'][] = array(
					'charCode' => (string)$item->CharCode,
					'name' => (string)$item->Name,
					'value' => str_replace(',', '.', (string)$item->Value),
					'nominal' => (string)$item->Nominal,
				);
			}
			
		}
		else
			throw new EssentialDataException(Yii::t('essentialdata', 'EssentialCbrDriver error: data file empty', array()), 500);

		return $result;
		
	}
	
}