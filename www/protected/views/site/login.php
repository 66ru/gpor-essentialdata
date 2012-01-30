<?php
$this->pageTitle=Yii::app()->name . ' - Войти';
?>
<h1>Вход для компаний</h1>

<div class="siteForm">
<?php echo CHtml::beginForm(); ?>

<?php echo CHtml::errorSummary($model, 'Исправьте ошибки:'); ?>

<?php 
echo CHtml::openTag('div', array('class'=>'row'.($model->getErrors('username')?' error':'') ));
echo CHtml::activeLabelEx($model,'username', array('class'=>'key'));
echo CHtml::openTag('div', array('class'=>'value') );
echo CHtml::activeTextField($model,'username', array('class'=>'normal'));
echo CHtml::closeTag('div');
echo CHtml::closeTag('div');
?>

<?php 
echo CHtml::openTag('div', array('class'=>'row'.($model->getErrors('password')?' error':'') ));
echo CHtml::activeLabelEx($model,'password', array('class'=>'key'));
echo CHtml::openTag('div', array('class'=>'value') );
echo CHtml::activePasswordField($model,'password', array('class'=>'normal'));
echo '<br/>';
echo CHtml::activeCheckBox($model,'rememberMe');
echo CHtml::activeLabelEx($model,'rememberMe', array('class'=>'indent_left'));
echo CHtml::closeTag('div');
echo CHtml::closeTag('div');
?>

<div class="buttons">
	<?php echo CHtml::submitButton('Войти', array('class'=>'submit')); ?>
</div>

<?php echo CHtml::endForm(); ?>
</div><!-- form -->

<?php
$model = new CForm(array(
    'elements'=>array(
        'username'=>array(
            'type'=>'text',
            'maxlength'=>32,
        ),
        'password'=>array(
            'type'=>'password',
            'maxlength'=>32,
        ),
        'rememberMe'=>array(
            'type'=>'checkbox',
        )
    ),

    'buttons'=>array(
        'login'=>array(
            'type'=>'submit',
            'label'=>'Login',
        ),
    ),
), $model);
?>