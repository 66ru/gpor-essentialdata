<?php
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
return array(
	'title' => 'EssentialData',
	'siteName' => 'EssentialData',
	'appName' => 'EssentialData',

	'yiiDebug' => true, // YII debug

	'domain' => '',

	/* email */
	'adminEmail' => '', // this is used in error pages and in rss (webMaster)
	'senderEmail' => '',

	'phpPath' => '', // Path to php

	'essentialDataFilePath' => '', // Путь до файлов с фидами

	'autoUrl' => '', //для сбора марок и моделей авто (http://66.ru/xml_auto.php)

	'hmnUrl' => '', // для провайдера hmn сервиса weather
	'hmnUrl2' => '', // резерв для провайдера hmn сервиса weather

	's-tv.login' => '', // Логин для получения телепрограммы с сайта s-tv.ru
	's-tv.pass' => '', // Пароль для получения телепрограммы с сайта s-tv.ru
);
