

<h1><?=$this->t('OP Progress Report');?></h1>


<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'op-progress-billing-grid',
    'htmlOptions'=>array('style'=>'width: 70%'),
    'cssFile' => false,
    'dataProvider'=>$model,
    'columns'=>array(
       array('header' => 'Type', 'name' => 'type','htmlOptions'=>array('style'=>'width: 180px;')),
        array('header' => 'Import(Michelle)', 'name' => 'import','type' => 'raw',
            'value' => '$data["id"] == 1 ? "<a href=\"".Yii::app()->createURL("imcoConsol/missingAccrual")."\" class=\"tab_link\" title=\"Missing Accrual-".$data["import"]."\">".$data["import"]."</a>" : "<a href=\"".Yii::app()->createURL("billing/importList")."\" class=\"tab_link\" title=\"Missing Actual-".$data["import"]."\">".$data["import"]."</a>"'),
        array('header' => 'Export(Frank)', 'name' => 'export','type' => 'raw',
            'value' => '$data["id"] == 1 ? "<a href=\"".Yii::app()->createURL("excoConsol/missingAccrual")."\" class=\"tab_link\" title=\"Missing Accrual-".$data["export"]."\">".$data["export"]."</a>" : "<a href=\"".Yii::app()->createURL("billing/exportList")."\" class=\"tab_link\" title=\"Missing Actual-".$data["export"]."\">".$data["export"]."</a>"'),
        array('header' => '3PL', 'name' => '3pl','type' => 'raw',
            'value' => '$data["id"] == 1 ? "Mandy(<a href=\"".Yii::app()->createURL("ediJob/missingAccrualFor3PL",["uid"=> 347])."\" class=\"tab_link\" title=\"Missing Accrual-".$data["3pl"][347]."\">".$data["3pl"][347]."</a>) Jessie(<a href=\"".Yii::app()->createURL("ediJob/missingAccrualFor3PL",["uid"=> 503])."\" class=\"tab_link\" title=\"Missing Accrual-".$data["3pl"][503]."\">".$data["3pl"][503]."</a>)" : "<a href=\"".Yii::app()->createURL("billing/list")."\" class=\"tab_link\" title=\"Missing Actual-".$data["3pl"]."\">".$data["3pl"]."</a>" '),
        array('header' => 'Air/Sea Freight', 'name' => 'freight','type' => 'raw',
            'value' => '$data["id"] == 1 ? "Mandy(<a href=\"".Yii::app()->createURL("ediJob/missingAccrualForFreight",["uid"=> 347])."\" class=\"tab_link\" title=\"Missing Accrual-".$data["freight"][347]."\">".$data["freight"][347]."</a>) Jessie(<a href=\"".Yii::app()->createURL("ediJob/missingAccrualForFreight",["uid"=> 503])."\" class=\"tab_link\" title=\"Missing Accrual-".$data["freight"][503]."\">".$data["freight"][503]."</a>)" : "<a href=\"".Yii::app()->createURL("billing/list")."\" class=\"tab_link\" title=\"Missing Actual-".$data["freight"]."\">".$data["freight"]."</a>"'),


    ),
)); ?>


<br/>
<h1><?=$this->t('Accounting Progress Report');?></h1>


<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'accounting-progress-billing-grid',
    'htmlOptions'=>array('style'=>'width: 70%'),
    'cssFile' => false,
    'dataProvider'=>$acmodel,
    'columns'=>array(
        array('header' => 'Type', 'name' => 'type','htmlOptions'=>array('style'=>'width: 180px;')),
        array('header' => 'Import', 'name' => 'import','type' => 'raw',
            'value' => '"<a href=\"".Yii::app()->createURL("billing/importList")."\" class=\"tab_link\" title=\"Pending HVLV-".$data["import"]."\">".$data["import"]."</a>"'),
        array('header' => 'Export', 'name' => 'export','type' => 'raw',
            'value' => '"<a href=\"".Yii::app()->createURL("billing/exportList")."\" class=\"tab_link\" title=\"Pending HVLV-".$data["export"]."\">".$data["export"]."</a>"'),
        array('header' => '3PL', 'name' => '3pl','type' => 'raw',
            'value' => '"<a href=\"".Yii::app()->createURL("billing/list")."\" class=\"tab_link\" title=\"Pending HVLV-".$data["3pl"]."\">".$data["3pl"]."</a>"'),
        array('header' => 'Air/Sea Freight', 'name' => 'freight','type' => 'raw',
            'value' => '"<a href=\"".Yii::app()->createURL("billing/list")."\" class=\"tab_link\" title=\"Pending HVLV-".$data["freight"]."\">".$data["freight"]."</a>"'),
    ),
)); ?>


<script type="text/javascript">
    $(function(){
        var tab = $("#<?=$_GET['tabid'];?>");
        var panel = tab.data('panel');

        tab.bind('onOpen', function(){
            $('#op-progress-billing-grid', panel).yiiGridView('update');
            $('#accounting-progress-billing-grid', panel).yiiGridView('update');
        });
    });
</script>