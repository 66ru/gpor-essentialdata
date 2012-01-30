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

	protected $url1 = 'http://news.hmn.ru/news_out/Reclama_66/';
	protected $url2 = 'http://news1.hmn.ru/news_out/Reclama_66/';
	protected $prefix = '66_ru';
	
	public function run() {
		$cities = array('10_days'=>array(), '3_days'=>array(), 'cities'=>array());
		
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
			if (!$xmldata = $this->component->loadUrl ($file1))
			{
				$xmldata = $this->component->loadUrl ($file2);
			}
			
			if (!$xmldata)
			{
				Yii::app()->essentialData->report(get_class($this).': url '.$file1.' return empty result');
				continue;
			}
			$array = $this->xmlUnserialize($xmldata);
			$array = $array['forecast'];
			
			$currDate = date('Y-m-d', strtotime(implode('-',array_values($array['f_provider']['forecast_to_date']['@attributes']))));
			$celements = array();
			$tmp = $array['c'];
			for ($i=0; $i<sizeof($tmp)/2; $i++)
			{
				$city_id = $tmp[$i]['@attributes']['id']; 
				
				$celements[$city_id] = $tmp[$i];
			}		
		
			
			foreach ($celements as $city_id => $itemtime)
			{
				$citiesNames[$city_id] = $itemtime['t']; 
				
				// День
				$t = 12;
				$dtime = $currDate.' '.((int)$t<10 ? '0'.$t : $t).':00:00';
				$time = strtotime($dtime);
				$weekday = date('w',$time)+1;
				
				$c = $this->getCloudiness($itemtime['dw']);
			 	$p = $this->getPrecipitation($itemtime['dw']);		
			 	
			 	$wind_direct = $this->windDirect($itemtime['dwd']);
					
				$cityItemDay = array(
					'date' => date('Y', $time).'-'.date('m', $time).'-'.date('d', $time),
					'hour' => $t,
					'temperature' => $itemtime['td'],
					'relwet' => $itemtime['hum_d'],
					'pressure' => $itemtime['pd'],
					'wind' => $itemtime['dws'],
					'cloudiness' => $c,
					'precipitation' => $p,
					'windDirection' => $wind_direct,
				);
			 	
					
				// Ночь
				$t = 0;
				$dtime = $currDate.' '.((int)$t<10 ? '0'.$t : $t).':00:00';
				$time = strtotime($dtime);
				$weekday = date('w',$time)+1;
				
				$c = $this->getCloudiness($itemtime['nw']);
			 	$p = $this->getPrecipitation($itemtime['nw']);	
			 	
			 	$wind_direct = $this->windDirect($itemtime['nwd']);	

				$cityItemNight = array(
					'date' => date('Y', $time).'-'.date('m', $time).'-'.date('d', $time),
					'hour' => $t,
					'temperature' => $itemtime['tn'],
					'relwet' => $itemtime['hum_n'],
					'pressure' => $itemtime['pn'],
					'wind' => $itemtime['nws'],
					'cloudiness' => $c,
					'precipitation' => $p,
					'windDirection' => $wind_direct,
				);
				$cities['10_days'][$currDate][$city_id] = array('day' => $cityItemDay, 'night' => $cityItemDay);
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
			if (!$xmldata = $this->component->loadUrl ($file1))
			{
				$xmldata = $this->component->loadUrl ($file2);
			}
			
			if (!$xmldata)
			{
				Yii::app()->essentialData->report(get_class($this).': url '.$file1.' return empty result');
				continue;
			}
			$array = $this->xmlUnserialize($xmldata);
			$array = $array['forecast'];
			
			
			$currDate = date('Y-m-d', strtotime(implode('-',array_values($array['f_provider']['forecast_to_date']['@attributes']))));
			
			
			$celements = array();
			$tmp = $array['c'];
			for ($i=0; $i<sizeof($tmp)/2; $i++)
			{
				$city_id = $tmp[$i]['@attributes']['id']; 
				
				$celements[$city_id] = $tmp[$i];
				
				$new = array();
				$tmp_ft = $celements[$city_id]['ft'];		
				for ($i2=0; $i2<sizeof($tmp_ft)/2; $i2++)		
				{
					$t = $tmp_ft[$i2]['@attributes']['t']; 
					$new[$t]= $tmp_ft[$i2];
				}
					
				$celements[$city_id]['ft'] = $new;
			}		
		
			
			foreach ($celements as $city_id => $data)
			{
				$citiesNames[$city_id] = $data['t']; 
				
				foreach($data['ft'] as $t => $itemtime)
				{
					$t = $t==24 ? 0 : $t;
					$dtime = $currDate.' '.((int)$t<10 ? '0'.$t : $t).':00:00';
					$time = strtotime($dtime);
					$weekday = date('w',$time)+1;
					$wind_direct = $this->windDirect($itemtime['wd']);
					
					$c = $this->getCloudiness($itemtime['w']);
				 	$p = $this->getPrecipitation($itemtime['w']);
				 	
					$cityItem = array(
						'date' => date('Y', $time).'-'.date('m', $time).'-'.date('d', $time),
						'hour' => $t,
						'temperature' => $itemtime['tf'],
						'relwet' => $itemtime['hum'],
						'pressure' => $itemtime['p'],
						'wind' => $itemtime['ws'],
						'cloudiness' => $c,
						'precipitation' => $p,
						'windDirection' => $wind_direct,
					);
				 	
					$cities['3_days'][$currDate][$city_id] = array($cityItem);
				}
			}
		}
		
		$cities['cities'] = $citiesNames;
		
		$this->setData($cities);
		
		if (!sizeof($cities))
			Yii::app()->essentialData->report(get_class($this).': data empty');
		
		return true;
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
			return 8;
		if ($degres==990)
			return 9;
		
		if (($degres>0 AND $degres<=11) OR ($degres>=349 AND $degres<=360))
			return 0;
		if ($degres>=12 AND $degres<=33)
			return 0;
		if ($degres>=34 AND $degres<=56)
			return 1; 
		if ($degres>=57 AND $degres<=78)
			return 1; 
		if ($degres>=79 AND $degres<=101)
			return 2;
		if ($degres>=102 AND $degres<=123)
			return 2; 
		if ($degres>=124 AND $degres<=146)
			return 3;
		if ($degres>=147 AND $degres<=168)
			return 3;    
		if ($degres>=169 AND $degres<=191)
			return 4; 
		if ($degres>=192 AND $degres<=214)
			return 4;  
		if ($degres>=215 AND $degres<=236)
			return 5;
		if ($degres>=237 AND $degres<=258)
			return 5;
		if ($degres>=259 AND $degres<=281)
			return 6;
		if ($degres>=282 AND $degres<=303)
			return 6; 
		if ($degres>=304 AND $degres<=326)
			return 7;
		if ($degres>=327 AND $degres<=348)
			return 7;
		return '';
	}
}