<?php
/**
 * EssentialHmnDriver class file.
 *
 * @author Stepanoff Alex <stenlex@gmail.com>
 */

require_once dirname(dirname(__FILE__)).'/EssentialDataDriverBase.php';

class EssentialHmnDriver extends EssentialDataDriverBase {
	
	protected $name = 'weather';
	protected $title = 'Прогноз погоды';

	protected $url1 = '';
	protected $url2 = '';
	protected $prefix = false;
	protected $cityId = false;

	public function run() {
		if ($this->prefix === false || !$this->cityId)
			throw new EssentialDataException(Yii::t('essentialdata', get_class($this).': cityId and prefix attributes required', array()), 500);

		if (!$this->url1 && !$this->url2)
			throw new EssentialDataException(Yii::t('essentialdata', get_class($this).': url1 and/or url2 attributes required', array()), 500);

		$feedItems = false;
		$result = array();
		
		$files_days = array(
			$this->prefix.'/0day_forecast.xml',
			$this->prefix.'/1day_forecast.xml',
			$this->prefix.'/2day_forecast.xml',
			$this->prefix.'/3day_forecast.xml',
			$this->prefix.'/4day_forecast.xml',
			$this->prefix.'/5day_forecast.xml',
			$this->prefix.'/6day_forecast.xml',
			$this->prefix.'/7day_forecast.xml',
			$this->prefix.'/8day_forecast.xml',
			$this->prefix.'/9day_forecast.xml',
			$this->prefix.'/10day_forecast.xml',
		);

		for ($if=0; $if<sizeof($files_days); $if++)
		{
			$file1 = $this->url1.$files_days[$if];
			$file2 = $this->url2.$files_days[$if];

			$xmldata = '';
			if (!$xmldata = $this->component->loadUrl ($file1, false))
			{
				$xmldata = $this->component->loadUrl ($file2, false);
			}

			if (!$xmldata)
			{
				Yii::app()->essentialData->report(get_class($this).': url '.$file1.' return empty result');
				continue;
			}
			$array = $this->xmlUnserialize($xmldata);
			$array = $array['forecast'];
			
			$currDate = date('Y-m-d', strtotime(implode('-',array_values($array['f_provider']['forecast_to_date']['@attributes']))));
			$tmp = $array['c'];
			for ($i=0; $i<sizeof($tmp); $i++)
			{
				$city_id = $tmp[$i]['@attributes']['id'];

				$feedItems[$city_id] = $tmp[$i];
			}

			if (!$feedItems)
				throw new EssentialDataException(Yii::t('essentialdata', get_class($this).': cityId '.$this->cityId.' not found in source', array()), 500);
			
			foreach($feedItems as $city_id=>$feedItem)
			{

				// День
				$c = $this->getCloudiness($feedItem['dw']);
				$p = $this->getPrecipitation($feedItem['dw']);

				$wind_direct = $this->windDirect($feedItem['dwd']);

				$cityItemDay = array(
					'temperature' => (string)round($feedItem['td']),
					'relwet' => isset($feedItem['hum_d'])?$feedItem['hum_d']:0,
					'pressure' => $feedItem['pd'],
					'wind' => $feedItem['dws'],
					'cloudiness' => $c,
					'precipitation' => $p,
					'windDirection' => $wind_direct,
				);

				// Ночь
				$c = $this->getCloudiness($feedItem['nw']);
				$p = $this->getPrecipitation($feedItem['nw']);

				$wind_direct = $this->windDirect($feedItem['nwd']);

				$cityItemNight = array(
					'temperature' => (string)round($feedItem['tn']),
					'relwet' => $feedItem['hum_n'],
					'pressure' => $feedItem['pn'],
					'wind' => $feedItem['nws'],
					'cloudiness' => $c,
                    'precipitation' => $p,
                    'windDirection' => $wind_direct,
				);
				if($this->cityId == $city_id)
                {
					$result[$currDate]['day'] = $cityItemDay;
                    $result[$currDate]['night'] = $cityItemNight;
                    $result[$currDate]['name'] = $feedItem['t'];
                }
				else
					$result[$currDate]['other'][$city_id] = array('day' => $cityItemDay, 'night' => $cityItemNight, 'name'=>$feedItem['t']);
			}
		}


		/**
		 * 
		 * Тут берется погод полная на 4 дня текущих
		 * 
		 */
		$files_3 = array(
			$this->prefix.'/0day_d_forecast.xml',
			$this->prefix.'/1day_d_forecast.xml',
			$this->prefix.'/2day_d_forecast.xml',
			$this->prefix.'/3day_d_forecast.xml',
		);

		for ($if=0; $if<sizeof($files_3); $if++)
		{
			$file1 = $this->url1.$files_3[$if];
			$file2 = $this->url2.$files_3[$if];

			$xmldata = '';
			if (!$xmldata = $this->component->loadUrl ($file1, false))
			{
				$xmldata = $this->component->loadUrl ($file2, false);
			}

			if (!$xmldata)
			{
				Yii::app()->essentialData->report(get_class($this).': url '.$file1.' return empty result');
				continue;
			}
			$array = $this->xmlUnserialize($xmldata);
			$array = $array['forecast'];


			$currDate = date('Y-m-d', strtotime(implode('-',array_values($array['f_provider']['forecast_to_date']['@attributes']))));


			$feedItems = false;
			$tmp = $array['c'];
			for ($i=0; $i<sizeof($tmp); $i++)
			{
				$feedItem = false;

				$city_id = $tmp[$i]['@attributes']['id'];
				$feedItem = $tmp[$i];

				$new = array();
				$tmp_ft = $feedItem['ft'];
				for ($i2=0; $i2<sizeof($tmp_ft); $i2++)
				{
					$t = $tmp_ft[$i2]['@attributes']['t'];
					$new[$t]= $tmp_ft[$i2];
				}

				$feedItem['ft'] = $new;
				$feedItems[$city_id]=$feedItem;
			}

			if (!$feedItems)
				throw new EssentialDataException(Yii::t('essentialdata', get_class($this).': cityId '.$this->cityId.' not found in source', array()), 500);

			foreach($feedItems as $city_id=>$feedItem)
			{
				foreach($feedItem['ft'] as $t => $itemtime)
				{
					$t = $t==24 ? 0 : $t;
					$dtime = $currDate.' '.((int)$t<10 ? '0'.$t : $t).':00:00';
					$time = strtotime($dtime);
					$weekday = date('w',$time)+1;
					$wind_direct = $this->windDirect($itemtime['wd']);

					$c = $this->getCloudiness($itemtime['w']);
					$p = $this->getPrecipitation($itemtime['w']);

                    if(in_array(self::hourToDayPeriod($t),array('morning','day','evening'))) {

                        if(round($itemtime['tf'])>round($itemtime['tt']))
                            $temp = (string)round($itemtime['tf']);
                        else
                            $temp = (string)round($itemtime['tt']);
                    }
                    else
                        if(round($itemtime['tf'])>round($itemtime['tt']))
                            $temp = (string)round($itemtime['tt']);
                        else
                            $temp = (string)round($itemtime['tf']);



					$cityItem = array(
						'temperature' => $temp,
						'relwet' => $itemtime['hum'],
						'pressure' => $itemtime['p'],
						'wind' => $itemtime['ws'],
						'cloudiness' => $c,
						'precipitation' => $p,
						'windDirection' => $wind_direct,
					);
					if($this->cityId==$city_id)
					{
						$result[$currDate][self::hourToDayPeriod($t)] = $cityItem;
						$result[$currDate]['name'] = $feedItem['t'];
					}
					else
					{
						$result[$currDate]['other'][$city_id]['name'] = $feedItem['t'];
						$result[$currDate]['other'][$city_id][self::hourToDayPeriod($t)] = $cityItem;
					}
				}
			}
		}

		$this->setData($result);

		if (!sizeof($result))
			Yii::app()->essentialData->report(get_class($this).': data empty');
		
		return true;
	}
	
