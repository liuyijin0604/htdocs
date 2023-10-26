<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'links' => array(
		'Manage Manifest',
	),
));

?>

<style type="text/css">
    #manifest-div{
        margin-top:20px;
    }
    #manifest-div span {
        font-weight: bold;
        font-size: 30px;
    }
    #new-shipment-input{
        margin-top: 20px;
    }
    #warning{
        display:none;
        margin: 10px 0;
        border: 1px solid;
        padding:15px 20px;
        font-size: 14px;
        background: #fe0;
    }
</style>


    <div id="manifest-div" >Manifest ID:<span><?= $manifest->id; ?></span> , Total Items: <span> <?= $manifest->totPacks(); ?> </span> , Total Weight: <span> <?= $manifest->totWeight(); ?> </span>kg</div>

<div id="new-shipment-input">
    <input type="hidden" id="manifest-id" name="manifest_id" value="<?= $manifest->id; ?>">
   Add New Shippment ->  Barcode : <input type="text" name="barcode" id="add-new-shipment">
</div>

<?php
$form=$this->beginWidget('CActiveForm', array(
    'id'=>'update-manifest-weight',
    'action' => $this->createUrl('manifest/updatew'),
    'enableAjaxValidation'=>false,
    'htmlOptions' =>[
        'data-bit' => '3',
    ]
)); ?>

    <div class="form-group" style="margin-top: 40px;">
        <label for="manifest-weight">Data File - <small>.xlsx File</small></label>
        <input type="file" name="manifest_weight" id="manifest-weight" />
    </div>
    <div id="update-w-result"></div>
    <div class="form-group buttons">
        <button class="btn btn-primary btn-lg" id="upload_btn" type="submit"><?=$this->t('Update Weight in Bulk');?></button>
    </div>

    <?php $this->endWidget(); ?>

    <div class="ims-manifest-export">
        <a href="<?=$this->createUrl('manifest/export',array('mid'=>$manifest->id));?>" class="export_search" id="ims-manifest-export-btn" target="_blank" > Export</a>
        <a class=" ajax-link" href="<?=$this->createUrl('shipment/create',array('man_id'=>$manifest->id,'type'=>'manifest'));?>"><br />New Shipment</a>
    </div>
       


<div id="warning">
</div>

<?php
$org = Org::model()->findByPk(Yii::app()->user->org);
$this->widget('application.extensions.booster.TbExtendedGridView', array(
        'id' => 'ims-manifest-list',
	'fixedHeader' => true,
	'headerOffset' => 40,
	'type' => 'striped',
	'dataProvider' => $model->search(true, empty($org->extra['pager_size'])? 20 : $org->extra['pager_size']),
	'responsiveTable' => true,
	'template' => "{summary}\n{items}\n{pager}",
	'filter'=>$model,
	'selectableRows' => 2,
	'enableSorting' => false,
	'bulkActions' => array(
		'align' => 'left',
		'actionButtons' => array(
			array(
				'id' => 'bulk-print',
				'buttonType' => 'button',
				'context' => 'primary',
				'size' => 'small',
				'label' => 'Bulk Print',
				'click' => 'js:function(values){
                                        window.open("../../shipment/bulkPrint/"+values.join(",")+"/bulk.pdf");
				}'
				),
		),
		'checkBoxColumnConfig' => array(
			'name' => 'id'
		),
	),
	'columns' => array(
		array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a target=\"_blank\" href=\"".Yii::app()->createURL("ims/shipment/update", array("id" => $data->id,"man_id"=>"'.$manifest->id.'" ))."\" class=\"ajax-link\" >".$data->hbn."</a>"'),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList(get_class($model).'[status]', $model->status, $this->t($model->statusList()), array('prompt' => $this->t('All'), 'class' => 'form-control')),),
		array('name' => 'weight'),
                'ref',
               'cref',
		array('name' => 'cnor_name', 'value' => 'empty($data->cnor)? "" : $data->cnor->name', 'visible' => $type =='im'),
		array('name' => 'cnor_tel', 'value' => 'empty($data->cnor)? "" : $data->cnor->tel', 'visible' => $type =='im'),
		array('name' => 'cnee_name', 'value' => 'empty($data->cnee)? "" : $data->cnee->name',),
		array('name' => 'cnee_tel', 'value' => 'empty($data->cnee)? "" : $data->cnee->tel',),
		'state',
		array(
			'class'=>'application.extensions.booster.TbButtonColumn',
			'template'=>'{print} &nbsp; {remove} &nbsp; ',
			'header' => 'Actions',
			'buttons'=>array(
				'remove' => array(
					'visible'=>'$data->canBeDelete()',
                    'icon' => 'minus',
					'url' => '$data->hbn',
					'options' => array( 'data-hbn' => '$data->hbn', 'class' => 'remove-item-link', 'label'=>$this->t('Remove'), 'title' => 'Remove'),
				),
				'print' => array(
					'visible'=>'true',
					'icon' => 'print',
					'url' => 'Yii::app()->createUrl("ims/shipment/print", ["id" => $data->id])',
					'options' => array('target' => '_blank', 'class' => 'print-label-link', 'label'=>$this->t('Print'), 'title' => 'Print'),
				)
			),
		),
	),
)
);

