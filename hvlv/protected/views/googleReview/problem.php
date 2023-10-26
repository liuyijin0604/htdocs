<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'google-review-problem-form',
        'enableClientValidation' => true,
        'action' => $this->createUrl('googleReview/problem', array('id' => $model->id)),
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),
    ));
    ?>
    <h2>Problems</h2>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo CHtml::textArea('problem', $model->problem, array('rows' => 4, 'cols' => 60)); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Save', array('id'=>'save_button')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
    $(function() {
        var win = $('#jqmw_<?= $_GET["tabid"]; ?>');
        $('form#google-review-problem-form', win).on({
            'success': function(e, r) {
                $(win).modal('hide');
            },
            'reset': true
        });

        var tab = $('#<?= $_GET["tabid"]; ?>');

        tab.unbind('reload_google_review_grid').bind('reload_google_review_grid', function() {
            $('#<?= $_GET["tabid"]; ?>_sub_google_review_grid', tab.data('panel')).yiiGridView('update');
            return false;
        });

        var panel = tab.data('panel');

        $(panel).on('click', ' #save_button', function(event) {
            event.preventDefault();
            if (confirm("Are you Confirm?")) {
                $.get($(this).attr('href'), function(r) {
                    myApp.notice("Done!");
                    tab.trigger('reload_google_review_grid');
                });
            }
        });
    });
</script>