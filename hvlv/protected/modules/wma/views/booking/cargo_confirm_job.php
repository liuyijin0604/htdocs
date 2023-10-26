<?php

$objShipment = ImParcel::model()->find('ref=:ref',[':ref'=>$_GET['caref']]);

?>
<style type="text/css">
	p{
		font-size: 16px;
		color:black;
	}
	.display_none {
        display: none;
    }
	.btn_next{
		color: #fff;
    	background-color: #007aff;
    	border: 1px solid #007aff;
		width: 200px;
		height: 40px;
		float: right;
	}
</style>


<div class="content-padded">
	<div class="form">
	<?php 


	$form = $this->beginWidget('CActiveForm', array(
		'id' => 'cargo-confirm-form',
		'enableAjaxValidation' => false,
	)); ?>
		
		<div class="row">

			<div id="divDelivery" class="col-12" style="padding-left: 5px;">
				<h3>Please confirm delivery details: </h3>
				<br/>
				<br/>
				<h4>Please select delivery time:</h4>
				<div id="divDeliveryNote"><?=$strDeliveryNote?></div>
				<div id="divDeliveryTime"></div>

				<br/>
				<br/>
				<?php echo CHtml::button($this->t('confirm'), ['class' => 'save_btn btn btn-primary btn-block','onclick'=>'funcSubmit()']); ?>
			</div>
			<br/>




		</div>

	<?php $this->endWidget(); ?>

	</div>
</div>







<script type="text/javascript">
	function funcDeliveryTime(){
        htmlobj = $.ajax({
            type: "GET",
			<?php
					$strUrlPrefix = 'https://'.$_SERVER['HTTP_HOST'];
					if($strUrlPrefix == 'https://localhost:82'){
						$strUrlPrefix = 'http://localhost:82/wma/booking';
					}
			?>
            url: '<?=$strUrlPrefix.'/availableJob?caref='.$objShipment->ref?>',
            async: false
        }); 
        obj = JSON.parse(htmlobj.responseText);
		console.log(obj);
        if (obj.isSuccess) {
			var listDeliveryTime = obj.listJob;
        }
        else{

        }

		var listHtml = $.map(listDeliveryTime,(item,i)=>{
			return '<input value="'+i+'" id="delivery_time_'+i+'" type="radio" name="delivery_time"><label for="delivery_time_'+i+'" id="label_time_'+i+'" >'+item+'</label><br/><br/>';
		});
		

		strHtml = '<span id="delivery_time">';
		for(var i=0; i<listHtml.length;i++ ){
			strHtml += listHtml[i];
		}

		strHtml += '</span>';

		$("#divDeliveryTime").html(strHtml);
	}
	funcDeliveryTime();

	function funcSubmit(){
		strDeliveryTime = $("input[name='delivery_time']:checked").val();
		if(strDeliveryTime == null){
			alert('Please select delivery time.');
			return;
		}

		strUrl = window.location.href;
		listData = $('#cargo-confirm-form').serializeArray();
        htmlobj = $.ajax({
            type: "POST",
            url: "<?=$strUrlPrefix.'/availableJob?caref='.$objShipment->ref?>",
            data: listData,
            async: false
        });
        obj = JSON.parse(htmlobj.responseText);
        if (obj.isSuccess) {
			window.location.replace(strUrl);
        }
        else{
			funcDeliveryTime();
			$("#divDeliveryNote").html("(time slot has been taken,please select another one.)");
        }

	}




</script>


