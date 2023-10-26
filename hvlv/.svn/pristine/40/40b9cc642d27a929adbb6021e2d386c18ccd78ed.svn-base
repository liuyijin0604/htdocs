<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'google-review-comment-form',
        'enableClientValidation' => true,
        'action' => $this->createUrl('googleReview/addComment', array('id' => $model->id)),
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),
    ));
    ?>
    <h2>Comments</h2>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo CHtml::textArea('comment', $model->comment, array('rows' => 4, 'cols' => 60)); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Save', array('id'=>'save_button')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
    $( document ).ready(function() {
    $('.popCancel').on('click', function(event){
        var tab = $('#<?= $_GET["tabid"]; ?>');

		tab.unbind('reload_google_review_history_grid').bind('reload_google_review_history_grid', function() {
			$('#<?= $_GET["tabid"]; ?>_sub_google_review_history_grid', tab.data('panel')).yiiGridView('update');
			return false;
		});

		var panel = tab.data('panel');

		event.preventDefault();
			$.get($(this).attr('href'), function(r) {
				tab.trigger('reload_google_review_history_grid');
            });
    });
});
</script>