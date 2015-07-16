<?php
$params = array();

$localConfigFile 		= dirname(__FILE__).DS.'../../localConfig/params.php';
$localDistConfigFile 	= dirname(__FILE__).DS.'../../localConfig/params-dist.php';

if (!file_exists($localDistConfigFile))
	die('local config-dist doesn`t exists at '.$localDistConfigFile."\n");
$localDistConfig = require($localDistConfigFile);
    

if (!file_exists($localConfigFile))
	die('local config doesn`t exists at '.$localConfigFile."\n");
$localConfig = require($localConfigFile);

$params = array_replace_recursive($localDistConfig, $localConfig);
$emptyKeys = array();
foreach ($params as $k=>$v) {
    if (is_string($v) && empty($v))
        $emptyKeys[] = $k;
}
if (sizeof($emptyKeys)) {
    echo 'Error: params<br>'.implode(',<br>', $emptyKeys).'<br>required';
    die();
}

$mainConfig = array(
    'basePath'=>dirname(__FILE__).DS.'..',
    'runtimePath' => dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'data',
    'name'=>$params['appName'],
    'language' => 'ru',
    'defaultController'=>'site',

    // preloading 'log' component
    'preload'=>array('log'),

    // autoloading model, component and helper classes
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

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params'=>$params,

    // application components
    'components'=>array(
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error, warning, info',
                ),
                array(
                    'class'=>'CWebLogRoute',
                    'enabled' => YII_DEBUG_LOG,
                    'levels'=>'info, error, warning, trace, profile',
                    'showInFireBug' => false,
                ),
                array(
                    'class'=>'CProfileLogRoute',
                    'enabled' => YII_DEBUG_LOG,
                    'showInFireBug' => false,
                    'report' => 'summary',
                ),
            ),
        ),
        'essentialData' => require(dirname(__FILE__).'/essentialData.php'),
        'clientScript'=>array(
            'class'=>'application.components.ExtendedClientScript',
            'combineFiles'=>false,
            'compressCss'=>false,
            'compressJs'=>false,
        ),
        'urlManager'=>require(dirname(__FILE__).'/urlManager.php'),

        'cache' => array(
            'class' => 'CFileCache'
        ),

        'errorHandler' => array(
            'class' => 'application.components.ExtendedErrorHandler',
            'ignoredErrorCodes' => array(404),
        ),
        'localConfig' => array(
            'class' => 'application.components.LocalConfigComponent'
        ),
    ),

    'modules'=>require(dirname(__FILE__).'/modules.php'),

);

return $mainConfig;
