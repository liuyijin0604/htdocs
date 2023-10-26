<?php
$this->widget('zii.widgets.CBreadcrumbs', [
    'homeLink'=>CHtml::link('Home', ['site/index']),
    'links' => [
        'Import Shipments',
    ],
]);
?>
<h2><?=$this->t('Upload Shipments');?> &nbsp;
</h2>
<div class="form">
<?php
$user=User::model()->findByPk(Yii::app()->user->id);
$rs= ImportChargeCode::model()->findAll('status=1 AND org_id=:oid', [':oid'=>$user->org_id]);
$chargecodeInfo=[];
foreach ($rs as $r) {
    if(!empty($r->description))
    {
        $chargecodeInfo[$r->chargecode]=$r->chargecode."(".$r->description.")";
    }else
    {
        $chargecodeInfo[$r->chargecode]=$r->chargecode;
    }
}
$form=$this->beginWidget('CActiveForm', [
    'id'=>'manifest-form',
    'htmlOptions'=>['target'=>'result-output','class'=>'ifrm-form','enctype' =>'multipart/form-data'],
    'action' => $this->createUrl('tools/manifest'),
]); ?> 
    <div class="form-group" style="max-width: 10em;">
         <?php echo CHtml::label('Chargecode', 'chargecode')?>
         <?php echo CHtml::dropDownList('chargecode', '', $chargecodeInfo, ['class'=>'form-control','prompt'=>"Choose One"])?>  
    </div>
    <?php if(in_array(User::currentUserOrgId(),[Org::ORGID_CLIENT_AUSTWAY,Org::ORGID_CLIENT_AOCHEN,4045,3103,4165,3527,4213,4534,4696])):?>
    <div class="form-group" style="max-width: 10em;">
         <?php echo CHtml::label('Import Type', 'Import Type')?>
         <?php echo CHtml::dropDownList('import_type', 1, [1=>'整单',2=>'整单+分单',3=>'导入箱数据'], ['class'=>'form-control'])?>  
    </div>
    <?php else:?>
    <div class="form-group" style="max-width: 10em;">
         <?php echo CHtml::label('Import Type', 'Import Type')?>
         <?php echo CHtml::dropDownList('import_type', 1, [1=>'整单',3=>'导入箱数据'], ['class'=>'form-control'])?>  
    </div>
     
    <?php endif;?>
    <div class="form-group" style="max-width: 10em;">
        <?php echo CHtml::label('currency','manifest'); ?>
        <?php echo CHtml::dropDownList('currency','AUD', array(
            'AUD' => 'AUD',
            'USD' => 'USD'
        ), array('class'=>'form-control')); ?>
    </div>

    <div class="form-group">
        <label for="manifest">Data File - <small>.xlsx File</small> (<a href="../TLA_Import_Template.xlsx" target="_blank">Get template file</a>)</label>
        </br>
        <?php if(in_array(User::currentUserOrgId(),[Org::ORGID_CLIENT_AUSTWAY,Org::ORGID_CLIENT_AOCHEN,4045,3103,4165,3527,4213,4534,4696])):?>
           <label for="manifest">Data File - <small>.xlsx File</small> (<a href="../TLA_Import_Template_for_3PL.xlsx" target="_blank">Get template file for 3PL shipments</a>)</label>
            </br>
        <?php endif;?>
        <label for="manifest">Data File - <small>.xlsx File</small> (<a href="../TLA_Import_Packages_Template.xls" target="_blank">Get Packages template file</a>)</label>
        <input type="file" name="manifest" id="manifest" />
    </div>
    <div class="form-group">
        <label><input type="radio" name="action" value="create" checked/> Create </label> &nbsp; <label><input type="radio" name="action" value="update" /> Update</label> &nbsp; <label><input type="radio" name="action" value="createAndSkip" /> Create and Skip Error Shipments</label>
    </div>
    <div id="result"></div>
    <div class="form-group buttons">
        <button class="btn btn-primary btn-lg" id="upload_btn" type="submit"><?=$this->t('Upload');?></button>
    </div>

<!--    <div class="form-group">
        <a href="http://www.pcaexpress.com.au/sac/" target="_blank">点击查看禁止申报品名清单</a>
    </div>-->
