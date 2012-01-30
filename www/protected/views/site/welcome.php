<?php 
$cs=Yii::app()->clientScript;
$cs->registerCssFile(Yii::app()->request->baseUrl.'/css/welcome.css');
?>
<div class="welcomeText">
<p>Проект Авторизатор.ру — cайт авторизации нового поколения.</p>
</div>
<div class="welocmeRight">

<div class="statistic_container">
<table class="statistic">
	<tr>
		<td class="title" colspan="3">Нашим сайтом пользуются:</td>
	</tr>
	<tr>
	<td class="statisticRow first">
		<span class="total red">150000</span>
		<span class="details">пользователей</span>
	</td>
	<td class="statisticRow">
		<span class="total red">25000</span>
		<span class="details">вконтакте</span>
	</td>
	<td class="statisticRow last">
		<span class="total green">13000</span>
		<span class="details">facebook</span>
	</td>
	</tr>
</table>
</div>
</div>
<div class="br"></div>
<iframe frameborder="0" name="authWidget" src="http://authorizator.localhost/auth/authIframe" width="200" height="100"></iframe>

<?php
		if (count($partners))
		{ 
			echo CHtml::tag('h1', array('class'=>'clear'), 'Наши партнеры');
			echo CHtml::openTag('div', array('class'=>'partners'));
			foreach ($partners as $partner)
			{
			?>
	<a href="<?php echo $partner['link'];?>" target="_blank" class="partnerLogo" style="background-image:url(<?=$partner['logo']?>)" title="<?php echo $partner['name'];?>"></a>
			<?php
			}
			echo CHtml::closeTag('div');
		}
?>
