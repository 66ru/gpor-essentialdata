<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
<?php 
//$cs=Yii::app()->clientScript;
//$cs->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery_lib.js', CClientScript::POS_HEAD);
//$cs->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery_ui.js', CClientScript::POS_HEAD);
//$cs->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.form.js', CClientScript::POS_HEAD);
//$cs->registerScriptFile(Yii::app()->request->baseUrl.'/js/multiselect.js', CClientScript::POS_HEAD);
//$cs->registerScriptFile(Yii::app()->request->baseUrl.'/js/common.js', CClientScript::POS_HEAD);
?>
	<!-- link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery-ui.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/multiselect.css" / -->

	<title><?php echo $this->pageTitle; ?></title>
</head>

<body>
<div id="body_container" align="center">
<table id="body" align="center" cellpadding="0" cellspacing="0" width="1018">
	<tr>
		<td class="body_left" rowspan="2">&nbsp;</td>
		<td class="main_content">
		<div class="main_container">
			<div id="header">
				<h1><a href="/"><?php echo CHtml::encode(Yii::app()->name); ?></a></h1>
			</div>
			<div id="header_hr"></div>
			<div id="content">
				<?php echo $content; ?>
			</div>
		</div>
		</td>
		<td class="right_content" rowspan="2">
		<div class="right_container">
		</div>
		</td>
		<td class="body_right" rowspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td class="main_content align_bottom">
			<div id="footer">
				<div class="footer_hr"></div>
				<div class="footer_links">
				</div>
				<div class="footer_text">
					©<?php echo CHtml::encode(Yii::app()->name); ?>, 2012
				</div>
			</div><!-- footer -->
		</td>
	</tr>
</table>
</div>
</body>
</html>