<?php $this->endWidget(); ?>

    <div class="account-credit-info" >
        <b>Credit Information</b> <br>
        --------------------------- <br>

       <!-- Limits : <span class="credit-limits"><?php echo $credit['limit']; ?></span> <span class="currency"><?php echo $credit['currency']; ?> <span>  <br>-->
        Balance : <span class="credit-balance <?php if ($credit['balance_warning']) {
        echo 'warning' ;
    } ?>"><?php echo $credit['percent']>0.99999?"100%":number_format($credit['percent']*100,2,'.','')."%"; ?> </span> <br>
        <br>
        Terms : <span class="credit-terms"><?php echo $credit['terms']; ?></span> Days <br>
      
    </div>
</div>
<iframe id="result-output"  name="result-output" style="border: 1px solid black; margin: 10px; width: 1000px;"></iframe>
<div class="form-group buttons">
        <button class="btn btn-primary btn-sm new_manifest" id="upload_btn" ><?=$this->t('New Manifest');?></button>
</div>

<div style="position:relative">
<div class="dropdown" style="position:absolute; right: 0;">
<a href="<?=Yii::app()->createUrl('ims/shipment/exportOrgTLDWithoutPackages', ['type' => 'dlv']);?>" arget="_blank"><span class="glyphicon glyphicon-stats"></span> Export TLD Without Packages Information</a>
</div>
</div>
</br>


<?php $this->widget('zii.widgets.grid.CGridView', [
    'id'=>'manifest-grid',
    'cssFile' => false,
    'dataProvider'=>$model->search(true,5),
    'filter'=>$model,
    'columns'=>[
        ['header' => 'search hbn/ref/cref','filter'=>CHtml::textField('Manifest[pref]',$model->pref),'value'=>''],
        ['name' => 'id', 'header' => 'ID'],
        ['name' => 'file', 'header' => 'File', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("tools", array( "downloadManifest" => $data->id))."\" class=\"tab_link\" title=\"".$data->getFileName() . "\">" . $data->getFileName() ."</a>" ',],
        ['name' => 'status', 'header' => 'Status', 'value' => '$data->getStatus()', 'filter'=>CHtml::dropDownList('Manifest[status]', $model->status, $this->tarray(Manifest::$states), ['prompt'=>$this->t('All')]),],
        ['header' => 'Charge Code', 'value' => 'empty($data->mdata["chargecode"])? "" : $data->mdata["chargecode"]'],
        ['name' => 'created', 'header' => 'Created'],
        ['name' => 'allshipments', 'header' => 'Shipments','type' => 'raw','value' => '$data->totPacks()'],
        ['header' => 'Packages Details','type' => 'raw','value' => '$data->showPacksWithoutPackages()'],
        [
            'class'=>'oButtonColumn',
            'template'=>'{Parcel Labels} {Manage}',
            'buttons'=>[
                'Parcel Labels' => [
                    'url'=> 'Yii::app()->createURL("tools", array("downloadLabels" => $data->id))',
                    'visible'=>'$data->isWDT()?false:true',
                    'imageUrl'=>false,
                    'options' => ['class' => 'tab_link grid_print_btn',  'label'=>$this->t('Parcel Labels'),'target' => '_blank'],
                ],
                'Manage' => [
                    'url'=> 'Yii::app()->createURL("manifest", array("manage" => $data->id))',
                    'imageUrl'=>false,
                    'options' => ['class' => 'tab_link grid_edit_btn',  'label'=>$this->t('Manage'),'target' => '_blank'],
                ],
            ],
        ],
    ],
]); ?>
<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
        $('#result-output').on('load',function(){
         posApp.btnLoading($('button[type=submit]', $('#manifest-form')),true);
         $('#manifest-grid').yiiGridView('update');
        });
    $('#manifest-form').on('submit', function(){
        if($('#manifest').val() == ''){
            alert('Please select a file');
            return false;
        }
         $('#result-output').contents().find("body").html('');
          posApp.btnLoading($('button[type=submit]', $('#manifest-form')));
       });
       $('.new_manifest').on('click',function(){
           if(confirm('Are you sure to create a new Manifest?')){
               $.get('newManifest',function(r){
                   if(r=='done'){
                         $('#manifest-grid').yiiGridView('update');
                   }
               });
          }
       });
});
</script>
<?php $this->registerJS(ob_get_clean(), 8); ?>