	public static function hourToDayPeriod ($hour)
	{
		$hour = (int)$hour;
		if ($hour>0 && $hour<7)
			return 'night';
		if ($hour>=7 && $hour<13)
			return 'morning';
		if ($hour>=13 && $hour<19)
			return 'day';
		if ($hour>=19 || $hour==0)
			return 'evening';
	}
	
	protected function xmlUnserialize ($xml)
	{
		return XML2Array::createArray ($xml);
	}

	protected function getCloudiness ($c_from_xml)
	{
		/* 
		 * -------------- 66 ---------------
		 * 0 - ясно
		 * 1 - переменная облачность
		 * 2 - облачно
		 * 3 - пасмурно
		 * 4 - дождь
		 * 5 - ливень
		 * 6 - снег
		 * 7 - град
		 * 8 - гроза
		 * 9 - вечером ясно
		 * 10 - вечером переменная облачность
		 * 11 - вечером облачно	
		 *
		 * -------------- HMN ---------------
		    [1] => дождь
		    [2] => снег
		    [3] => дождь, возможен град
		    [4] => осадки
		    [5] => облачно
		    [6] => переменная облачность
		    [7] => ясно
		    [8] => дождь, гроза
		    [9] => переменная облачность, дождь
		    [10] => переменная облачность, небольшой дождь
		    [11] => облачно, небольшой дождь
		    [12] => переменная облачность, небольшой снег
		    [13] => облачно, небольшой снег
		    [14] => переменная облачность, небольшие осадки
		    [15] => облачно, небольшие осадки
		    [16] => облачно, без существенных осадков
		    [17] => метель
		 
		*/
		
		if ($c_from_xml == 1) return 3;
		elseif ($c_from_xml == 2) return 3;
		elseif ($c_from_xml == 3) return 3;
		elseif ($c_from_xml == 4) return 1;
		elseif ($c_from_xml == 5) return 2;
		elseif ($c_from_xml == 6) return 1;
		elseif ($c_from_xml == 7) return 0;
		elseif ($c_from_xml == 8) return 3;
		elseif ($c_from_xml == 9) return 1;
		elseif ($c_from_xml == 10) return 1;
		elseif ($c_from_xml == 11) return 2;
		elseif ($c_from_xml == 12) return 1;
		elseif ($c_from_xml == 13) return 2;
		elseif ($c_from_xml == 14) return 1;
		elseif ($c_from_xml == 15) return 2;
		elseif ($c_from_xml == 16) return 2;
		elseif ($c_from_xml == 17) return 1;
		else return 0;
	}
	