?>

<?php ob_start(); ?>
<script type="text/javascript">
$(function(){

    $('#update-manifest-weight').on('success', function(e,r) {
        var rdiv = $('#update-w-result');
        rdiv.empty();
        if (r.done == true) {
            $('form#update-manifest-weight').resetForm();
            $('#ims-manifest-list').yiiGridView('update');
            rdiv.append('<h4>Result:</h4><p class="green" style="font-weight:bold;">Update successfully</p>');
        } else {
            rdiv.append('<h4>Errors:</h4><p class="red" style="font-weight:bold;">'+r.msg+'</p>');
        }
        posApp.btnLoading($('button[type=submit]', this), true);
    }).on('submit', function(){
        if($('#manifest-weight').val() == ''){
            alert('Please select a file');
            return false;
        }
    });;
    <?php if($isWDT):?>
        $(document).off('click','.print-label-link').on('click','.print-label-link',function(e){
            e.preventDefault();
            var href = $(this).attr('href');
            var ppwd = prompt('打印拆单单号前请输入密码，拆单单号不可用于贴箱清关，请注意');
            if (ppwd) {
                data= {'ppwd':ppwd};
                $.ajax({
                    type : 'POST',
                    url : '<?php echo Yii::app()->createAbsoluteUrl("ims/shipment/authPassword") ;?>',
                    data: data,
                    dataType: 'json',
                    success:function(r){
                        if ( r.success == 1 ) {
                           window.open(href+"?ppwd="+encodeURIComponent(ppwd));
                        } else {
                            alert("wrong password");
                        }
                    }
                });

            }
        });
    <?php endif;?>

    $(document).off('click','.remove-item-link').on('click','.remove-item-link',function(e){
        e.preventDefault();
        var barcode = $(this).attr('href');
        if ( confirm('Are you sure remove the shipment?') ) {
            var data = {};
            data['hbn'] = barcode;
            data['mid'] = $('#manifest-id').val();

            $('#warning').hide();

            $.ajax({
                type : 'POST',
                url : '<?php echo Yii::app()->createAbsoluteUrl("manifest/ajaxRemoveShipment") ;?>',
                data: data,
                dataType: 'json',
                success:function(r){
                    if ( r.success == 1 ) {
                        // refresh data
                        location.href = r.url;
                    } else {
                        var msg = '<div>' + r.msg + '</div>';
                        var elm = $(msg);
                        $('#warning').html('');
                        $('#warning').prepend(elm.fadeIn());
                        $('#warning').show();
                    }
                }
            });
        }
    });


    $('#add-new-shipment').keydown(function(e){
        if (e.keyCode == 13 ) {
            var barcode = $(this).val();
            if ( barcode.length > 0 ) {
                var data = {};
                data['hbn'] = barcode;
                data['mid'] = $('#manifest-id').val();
                $('#warning').hide();
                $.ajax({
                    type : 'POST',
                    url : '<?php echo Yii::app()->createAbsoluteUrl("manifest/ajaxAddShipment") ;?>',
                    data: data,
                    dataType: 'json',
                    success:function(r){
                        if ( r.success == 1 ) {
                            // refresh data
                            location.href = r.url;
                        } else {
                            $('#add-new-shipment').select();
                            $('#add-new-shipment').focus();

                            var msg = '<div>' + r.msg + '</div>';
                            var elm = $(msg);
                            $('#warning').html('');
                            $('#warning').prepend(elm.fadeIn());
                            $('#warning').show();

                        }
                    }
                });
            }
        }
    });
});
</script>

<?php $this->registerJS(ob_get_clean()); ?>