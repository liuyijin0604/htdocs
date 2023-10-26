<?php
/* @var $this ChargeCodeController */

$chargecodes = new ExportChargecodeMap('search');
$chargecodes->unsetAttributes();
if ( isset($_GET['Chargecode']) ) {
    $chargecodes->attributes = $_GET['Chargecode'];
}
?>
<h1><?=$this->t('Mapped Edi Charge Codes');?></h1>

<?php
$this->widget('application.extensions.editablegrid.CEditableGridView', array(
    'id'=>$_GET["tabid"].'_edichargecodes-grid',
    'cssFile' => false,
    'dataProvider'=> $chargecodes->search(),
    'formUrl' => $this->createUrl('billing/ediChargecodeMapgrid'),
    'filter'=> $chargecodes,
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
        array('name' => 'name'),
        array('name' => 'chargecode', 'class' => 'CEditableColumn'),
        array('class'=>'CEditableButtonColumn', 'template' => '{edit} {cancel} {save}')
    ),
));

?>

