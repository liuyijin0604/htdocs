<?php
// get template data
$rateTemplates = array();
if ( $model->id != 114 ) {
    $rateTemplates = WmsOrgQuote::model()->findAll('org_id = 114 AND status = 1');
}

echo '<a class="jqm_link" data-win-class="XL" href="org/createQuote/'.$model->id.'"><div style="background-position:-16px 0" class="icon"></div>'.$this->t('New Quote').'</a>';
?>

<?php if (!empty($rateTemplates)) : ?>
<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div style="background-position:-48px -688px;margin-left: 10px;" class="icon"></div>Make from template</a>
<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
        <?php foreach ( $rateTemplates as $template ) : ?>
		<li><a href="<?=$this->createUrl('org/createQuote/'.$model->id).'?code='.$template->quote_no;?>" class="jqm_link" data-win-class="XL"><?php echo  $template->mdata['note'] . '-' . $template->quote_no ; ?></a></li>
        <?php endforeach; ?>
	</ul>
</div>
<?php endif; ?>

<?php if ( $model->id != 114 ) : ?>
<a class="tab_link" title="Quote Template" href="org/editQuoteTemplate"><div style="background-position:-16px 0" class="icon"></div>Edit Template</a>
<?php endif; ?>

<a class="jqm_link" href="org/importRateTemplate/<?=$model->id?>.app"><div class="icon" style="background-position:-16px 0"></div>Import Rate</a>

<?php

echo '<br>';

$ss = new WmsOrgQuote('search');
$ss->org_id = $model->id;

$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'org-wms-rate-grid',
    'cssFile' => false,
    'dataProvider'=>$ss->search(),
    'filter'=>$ss,
    'columns'=>array(
       'quote_no',
        'note',
        'vfrom',
        'vto',
        array(
            'name'=>'status',
            'value'=>'$data->getStatus()',
            'filter'=>CHtml::dropDownList('WmsOrgQuote[status]', $ss->status, $this->t(WmsOrgQuote::$states), array('prompt'=>$this->t('All'))),
        ),

        array(
            'class'=>'oButtonColumn',
            'template'=>'{update}',
            'buttons'=>array
            (
                'update' => array(
                    'imageUrl'=>false,
                    'options' => array('class' => 'jqm_link grid_edit_btn', 'label' => 'Update','data-win-class' => 'XL'),
                    'visible' => 'true',
                    'url' => 'Yii::app()->createUrl("org/updateQuote", ["fid" => $data->id])',
                    'label' => 'Update'

                ),
            ),
        ),
    ),
));
?>


<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
        tab.bind('onOpen', function(){
            $('#org-wms-rate-grid', panel).yiiGridView('update');
        });
    });
</script>

