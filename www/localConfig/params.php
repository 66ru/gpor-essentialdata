<?php
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
return array(
	'title' => 'Авторизатор',
	'siteName' => 'authorizator.gpor.ru',
	'miscSuffix' => 'authorizator',
	'appName' => 'authorizator.gpor.ru',

	'yiiDebug' => true, // YII debug
	'commentNeedApproval' => true,

    'sessionName' => 'gpor-auth',
	'cookieDomain' => '.authorizator.localhost',
	'domain' => 'authorizator.localhost',
	'interfaceResourcesUrl' => 'http://authorizator.gpor.ru/static', // Путь до ресурсов (css, js, картинки) интерфейса
	'commonDomainResources' => 'authorizator.gpor.ru',
	'apiUrl' => 'http://api.authorizator.gpor.ru/',
	'liteUrl' => '/lite/', // ссылка на lite скрипты

	/* email */
	'adminEmail' => 'gpor-dev-errors@googlegroups.com', // this is used in error pages and in rss (webMaster)
	'senderEmail' => 'gpor-dev-errors@googlegroups.com',

	'vkontakteApiId' => '', // Vkontakte.ru api id

	/* Database */
	'dbHost' => 'localhost',
	'dbName' => 'authorizator',
	'dbUser' => 'root',
	'dbPass' => '',

//	'googleClosureCompiler' => 'java -jar /usr/var/spool/web/new.66.ru/www/protected/compiler.jar --compilation_level SIMPLE_OPTIMIZATIONS', // Path to Google Closure Compiler and default flags
//	'yuiCompressor' => 'java -jar /usr/var/spool/web/new.66.ru/www/protected/yuicompressor-2.4.2.jar', // Path to YUI Compressor
//	'yuiCompressorFlags' => '--type css --charset utf-8 -v', // YUI Compressor default flags
	'phpPath' => '~/bin/php', // Path to php

	'staticDir' => '/usr/var/spool/web/t.66.ru/new66', // Path to static dir
    'originalBasePath' => '', // Полный путь до папки где лежат оригинальные изображения(не нужно создавать ее руками!)

);
?>