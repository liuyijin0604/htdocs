<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'marketing-email-group-form',
        'enableClientValidation' => true,
        'action' => $this->createUrl('marketingTool/selectGroup', array('id' => $model->id)),
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),
    ));
    ?>
    <h2>Select Group To Send Email</h2>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php
        $strSql = 'select distinct `group` from marketing_address';
        $listRows = Yii::app()->db->createCommand($strSql)->queryAll();
        if (!empty($listRows)) {
            echo '<div class="row rowcol rowleft">';
            echo CHtml::label('Group', 'group');
            $listGroup = [];
            foreach ($listRows as $objRow) {
                $listGroup[$objRow['group']] = $objRow['group'];
            }
            $listSelect = [];
            if (!empty($model->mdata['Group'])) {
                $listSelect = $model->mdata['Group'];
            }
            echo CHtml::checkBoxList('group', $listSelect, $listGroup, ['labelOptions' => ['class' => 'radio_label'], 'separator' => '&nbsp;&nbsp']);
            echo '</div><br/><br/>';
        }
        ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Save', array('id' => 'save_button')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
    $(function() {
        var win = $('#jqmw_<?= $_GET["tabid"]; ?>');
        $('form#marketing-email-group-form', win).on({
            'success': function(e, r) {
                $(win).modal('hide');
            },
            'reset': true
        });
    });
</script>