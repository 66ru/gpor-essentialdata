<?php 
$cs=Yii::app()->clientScript;
$cs->registerCssFile(Yii::app()->request->baseUrl.'/css/contacts.css');
?>
<h1>Контакты</h1>

<div class="contacts">
<?php 
$i = false;
foreach ($managers as $manager)
{
echo CHtml::openTag('div', array('class'=>'item'.($i?' ch':'') ));

echo CHtml::tag('div', array('class'=>"photo", 'style'=>'background-image:url('.FileUtils::thumb($manager['photo'], 120, 120).')'),'');

echo CHtml::openTag('div', array('class'=>"details") );
echo CHtml::tag('span', array('class'=>"name"), $manager['name'] );
if (!empty($manager['post']))
	echo CHtml::tag('span', array('class'=>"post gray"), $manager['post'] );
if (!empty($manager['email']))
	echo CHtml::tag('span', array('class'=>"mail"), '<a href="mailto:'.$manager['email'].'">'.$manager['email'].'</a>' );
if (!empty($manager['phone']))
	echo CHtml::tag('span', array('class'=>"phone"), 'Телефон: '.$manager['phone'] );
if (!empty($manager['mobile']))
	echo CHtml::tag('span', array('class'=>"phone"), 'Сотовый: '.$manager['mobile'] );
if (!empty($manager['icq']))
	echo CHtml::tag('span', array('class'=>"phone"), $manager['icq'] );
	$i = $i?false:true;
	
echo CHtml::closeTag('div');
echo CHtml::closeTag('div');

echo CHtml::tag('div', array('class'=>"hr clear"), '' );

}
?>
</div>