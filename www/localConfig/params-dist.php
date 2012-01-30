<?php
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
return array(
	'title' => 'EssentialData',
	'siteName' => 'essentialdata.gpor.ru',
	'appName' => 'essentialdata.gpor.ru',

	'yiiDebug' => true, // YII debug

	'domain' => '',
	'interfaceResourcesUrl' => '/static', // Путь до ресурсов (css, js, картинки) интерфейса

	/* email */
	'adminEmail' => 'gpor-dev-errors@googlegroups.com', // this is used in error pages and in rss (webMaster)
	'senderEmail' => 'gpor-dev-errors@googlegroups.com',

	'phpPath' => '~/bin/php', // Path to php
);
?>