	protected function getPrecipitation ($c_from_xml)
	{
		/* 
		 * -------------- 66 ---------------
		 * 4 - дождь
		 * 5 - ливень
		 * 6 - снег
		 * 7 - град
		 * 8 - гроза
		 *
		 * -------------- HMN ---------------
		    [1] => дождь
		    [2] => снег
		    [3] => дождь, возможен град
		    [4] => осадки
		    [8] => дождь, гроза
		    [9] => переменная облачность, дождь
		    [10] => переменная облачность, небольшой дождь
		    [11] => облачно, небольшой дождь
		    [12] => переменная облачность, небольшой снег
		    [13] => облачно, небольшой снег
		    [14] => переменная облачность, небольшие осадки
		    [15] => облачно, небольшие осадки
		    [17] => метель
		 
		*/
			
		if ($c_from_xml == 1) return 4;
		elseif ($c_from_xml == 2) return 6;
		elseif ($c_from_xml == 3) return 4;
		elseif ($c_from_xml == 4) return 4;
		elseif ($c_from_xml == 8) return 8;
		elseif ($c_from_xml == 9) return 4;
		elseif ($c_from_xml == 10) return 4;
		elseif ($c_from_xml == 11) return 4;
		elseif ($c_from_xml == 12) return 6;
		elseif ($c_from_xml == 13) return 6;
		elseif ($c_from_xml == 14) return 4;
		elseif ($c_from_xml == 15) return 4;
		elseif ($c_from_xml == 17) return 6;
		else return 0;
	}
	
	protected function windDirect ($degres)
	{
		if ($degres==0)
			return '-'; // штиль
		if ($degres==990)
			return '?'; // разного направления
		
		if (($degres>0 AND $degres<=11) OR ($degres>=349 AND $degres<=360))
			return 'n';
		if ($degres>=12 AND $degres<=33)
			return 'n';
		if ($degres>=34 AND $degres<=56)
			return 'ne'; 
		if ($degres>=57 AND $degres<=78)
			return 'ne'; 
		if ($degres>=79 AND $degres<=101)
			return 'e';
		if ($degres>=102 AND $degres<=123)
			return 'e'; 
		if ($degres>=124 AND $degres<=146)
			return 'se';
		if ($degres>=147 AND $degres<=168)
			return 'se';    
		if ($degres>=169 AND $degres<=191)
			return 's'; 
		if ($degres>=192 AND $degres<=214)
			return 's';  
		if ($degres>=215 AND $degres<=236)
			return 'sw';
		if ($degres>=237 AND $degres<=258)
			return 'sw';
		if ($degres>=259 AND $degres<=281)
			return 'w';
		if ($degres>=282 AND $degres<=303)
			return 'w'; 
		if ($degres>=304 AND $degres<=326)
			return 'nw';
		if ($degres>=327 AND $degres<=348)
			return 'nw';
		return '';
	}
}