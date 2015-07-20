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
        0 => "Ясно",
        1 => "Переменная облачность",
        2 => "Облачно",
        3 => "Пасмурно",
        4 => "Морось",
        5 => "Слабый дождь",
        6 => "Временами дождь",
        7 => "Дождь",
        8 => "Ливень",
        9 => "Гроза",
        10 => "Град",
        11 => "Гроза, град",
        12 => "Дождь со снегом",
        13 => "Ледяной дождь",
        14 => "Слабый снег",
        15 => "Временами снег",
        16 => "Снег",
        17 => "Сильный снег",
        18 => "Пыль в воздухе",
        19 => "Пыль с ветром",
        20 => "Пыльная буря",
        21 => "Туман",
        22 => "Смерчь",
        23 => "Метель",
        24 => "Поземок",

        100 => "",
    );

    public static function getWeatherConditions()
    {
        return self::$weatherConditions;
    }
}
