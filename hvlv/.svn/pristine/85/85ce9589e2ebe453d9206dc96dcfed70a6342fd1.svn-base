<style type="text/css">

.row div
{
    display: inline;    
}
</style>

<?php
$user=User::model()->findByPk(Yii::app()->user->id);
$org=$user->org;
$rs= ImportChargeCode::model()->findAll('status=1 AND org_id=:oid', [':oid'=>$user->org_id]);
$this->widget('zii.widgets.CBreadcrumbs', [
    'homeLink'=>CHtml::link('Home', ['site/index']),
    'links' => [
        'Value Added Service',
    ],
]);
?>
<h2><?=$this->t('Value Added Service');?> &nbsp;
</h2>
<div class="form">
<?php
$user=User::model()->findByPk(Yii::app()->user->id);
$org=$user->org;
$rs= ImportChargeCode::model()->findAll('status=1 AND org_id=:oid', [':oid'=>$user->org_id]);

$form=$this->beginWidget('CActiveForm', [
    'id'=>'value-added-service-form',
    'enableAjaxValidation' => false,
]); ?> 
    </br>
    <?php echo CHtml::hiddenField('user_id', $user->id); ?>
    <?php echo CHtml::hiddenField('org_id', $org->id); ?>
    <div class="form-group" style="width: 80%">        
        <div style="display: inline-block;">
             <?php echo CHtml::label('Address Validation', 'Address Validation')?>
             <div class="form-group">
                <label><input type="radio" name="address_valid_active" id="addrvalid_inactive" value="0" <?php echo ($org->extra['address_valid_active']=='0')?'checked':'' ?>/> Inactive </label> &nbsp; <label><input type="radio" name="address_valid_active" id="addrvalid_active" value="1" <?php echo ($org->extra['address_valid_active']=='1')?'checked':'' ?>/> Active</label>
            </div>
             <?php echo CHtml::dropDownList('address_validation', $org->extra['address_valid'], org::$addressValidation, ['class'=>'form-control', 'disabled' => ($org->extra['address_valid_active']=='0')?true:false])?>  
        </div>
        <div style="display: inline-block;padding:10px;">
            <p style="float: right;">Service Fee：</br>Detection: $50/month </br>Correction: $100/month</p>
        </div>
        <div style="display: inline-block;padding:10px;">
            <p style="float: right;">Notice：</br>You can only change the service after </br>one month subscription or send us email.</p>
    </div>

    </br>
    </br>
    </br>
    </br>
    <div class="form-group buttons">
        <button class="btn btn-primary btn-lg" id="save_btn"><?=$this->t('save');?></button>
    </div>

<?php $this->endWidget(); ?>

</div>

</br>

<script type="text/javascript">
$(function(){
        $('#result-output').on('load',function(){
         posApp.btnLoading($('button[type=submit]', $('#manifest-form')),true);
         $('#manifest-grid').yiiGridView('update');
        });

        $('input:radio').click(function() {
           if ($('#addrvalid_inactive').is(":checked")) {
              $("#address_validation").prop("disabled", true);
           } 
           else if($('#addrvalid_active').is(":checked")) {
              $("#address_validation").prop("disabled", false);  
           }
         });

        $('#save_btn').on('mousedown', function(){
            <?php $url = $this->createUrl('tools/saveValueAddedService');?>
            var form = new FormData(document.getElementById("value-added-service-form"));
            $.ajax({
                     url: '<?=$url?>',
                     type: "post",
                     data: form,
                     processData: false,
                     contentType: false,             
                     success: function(r) {
                        response = JSON.parse(r);
                        if(response.done==true){
                            alert("Done");
                        }
                        else{
                            alert(response.msg);
                        }

                       },
                     error: function(e) {
                         console.log(e);
                     }
                 });
            });
});
</script>
