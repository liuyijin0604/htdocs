<?php
    $wmsQuote = new WmsOrgQuote();
?>

<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'org-quote-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
        ),
    ));
    ?>

    <div class="row">
        <div class="rowcol">
            <?php echo CHtml::label('Note:','for-org-quote'); ?>
            <?php echo CHtml::textField('note',''); ?>
        </div>
        <div class="rowcol">
            <?php echo CHtml::label('Valid From','for-org-quote'); ?>
            <?php echo CHtml::textField('vfrom',$wmsQuote->vfrom, ['size' => '12', 'class' => 'date_input']); ?>
        </div>
        <div class="rowcol">
            <?php echo CHtml::label('Valid To','for-org-quote'); ?>
            <?php echo CHtml::textField('vto', $wmsQuote->vto, ['size' => '12', 'class' => 'date_input']); ?>
        </div>
        <div class="rowcol" style="margin-top: 18px;">
            <?php echo $form->radioButtonList($wmsQuote,'status', WmsOrgQuote::$states, array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp')); ?>
        </div>
        <input type="hidden" name="type" value="<?=$type?>"/>
    </div>

    <div class="row" style="margin-top: 10px;">

        <div style="width: 180px;font-weight: bold;margin-bottom: 16px;font-size: 16px;margin-top: 10px;"><span>Quote Details</span></div>

        <?php
            $quotes = $type==1?WmsOrgQuote::$newChgcodes:WmsOrgQuote::$chgcodes;
            $index = 0;
            foreach ($quotes as $k => $quote ) {
                if ($quote == 'sep') {
                    echo '</div><div class="row" style="margin-bottom: 8px;">';
                    continue;
                }
                if ( $k % 1000 == 0 ) {
                    if ( $index > 0 ) echo '</div>';
                    echo  '<div class="row" style="margin-bottom: 8px;"><label style="color: blue;">'. $quote . '</label>';
                    $index++;
                } else {
                    if ($k == WmsOrgQuote::QUOTE_PALLET_STORAGE_WEEK || $k == WmsOrgQuote::QUOTE_CBM_STORAGE_WEEK) {
                        if ($k == WmsOrgQuote::QUOTE_PALLET_STORAGE_WEEK) {
                            $flag = isset($model->mdata[WmsOrgQuote::QUOTE_PALLET_STORAGE_WEEK]) && $model->mdata[WmsOrgQuote::QUOTE_PALLET_STORAGE_WEEK] == '' && isset($model->mdata[WmsOrgQuote::QUOTE_CBM_STORAGE_WEEK]) && $model->mdata[WmsOrgQuote::QUOTE_CBM_STORAGE_WEEK] != '';
                            echo '<div class="rowcol" style="width:220px; margin-top:5px;">';
                            echo CHtml::label('Storage / Week:', 'for-org-quote');
                            if ($flag)
                                echo CHtml::textField('storage', $model->mdata[WmsOrgQuote::QUOTE_CBM_STORAGE_WEEK]);
                            else if (isset($model->mdata[WmsOrgQuote::QUOTE_PALLET_STORAGE_WEEK]) && $model->mdata[WmsOrgQuote::QUOTE_PALLET_STORAGE_WEEK])
                                echo CHtml::textField('storage', $model->mdata[WmsOrgQuote::QUOTE_PALLET_STORAGE_WEEK]);
                            else
                                echo CHtml::textField('storage', '');
                            echo '</div><div class="rowcol" style="width:220px; margin-top:5px;">';
                            echo CHtml::label('Storage Type:', 'for-org-quote');
                            echo CHtml::radioButtonList('storage_type', $flag ? 'cbm' : 'pallet', array('pallet' => 'Pallet', 'cbm' => 'CBM'), array('separator' => '&nbsp;&nbsp;', 'labelOptions' => array('class' => 'radio_label', 'style' => 'display: inline;'), 'style' => 'margin-top:3px;'));
                            echo '</div>';
                        }
                    } else if ($k == WmsOrgQuote::QUOTE_PACKING_ORDER_STATUS) {
                        echo '<div class="rowcol" style="width:220px; margin-top:5px;">';
                        echo CHtml::label($quote[0] . ':', 'for-org-quote');
                        echo CHtml::radioButtonList(WmsOrgQuote::QUOTE_PACKING_ORDER_STATUS, (isset($model->mdata[$k]) && $model->mdata[$k]) ? 1 : 0, array(0 => 'Disable', 1 => 'Active'), array('separator' => '&nbsp;&nbsp;', 'labelOptions' => array('class' => 'radio_label', 'style' => 'display:inline;'), 'style' => 'margin-top:3px;'));
                        echo '</div>';
                    } else if ($k == WmsOrgQuote::QUOTE_PACKING_ORDER_EXTRA) {
                        $checklist = isset($model->mdata['extra_packing']) ? $model->mdata['extra_packing'] : [];
                        echo '<div class="rowcol" style="width:220px; margin-top:5px;">';
                        echo CHtml::label($quote[0] . ':', 'for-org-quote');
                        echo CHtml::checkBoxList('extra_packing', $checklist, array('box' => 'Box', 'wrapper' => 'Wrapper'), array('separator' => '&nbsp;&nbsp;', 'labelOptions' => array('class' => 'radio_label', 'style' => 'display:inline;'), 'style' => 'margin-top:3px;'));
                        echo '</div>';
                    } else {
                        echo '<div class="rowcol" style="width:220px; margin-top:5px;">';
                        echo CHtml::label($quote[0] . ':', 'for-org-quote');
                        $quoteValue = '';
                        if ( isset($model->mdata[$k]) ) $quoteValue = $model->mdata[$k];
                        echo CHtml::textField($k, $quoteValue);
                        echo '</div>';
                    }
                }
            }
        ?>

    </div>

    <div class="row buttons">
        <?php echo CHtml::hiddenField('orgid',$model->id); ?>
        <?php echo CHtml::submitButton('Create'); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
    $(function(){
        var win = $('#jqmw_<?=$_GET["tabid"];?>');
        $('form#org-quote-form', win).on('success', function(e, r){
            win.data('opener').trigger('onOpen');
            win.jqmHide();
        });
    });
</script>