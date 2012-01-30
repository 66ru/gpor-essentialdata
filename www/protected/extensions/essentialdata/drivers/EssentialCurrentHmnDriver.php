<?php
/**
 * EssentialCurrentHmnDriver class file.
 *
 * @author Stepanoff Alex <stenlex@gmail.com>
 */

require_once dirname(dirname(__FILE__)).'/EssentialDataDriverBase.php';

class EssentialCurrentHmnDriver extends EssentialDataDriverBase {
	
	protected $name = 'weatherCurrent';
	protected $title = 'Текущее значение погоды';

	protected $url1 = 'http://news.hmn.ru/news_out/Reclama_66/';
	protected $url2 = 'http://news1.hmn.ru/news_out/Reclama_66/';
	protected $prefix = '66_ru';

	public function run() {
		$file_act = $this->prefix.'/fact_astro.xml';

		$file1 = $this->url1.$file_act;
		$file2 = $this->url2.$file_act;	

		$xmldata = '';
		if (!$xmldata = $this->component->loadUrl ($file1))
		{
			$xmldata = $this->component->loadUrl ($file2);
		}
			
		if (!$xmldata)
		{
			Yii::app()->essentialData->report(get_class($this).': url '.$file1.' return empty result');
				return false;
		}
		
		$array = $this->xmlUnserialize($xmldata);


		$cities = array('data'=>array(), 'cities'=>array());
		for($i=0; $i<20; $i++)
		{
			if (isset($array['fact_astro']['c'][$i]))
			{
				$weather = $array['fact_astro']['c'][$i];
				$city_id = $array['fact_astro']['c'][$i]['@attributes']['id'];

				$weather['city_id'] = $city_id;
			
				list($ico, $text, $weatherStatus) = $this->codeRepl($weather['yc'],$weather['cb']);

				$cities['data'][$city_id] = array(
					'temperature' => (string)intval($weather['tf']),
					'condition' => $text,
					'ico' => $this->codeToIcon($ico),
				);
				$cities['cities'][$city_id] = $weather['t'];
			}
		}
		
		$this->setData($cities);
		
		if (!sizeof($cities))
			Yii::app()->essentialData->report(get_class($this).': data empty');
		
		return true;
	}
	
	protected function xmlUnserialize ($xml)
	{
		return XML2Array::createArray ($xml);
	}

	protected function codeRepl ($Yc,$Cb)
	{
		$clouds_descr=array(
					"ясно","ясно","малооблачно","небольшая облачность","переменная облачность","переменная облачность",
					"облачно с прояснениями","облачность с просветами","пасмурно","неба не видно","слабая облачность"
				);
		$codes_descr=array(
					"","облачность","облачность","облачность","облачность","мгла",
					"пыль в воздухе","пыль с ветром","пыльные вихри","пыльная буря","дымка",
					"туман","туман","зарница","осадки","осадки",
					"осадки","гроза","шквал","смерчь","морось, снежные зерна",
					"дождь","снег","дождь со снегом","осадки","ливневый дождь",
					"дождь со снегом","град, крупа","туман","гроза","пыльная буря",
					"пыльная буря","пыльная буря","пыльная буря","пыльная буря","пыльная буря",
					"поземок","сильный поземок","метель","сильная метель","облачность",
					"местами туман","туман","сильный туман","туман","сильный туман",
					"туман","сильный туман","туман, изморозь","туман, изморозь","слабая морось",
					"слабая морось","морось","морось","сильная морось","сильная морось",
					"слабая морось","сильная морось","морось с дождем","морось с дождем","слабый дождь",
					"слабый дождь","временами дождь","дождь","сильный дождь","сильный дождь",
					"дождь, гололед","дождь, гололед","дождь со снегом","дождь со снегом","слабый снег",
					"слабый снег","временами снег","снег","сильный снег","сильный снег",
					"ледяные иглы","снежные зерна","отдельные снежинки","ледяной дождь","ливневый дождь",
					"ливневый дождь","ливневый дождь","дождь со снегом","дождь со снегом","слабый снег",
					"сильный снег","крупа","крупа","град","град",
					"дождь, гроза","дождь, гроза","дождь, гроза","дождь, гроза","гроза",
					"гроза, град","сильная гроза","гроза, буря","гроза, град"
				);
		$weatherStatus = 0;
		
		$a = array(0,1,2,3,4,5,6,7,8,10,14,15,16,36,37,40);
	
		if ($Yc=="-" && $Cb=="-")
		{
			$rico = 0;
			$rtext = $clouds_descr[$rico];
		}
		else
		{		
			if ($Yc=="" || $Yc=="-" || in_array($Yc,$a))
			{
				$rico = "10".$Cb;
				$rtext = $clouds_descr[$Cb];
				$weatherStatus = count($codes_descr)+$Cb;
			}
			else
			{
				$m7=	Array(21=>25,22=>74,23=>26,24=>26,82=>81,88=>87,90=>89);
				$br7=	Array(25=>82,26=>67,27=>87);
				$b7=	Array(29=>97,50=>51,52=>53,54=>55,60=>61,62=>63,64=>65,70=>71,72=>73,74=>75,76=>71,78=>71,80=>61,81=>82,83=>66,84=>67,85=>71,86=>75,87=>88,89=>90,91=>97,92=>97,93=>99,94=>99,95=>97,96=>99);
				$b2_7=	Array(76=>70,78=>70);
				 
				$rico=$Yc;
		
				If (($Cb<7) AND (IsSet($m7[$Yc]))) $rico=$m7[$Yc];
				If (($Cb>=7) AND (IsSet($br7[$Yc]))) $rico=$br7[$Yc];
				If (($Cb>7) AND (IsSet($b7[$Yc]))) $rico=$b7[$Yc];
				If (($Cb>2) AND ($Cb<7) AND IsSet($b2_7[$Yc])) $rico=$b2_7[$Yc];		
				
				
				$weatherStatus = $rico;
				$rtext = $codes_descr[$rico];
			}
		}
		return array($rico,$rtext,$weatherStatus);
		
	}

