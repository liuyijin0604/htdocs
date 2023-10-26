
<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
    'links' => array(
        'Receive & Scan',
    ),
));
?>
<h2> Receive & Scan </h2>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'scan-form',
	'enableAjaxValidation'=>false,
    'htmlOptions' =>[
        'data-bit' => '2', // get success ajax callback
    ]
));
$o = Org::model()->findByPk(Yii::app()->user->org);
if(sizeof($o->extra['warehouse']) > 1){
	echo '<p>Depot: ',CHtml::dropdownList('dpt_id', '', $o->getWarehouseList(), array('empty' => 'Select One', 'class' => 'required')), '</p>';
}else{
	echo CHtml::hiddenField('dpt_id', $o->extra['warehouse'][0]);
}
?>
<p>Barcode: <input id="scan" type="text" size="30" name="barcode" /></p>
<?php $this->endWidget(); ?>
<div id="summary">
</div>
<div id="result">
</div>
<audio id="sound" src=""></audio>
</div>

<style type="text/css">
#result .result_item{
	display:none;
	margin: 10px 0;
	border: 1px solid;
	padding:15px 20px;
	font-size: 14px;
}
#result .result_item h1{
	font-size: 18px;
}
#result .result_item.miss{
	background: #fee;
}
#result .result_item.dup{
	background: #fe0;
}
#result .result_item.hit{
	background: #efe;
}
#summary{
	font-size: 16px;
	margin: 10px 0;
}
#summary span{
	font-size: 30px;
	font-weight: bold;
}
</style>

<?php ob_start(); ?>

<script type="text/javascript">
$(function(){

	$('input#scan').focus();
	$('form#scan-form').on('success', function(e,r) {
        var cls = r.id == 0 ? 'miss' : (r.dup > 0 ? 'dup' : 'hit');
        var states = {miss: 'Not Found', hit: 'Found', dup: 'Already Scanned'};

        var msg = '<div id="' + r.bc;
        msg += '" class="result_item ' + cls + '"><h1>' + r.bc + ' - ' + states[cls] ;
        if (r.id > 0 && r.dup == 0) {
            if (r.storage_result == 1) {
                msg += ' -  store in ' + '<b>' + r.storage_msg + '</b>'
            } else {
                msg += ' -  Warning : ' + '<span type="color:red;"><b>' + r.storage_msg + '</b></span>'
            }
        }
        msg += '</h1>';

        msg += (cls == 'miss'? '' : '<p>Pkg Scanned: '+ r.sc +' of '+ r.pkg +'<p>To: <b>'+r.cnee_name+'</b><br />	    '+r.cnee_address+', '+r.cnee_suburb+', '+r.cnee_state+' '+r.cnee_postcode+'<br />	<b>'+r.cnee_country+'</b>	</p><p>		Weight: <b>'+r.weight+'kg</b> &nbsp; &nbsp; CBM: <b>'+r.cbm+'m<sup>3</sup></b></p>')+'</div>';
		var msgElement = $(msg);
        $('#result').prepend(msgElement.fadeIn());
		$('#summary').html('Scaned: <span>'+$('#result .result_item').length+'</span> &nbsp; &nbsp; Found: <span>'+$('#result .result_item.hit').length+'</span> &nbsp; &nbsp; Not Found: <span>'+$('#result .result_item.miss').length+'</span> &nbsp; &nbsp; Already Scanned: <span>'+$('#result .result_item.dup').length+'</span>');
		/*$('#sound', pane).attr('src', 'outturn/sound/'+r.sound);
		$('#sound', pane)[0].play();*/
		return true;
	}).on('submit', function(){
		$('input#scan').focus();
	});
	$('input#scan').on('focus', function(){
		$(this).select();
	}).focus();
});
</script>

<?php $this->registerJS(ob_get_clean(),8); ?>