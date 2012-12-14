<?php

require_once dirname(dirname(__FILE__)).'/EssentialDataDriverBase.php';

class EssentialAutoDriver extends EssentialDataDriverBase {
	
	protected $name = 'auto';
	protected $title = 'База марок и моделей авто';
	protected $attributes = array();

	public $days = 30;
	
	protected $url = '';

	public function run() {	

		$xmldata = $this->component->loadUrl ($this->url, false);
		$array = $this->xmlUnserialize($xmldata);

		$array['channel']['marks'] = $array['channel']['marks']['item'];
		$array['channel']['models'] = $array['channel']['models']['item'];

		foreach($array['channel']['marks'] as &$item)
		{
			$text= $item['text']['@cdata'];
			$item['text'] = $text;
		}

		foreach($array['channel']['models'] as &$item)
		{
			$text= $item['text']['@cdata'];
			$item['text'] = $text;
		}

		$result = array(
			'marks'=>$array['channel']['marks'],
			'models'=>$array['channel']['models'],
		);

		$this->setData($result);
		
		return true;
	}

	protected function xmlUnserialize ($xml)
	{
		return XML2Array::createArray ($xml);
	}

}