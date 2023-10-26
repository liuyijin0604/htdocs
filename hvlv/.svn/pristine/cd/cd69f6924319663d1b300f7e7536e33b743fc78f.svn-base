<h2>Courier Cost Check</h2>
<h1> Check Parcel </h1>
<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'courier-check-form',
        'enableAjaxValidation'=>false,
//        'action' => $this->createUrl('imparcel/ajaxCourierCost'),
    ));
    ?>

<div class="row">
    <div class="rowcol">
        <?php echo CHtml::label('Suburb','forcheck'); 
        $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
            'name'=>empty($_GET['tabid'])?'cost_suburb':$_GET['tabid'].'_cost_suburb',
            'sourceUrl'=>array('postcode/suggest'),
            'value'=>'',
            'options'=>array(
                  'showAnim'=>'fold',
                  'minLength'=>2,
                  'delay'=>200,
                  'select'=>'js:function(event,ui){$(this).val(ui.item["value"]); $(this).trigger("ac_after_select",ui);return false;}'
            ),
            'htmlOptions' => array(
					'size' => '20',
					'name' => 'suburb',
				),
             
        ));
        ?>

    </div>
<input type="hidden" name="flag" value="1"/>
<!--    <div class="rowcol">
        <?php echo CHtml::label('State','forfwcheck'); ?>
        <?php echo CHtml::dropDownList('state', 0, array('ACT' => 'ACT - Australia Capital Territory', 'NSW' => 'NSW - New South Wales', 'NT' => 'NT - Northern Territory', 'QLD' => 'QLD - Queensland', 'SA' => 'SA - South Australia', 'TAS' => 'TAS - Tasmania', 'VIC' => 'VIC - Victoria', 'WA' => 'WA - Western Australia'), array('empty' => $this->t('Select One'), 'style' => 'width:100px')); ?>
    </div>-->

    <div class="rowcol">
        <?php echo CHtml::label('Postcode','forcheck'); ?>
        <?php echo CHtml::textField('postcode','',array('size'=>50,'maxlength'=>255)); ?>
    </div>

    <div class="rowcol">
        <?php echo CHtml::label('Weight','forfwcheck'); ?>
        <?php echo CHtml::textField('weight','',array('size'=>50,'maxlength'=>255)); ?>
    </div>
    <div class="rowcol">
        <?php echo CHtml::label('Courier','forfwcheck'); ?>
        <?php echo CHtml::dropDownList('courier', '', ImParcel::$couriers, array('prompt' => $this->t('All'), 'style' => 'width:100px')); ?>
    </div>
</div>
<br/>
    <input id="testfw_check_btn" type="submit" value="Check" />

    <?php $this->endWidget(); ?>
</div>
<div id="courier_result" style="margin: 10px 0; border: 1px solid;padding:20px;height: 100px; width: 70%">
</div>
<script>
 $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');

        $('form#courier-check-form', panel).data('custom_success', function(r){
            $('#courier_result', panel).empty();
            $('#courier_result', panel).prepend($('<p>'+r.msg+'</p>').css('color', r.color).fadeIn());
            $('input[type="submit"]',panel).prop('disabled',false);
            return true;
        })
        
        	//suburb ac
	$('#<?=$_GET['tabid'].'_cost_suburb'?>', panel).off('ac_after_select').on('ac_after_select', function(evt, ui){
		$("#postcode", panel).val(ui.item['pc']);
	});

    });
 </script>
