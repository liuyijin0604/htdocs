<div class="form">

<p>Barcode: <input id="dxt-hbn" type="text" size="15" name="hbn" />
    <?php echo CHtml::button($this->t('Submit'),array('onclick' => 'getshipment();','id' => 'bc-submit')); ?>
</p>
</div>
<div id="dxt-shipment-data"></div>

<script type="text/javascript">

    var tab = $('#<?=$_GET["tabid"];?>');
    var panel = tab.data('panel');

        function getshipment(){
            var hbn = $('#dxt-hbn',panel).val();
            if ( hbn.length <= 0  ) {
                alert('please type an valid barcode');
                return;
            }

            $.ajax({
                type : 'POST',
                url : '<?php echo Yii::app()->createAbsoluteUrl("dxt/ajaxShipment") ;?>',
                data: {'hbn': hbn},
                dataType: 'html',
                success:function(resp){
                    $('#dxt-shipment-data',panel).html(resp);
                }
            });
        }

        $(document).ready( function(e){

            $(document).on('keypress','#dxt-hbn',function(e){


                var code = (e.keyCode ? e.keyCode : e.which);

                if ( code == 13 ) {
                     $('#bc-submit',panel).trigger('click');
                }
            });

        });

</script>