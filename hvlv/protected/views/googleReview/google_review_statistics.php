<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'google-review-statistics-form',
        'enableClientValidation' => true,
        'action' => $this->createUrl('googleReview/addWeeklyStatistics', array('id' => $model->id)),
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),
    ));
    ?>
    <h2>Real Google Review Data</h2>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo CHtml::label('Total Number', 'total'); ?>
        <?php echo CHtml::textField('total', '', array('id'=>'total')); ?>
    </div>

    <div class="row">
    <?php echo CHtml::label('Rating', 'rating'); ?>
        <?php echo CHtml::textField('rating', '', array('id'=>'rating')); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Save', array('id'=>'save_button')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
    $(function() {
        var win = $('#jqmw_<?= $_GET["tabid"]; ?>');
        $('form#google-review-comment-form', win).on({
            'success': function(e, r) {
                $(win).modal('hide');
            },
            'reset': true
        });
    });
</script>