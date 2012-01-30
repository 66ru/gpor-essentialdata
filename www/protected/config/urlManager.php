<?php 
return array(
	'urlFormat'=>'path',
	'showScriptName'=>false,
	'rules'=>array(
		'/<service:([a-zA-Z0-9_]+)>' => 'site/service',
		'/<service:([a-zA-Z0-9_]+)>/<driver:([a-zA-Z0-9_]+)>' => 'site/feed',
	),
);
?>