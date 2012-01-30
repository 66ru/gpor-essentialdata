<?php
/**
 * EssentialLiborDriver class file.
 *
 * @author Stepanoff Alex <stenlex@gmail.com>
 */

require_once dirname(dirname(__FILE__)).'/EssentialDataDriverBase.php';

class EssentialLiborDriver extends EssentialDataDriverBase {
	
	protected $name = '';
	protected $title = '';
	protected $attributes = array();

	public $days = 30;
	public $url = false;
	public $checkName = false;
	
	public function run() {	
		if (!$this->url || !$this->checkName)
			throw new EssentialDataException(Yii::t('essentialdata', get_class($this).': url and checkName attributes required', array()), 500);

		$result = array();
		
		$oldData = $this->getData();
		$this->setData(array());
		
		$max = $this->days;
		for($i=0; $i<$max; $i++)
		{
			$date = date('Y-m-d', time()-(60*60*24*$i) );
			if ($i == 0)
			{
				$res = $this->getIndexData();
				if ($res !== false)
					$result[$date] = $res;
			}
			elseif (isset($oldData[$date]))
			{
				$result[$date] = $oldData[$date];
			}
		}
		$this->setData($result);
		
		return true;
	}
	
	
	protected function getIndexData ()
	{
		$result = false;
		$foundIndex = false;
		$html = $this->component->loadUrl($this->url);

		preg_match_all('#<div class="interactivetopaction">Updated\s*(.*?)\s*</div>#s', $html, $dateMatches);
		if (!count($dateMatches) || count($dateMatches[1]) != 1 )
		{
			throw new EssentialDataException(Yii::t('essentialdata', get_class($this).' '.$this->name.': html parsing. required data wrong', array()), 500);
		}
		$tmp = explode('/', $dateMatches[1][0]);
		if (count($tmp) != 3)
		{
			throw new EssentialDataException(Yii::t('essentialdata', get_class($this).' '.$this->name.': html parsing. required data wrong', array()), 500);
		}
		$date = $tmp[2].'-'.$tmp[1].'-'.$tmp[0];
		
		preg_match_all('#<tr bgcolor="\#FFFFFF">\s*(.*?)\s*</tr>#s', $html, $matches);
		if (!count($matches) || count($matches[1]) != 2)
		{
			throw new EssentialDataException(Yii::t('essentialdata', get_class($this).' '.$this->name.': html parsing. required data wrong', array()), 500);
		}
		preg_match_all('#<td[^>]*>\s*(.*?)\s*</td>#s', $matches[1][1], $cols);
		if (!count($cols) || count($cols[1]) != 4 || !strstr($cols[1][0], $this->checkName) )
		{
			throw new EssentialDataException(Yii::t('essentialdata', get_class($this).' '.$this->name.': html parsing. required data wrong', array()), 500);
		}
		
		$result = $cols[1][1];

		return $result;
		
	}
	
}