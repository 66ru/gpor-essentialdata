<?php

/**
 * EssentialBankinformDriver class file.
*/
require_once dirname(dirname(__FILE__)).'/EssentialDataDriverBase.php';

class EssentialBankinformDriver extends EssentialDataDriverBase {
    
    protected $name = 'bankinform';
    protected $attributes = array();
    
    protected $url = false;

    public function run() 
    {
        if (!$this->url) {
            throw new EssentialDataException(Yii::t('essentialdata', get_class($this) . ': url attributes required', array()), 500);
        }
        $data = $this->component->loadXml($this->url);
        
        if (!$data || !is_object($data))
            throw new EssentialDataException(Yii::t('essentialdata', get_class($this) . ': result data empty'), 500);

        if ($data->banks)
        {
            $result['data'] = array();
            foreach ($data->banks->bank as $item)
            {
                $result['data'][] = array(
                    'bankName' => (string)$item->name,
                    'buy'=>array('USD'=>str_replace(',', '.', (string)$item->usd->buy), 'EUR'=>str_replace(',', '.', (string)$item->eur->buy)),
                    'sale'=>array('USD'=>strn_replace(',', '.', (string)$item->usd->buy), 'EUR'=>str_replace(',', '.', (string)$item->eur->sale)),
                );
            }  
        }
        else {
            throw new EssentialDataException(Yii::t('essentialdata', 'EssentialBankinformDriver error: data file empty', array()), 500);
        }
        $this->setData($result);
        
        return true;
    }   
}