<h1><?=$this->t('Organisations-Invoice Due In This Week');?></h1>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'org-due-term-grid',
	'cssFile' => false,
	'dataProvider'=>$dataProvider[0],
	'filter'=>$dataProvider[1],
	'columns'=>array(
		array('name'=>'id', 'type'=>'raw','value'=>'"<a href=\"".Yii::app()->createURL("org/update",array("id"=>$data["id"]))."\" class=\"tab_link\" title=\"".$data["id"]."\">".$data["id"]."</a>"',),
                 'name',
                array('name'=>'left_term', 'value'=>'$data["left_term"]."  days"'),
                'invoice',
                'invoice_date',
                array('name'=>'credit', 'header'=>'Total Credit'),
                array('name'=>'percent','value'=>'$data["percent"]."%"'),
                array('name'=>'credit_left','header'=>'Credit Left'),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

});
</script>
