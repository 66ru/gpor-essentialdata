<?php
/**
 * EssentialCbrefDriver class file.
 *
 * @author Stepanoff Alex <stenlex@gmail.com>
 */

require_once dirname(dirname(__FILE__)).'/EssentialDataDriverBase.php';

class EssentialCbrefDriver extends EssentialDataDriverBase {
	
	protected $name = 'cbref';
	protected $title = 'Значение ставки рефинансирования ЦБ РФ';

	protected $days = 30;
	
	public function run()
	{
		$result = array();
		$oldData = $this->getData();
		$this->setData(array());
		
		$max = $this->days;
		for($i=$max; $i>=0; $i--)
		{
			$date = date('Y-m-d', time()-(60*60*24*($i)) );
			if (isset($oldData[$date]))
				$result[$date] = $oldData[$date];
		}
		
		$soapClient = new SoapClient("http://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?op=MainInfoXML&WSDL");
		
		try {
		    $response = $soapClient->MainInfoXML();
		} catch (SoapFault $fault) {
			throw new EssentialDataException(Yii::t('essentialdata', get_class($this) . ': result data wrong'), 500);
		}
		
		if (is_object($response))
			$response = $response->MainInfoXMLResult->any;
		else
			throw new EssentialDataException(Yii::t('essentialdata', get_class($this) . ': result data wrong'), 500);
		
		if ($response && preg_match('/<stavka_ref.*Date="(.*)".*>(.+)<\/stavka_ref>/', $response, $matches) )
		{
		    $stavka_ref = $matches[2];
		    $result[date('Y-m-d')] = $stavka_ref;
		}
		else
		{
			throw new EssentialDataException(Yii::t('essentialdata', get_class($this) . ': result data wrong'), 500);
		}
		$this->setData($result);
		return true;
	}
	
}