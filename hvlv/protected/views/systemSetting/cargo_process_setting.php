<h3>Cargo Process Settings</h3>
<div class="form">
<?php 
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'cargo-process-setting_form',
	'enableAjaxValidation'=>false)
	);
?>

<div class="row">
	<div class="col" style="margin-right: 25px" id ="calculation_rules_box">
		<?php 	
			foreach($model->mdata as $key => $value)
			{
						 echo "<div class=\"col calculation_rules\">";
						 echo CHtml::label($key,$key); 
						 echo CHtml::label('minimum ','minimum'); 
						 echo CHtml::textField("mdata[$key][minimum]",@$model->mdata[$key]['minimum']),'AUD'; 

						 echo CHtml::label('costCBM ','costCBM'); 
						 echo CHtml::textField("mdata[$key][costCBM]",@$model->mdata[$key]['costCBM']),'AUD/CBM'; 

						 echo CHtml::label('minimum include CBM ','minimum include CBM'); 
						 echo CHtml::textField("mdata[$key][minimumIncludeCBM]",@$model->mdata[$key]['minimumIncludeCBM']),'CBM'; 

						 echo CHtml::label('freeKM ','freeKM'); 
						 echo CHtml::textField("mdata[$key][freeKM]",@$model->mdata[$key]['freeKM']),'KM'; 

						 echo CHtml::label('extraFeeKM ','extraFeeKM'); 
						 echo CHtml::textField("mdata[$key][extraFeeKM]",@$model->mdata[$key]['extraFeeKM']),'AUD/KM'; 

						 echo CHtml::label('fuelCharge ','fuelCharge'); 
						 echo CHtml::textField("mdata[$key][fuelCharge]",@$model->mdata[$key]['fuelCharge']),'%'; 
						 echo "</div>";
			}
		?>

	</div>

	<div class="row rowcol-left">
			<?php echo CHtml::submitButton('submit')?><?php echo CHtml::button('+',['id'=>'add_Calculation'])?><?php echo CHtml::button('-',['id'=>'reduce_Calculation'])?>
	</div>
</div>

	
	
	

<script type="text/javascript">
	$(function(){
			var tab = $('#<?=$_GET["tabid"];?>');

			$("#add_Calculation").on("click",function(){
				let rules = $(".calculation_rules");
				let nextNumber = rules.length+1;
				var mininumStr = "<label for=\"minimum\">minimum </label><input type=\"text\" value=0 name=\"mdata[c"+nextNumber+"][minimum]\" id=\"mdata_c"+nextNumber+"_minimum\" aria-invalid=\"false\" class=\"valid\">AUD";

				var costCBMStr = "<label for=\"costCBM\">costCBM </label><input type=\"text\" value=0 name=\"mdata[c"+nextNumber+"][costCBM]\" id=\"mdata_c"+nextNumber+"_costCBM\" aria-invalid=\"false\" class=\"valid\">AUD/CBM";

				var mininumCBMStr = "<label for=\"minimum include CBM\">minimum include CBM </label><input type=\"text\" value=0 name=\"mdata[c"+nextNumber+"][minimumIncludeCBM]\" id=\"mdata_c"+nextNumber+"_minimumIncludeCBM\" aria-invalid=\"false\" class=\"valid\">CBM";
				var freeKMStr = "<label for=\"freeKM\">freeKM </label><input type=\"text\" value=0 name=\"mdata[c"+nextNumber+"][freeKM]\" id=\"mdata_c"+nextNumber+"_freeKM\">KM";
				var extraFeeKMStr = "<label for=\"extraFeeKM\">extraFeeKM </label><input type=\"text\" value=0 name=\"mdata[c"+nextNumber+"][extraFeeKM]\" id=\"mdata_c"+nextNumber+"_extraFeeKM\" aria-invalid=\"false\" class=\"valid\">AUD/KM";

				var fuelChargeStr = "<label for=\"fuelCharge\">fuelCharge </label><input type=\"text\" value=0 name=\"mdata[c"+nextNumber+"][fuelCharge]\" id=\"mdata_c"+nextNumber+"_fuelCharge\" aria-invalid=\"false\" class=\"valid\">%";
				var allStr = "<div class=\"col calculation_rules\"><label for=\"C"+nextNumber+"\">C"+nextNumber+" </label>"+mininumStr+costCBMStr+mininumCBMStr+freeKMStr+extraFeeKMStr+fuelChargeStr+"</div>";

				let oHtml = $('#calculation_rules_box').html();
				$('#calculation_rules_box').html(oHtml+allStr);
				return true;
			});

			$("#reduce_Calculation").on("click",function(){
				let rules = $(".calculation_rules");
				
				let nHtml = "";
				for (var i = 0; i <rules.length-1; i++) {
					nHtml+="<div class=\"col calculation_rules\">";
					nHtml+=rules[i].innerHTML;
					nHtml+="</div>";
				}

				$('#calculation_rules_box').html(nHtml);
				return true;
			});

	});
</script>


<?php $this->endWidget();?>
		
