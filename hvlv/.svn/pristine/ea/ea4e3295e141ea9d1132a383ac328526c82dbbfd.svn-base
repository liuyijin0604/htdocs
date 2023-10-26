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
	width: 60%;
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
        'Residential Address Checking',
    ],
]);
?>

<div id ="trackingPanel">
<h1>Residential Address Checking</h1>

<?php
$form=$this->beginWidget('CActiveForm', [
    'id'=>'check-residential-form',
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
         <?php echo CHtml::dropDownList('state', 1, ['NSW'=>'NSW','NT'=>'NT','QLD'=>'QLD','SA'=>'SA','TAS'=>'TAS','VIC'=>'VIC','WA'=>'WA'], ['class'=>'form-control'])?>  
    </div>
    <div class="form-group" style="max-width: 10em;">
         <?php echo CHtml::label('PostCode', 'PostCode_label')?>
         <?php echo CHtml::textField('postcode', '', ['class'=>'form-control'])?>   
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
<div id="result" style="width:400px; margin:0 auto; padding-top:80px; font-size:2em;color:#666;"></div>
<div id="break" style="height:180px;"></div>

<script type="text/javascript">
$(function(){

	$('#check_btn').on('mousedown', function(){
		<?php $url = $this->createUrl('tools/checkResidentialAddress');?>
		var form = new FormData(document.getElementById("check-residential-form"));
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
							if(r.done){
								$("#result").css('color', 'red');
							}
							else{
								$("#result").css("color", "green");
							}
							$('#div_loading').attr('style', 'display:none');
							e.preventDefault();
                            return false;
	           },
             error: function() {
                 console.log('false');
                 return false;
             }
         });
    });

	$('#modal_close').click(function() {		
		$('#modal-tracking<?=@$_GET['tabid']?> .modal-body').empty();
	});

});


</script>