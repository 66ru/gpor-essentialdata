<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name'=>'ТопРабота',
    'runtimePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'data',
    'language' => 'ru',
    'commandMap' => array(
//        'mailsend'                  => $extDir . DS . 'mailer' . DS . 'MailSendCommand.php',
    ),

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.extensions.*',
		'application.extensions.essentialdata.*',
		'application.extensions.essentialdata.services.*',
		'application.extensions.essentialdata.drivers.*',
    	'application.helpers.*',
		'application.widgets.*',
    ),

	// application components
	'components'=>array(
        'urlManager'=>require(dirname(__FILE__).'/urlManager.php'),
        
        'cache' => array(
			'class' => 'CFileCache',
			'cachePath' => ROOT_PATH. DS . 'protected' . DS . 'runtime' . DS . 'cache',
		),
        
        'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error',
//					'levels'=>'error, warning',
				),
			),
		),
        'errorHandler' => array(
        	'class' => 'application.components.ExtendedErrorHandler'
        ),

		'essentialData' => require(dirname(__FILE__).'/essentialData.php'),

	),
	'params'=>require(dirname(__FILE__).'/params.php'),
	'modules'=>require(dirname(__FILE__).'/modules.php'),
);