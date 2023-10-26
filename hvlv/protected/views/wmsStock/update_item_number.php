<h1><?=$this->t('Update Item Number');?></h1>


<div class="form">

	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'update-item-number-form',
		'enableAjaxValidation' => false,
	)); ?>
	
	<div class="row rowcol">
		<label>Customer:</label>
		<?php echo CHtml::hiddenField('agent_id');
			$acname1 = empty($_GET["tabid"])? 'agent_ac' : $_GET["tabid"].'_agent_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname1,
				'sourceUrl' => array('org/ownerSuggest'),
				'value' => '',
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
						'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
				),
				'htmlOptions' => array(
					'size' => '30',
				),
		));
		?>
	</div>

	<div class="row" style="margin-top: 20px;">
		<label for="postw-batch">Load File</label> <a href="<?=$this->createUrl('wmsStock/updateItemNumber', array('template' => true))?>" target="_blank"> Template File</a>
	</div>
	<div class="row">
		<input type="file" name="file" id="file" />
	</div>

	<br/>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit', ['id' => 'btn-save']); ?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
	$(function(){
		var tab = $('#<?=$_GET["tabid"];?>');
		var panel = tab.data('panel');
		tab.off('reload_tab').on('reload_tab', function() {
			var t = $('.ui-tabs', panel);
			t.tabs('load', t.tabs('option', 'active'));
		});

		var win = $('#jqmw_<?=$_GET["tabid"];?>');

		$('form#update-item-number-form', win).on('success', function(e, r) {
			win.data('opener').trigger('onOpen');
			win.jqmHide();
		});

	});
</script>
