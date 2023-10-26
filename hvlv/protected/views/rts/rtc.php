
<h1>Return to Customer Directly</h1>

<?php
$modelRtc = ImParcel::model();
$modelRtc->setAttribute('status',83);
if ( isset($_GET['ImParcel']) ) {
    $modelRtc->attributes = $_GET['ImParcel'];
}
?>
<div class="row">

    <?php echo CHtml::label('Department','forresend_dptid'); ?>
    <?php echo CHtml::dropDownList('resend_dptid','Select one', Org::dptList()); ?>

    <?php echo CHtml::label('Company','forresend_company'); ?>
    <?php echo CHtml::textField('resend_company',''); ?>

    <?php echo CHtml::label('Note','forresend_note'); ?>
    <?php echo CHtml::textField('resend_note',''); ?>

    <?php echo CHtml::submitButton($this->t('Return'), array('class' => 'returned_btn')); ?>

</div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'im-rts-rtc-parcel-grid',
    'cssFile' => false,
    'selectableRows' => 2,
    'dataProvider'=>$modelRtc->search(false),
    //'filter'=>$model,
    'columns'=>array(
        array(
            'id'=>'selectedItems',
            'class'=>'CCheckBoxColumn',
        ),
        array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->hbn."\">".$data->hbn."</a>"',),
        'ref',
        array('header' => 'Customer', 'type' => 'raw', 'value' => 'empty($data->agent)? "" : $data->agent->name'),
       array('header' => 'Received', 'type' => 'raw','value' => 'empty($data->mdata["rts_scan_date"])? "" : $data->mdata["rts_scan_date"]'),
        'note'
    ),
)); ?>

<br><br>
<h1>RTC Manifest</h1>
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'im-rts-rtc-manifest-grid',
    'cssFile' => false,
    'dataProvider'=>$model->search(),
    'columns'=>array(
        'id',
        'ref',
        'created',
        array('header' => 'Shipments', 'type' => 'raw', 'value' => '$data->countLines()'),
        array(
            'class'=>'oButtonColumn',
            'template'=>'{label} {gatepass} {invoice} ',
            'buttons'=>array
            (
                'label' => array(
                    'url' => 'Yii::app()->createURL("rts/rtclabels", array("id" => $data->id))',
                    'imageUrl' => false,
                    'label' => 'Labels',
                    'options' => array('class' => 'grid_view_btn', 'target' => '_blank'),
                    'visible' => 'true',
                ),
                'gatepass' => array(
                    'url' => 'Yii::app()->createURL("rts/rtcgatepass", array("id" => $data->id))',
                    'imageUrl' => false,
                    'label' => 'GatePass',
                    'options' => array('class' => 'grid_view_btn', 'target' => '_blank'),
                    'visible' => 'true',
                ),
                'invoice' => array(
                    'url' => 'Yii::app()->createURL("rts/rtcinvoices",array("id" => $data->id))',
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
                $('#im-rts-rtc-manifest-grid', panel).yiiGridView('update');
                $('#im-rts-rtc-parcel-grid', panel).yiiGridView('update');
            });


        // send returned button clicked
        $('.returned_btn',panel).click(function(r){
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
                    url : '<?php echo Yii::app()->createAbsoluteUrl("rts/ajaxReturned") ;?>',
                    data: data,
                    dataType: 'json',
                    success:function(resp){
                        alert(resp.msg);
                        $('#im-rts-rtc-parcel-grid', panel).yiiGridView('update');
                        $('#im-rts-rtc-manifest-grid', panel).yiiGridView('update');
                    }
                });
            }
        });

    });
</script>

