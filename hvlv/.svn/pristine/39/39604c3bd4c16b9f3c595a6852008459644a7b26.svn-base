
<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
    'links' => array(
        'Bulk Receive',
    ),
));
?>
<h2> Bulk Receive</h2>

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
<p>Manifest Barcode: <input id="scan" type="text" size="30" name="barcode" /></p>

    <?php $this->endWidget(); ?>

    <input type="hidden" id="current-scanned-manifest" name="curmid" value="0">
    <input type="hidden" id="agree-average-extra" name="average_extra" value="0">

    <div id="warning">
    </div>
<div id="result">
    <div id="weight-div" style="margin-bottom: 10px;">
        <span style="font-size:18px;"> Check Weight : </span>
        <div class="weight-input"><input type="number" name="w[]" value="">kg</div>
    </div>
    <div class="form-group buttons">
        <input type="number" name="cbm" value="0.0">M<sup>3</sup> <br><br>
        <button class="btn btn-primary btn-lg" id="check-weight-confirm"><?=$this->t('Confirm');?></button>
    </div>
</div>

    <div id="result-list">
    </div>

</div>

<style type="text/css">
    #warning{
        display:none;
        margin: 10px 0;
        border: 1px solid;
        padding:15px 20px;
        font-size: 14px;
        background: #fe0;
    }
    #warning span{
        color:#ff0000;
    }
    #result{
        display:none;
    	margin: 10px 0;
	    border: 1px solid;
	    padding:15px 20px;
	    font-size: 14px;
    }
    #result span {
        font-weight: bold;
        font-size: 30px;
    }

    #result-list{
        display:none;
        margin: 10px 0;
        border: 1px solid;
        padding:15px 20px;
        font-size: 14px;
    }
    #result-list span {
        font-weight: bold;
        font-size: 30px;
    }

    .weight-input{
        margin-bottom: 4px;
    }
</style>

<?php ob_start(); ?>

<script type="text/javascript">

    var warningTimerout = 0;
    function scanedOk(r){
        var curId = parseInt($('#current-scanned-manifest').val());
        if ( curId > 0 &&　curId != r.id ) {
            var msg = '<div> Current Manifest is : <b>' + curId + '</b> , But yours are :  <span>' + r.id + '</span></div>';
            var obj = $(msg);
            $('#warning').show();
            $('#warning').html(obj.fadeIn());

            if ( warningTimerout > 0 ) clearTimeout(warningTimerout);
            warningTimerout = setTimeout(function(){
                $('#warning').fadeOut();
            },4000);

        } else
        {
            if ( curId > 0 ) {
                // just add a new weight input box
                var msg = '<div class="weight-input"><input type="number" name="w[]" value="">kg </div>';
                var obj = $(msg);
                $('#weight-div').append(obj.fadeIn());
            } else {
                // show the scanned manifest information
                var msg = '<div class="manifest-info-div">Manifest ID:<span>' + r.id + '</span> Found  , Total Items: <span>' + r.totPacks + '</span> , Total Weight: <span>' + r.totWeight + '</span>kg</div>';
                var obj = $(msg);
                $('#result').show();
                $('#result').prepend(obj.fadeIn());
                $('#current-scanned-manifest').val(r.id);
            }
        }
    }

    function confirmWeight(r){
        // show the scanned manifest information
        var msg = '<div>Manifest ID:<span>' + r.id + '</span> Received  , Total Items: <span>' + r.totPacks + '</span> , Total Weight: <span>' + r.totWeight + '</span>kg , Checked Weight: <span> ' + r.checkWeight +'</span>kg </div>';
        var obj = $(msg);
        $('#result-list').show();
        $('#result-list').prepend(obj.fadeIn());

        //var intiItems = '<div id="weight-div" style="margin-bottom: 10px;"> <span style="font-size:18px;"> Check Weight : </span> <div class="weight-input"><input type="number" name="w[]" value="0">kg </div>';
        $('.manifest-info-div').remove();
      //  $('.weight-input').remove();
        var index = 0;
        $('#weight-div .weight-input').each(function(e) {
            if (index > 0 ) $(this).remove();
            index++;
        });
        var firstWeight = $('#weight-div .weight-input:first');
        firstWeight.find('input').val(0);

       // $('#weight-div').html();
        $('#result').hide();

        $('#current-scanned-manifest').val(0);

    }

    function ajaxConfirmWeight(){
        var dptId = parseInt($('#dpt_id').val());
        if ( isNaN(dptId) ) {
            alert('Please select one warehouse firstly.');
            return false;
        }

        var data = {};
        data['barcode'] = $('input[name="barcode"]').val();
        data['w'] = {};
        var index = 0;
        $('input[name="w[]"]').each(function(){
            data['w'][index] = $(this).val();
            index++;
        });
        data['dpt_id'] = dptId;
        data['cbm'] = $('input[name="cbm"]').val();
        data['average_extra'] = $('#agree-average-extra').val();

        $.ajax({
            type : 'POST',
            url : '<?php echo Yii::app()->createAbsoluteUrl("warehouse/ajaxCheckWeight") ;?>',
            data: data,
            dataType: 'json',
            success:function(r){
                if ( r.success == 1 ) {
                    confirmWeight(r);
                } else {
                    var msg = '<div>' + r.msg + '</div>';
                    var obj = $(msg);
                    $('#warning').show();
                    $('#warning').html(obj.fadeIn());
                }
            }
        });
    }

$(function(){

	$('input#scan').focus();

    $('div#warning').on('click','#extra-weight-confirm',function(e){
        $('#agree-average-extra').val(1);
        ajaxConfirmWeight();
        $('#agree-average-extra').val(0); // for next manifest
        $('#warning').hide();
    });

    $('div#warning').on('click','#extra-weight-cancel',function(e){
        $('#agree-average-extra').val(0);
        $('#warning').hide();
    });

    $('#check-weight-confirm').click(function(e){
        $('#agree-average-extra').val(0);
        ajaxConfirmWeight();
    });

	$('form#scan-form').on('success', function(e,r) {
        if ( r.success == 1 ) {
            scanedOk(r);
        }  else {
            var msg = '<div>' + r.msg + '</div>';
            var obj = $(msg);
            $('#warning').show();
            $('#warning').html(obj.fadeIn());

            setTimeout(function(){
                $('#warning').fadeOut();
            },3000);
        }
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