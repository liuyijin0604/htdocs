<style type="text/css">
label.left {
	float: left;
	min-width: 70px;
}
input[type=text]{
    width: 500px;
}

</style>
  <div>
  <h1>Marketing Email</h1>
  
  <div class="form" style="height:2000px">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'email-form',
	'enableAjaxValidation'=>false,
)); ?>

    <div class="row rowcol rowleft">
        <?php echo CHtml::label('Subject','subject'); ?>
        <?php echo CHtml::textField( 'subject',$model->subject); ?>
    </div>

	<div class="row" style="min-height:330px">
		<?php //echo $form->textArea($model,'body',array('rows'=>6, 'cols'=>50));
        if(empty($model->html)){
            $model->html = MarketingEmail::templete_tla;
        }
		$this->widget('ext.ckeditor.CKEditorWidget',array(
		  "model"=>$model,
		  "attribute"=>'html',
		  "id" => 'email_body_'.$_GET['tabid'],
		  "ckBasePath"=>'//cdn.ckeditor.com/4.4.7/standard/',
		  //"defaultValue"=>"Test Text",
		  "config" => array(
              "height"=>"1800px",
              "width"=>"700px",
			  "toolbar"=>"Standard",
			  ),
		  ));
		?>
		
	</div>
	
    <input type="hidden" id="id" name="id" value="<?=$model->id?>" />
	
	<div class="row buttons">
		<?php echo CHtml::submitButton( 'Save'); ?>
	</div>
<?php $this->endWidget(); ?>

</div><!-- form -->
  </div>



<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	var tab = $('#<?=$_GET["tabid"];?>');
	var tabs = $('#<?=$_GET["tabid"];?>-email-tabs').tabs();
	

	var ckDestroy = function(){
		if(CKEDITOR){
			for(i in CKEDITOR.instances){
				if($('#'+i, tabs).length > 0) CKEDITOR.instances[i].destroy(true);
			}
		}
	};
	
	// tabs.off('click', 'input.ab_add').on('click', 'input.ab_add', function(){
	// 	var t = $('input#extra_'+$(this).data('to'), tabs);
	// 	var tv = t.val();
	// 	var es = [];
	// 	if(tv.length > 0){
	// 		tv.replace(/,+$/,'');
	// 		es = tv.split(',');
	// 	}
	// 	$('input.chkbox:checked', tabs).each(function(i){
	// 		var e = $(this).val();
	// 		if(e.length == 0) return;
	// 		if($.inArray(e, es) == -1) es.push(e);
	// 	});
	// 	t.val(es.join(',', es));
	// 	t.trigger('change');
	// }).off('click', 'input#chkbox_all').on('click', 'input#chkbox_all', function(r){
	// 	$('input.chkbox', tabs).attr('checked', this.checked);
	// }).off('change', 'input#extra_to, input#extra_cc, input#extra_bcc').on('change', 'input#extra_to, input#extra_cc, input#extra_bcc', function(){
	// 	var ec = $(this).val().split(',');
	// 	$(this).next('.count').text(ec[0]==''? '' : ec.length);
	// });
	
	$('.grid-view .summary').css('margin-top', '-15px');
	
	$('form#email-form', tabs).data({reset: true}).on('success', function(e, r){
		// tab.trigger('load');
        $('#jqmw_<?=$_GET["tabid"];?>').jqmHide();
        $('#<?=$_GET["tabid"]?>_email-grid').yiiGridView('update');
	});
	
	$('form#email-form', win).on('success', function(e, r){
		// ckDestroy();
		// win.data('opener').trigger('onOpen');
		// win.jqmHide();
        $('#jqmw_<?=$_GET["tabid"];?>').jqmHide();
        $('#<?=$_GET["tabid"]?>_email-grid').yiiGridView('update');
	});

	tab.on('onClose', ckDestroy);

	win.on('close', ckDestroy);
});
</script>