<?php
/* @var $this ChargeCodeController */

$this->breadcrumbs=[
    'Charge Code'=>['/chargeCode'],
    'List',
];

$chargecodes = new Chargecode('search');
$chargecodes->unsetAttributes();
if (isset($_GET['Chargecode'])) {
    $chargecodes->attributes = $_GET['Chargecode'];
}

?>
<div style="right: 20px;position: absolute;">
    <a class="export_search" href="<?=$this->createUrl('chargeCode/export');?>" title="Export all GL Codes"><div class="icon" style="background-position:-16px 0"></div>Export</a>
    <a class="jqm_link" href="<?=$this->createUrl('chargeCode/syncXero', ['tabid' => $_GET['tabid']]);?>" title="Sync With Xero"><div class="icon" style="background-position:-160px -32px"></div>Sync With Xero</a>
</div>

<h1><?=$this->t('GL Codes');?></h1>

<?php
$this->widget('zii.widgets.grid.CGridView', [
    'id'=>$_GET["tabid"].'_ledger-grid',
    'cssFile' => false,
    'dataProvider'=> $chargecodes->search(),
    // 'formUrl' => $this->createUrl('chargeCode/grid'),
    'filter'=> $chargecodes,
    //    'summaryText' => '',
    //    'showQuickBar' => true,
    //    'afterSave' => "function(r){
    //  if(r.done == true){
    //      myApp.notice(r.msg, 5000);
    //  }else{
    //      myApp.alert(r.msg, false);
    //  }
    //  return r.done;
    // }",
    'columns'=>[
        ['name' => 'code'],
        ['name' => 'name'],
        // ['name' => 'description'],
        ['name' => 'dpmt', 'type' => 'raw', 'value' => 'CHtml::dropDownList("dpmt[".$data->id."]", $data->dpmt, Invoice::$dpmts, ["prompt"=>"Any", "class"=>"dpmt_sel"])', 
            'filter'=>CHtml::dropDownList('Chargecode[dpmt]', $chargecodes->dpmt, $this->t(Invoice::$dpmts), ['prompt'=>$this->t('All')]),],
        ['name' => 'type'],
        ['name' => 'tax_code'],
        ['name' => 'class'],
        // array('class'=>'CEditableButtonColumn', 'template' => '{edit} {cancel} {save}')
    ],
]);

?>

<script type="text/javascript">
$(function(){
    var tab = $('#<?=$_GET["tabid"];?>');
    var panel = tab.data('panel');

    panel.on('change', 'select.dpmt_sel', function(){
        $.post('chargeCode/dpmt', $(this).attr('name')+'='+$(this).val(), function(r){
            myApp.notice('Success');
        }, 'json');
    });
});
</script>
