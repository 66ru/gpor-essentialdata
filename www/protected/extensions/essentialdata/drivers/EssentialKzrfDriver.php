<?php

require_once dirname(dirname(__FILE__)).'/EssentialDataDriverBase.php';

class EssentialKzrfDriver extends EssentialDataDriverBase {
	
	protected $name = 'kzrf';
	protected $title = 'Значение ставки рефинансирования Национального банка республики Казахстан';

	private $url = 'http://www.nationalbank.kz/rss/rss_reward_nbk.xml';

	public function run()
	{
		$result = array();
		$this->setData(array());

		$xml = @file_get_contents($this->url);
		if (!empty($xml)) {
			$xdoc = new SimpleXMLElement($xml);
			$i = 0;
			$currentDate = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
			$monthAgo = mktime(0, 0, 0, date("m")-1, date("d"), date("Y"));

			while ($currentDate >=$monthAgo) {
				$description = (string)$xdoc->channel->item[$i]->description;

				$regex = '#Дата\sустановления\sставки.+?(\d{2}\.\d{2}\.\d{4})#';
				if (!preg_match($regex, $description, $matches))
					throw new EssentialDataException(Yii::t('essentialdata', get_class($this) . ": preg_match ($regex) failed on text: $description"), 500);
				$fromDate = strtotime($matches[1]);

				$regex = '#Размер\sставки\s%рефинансирования\(единая\):(\d+(?:\.\d+)?)#';
				if (!preg_match($regex, $description, $matches))
					throw new EssentialDataException(Yii::t('essentialdata', get_class($this) . ": preg_match ($regex) failed on text: $description"), 500);
				$rate = $matches[1];

				while ($currentDate >= $fromDate && $currentDate >=$monthAgo) {
					$printDate = date('Y-m-d', $currentDate);
					$result[$printDate] = $rate;
					$currentDate-= 60*60*24;
				}
				$i++; // взять следующий элемент ставки из rss
			}
		} else {
			throw new EssentialDataException(Yii::t('essentialdata', get_class($this) . ': can`t get '.$this->url), 500);
		}

		$this->setData($result);
		return true;
	}
	
}