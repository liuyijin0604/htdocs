<h1><?= $this->t('View CRM Tickets'); ?> <?php echo $model->crm_id; ?></h1>


<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'crm-ticket-view',
    'cssFile' => false,
    'summaryText' => '',
    'dataProvider' => $model->search(),
    'columns' => array(
        array(
            'name' =>  'operator_id',
            'value' => '$data->getUser()'
            ),
        'time',
        'note'
    )
));
?>

<?php if ( $status == 0 ) : ?>

<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'crm-notes-form',
        'enableClientValidation'=>true,
        'action'=>$this->createUrl('crm/crmMoreNotes', array('id' => $model->crm_id)),
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
        ),
    ));
    ?>
    <h2>Add Notes</h2>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo CHtml::textArea('notes', '', array('rows'=>4, 'cols' => 60)); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Add'); ?>
        <?php echo CHtml::button('Clost Ticket',array('id' => 'btn-close-ticket','data-ticketid' => $model->crm_id) ); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
    $(function(){
        $('form#crm-notes-form').on(
            {'success': function(e, r){
                $('#crm-ticket-view').yiiGridView('update');
            },
                'reset': true
            }
        );


        // client close ticket
        $('#btn-close-ticket').click(function(){

            // post close ticket request to server
            var action_url = 'crm/closeCrm/'+ $(this).data('ticketid') + '.app';
            var notes = $('textarea#notes').val();

            var data = $('#crm-notes-form').serialize()

            $.ajax({
                url: action_url,
                dataType: 'json',
                type: 'post',
                data : data,
                success: function(r){
                    if ( r.success == 1 ) {
                        $('div.popCancel').trigger('click');

                        $("[id$='_crm-grid']").yiiGridView('update');

                    }
                },
                error: function(r, e){ alert(e); }
            });

        });
    });
</script>

<?php else : ?>

    <div class="row buttons">
        <?php echo CHtml::button('Reopen',array('id' => 'btn-reopen-ticket','data-ticketid' => $model->crm_id) ); ?>
    </div>


    <script>
        // client close ticket
        $('#btn-reopen-ticket').click(function () {

            // post close ticket request to server
            var action_url = 'crm/reopenCrm/' + $(this).data('ticketid') + '.app';

            $.ajax({
                url: action_url,
                dataType: 'json',
                type: 'post',
                success: function (r) {
                    if (r.success == 1) {
                        $('div.popCancel').trigger('click');

                        $("[id$='_crm-grid']").yiiGridView('update');
                    } else {
                        alert(r.error);
                    }
                },
                error: function (r, e) {
                    alert(e);
                }
            });

        });
    </script>
<?php endif; ?>
