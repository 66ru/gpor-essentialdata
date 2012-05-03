<?php

require_once dirname(dirname(__FILE__)).'/EssentialDataDriverBase.php';

class EssentialKzrDriver extends EssentialDataDriverBase {
	
	protected $name = 'kzr';
	protected $title = 'Курсы валют с сайта Национального банка республики Казахстан';

	private $url = 'http://www.nationalbank.kz/rss/get_rates.cfm?fdate=';

	public function run()
	{
		$result = array();
		$oldData = $this->getData();
		$this->setData(array());

		$currentDate = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		$monthAgo = mktime(0, 0, 0, date("m")-1, date("d"), date("Y"));
		while ($currentDate >=$monthAgo) {
			$printDate = date('Y-m-d', $currentDate);
			if (!empty($oldData[$printDate]))
				$result[$printDate] = $oldData[$printDate];
			else
				$result[$printDate] = $this->getRatesByDate($currentDate);

			$currentDate-= 60*60*24;
		}

		$this->setData($result);
		return true;
	}

	/**
	 * @param int $date timestamp
	 * @throws EssentialDataException
	 * @return array
	 */
	private function getRatesByDate($date) {
		$result = array();
		$strDate = date('d.m.Y', $date);
		$xml = @file_get_contents($this->url.$strDate);
		if (!empty($xml)) {
			$xdoc = new SimpleXMLElement($xml);
			foreach($xdoc->item as $item) {
				$result[] = array(
					'charCode' => (string)$item->title,
					'name' => (string)$item->fullname,
					'value' => (string)$item->description,
					'nominal' => (string)$item->quant,
				);
			}
		} else {
			throw new EssentialDataException(Yii::t('essentialdata', get_class($this) . ': can`t get '.$this->url), 500);
		}

		return $result;
	}
}