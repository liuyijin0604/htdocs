<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'homeLink' => CHtml::link('Home', array('site/index/org_id/' . Yii::app()->session['org_id'])),
	'links' => array(
		'Org List',
	),
));
?>
<h1><?=$this->t('Manage Organisation');?></h1>
<p>
<?=$this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.');?>
</p>
<div class="row">
	<div class="col-xs-3">
		<?php if (Yii::app()->user->grp <= 60 || Yii::app()->user->grp == 80) {?>
		<div class="dropdown" style="display: inline-block; margin-right: 1em">
			<a class="tab_link ajax-link btn btn-default" style="width: 10em" type="button" href="<?=$this->createUrl('accounts/create');?>" title="New Org."><div class="icon" style="background-position:-16px 0"></div> New Org.</a>
		</div>
		<?php }?>
	</div>
</div>
<?php
$model = new Org('search');
$_GET['tabid'] = 1232112;
$model->unsetAttributes();
if (isset($_GET['Org'])) {
	$model->attributes = $_GET['Org'];
}
$model->oids = User::getOrgIds();
$this->widget('application.extensions.booster.TbExtendedGridView', array(
	'id' => 'org-grid',
	'cssFile' => false,
	'dataProvider' => $model->search(),
	'filter' => $model,
	'columns' => array(
		'id',
		array(
			'name' => 'type',
			'value' => '$data->getType()',
			'filter' => CHtml::dropDownList('Org[type]', $model->type, array(30 => 'Client'), array('prompt' => $this->t('All'), 'class' => 'form-control')),
		),
		array(
			'name' => 'code',
			'type' => 'raw',
			'value' => '"<a class=\"tab_link ajax-link\" href=\"update/id/".$data->id."\" title=\"Org-".$data->code."\">".$data->code."</a>"',
		),
		'name',
		'phone',
		array(
			'name' => 'status',
			'value' => '$data->getStatus()',
			'filter' => CHtml::dropDownList('Org[status]', $model->status, $this->t(Org::$states), array('prompt' => $this->t('All'), 'class' => 'form-control')),
		),

		array(
			'class' => 'application.extensions.booster.TbButtonColumn',
			'template' => '{update}',
			'buttons' => array
			(

				'update' => array(
					'imageUrl' => false,
					'icon' => 'glyphicon glyphicon-edit',
					'options' => array('class' => 'tab_link ajax-link grid_edit_btn', 'label' => $this->t('Update'), 'title' => '"Org-".$data->code'),
				),
			),
		),
	),
));?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('.search-button', panel).click(function(){
		$('.search-form').toggle();
		return false;
	});
	$('.search-form form', panel).submit(function(){
		$('#org-grid', panel).yiiGridView('update', {
			data: $(this).serialize()
		});
		return false;
	});

	$('a.org_export_search', panel).on('mousedown', function(){
		var q = $('.filters input, .filters select', panel).serialize();
		$(this).attr('href', $(this).data('baseurl') + '&' + q);
	});

	tab.bind('onOpen', function(){
		$('#org-grid', panel).yiiGridView('update');
	});
});
</script>
