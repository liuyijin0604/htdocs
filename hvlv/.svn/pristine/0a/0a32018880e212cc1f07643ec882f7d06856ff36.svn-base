<style>
    hr.line {
        width: 100%;
        margin-bottom: 10px;
        border: 1px solid rgb(160, 160, 160);
        border-radius: 5px;
    }

    .display_none {
        display: none;
    }
</style>
<h1>Bidding - <?= $objBidding->id; ?></h1>
<hr class="line" />
<div class="row">
    <?php if (!empty($model->consol)) {
        $objOrg = Org::model()->findByPk($model->consol->dpt_id)
    ?>
        <div class="col col-md-6 col-sm-12">
            <h3><?= $this->t('Pick Up'); ?></h3>
            <div class="row">
                <div class="col col-sm-6 col-xs-12">
                    <label><?= $this->t('Address'); ?></label><br />
                    <?php echo ($objOrg->address) ? $objOrg->address : ''; ?>
                </div>
            </div>
            <div class="row">
                <div class="col col-sm-6 col-xs-12">
                    <label><?= $this->t('Suburb'); ?></label><br />
                    <?php echo @$objOrg->suburb." ".@$objOrg->postcode; ?>
                </div>
            </div>
            <div class="row">
                <div class="col col-sm-6 col-xs-12">
                    <label><?= $this->t('State'); ?></label><br />
                    <?php echo ($objOrg->state) ? $objOrg->state : ''; ?>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="col col-md-6 col-sm-12">
            <h3><?= $this->t('Pick Up'); ?></h3>
            <div class="row">
                <div class="col col-sm-6 col-xs-12">
                    <label><?= $this->t('Address'); ?></label><br />
                    <?php echo ($model->cnor->address) ? $model->cnor->address : ''; ?>
                </div>
            </div>
            <div class="row">
                <div class="col col-sm-6 col-xs-12">
                    <label><?= $this->t('Suburb'); ?></label><br />
                    <?php echo @$model->cnor->suburb." ".@$model->cnor->postcode; ?>
                </div>
            </div>
            <div class="row">
                <div class="col col-sm-6 col-xs-12">
                    <label><?= $this->t('State'); ?></label><br />
                    <?php echo ($model->cnor->state) ? $model->cnor->state : ''; ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="col col-md-6 col-sm-12">
        <h3><?= $this->t('Delivery'); ?></h3>
        <div class="row">
            <div class="col col-sm-6 col-xs-12">
                <label><?= $this->t('Address'); ?></label><br />
                <?php echo ($model->cnee->address) ? $model->cnee->address : ''; ?>
            </div>
        </div>
        <div class="row">
            <div class="col col-sm-6 col-xs-12">
                <label><?= $this->t('Suburb'); ?></label><br />
                <?php echo @$model->cnee->suburb." ".@$model->cnee->postcode; ?>
            </div>
            <!-- <div class="col col-sm-6 col-xs-12">
                <label><?= $this->t('Name'); ?></label><br />
                <?php //echo $model->cnee->name ?>
            </div> -->
        </div>
        <div class="row">
            <div class="col col-sm-6 col-xs-12">
                <label><?= $this->t('State'); ?></label><br />
                <?php echo ($model->cnee->state) ? $model->cnee->state : ''; ?>
            </div>
            <!-- <div class="col col-sm-6 col-xs-12">
                <label><?= $this->t('Tel'); ?></label><br />
                <?php //echo ($model->cnee->tel) ? $model->cnee->tel : ''; ?>
            </div> -->
        </div>
        <div class="row">
            <!-- <div class="col col-sm-6 col-xs-12">
                <label><?= $this->t('Postcode'); ?></label><br />
                <?php //echo ($model->cnee->postcode) ? $model->cnee->postcode : ''; ?>
            </div> -->
            <!-- <div class="col col-sm-6 col-xs-12">
                <label><?= $this->t('Email'); ?></label><br />
                <?php //echo ($model->cnee->email) ? $model->cnee->email : ''; ?>
            </div> -->
        </div>
    </div>
</div>
<hr class="line" />
<div class="row">
    <div class="col col-md-6 col-sm-12">
        <h3><?= $this->t('Items'); ?></h3>
    </div>
</div>
<div class="row">
    <div class="col col-md-2 col-sm-4 col-xs-6">
        <label><?= $this->t('Total Packages'); ?></label><br />
        <?php echo $model->pkg; ?>
    </div>
    <div class="col col-md-2 col-sm-4 col-xs-6">
        <label><?= $this->t('Total Weight'); ?></label><br />
        <?php echo $model->weight; ?>kg
    </div>
    <div class="col col-md-2 col-sm-4 col-xs-6">
        <label><?= $this->t('Total CBM'); ?></label><br />
        <?php echo $model->cbm; ?>m<sup>3</sup>
    </div>

    <!-- <div class="col col-md-4 col-sm-4 col-xs-6">
        <label><?= $this->t('Reference No.'); ?></label><br />
        <?php echo $model->ref; ?>
    </div> -->
</div>
<br />
<div class="form-group">
    <?php echo CHtml::link($this->t('Item Detail +'), '#', ['class' => 'search-button']); ?>
    <div class="item_table" style="display:none">
        <table id="items" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th><?= $this->t('Weight'); ?></th>
                    <th><?= $this->t('Length'); ?></th>
                    <th><?= $this->t('Height'); ?></th>
                    <th><?= $this->t('Width'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $tt = 1;
                if (!empty($model->packs)) {
                    foreach ($model->packs as $i => $g) {
                        echo '<tr><td>' . ($i + 1) . '</td><td>', $g['weight'], 'kg</td><td>', $g['length'], 'cm</td><td>', $g['height'], 'cm</td><td>', $g['width'], 'cm</td></tr>';
                        $tt++;
                    }
                }
                ?>
            </tbody>
            <!-- <tfoot>
            <tr>
                <td colspan="3"><b class="pull-right">Total:</b></td>
                <th id="tot_v"></th>
                <th id="tot_qty"><?= $tt; ?></th>
            </tr>
        </tfoot> -->
        </table>
    </div>
</div>
<hr class="line" />
<div class="row">
    <div class="col col-md-6 col-sm-12">
        <h3><?= $this->t('Bidding Information'); ?></h3>

    </div>
</div>
<div class="row">
    <div class="col col-md-6 col-sm-12">
        <div class="row">
            <div class="col col-sm-6 col-xs-12">
                <label><?= $this->t('Required Delivery Date'); ?></label><br />
                <?php echo $objBidding->getBiddingDate(); ?>
            </div>
        </div>
        <div class="row">
            <div class="col col-sm-6 col-xs-12">
                <label><?= $this->t('Address Type'); ?></label><br />
                <?php echo CargoProcessBidding::$address_Types[$objBidding->address_type]; ?>
            </div>
        </div>
        <div class="row">
            <div class="col col-sm-6 col-xs-12">
                <label><?= $this->t('Unload Type'); ?></label><br />
                <?php echo CargoProcessBidding::$unloading_Types[$objBidding->unload_type]; ?>
            </div>
        </div>
    </div>

    <div class="col col-md-6 col-sm-12">
        <div class="row">
            <div class="col col-sm-6 col-xs-12">
                <label><?= $this->t('Business Hour'); ?></label><br />
                <?php echo $objBidding->getReceivbleTime(); ?>
            </div>
        </div>
        <div class="row">
            <div class="col col-sm-6 col-xs-12">
                <label><?= $this->t('Delivery Range'); ?></label><br />
                <?php echo CargoProcessBidding::$delivery_Ranges[$objBidding->delivery_range]; ?>
            </div>

        </div>
        <div class="row">
            <div class="col col-sm-6 col-xs-12">
                <label><?= $this->t('Reference Price'); ?></label><br />
                <?php echo $objBidding->getReservePrice(); ?>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <?php
    // $objOneBidding = new CargoProcessBidding('search');
    // $objOneBidding->id = $objBidding->id;

    // $columns = [
    //     ['name' => 'id', 'type' => 'raw', 'value' => '$data->id'],
    //     //['name' => 'shipment_id', 'type' => 'raw', 'value' => '$data->shipment->ref', 'htmlOptions' => array('style' => 'width: 120px')],
    //     //['header' => 'Ref', 'type' => 'raw', 'value' => '@$data->shipment->ref'],
    //     ['header' => 'Pick Up', 'value' => '@$data->getFromString()'],
    //     ['header' => 'Delivery To', 'value' => '@$data->getToString()'],
    //     //['header' => 'Cargo Type', 'value' => '@$data->getCargoType()'],
    //     'str_date',
    //     'end_date',
    //     //['header' => 'Date Period', 'value' => '@$data->getBiddingDate()'],
    //     ['header' => 'Business Hour', 'value' => '@$data->getReceivbleTime()'],
    //     ['header' => 'CBM(M³)', 'value' => '@$data->cargo_process->getTotalCBM()'],
    //     ['header' => 'Weight(Kg)', 'value' => '@$data->cargo_process->getWeight()'],
    //     ['header' => 'Forklift', 'value' => '@$data->getBiddingForlift()'],
    //     //'op_cost',
    //     ['header' => 'Reference Price', 'value' => '@$data->getReservePrice()'],
    //     ['header' => 'My Quote', 'value' => '@$data->getMyCost(' . User::getCurrentUser()->org_id . ')'],
    // ];


    // $this->widget(
    //     'application.extensions.booster.TbExtendedGridView',
    //     array(
    //         'fixedHeader' => true,
    //         'id' => 'bidding_grid_view',
    //         //'filter' => $model,
    //         'type' => 'striped bordered',
    //         'headerOffset' => 40,
    //         'responsiveTable' => true,
    //         'dataProvider' => $objOneBidding->search(true, 30, false, true),
    //         'template' => "{items}\n{pager}",
    //         'afterAjaxUpdate' => 'function(){initButtons();}',
    //         'columns' => $columns,
    //     ),
    // );
    ?>
</div>
<div class="form-group">
    <?php echo CHtml::textArea('CargoProcessBidding[note]', $objBidding->note, ['style' => 'width:480px;height:200px;', 'id' => 'note', 'disabled' => 'disabled']); ?>
</div>
<hr class="line" />
<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'shipment-form',
        'enableAjaxValidation' => false,
        'htmlOptions' => [
            'data-bit' => '1',
        ]
    ));
    ?>

    <div class="row">
        <div class="col col-md-6 col-sm-12">
            <h3><?= $this->t('My Plan'); ?></h3>
            <div class="row">
                <div class="col col-sm-6 col-xs-12">
                    <?php echo $form->labelEx($objBiddingDetail, 'choosedate', array('required' => 'required')); ?>
                    <?php echo $form->dropDownList($objBiddingDetail, 'choosedate',  $objBidding->getDay(), ['style' => 'height:27px;', 'empty' => 'Select One']); ?>
                </div>
            </div>
            <br />
            <div class="row">
                <div class="col col-sm-6 col-xs-12">
                    <?php echo $form->labelEx($objBiddingDetail, 'cost', array('required' => 'required')); ?>
                    <?php echo $form->textField($objBiddingDetail, 'cost', ['size' => 14, 'id' => 'cost']); ?>
                </div>
            </div>
            <br />
            <div class="form-group">
                <label><?= $this->t('Notes'); ?></label><br />
                <?php echo $form->textarea($objBiddingDetail, 'note', ['style' => 'width:480px;height:200px;', 'id' => 'note']); ?>
            </div>
            <div class="display_none">
                <?php echo $form->textField($objBiddingDetail, 'bid_id', ['value' => $objBidding->id]); ?>
            </div>
            <div class="form-group">
                <?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save'), ['name' => 'act_btn', 'class' => 'btn btn-primary btn-lg']); ?>
            </div>
        </div>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->


<script type="text/javascript">
    $(function() {
        var tab = $('#<?= $_GET["tabid"]; ?>');
        var panel = tab.data('panel');

        $('.search-button', panel).click(function() {
            $('.item_table', panel).toggle();
            return false;
        });
    });
</script>