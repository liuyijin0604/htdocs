
<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'ledger-input-search-form',
    'enableAjaxValidation'=>false,
)); ?>

<div class="row">
    <div class="rowcol">
        <?php echo CHtml::label('GL Code','chgcode'),
        $form->hiddenField($model['gllist'],'chgcode', array('data-ov' => ''));
        $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
            'name' => 'glcode',
            'sourceUrl' => array('chargeCode/chargeCodeSuggest'),
            'value' => '',
            'options' => array(
                'showAnim' => 'fold',
                'minLength' => 2,
                'delay' => 200,
                'autoFocus' => true,
                'select' => 'js:function(evt, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
                'change' => 'js:function(evt, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
            ),
            'htmlOptions' => array(
                'class' => 'required',
                'size' => '25',
            ),
        ));
        ?>
    </div>
    <div class="rowcol">
        <?php echo CHtml::label('Type','forledgerinputsearch'); ?>
        <?php echo CHtml::dropDownList('type','ImcoConsol',['ImcoConsol' => 'Import','ExcoConsol' => 'Export']); ?>
        <?php echo CHtml::button($this->t('Search'),array('onclick' => 'ledgerInputSearch();')); ?>
    </div>

</div>

<div style="margin-bottom: 20px;padding-bottom: 20px;"></div>

<div class="row" style="width: 85%;">
<div id="chargecode-input-view">
    <div class="grid-view">
        <table class="items">
            <thead>
            <tr>
                <th>Console</th>
                <th>GL Code</th>
                <th>Accrual Cost</th>
                <th>Real Cost</th>
                <th>Currency</th>
                <th>Exchange Rate</th>
                <th>Note</th>
            </tr>
            </thead>
            <tbody>
                <?php
                    $index = 0;
                    $rowData = '';
                    if ( !empty($model['data']) ) {
                        foreach ($model['data'] as $data) {
                            $rowData = '<tr class="odd">';
                            if ($index++ % 2 == 0) {
                                $rowData = '<tr class="even">';
                            }


                            $rowData .= '<td><lable><a href ="' . Yii::app()->createURL(lcfirst($data['console_model']) . "/update", array("id" => $data['console_id'])) . '" class="tab_link", title="' . $data['console'] . '" >' . $data['console'] . '</a></lable></td>';
                            $rowData .= '<td><lable>' . $data['code'] . '</lable></td>';
                            $rowData .= '<td><lable>' . $data['cost'] . '</lable></td>';
                            $rowData .= '<td><input name="items[' . $data['id'] . '][cost]" value="" ></td>';
                            $rowData .= '<td><select name="items[' . $data['id'] . '][currency]">';
                            foreach (Ledger::$currencies as $k => $v) {
                                if ($k == $data['currency']) {
                                    $rowData .= '<option value="' . $k . '" selected="selected">' . $v . '</option>';
                                } else {
                                    $rowData .= '<option value="' . $k . '">' . $v . '</option>';
                                }
                            }
                            $rowData .= '</select>';
                            $rowData .= '<td><input name="items[' . $data['id'] . '][rate]" value="1.0" ></td>';
                            $rowData .= '<td><input name="items[' . $data['id'] . '][note]" value="" ></td>';
                            $rowData .= '</tr>';
                            echo $rowData;
                        }
                    } else {
                        echo  '<tr class="even"><td colspan="7">No more results</td></tr>';
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<div class="row">
    <div class="rowcol">
    <?php echo CHtml::label('Date','forledgerinputsearch'); ?>
    <?php echo CHtml::textField('date',$model['date'],['class' => 'date_input']); ?>
    </div>
    <div class="rowcol">
        <?php echo CHtml::label('Invoice No.','forledgerinputsearch'); ?>
        <?php echo CHtml::textField('invoice',''); ?>

        <?php echo CHtml::button($this->t('Commit Input'),array('onclick' => 'ledgerInputCommit();')); ?>
    </div>
</div>


<?php $this->endWidget(); ?>
