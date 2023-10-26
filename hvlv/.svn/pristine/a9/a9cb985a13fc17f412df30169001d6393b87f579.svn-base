<style type="text/css">
.address-line-div {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      width: 100%;
  }

.form-group {
	display:inline-block;
}

#trackingPanel {
	margin:0 auto;
	width: 90%;
}


.load{
    width: 80px;
    height: 40px;
    margin: 0 auto;
    margin-top:100px;
}
.load span{
    display: inline-block;
    width: 8px;
    height: 100%;
    border-radius: 4px;
    background: lightgreen;
    -webkit-animation: load 1s ease infinite;
}
@-webkit-keyframes load{
    0%,100%{
        height: 40px;
        background: lightgreen;
    }
    50%{
        height: 70px;
        margin: -15px 0;
        background: lightblue;
    }
}
.load span:nth-child(2){
    -webkit-animation-delay:0.2s;
}
.load span:nth-child(3){
    -webkit-animation-delay:0.4s;
}
.load span:nth-child(4){
    -webkit-animation-delay:0.6s;
}
.load span:nth-child(5){
    -webkit-animation-delay:0.8s;
}


</style>
<?php $this->widget('zii.widgets.CBreadcrumbs', [
    'homeLink'=>CHtml::link('Home', ['site/index']),
    'links' => [
        'Customized Container Quotation',
    ],
]);
?>

<div id ="trackingPanel">
<h1>Customized Container Quotation</h1>

<?php
$form=$this->beginWidget('CActiveForm', [
    'id'=>'check-container-quotation-form',
    'htmlOptions'=>['target'=>'result-output','class'=>'ifrm-form','enctype' =>'multipart/form-data'],
]); ?> 
    <div class="address-line-div" style="max-width: 26em; width: 100%;clear: both;">
         <?php echo CHtml::label('Address Line', 'Address_line_label')?>
         <?php echo CHtml::textField('address_line', '', ['class'=>'form-control','style'=>'width:400px'])?>  
    </div>
    <div class="form-group" style="max-width: 10em;">
         <?php echo CHtml::label('Suburb', 'Suburb_label')?>
         <?php echo CHtml::textField('suburb', '', ['class'=>'form-control'])?>   
    </div>
    <div class="form-group" style="max-width: 10em;">
         <?php echo CHtml::label('State', 'State_label')?>
         <?php echo CHtml::dropDownList('state', 1, ['NSW'=>'NSW','VIC'=>'VIC'], ['class'=>'form-control'])?> 
         <!-- <?php echo CHtml::dropDownList('state', 1, ['NSW'=>'NSW','NT'=>'NT','QLD'=>'QLD','SA'=>'SA','TAS'=>'TAS','VIC'=>'VIC','WA'=>'WA'], ['class'=>'form-control'])?>-->
    </div>
    <div class="form-group" style="max-width: 10em;">
         <?php echo CHtml::label('PostCode', 'PostCode_label')?>
         <?php echo CHtml::textField('postcode', '', ['class'=>'form-control'])?>   
    </div>

    <div style="display: block;">
         <?php echo CHtml::label('Standard Trailer Request', 'Standard Trailer Request')?>
         <div class="form-group">
            &nbsp;&nbsp;
            <label><input type="radio" name="standard_trailer" id="20ft_standard" value="20ft_standard" required/> 20ft Standard</label> &nbsp; 
            <label><input type="radio" name="standard_trailer" id="40ft_standard" value="40ft_standard"/> 40ft Standard</label> &nbsp; 
            <label><input type="radio" name="standard_trailer" id="20ft_sideloader" value="20ft_sideloader"/> 20ft Sideloader</label> &nbsp; 
            <label><input type="radio" name="standard_trailer" id="40ft_sideloader" value="40ft_sideloader"/> 40ft Sideloader</label> &nbsp; 
        </div> 
    </div>
    <div style="display: block;">
         <?php echo CHtml::label('Is this the residential address?', 'Is this the residential address')?>
         <div class="form-group">
            &nbsp;&nbsp;
            <label><input type="radio" name="check_residential" id="yes_residential" value="yes_residential" required/> Yes</label> &nbsp; 
            <label><input type="radio" name="check_residential" id="no_residential" value="no_residential"/> No</label> &nbsp; 
        </div> 
    </div>

    <div class="address-line-div" style="max-width: 10em;">
             <?php echo CHtml::button('Check', array('id'=>'check_btn', 'class'=>'btn btn-primary btn-lg')); ?>
      </div>
     
</div>

<?php $this->endWidget(); ?>

<div class="load" id='div_loading' style="display: none;">
		<span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
</div>
<div class="load" id='div_loading' style="display: none;">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
</div>
<div id="result" style="width:80%; margin:0 auto; padding-top:80px; font-size:2em;color:#666;"></div>
<div id="residential_notice" style="width:60%; margin:0 auto; padding-top:80px; font-size:2em;color:#666;">    
    <p>Sorry we can not deliver full container to residential address, please contact your account manager. </p>
    <p>私人地址不支持整柜派送，请联系客户经理咨询。 </p>
</div>
<div id="break" style="height:180px;"></div>

<script type="text/javascript">
$(function(){
    $("#check_btn").hide(); 
    $("#residential_notice").hide(); 

	$('#check_btn').on('mousedown', function(){
		<?php $url = $this->createUrl('tools/getContainerQuotation').'?cach='.rand(1,1000000);?>
		var form = new FormData(document.getElementById("check-container-quotation-form"));
		$('#div_loading').attr('style', '');
		$('#result').empty();
    $.ajax({
             url: '<?=$url?>',
             type: "post",
             data: form,
             processData: false,
             contentType: false,             
             success: function(r) {
             	r = JSON.parse(r);							
				$('#result').append(r.msg);
				if(!r.done){
					$("#result").css('color', 'red');
				}
				else{
					$("#result").css('color', 'blue');
				}
				$('#div_loading').attr('style', 'display:none');
                $("#check-container-quotation-form :input").prop('disabled', true);
				e.preventDefault();
                return false;
	         },
             error: function() {
                 console.log('false');
                 return false;
             }
         });
    });

    $('#yes_residential').click(function(){  
        $("#residential_notice").show();
        $("#check_btn").hide();               
    }); 

    $('#no_residential').click(function(){
        $("#residential_notice").hide();  
        $("#check_btn").show();  
    });

	$('#modal_close').click(function() {		
		$('#modal-tracking<?=@$_GET['tabid']?> .modal-body').empty();
	});

});


</script>