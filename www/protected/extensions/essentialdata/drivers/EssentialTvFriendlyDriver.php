<?php

require_once dirname(dirname(__FILE__)) . '/EssentialDataDriverBase.php';

class EssentialTvFriendlyDriver extends EssentialDataDriverBase
{
	protected $name = 'stvfriendly';
	protected $title = 'Телепрограмма с сайта s-tv.ru';

	/** @var resource Curl instance */
	private $c;

	protected $host = "xmltv.s-tv.ru";
	protected $login;
	protected $pass;
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
		$this->tempCookieFile = tempnam(sys_get_temp_dir(), '');
	}

	function __destruct()
	{
		curl_close($this->c);
	}

	public function run()
	{
		if (!$this->login && !$this->pass)
			throw new EssentialDataException(Yii::t('essentialdata', get_class($this).': login and pass attributes required', array()), 500);
		// auth
		$this->getTVUrl("http://$this->host/xchenel.php?login=$this->login&pass=$this->pass&show=$this->show&xmltv=$this->xmlTV");

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
		$prefixMap = array(
			'1' => 'Х/ф',
			'2' => 'Т/с',
		);

		$efirDate = '2012-06-18'; // this value doesn't affect, reduntant?
		$channels = $this->getTVUrl("http://$this->host/standart/list_channel.php?efirdate=$efirDate&login=$this->login&pass=$this->pass");
		$channelsXml = simplexml_load_string($channels);
		foreach ($channelsXml->File as $channelXml) {
			$channelId = (string)$channelXml->ChannelSymbId;
			$tvMap[$channelId]['name'] = (string)$channelXml->ChannelName;
			$tvMap[$channelId]['events'] = array();

			// removing userid parametr from url
			$eventsUrl = (string)$channelXml->Name;
			$query = parse_url($eventsUrl, PHP_URL_QUERY);
			$query = html_entity_decode($query);
			$query = explode('&', $query);
			foreach ($query as $id => $param) {
				$param = explode('=', $param);
				if ($param[0] == 'userid')
					unset($query[$id]);
			}
			$query = implode('&', $query);
			$eventsUrl = substr($eventsUrl, 0, strpos($eventsUrl, '?') + 1) . $query;

			$events = $this->getTVUrl($eventsUrl);
			$eventsXml = simplexml_load_string($events);
			foreach ($eventsXml as $eventXml) {
				$typesMap[(string)$eventXml->Flag->ID] = (string)$eventXml->Flag->Name;
				$shortInfo = array();

				$tmp = array();
				$tmp['directors'] = array();
				$tmp['actors'] = array();
				if ((string)$eventXml->Flag->ID == "1" || (string)$eventXml->Flag->ID == "2") {
					$shortInfo[0] = null;
					$genre = (string)$eventXml->Gate->Genre;
					if (!empty($genre))
						$shortInfo[0] = $genre;

					$country = (string)$eventXml->Gate->Country;
					if (!empty($country))
						$shortInfo[0] .= (empty($shortInfo[0])) ? '' : '. ' . $country;

					$year = (string)$eventXml->Gate->Year;
					if (!empty($year))
						$shortInfo[0] .= (empty($shortInfo[0])) ? '' : ', ' . $year;

					if (!empty($eventXml->Gate->Humans))
						foreach ($eventXml->Gate->Humans->Human as $human) {
							if ($human->Amplois->ID == 15 || $human->Amplois->ID == 2) // Режиссер
								$tmp['directors'][] = (string)$human->Name;
							elseif ($human->Amplois->ID == 1) // Актер
								$tmp['actors'][] = (string)$human->Name;
						}
					if (!empty($tmp['directors'])) {
						$shortInfo[1] = "Реж: ";
						$shortInfo[1] .= join(', ', $tmp['directors']);
					}
					if (!empty($tmp['actors'])) {
						$shortInfo[2] = "В ролях: ";
						$shortInfo[2] .= join(', ', $tmp['actors']);
					}
				}

				$shortInfo = join(".\n", $shortInfo) . ".";

				$event['id'] = (string)$eventXml->ID;
				$event['time'] = strtotime((string)$eventXml->Start);
				$event['title'] = ((isset($prefixMap[(string)$eventXml->Flag->ID])) ? $prefixMap[(string)$eventXml->Flag->ID] . " " : '') . (string)$eventXml->Gate->Title . (empty($eventXml->Gate->SubTitle) ? '' : ". " . (string)$eventXml->Gate->SubTitle);
				$event['typeId'] = (string)$eventXml->Flag->ID;
				$event['info'] = (string)$eventXml->Gate->Info;
				$event['shortInfo'] = $shortInfo;
				$event['images'] = array();
				if (!empty($eventXml->Gallery))
					foreach ($eventXml->Gallery->Image as $image) {
						$event['images'][] = (string)$image;
					}


				foreach ($event as $id => $entry)
					if (empty($entry))
						unset($event[$id]);

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
}
