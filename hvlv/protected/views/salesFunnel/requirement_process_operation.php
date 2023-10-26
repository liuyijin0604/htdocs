<?php
	$clientStatus = ($customer_type==SalesfunnelRequirementsSubmission::CUSTOMERTYPEREPEAT?SalesfunnelRequirementsSubmission::$repeatClientStatuses:SalesfunnelRequirementsSubmission::$singleClientStatuses);
?>

<h3>Requirement Process-<?= $clientStatus[$processType]?></h3>
<h3>Type:<?=@$model->customer_type==2?"Repeat":"Single"?>&nbsp; Company:<?= @$model->company_name?></h3>
<div class="form">

</br>
<?php
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'requirement-process-operation-form',
	'enableClientValidation'=>true,
		'action'=> $this->createURL('salesFunnel/reqOperationUpdate',array('id'=>$model->id)),
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
));


	switch ($processType)
	{
		case SalesfunnelRequirementsSubmission::STAT_PRC:
			echo CHtml::button('Edit Process Email', array('class' => 'edit_process_email',"style"=>"font-size:1.5em;"));
			echo CHtml::link('',$this->createUrl('salesFunnel/editProcessEmail')."?id=".$model->id, array('id'=>'processLink','class' => 'jqm2_link',"style"=>"font-size:1.5em;display:none","data-win-class"=>"L"));
			break;

		case SalesfunnelRequirementsSubmission::STAT_INV:
			echo CHtml::button('Edit Process Email', array('class' => 'edit_process_email',"style"=>"font-size:1.5em;"));
			echo CHtml::link('',$this->createUrl('salesFunnel/editProcessEmail')."?id=".$model->id, array('id'=>'processLink','class' => 'jqm2_link',"style"=>"font-size:1.5em;display:none","data-win-class"=>"L"));
			break;

		case SalesfunnelRequirementsSubmission::STAT_APV:
			echo CHtml::button('Edit Process Email', array('class' => 'edit_process_email',"style"=>"font-size:1.5em;"));
			echo CHtml::link('',$this->createUrl('salesFunnel/editProcessEmail')."?id=".$model->id, array('id'=>'processLink','class' => 'jqm2_link',"style"=>"font-size:1.5em;display:none","data-win-class"=>"L"));
			break;

		case SalesfunnelRequirementsSubmission::STAT_DEP:
			echo CHtml::button('Edit Process Email', array('class' => 'edit_process_email',"style"=>"font-size:1.5em;"));
			echo CHtml::link('',$this->createUrl('salesFunnel/editProcessEmail')."?id=".$model->id, array('id'=>'processLink','class' => 'jqm2_link',"style"=>"font-size:1.5em;display:none","data-win-class"=>"L"));
			break;

		case SalesfunnelRequirementsSubmission::STAT_SYS:
			echo CHtml::button('Edit Process Email', array('class' => 'edit_process_email',"style"=>"font-size:1.5em;"));
			echo CHtml::link('',$this->createUrl('salesFunnel/editProcessEmail')."?id=".$model->id, array('id'=>'processLink','class' => 'jqm2_link',"style"=>"font-size:1.5em;display:none","data-win-class"=>"L"));
			break;

		case SalesfunnelRequirementsSubmission::STAT_CON:
			echo CHtml::button('Edit Process Email', array('class' => 'edit_process_email',"style"=>"font-size:1.5em;"));
			echo CHtml::link('',$this->createUrl('salesFunnel/editProcessEmail')."?id=".$model->id, array('id'=>'processLink','class' => 'jqm2_link',"style"=>"font-size:1.5em;display:none","data-win-class"=>"L"));
			break;

			
		default:
			// code...
			break;
	}
		


?>
<?php $this->endWidget(); ?>
</br>

<div class="row">
	<div class="row rowcol rowleft">
	<?php
		echo "</br></br>";
		echo CHtml::button('Process Done', array('class' => 'process_done'))."&nbsp;&nbsp;";
		echo CHtml::checkbox('ignore', false) . 'ignore';
	?>
	</div>
</div>


<br>
<br>

</div>
<script type="text/javascript">

$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	var tab = $('#<?=$_GET["tabid"];?>');
	var pane = $('#<?=$_GET["tabid"];?>').data('panel');

	tab.unbind('reload_submission_grid').bind('reload_submission_grid', function(){
		$('#submission-grid-list1', tab.data('panel')).yiiGridView('update');
		return false;
	});

	const maniResult = function(r){
		if(r == 'done'){
			myApp.notice('Done', 5000);
		}else{
			myApp.alert(r, false);
		}
		tab.trigger('reload_submission_grid');
	};



	tab.data('panel').off('change', 'select.pfile_status').on('change', 'select.pfile_status', function(){
		$.post('files/status', {'id': $(this).data('id'), 'status': $(this).val() });
	});

	win.unbind('reload_consol_invoice_grid').bind('reload_consol_invoice_grid',function(){
			$('#consol_invoice_grid_<?=$_GET['tabid']?>',win).yiiGridView('update');
	});

	$('.edit_process_email',win).on('click',function(){
		$('#processLink',win).click();
	});

	$('.process_done',win).on('click',function(){
		let isIgnore = $('#ignore').prop('checked');
		let data = {"id":<?=$model->id?>,"processType":<?=$processType?>,"isIgnore":isIgnore};
		console.log(data);
		// alert(data["isIgnore"]);
		$.ajax({
		    url: '<?= $this->createUrl('salesFunnel/requirementProcessUpdate')?>',
		    type: "post",
		    data: data,
		    processData: true,
		    contentType: 'application/x-www-form-urlencoded',
		    success: function(r) {
		    	r = JSON.parse(r);
		        if(r.done)
		         {
					myApp.notice('Done', 5000);
					win.jqmHide();
				 }else
				 {
					myApp.alert(r.msg, false);   
		         }

		       
		        tab.trigger('reload_submission_grid');
		     },
		    error: function(e) {
		        console.log(e);
		    }
		});
			 
	});
			
		$('.theclick',win).click(function(){
				 win.jqmHide();
		});
		
		
});

</script>
