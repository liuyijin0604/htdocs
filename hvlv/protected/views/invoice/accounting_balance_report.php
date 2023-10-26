<div class="form">
<?php
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'accounting-balance-report-form',
	'enableClientValidation'=>true,
	'action'=>$this->createUrl('invoice/createBalanceReport',['tabid' => $_GET["tabid"]]),
	'clientOptions'=>array(
		'validateOnSubmit'=>false,
	),
));
?>
<h2>Accounting Balance Report</h2>

<div class="row buttons">
	<?php echo CHtml::submitButton('Create Last Month Balance',['name' => 'btn_balance_report','id' => "balance_report_btn"]); ?>
    <?php echo CHtml::checkBox('owrp',false) ; ?> Overwrite
</div>

<?php $this->endWidget(); ?>

<div id="balance_report_result" style="margin: 10px 0; border: 1px solid;padding:20px;display: none;">
</div>

</div><!-- form -->

<br/>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'accounting-balance-report-grid',
    'cssFile' => false,
    'dataProvider'=>$model->search(),
  //  'filter'=>$model,
    'columns'=>array(

        'created_date',
        'date_month',
        'last_balance',
        'current_invoice',
        'current_payment',
        'current_balance',
    //    'note',
        array(
            'class'=>'oButtonColumn',
            'template'=>'{view}',
            'buttons'=>array(
                'view' => array(
                    'imageUrl'=>false,
                    'visible'=> 'true',
                    'url' => 'Yii::app()->createUrl("invoice/viewBalance", ["fid" => $data->id])',
                    'options' => array('class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('View')),
                )
            ),
        ),
    ),
)); ?>


<script type="text/javascript">
    $(function(){

        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');

        tab.on('onOpen', function(){
            $('#accounting-balance-report-grid', panel).yiiGridView('update');
        });

        $('form#accounting-balance-report-form', panel).data('custom_success', function(r){
            $('#balance_report_result', panel).show();
            if (r.success == 0 ) {
                $('#balance_report_result', panel).empty().prepend($('<p style="color: darkred;">' + r.msg + '</p>').fadeIn());
            } else {
                $('#balance_report_result', panel).empty().prepend($('<p style="color:#008000;">' + r.msg + '</p>').fadeIn());
            }
            $('#balance_report_btn', panel).attr('disabled', false);
            $('#accounting-balance-report-grid', panel).yiiGridView('update');
            return true;
        });

    });
</script>