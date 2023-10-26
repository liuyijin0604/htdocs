<br><br>

<h3><?=$this->t('Update Shipment');?> <?php echo $model->hbn; ?> - <i><?=$model->getStatus();?></i></h3>
<div class="form">
    <input type="hidden" id="dxt-s-hbn" value="<?php echo $model->hbn; ?>" >

    <br>
    <?php echo CHtml::label('Weight:','forme'); ?>
    <br>

    <?php echo CHtml::textField('weight',$model->weight,array('id'=>'dxt-weight')); ?>
    <?php echo CHtml::button($this->t('Submit'),array('onclick' => 'dxtsend();')); ?>

    <?php echo '<div id="show-result" style="display: none;color:#008000;" > </div>'; ?>

</div>


<script type="text/javascript">
        function dxtsend(){
            var hbn = $('#dxt-s-hbn').val();
            var weight = $('#dxt-weight').val();
            $('#show-result').hide();

            $.ajax({
                type : 'POST',
                url : '<?php echo Yii::app()->createAbsoluteUrl("dxt/ajaxWeight") ;?>',
                data: {'hbn': hbn,'w':weight},
                dataType: 'html',
                success:function(resp){
                    $('#show-result').html(resp);
                    $('#show-result').show();
                    $('#dxt-hbn').focus();
                    $('#dxt-hbn').select();
                }
            });
        }
</script>
