<?php

require_once dirname(dirname(__FILE__)).'/EssentialDataServiceBase.php';

class EssentialBankCurrencyService extends EssentialDataServiceBase {
    
    protected $name = 'bankcurrency';
    protected $title = 'Курсы валют банков';

    public function checkDriverData($data)
    {
        return true;
    }
    
}