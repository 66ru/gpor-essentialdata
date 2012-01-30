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
	
	protected $url = 'http://www.cbr.ru/scripts/_XML_daily.asp';

	public function run() {	
		
		$result = array();
		
		$oldData = $this->getData();
		$this->setData(array());
		
		$max = $this->days;
		for($i=0; $i<$max; $i++)
		{
			$date = date('Y-m-d', time()-(60*60*24*($i-1)) );
			if ($i <= 1 || !isset($oldData[$date]))
			{
				$result[$date] = $this->getDataOnTime (strtotime($date.' 00:00:01'));
			}
			else
			{
				$result[$date] = $oldData[$date];
			}
		}
		$this->setData($result);
		
		return true;
	}
	
	
	protected function getDataOnTime ($date)
	{
		$result = array();
		/*
		$testUrl = 'http://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx';
		$request = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$request .= "<soap12:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soap12=\"http://www.w3.org/2003/05/soap-envelope\">\n";
		$request .= "<soap12:Body>\n";
		$request .= "<GetCursOnDateXML xmlns=\"http://web.cbr.ru/\">\n";
		$request .= "<On_date>".date('r', $date)."</On_date>\n";
		$request .= "</GetCursOnDateXML>\n";
		$request .= "</soap12:Body>\n";
		$request .= "</soap12:Envelope>\n";
		$options = array (
			'data' => $request,
		);
		$length = mb_strlen($options['data']);
		$options['headers'] = array(
				"POST /DailyInfoWebServ/DailyInfo.asmx HTTP/1.1",
				"Host: www.cbr.ru",
				"Content-Type: application/soap+xml; \"charset=utf-8\"\n",
				"Content-Length: ".$length."\n",
//				"SOAPAction: \"http://web.cbr.ru/GetCursOnDateXML\"\n",		
		);
		$data = $this->component->makeRequest($testUrl, $options);
		print_r($data);
		die();
		 */
		$data = $this->component->loadXml($this->url);
		
		$key = 'Valute';
		if ($data->$key)
		{
			foreach ($data->$key as $item)
			{
				$result[] = array(
					'charCode' => (string)$item->CharCode,
					'name' => (string)$item->Name,
					'value' => (string)$item->Value,
					'nominal' => (string)$item->Nominal,
				);
			}
			
		}
		else
			throw new EssentialDataException(Yii::t('essentialdata', 'EssentialCbrDriver error: data file empty', array()), 500);

		return $result;
		
	}
	
}