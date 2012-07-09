<?php
/**
 * Created by JetBrains PhpStorm.
 * User: astronom
 * Date: 12.03.12
 * Time: 17:23
 * To change this template use File | Settings | File Templates.
 */
class EssentialCurrentWeatherHelper
{
    // NOTE: Статусы не должны повторяться, т.к. в дальнейшем используются в качестве ключей
    // NOTE: Статусы в UTF8
    
    private static $weatherConditions = array(
        0 => "ясно",
        1 => "переменная облачность",
        2 => "облачно",
        3 => "пасмурно",
        4 => "морось",
        5 => "слабый дождь",
        6 => "временами дождь",
        7 => "дождь",
        8 => "ливень",
        9 => "гроза",
        10 => "град",
        11 => "гроза, град",
        12 => "дождь со снегом",
        13 => "ледяной дождь",
        14 => "слабый снег",
        15 => "временами снег",
        16 => "снег",
        17 => "сильный снег",
        18 => "пыль в воздухе",
        19 => "пыль с ветром",
        20 => "пыльная буря",
        21 => "туман",
        22 => "смерчь",
        23 => "метель",
        24 => "поземок",

        100 => "",
    );

    public static function getWeatherConditions()
    {
        return self::$weatherConditions;
    }
}
