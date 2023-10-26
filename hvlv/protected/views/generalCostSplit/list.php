
<h1><?=$this->t('General Cost Split Manage');?></h1>

<?php
$this->widget('application.extensions.editablegrid.CEditableGridView', array(
    'id'=> 'general-cost-split-grid',
    'cssFile' => false,
    'dataProvider'=> $model->search(),
    'formUrl' => $this->createUrl('generalCostSplit/grid'),
  //  'filter'=> $model,
    'summaryText' => '',
    'showQuickBar' => true,
    'afterSave' => "function(r){
		if(r.done == true){
			myApp.notice(r.msg, 5000);
		}else{
			myApp.alert(r.msg, false);
		}
		return r.done;
	}",
    'columns'=>array(
       array('header' => 'Charge Code', 'name' => 'chargecode','class' => 'CEditableColumn','type' => 'autocomplete' ,
            'value' => '$data->getCharcodeDesc()' ,
            'acOptions' => array('source' => 'generalCostSplit/chargecodeList' )
        ),

        array('header' => 'Import(%)', 'name' => 'pim', 'value'  => 'pim','class' => 'CEditableColumn'),
        array('header' => 'Export(%)', 'name' => 'pex', 'value'  => 'pex', 'class' => 'CEditableColumn'),
        array('header' => 'Air/Sea(%)', 'name' => 'paf','value'  => 'paf',  'class' => 'CEditableColumn'),
        array('header' => '3PL(%)', 'name' => 'p3pl','value'  => 'p3pl',  'class' => 'CEditableColumn'),

        array('class'=>'CEditableButtonColumn', 'template' => '{edit} {cancel} {delete} {save}')
    ),
));
?>
