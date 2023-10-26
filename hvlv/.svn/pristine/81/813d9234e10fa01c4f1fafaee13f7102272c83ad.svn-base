<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
    'links' => array(
        'AddTrack',
    ),
));
?>
<h2>Add Track Note for Console : <?php echo $model->no; ?></h2>


<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'consol-addtrack-form',
	'enableAjaxValidation'=>false,
));
?>
	<div>
        <p>Note: <br>
            <textarea  id="note" rows="4" cols="50" name="note" /> </textarea>
        </p>
	</div>


	<div class="buttons">
		<?php echo CHtml::submitButton('Add'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php ob_start(); ?>

<script type="text/javascript">
$(function(){
    $('form#consol-addtrack-form').on('success', function(e,r) {
        return true;
    }).on('submit',function(e){
        var notes = $('#note').val().trim();
        if ( notes.length <= 0 ) {
            alert('invalid tracking note');
            e.preventDefault();
            $('#note').focus();
        }
    });
});
</script>

<?php $this->registerJS(ob_get_clean(),8); ?>