<h1>Восстановление пароля</h1>

<div class="siteForm">

<p>Укажите адрес почты, который вы указывали при регистрации.<br/>
Если вы не помните адреса, <?php echo CHtml::link ('сообщите нам', '/feedback');?> об этом.</p>

<?php echo CHtml::form('', 'post', array('enctype'=>'multipart/form-data') )?>

<?php echo CHtml::errorSummary($form, 'Исправьте ошибки:'); ?>

<?php 
echo CHtml::openTag('div', array('class'=>'row'.($form->getErrors('email')?' error':'') ));
echo CHtml::activeLabelEx($form,'email', array('class'=>'key'));
echo CHtml::openTag('div', array('class'=>'value') );
echo CHtml::activeTextField($form,'email', array('class'=>'normal'));
echo CHtml::closeTag('div');
echo CHtml::closeTag('div');
?>

<?php 
echo CHtml::openTag('div', array('class'=>'row'.($form->getErrors('verifyCaptchaCode')?' error':'') ));
echo CHtml::activeLabelEx($form,'verifyCaptchaCode', array('class'=>'key'));
echo CHtml::openTag('div', array('class'=>'value') );
echo CHtml::activeTextField($form,'verifyCaptchaCode');

echo CHtml::openTag('div', array('style'=>'padding-top:.5em;'), '');
$this->widget('CCaptcha', array('buttonLabel'=>'обновить код на картинке', 'buttonOptions'=>array('style'=>'display:block;')));
echo CHtml::closeTag('div');

echo CHtml::closeTag('div');
echo CHtml::closeTag('div');
?>

<div class="buttons">
<?php echo CHtml::submitButton('Выслать пароль', array('class'=>'submit')); ?>
</div>
<?php echo CHtml::endForm();?>
</div>