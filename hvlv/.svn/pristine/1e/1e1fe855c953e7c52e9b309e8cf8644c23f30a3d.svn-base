<div class="form">
<?php
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'accounting-mng-actions-form',
	'enableClientValidation'=>true,
	'action'=>$this->createUrl('invoice/mngactions',['tabid' => $_GET["tabid"]]),
	'clientOptions'=>array(
		'validateOnSubmit'=>false,
	),
));
?>
<h2>All Available Accounting Manager Tools</h2>

<div class="row buttons">
	<?php echo CHtml::submitButton('Close Last Month Balance',['name' => 'btn_close_balance','id' => "accounting-mng-actions_btn"]); ?>
</div>

<?php $this->endWidget(); ?>

<div id="accounting-mng-actions_result" style="margin: 10px 0; border: 1px solid;padding:20px;">
</div>

</div><!-- form -->


<script type="text/javascript">
    $(function(){

        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');

        $('form#accounting-mng-actions-form', panel).data('custom_success', function(r){
            $('#accounting-mng-actions_result', panel).empty().prepend($('<p>'+r.msg+'</p>').fadeIn());
            $('#accounting-mng-actions_btn', panel).attr('disabled', false);
            return true;
        });

    });
</script>