	protected function codeToIcon ($code)
	{
		/*
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
		*/
		
		// HNM Icons -> 66 Icons
		$code_to_icon = array(
			'0'=>0,
			'100' => 0,
			'101' => 0,
			'1010' => 0,
			'102' => 0,
			'103' => 2,
			'104' => 2,
			'105' => 2,
			'106' => 2,
			'107' => 3,
			'108' => 3,
			'109' => 0,
			'11' => 0,
			'12' => 0,
			'13' => 8,
			'17' => 8,
			'18' => 0,
			'19' => 0,
			'20' => 4,
			'21' => 4,
			'22' => 6,
			'23' => 6,
			'24' => 6,
			'25' => 4,
			'26' => 4,
			'27' => 4,
			'28' => 0,
			'29' => 8,
			'30' => 0,
			'31' => 0,
			'32' => 0,
			'33' => 0,
			'34' => 0,
			'35' => 0,
			'36' => 6,
			'37' => 6,
			'38' => 6,
			'39' => 6,
			'41' => 0,
			'42' => 0,
			'43' => 0,
			'44' => 0,
			'45' => 0,
			'46' => 0,
			'47' => 0,
			'48' => 0,
			'49' => 0,
			'50' => 4,
			'51' => 4,
			'52' => 4,
			'53' => 4,
			'54' => 4,
			'55' => 4,
			'56' => 4,
			'57' => 4,
			'58' => 4,
			'59' => 4,
			'6' => 0,
			'60' => 4,
			'61' => 4,
			'62' => 4,
			'63' => 5,
			'64' => 4,
			'65' => 4,
			'66' => 4,
			'67' => 4,
			'68' => 4,
			'69' => 4,
			'7' => 0,
			'70' => 6,
			'71' => 6,
			'72' => 6,
			'73' => 6,
			'74' => 6,
			'75' => 0,
			'76' => 0,
			'77' => 3,
			'78' => 0,
			'79' => 4,
			'8' => 0,
			'80' => 4,
			'81' => 4,
			'82' => 4,
			'83' => 4,
			'84' => 4,
			'85' => 6,
			'86' => 6,
			'87' => 6,
			'88' => 6,
			'89' => 6,
			'9' => 0,
			'90' => 3,
			'91' => 8,
			'92' => 8,
			'93' => 8,
			'94' => 8,
			'95' => 8,
			'96' => 8,
			'97' => 8,
			'98' => 8,
			'99' => 8,
		);
		
		if (isset($code_to_icon[$code]))
			return $code_to_icon[$code];
		else
			throw new EssentialDataException(Yii::t('essentialdata', 'Icon code '.$code.' not found in '.get_class($this), array()), 500);
	}

}