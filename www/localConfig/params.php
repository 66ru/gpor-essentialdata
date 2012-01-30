<?php
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
return array(
	'title' => 'EssentialData',
	'siteName' => 'essentialdata.gpor.ru',
	'appName' => 'essentialdata.gpor.ru',

	'yiiDebug' => false, // YII debug

	'domain' => 'http://essentialdata.localhost',
	'essentialDataFilePath' => 'c:\\wamp\\www\\gpor-essentialdata\\www\\files', // Путь до файлов с фидами

	/* email */
	'adminEmail' => 'gpor-dev-errors@googlegroups.com', // this is used in error pages and in rss (webMaster)
	'senderEmail' => 'gpor-dev-errors@googlegroups.com',

	'phpPath' => 'c:\\wamp\\bin\\php\\php5.3.0\\php', // Path to php
);
?>