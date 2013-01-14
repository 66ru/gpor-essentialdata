<?php

require_once dirname(dirname(__FILE__)).'/EssentialDataDriverBase.php';

class EssentialTvDriver extends EssentialDataDriverBase
{
	protected $name = 'stv';
	protected $title = 'Телепрограмма с сайта s-tv.ru';

	/** @var resource Curl instance */
	private $c;

	protected $host = "xmltv.s-tv.ru";
	protected $login = "";  	
	protected $pass = "";
	protected $show = "2"; //Отобразить в формате 1-HTML, 2-XML.
	protected $xmlTV = "8"; //Форматы программ (text, xmltv, xml, xml1,xml2, xls, text2, xmlfull), 1000-Ваш индивидуальный формат (если заказывали).

	/**
	 * @var int Передачи до 4 часов утра принадлежат вчерашнему дню
	 */
	public $dayShift = 14400;

	public $curlTimeout = 5;
	public $tempCookieFile;
	public $userAgent = "Mozilla/4.0 (compatible; MSIE 5.0; Windows 98)";

	function __construct()
	{
		$this->c = curl_init();
		$this->tempCookieFile = tempnam(sys_get_temp_dir(),'');
	}

	function __destruct()
	{
		curl_close($this->c);
	}

	public function run()
	{
		if (!$this->login && !$this->pass)
			throw new EssentialDataException(Yii::t('essentialdata', get_class($this).': login and pass attributes required', array()), 500);

		// Авторизуемся и получаем каналы
		$channelsArr = $this->getChannels();

		$tvMap = array();
		$typesMap = array(
			'1' => 'Фильм',
			'2' => 'Сериал',
			'3' => 'Спорт',
			'4' => 'Новости',
			'5' => 'Детям',
			'10' => 'Досуг',
			'20' => 'Познавательное',
			'1000' => 'Остальное',
		);

		$efirDate = date( "Y-m-d" ); // this value doesn't affect, reduntant?
		$getParams = 'efirdate='.$efirDate.'&login='.$this->login.'&pass='.$this->pass;
		$channels = $this->getTVUrl("http://$this->host/standart/list_channel.php?".$getParams);
		$channelsXml = simplexml_load_string($channels);

		foreach ($channelsXml->File as $channelXml)
		{
			$channelId = (string)$channelXml->ChannelSymbId;
			$channelPrgId = false;

			$tmp = $channelXml->Name;
			if (preg_match('#.*prg=(\d+).*#', $tmp, $matches)) {
				$channelPrgId = (int)$matches[1];
			}

			if (!isset($tvMap[$channelId]))
				$tvMap[$channelId] = array();
			$tvMap[$channelId]['name'] = (string)$channelXml->ChannelName;
			$tvMap[$channelId]['events'] = array();

			// removing userid parameter from url
			$eventsUrl = (string)$channelXml->Name;
			$query = parse_url($eventsUrl, PHP_URL_QUERY);
			$query = html_entity_decode($query);
			$query = explode('&', $query);
			foreach ($query as $id => $param)
			{
				$param = explode('=', $param);
				if ($param[0] == 'userid')
					unset($query[$id]);
			}

			// Добавляем временную зону
			$timeZone = 0;
			if (isset($channelsArr[$channelPrgId]))
				$timeZone = (int)$channelsArr[$channelPrgId]['timeZone'];
			$query[] = 'sh='.(string)$timeZone;

			$query = implode('&',$query);
			$eventsUrl = substr($eventsUrl,0,strpos($eventsUrl,'?')+1) . $query;

			$events = $this->getTVUrl($eventsUrl);
			$eventsXml = simplexml_load_string($events);

			foreach($eventsXml as $eventXml)
			{
				$typesMap[(string)$eventXml->Flag->ID] = (string)$eventXml->Flag->Name;

				// Возрастные ограничения
				$pg = '';
				if (isset($eventXml->Gate->PG->sn))
				{
					$pg = trim((string)$eventXml->Gate->PG->sn);
					if (!empty($pg))
						$pg = ' ('.$pg.')';
				}

				$event['id']			= (string)$eventXml->ID;
				$event['start']			= strtotime((string)$eventXml->Start);
				$event['finish']		= strtotime((string)$eventXml->Finish);
				$event['title']			= (string)$eventXml->Gate->Title . $pg;
				$event['subtitle']		= (string)$eventXml->Gate->SubTitle;
				$event['typeId']		= (string)$eventXml->Flag->ID;
				$event['info']			= (string)$eventXml->Gate->Info;
				$event['country']		= (string)$eventXml->Gate->Country;
				$event['company']		= (string)$eventXml->Gate->Company;
				$event['genre']			= (string)$eventXml->Gate->Genre;
				$event['year']			= (string)$eventXml->Gate->Year;
				$event['images']		= array();
				if (!empty($eventXml->Gallery))
				{
					foreach($eventXml->Gallery->Image as $image)
						$event['images'][] = (string)$image;
				}
				$event['directors'] = array();
				$event['actors'] = array();
				if (!empty($eventXml->Gate->Humans))
				{
					foreach($eventXml->Gate->Humans->Human as $human)
					{
						if ($human->Amplois->ID == 15 || $human->Amplois->ID == 2) // Режиссер
							$event['directors'][] = (string)$human->Name;
						elseif ($human->Amplois->ID == 1) // Актер
							$event['actors'][] = (string)$human->Name;
					}
				}

				foreach($event as $id => $entry)
				{
					if (empty($entry))
						unset($event[$id]);
				}

				$eventDay = date('Y-m-d', strtotime((string)$eventXml->Start) - $this->dayShift);
				$tvMap[$channelId]['events'][$eventDay][] = $event;
				if (empty($tvMap[$channelId]['logo']))
					$tvMap[$channelId]['logo'] = (string)$eventXml->Channel->Logo;
			}
		}

		$this->setData(array(
			'tvMap' => $tvMap,
			'typesMap' => $typesMap,
		));
		return true;
	}

	function getTVUrl($url)
	{
		$c = $this->c;
		curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_USERAGENT, $this->userAgent);
		curl_setopt($c, CURLOPT_REFERER, "http://$this->host");
		curl_setopt($c, CURLOPT_COOKIEJAR, $this->tempCookieFile);
		curl_setopt($c, CURLOPT_COOKIEFILE, $this->tempCookieFile);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_CONNECTTIMEOUT, $this->curlTimeout);
		return curl_exec($c);
	}

	/**
	 * Авторизуемся и получаемсписок каналов
	 */
	private function getChannels()
	{
		$getParams = 'login='.$this->login.'&pass='.$this->pass.'&show='.$this->show.'&xmltv='.$this->xmlTV;
		$channels = $this->getTVUrl("http://$this->host/xchenel.php?".$getParams);
		$channelsXml = simplexml_load_string($channels);
		if (!$channelsXml)
			return array();

		$arr = array();
		foreach ($channelsXml as $channelEntry)
		{
			$channelNameId = $channelEntry->ChannelID;
			if (preg_match('/\?.*prg=(\d*).*sh=(\d*)/', $channelEntry->Name, $matches))
			{
				$channelId = $matches[1];
				$channelTimeZone = $matches[2];
				$arr[(int)$channelId] = array(
					'prgId' => (int)$channelId,
					'timeZone' => $channelTimeZone,
					'nameId' => (string)$channelNameId,
				);
			}
		}
		return $arr;
	}
}
