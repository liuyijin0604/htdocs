<h1>Reshipping</h1>
<br>
<?php
$modelReshipped = ImParcel::model();
$modelReshipped->setAttribute('status',81);
if (!empty($_GET['RtsList'])) {
    unset($_GET['RtsList']['id']);
    $model->attributes = $_GET['RtsList'];
}
?>
<div class="row">

    <?php echo CHtml::label('Department','forresend_dptid'); ?>
    <?php echo CHtml::dropDownList('resend_dptid','Select one', Org::dptList()); ?>

    <?php echo CHtml::label('Company','forresend_company'); ?>
    <?php echo CHtml::textField('resend_company',''); ?>

    <?php echo CHtml::label('Note','forresend_note'); ?>
    <?php echo CHtml::textField('resend_note',''); ?>

    <?php echo CHtml::submitButton($this->t('Send Aupost'), array('class' => 'aupost_btn')); ?>

</div>

<?php echo $this->renderPartial('reshipped', array('model'=>$modelReshipped)); ?>
<br><br>
<h1>Reshipping Manifest</h1>
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'im-rts-reshipping-manifest-grid',
    'cssFile' => false,
    'filter' => $model,
    'dataProvider'=>$model->search(),
    'columns'=>array(
        'id',
        array('name' => 'ref', 'header' => 'Old Ref.'),
        'created',
        array('header' => 'Shipments', 'type' => 'raw', 'value' => '$data->countLines()'),
        array('header' => 'Location', 'value' => '$data->getRTS()'),
        array('header' => 'New Ref.', 'value' => '$data->getNewRef()'),
        array('header' => 'Status', 'value' => '$data->getNewRefStatus()'),
        array(
            'class'=>'oButtonColumn',
            'template'=>'{label} {gatepass} {invoice} ',
            'buttons'=>array
            (
                'label' => array(
                    'url' => 'Yii::app()->createURL("rts/labels", array("id" => $data->id))',
                    'imageUrl' => false,
                    'label' => 'Labels',
                    'options' => array('class' => 'grid_view_btn', 'target' => '_blank'),
                    'visible' => 'true',
                ),
                'gatepass' => array(
                    'url' => 'Yii::app()->createURL("rts/gatepass", array("id" => $data->id))',
                    'imageUrl' => false,
                    'label' => 'GatePass',
                    'options' => array('class' => 'grid_view_btn', 'target' => '_blank'),
                    'visible' => 'true',
                ),
                'invoice' => array(
                    'url' => 'Yii::app()->createURL("rts/invoices",array("id" => $data->id))',
                    'imageUrl'=>false,
                    'label' => 'Invoice',
                    'options' => array('class' => 'grid_view_btn tab_link'),
                    'visible' => 'true',
                ),
            ),
        ),
    ),
)); ?>

<script type="text/javascript">
    $(function(){
        var tab = $("#<?=$_GET['tabid'];?>");
        var panel = tab.data('panel');
        tab.on('onOpen', function(){
            $('#im-rts-reshipping-manifest-grid', panel).yiiGridView('update');
            $('#im-rts-reshipping-parcel-grid', panel).yiiGridView('update');
        });

        // send Aupost button clicked
        $('.aupost_btn',panel).click(function(r){
            // get selected RTS shipments
            var selected = [];
            $('.select-on-check').each(function(){
                if ( $(this).is(':checked') ) {
                    selected.push($(this).val());
                }
            });
            if ( selected.length <= 0 ) {
                alert('Please select at least one shipment');
            } else {
                var note = $('#resend_note',panel).val();
                var company = $('#resend_company',panel).val();
                var dptid = $('#resend_dptid',panel).val();
                var data = { 'sids' : selected ,'note' : note,'company' : company,'dptid' : dptid};
                $.ajax({
                    type : 'POST',
                    url : '<?php echo Yii::app()->createAbsoluteUrl("rts/ajaxResend") ;?>',
                    data: data,
                    dataType: 'json',
                    success:function(resp){
                        alert(resp.msg);
                        $('#im-rts-reshipping-parcel-grid', panel).yiiGridView('update');
                        $('#im-rts-reshipping-manifest-grid', panel).yiiGridView('update');
                    }
                });
            }
        });

    });
</script>