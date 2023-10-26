
<h1>Make an order</h1>

<div style="text-align: left;width: 100%;margin: 20px;">
    <p>
        <span style="font-size: 25px;color:#ff0000">Sorry your previous order as below is in delivering , you can't make a new order currently!</span> <br/><br/>
        <span><?php echo 'Created: ' . $order->added_datetime; ?></span> <br/>
        <span><?php echo 'Delivery: ' . $order->dispatch_date; ?></span> <br/>
        <span><?php echo 'Amount: ' .  $order->amount; ?></span> <br/>
        <span><?php echo 'Status: ' . $order->getStatus(); ?></span> <br/><br/>

        <?php
        $odata = CgOrderLine::model()->findAll('cd_order_id = :oid', [':oid' => $order->id]);
        $oDetails = '';
        foreach ($odata as $line) {
            $cgf = ConsumableGoods::model()->findByPk($line->cg_id);
            echo  '<span>' . $cgf->name . ' : ' . $line->qty . ' </span><br/>';
        }
        ?>
    </p>
</div>


<div style="margin-top: 30px;">
    <?php echo CHtml::button('Go Home', array('id' => 'gohome','style'=> 'text-align:center;font-size:xx-large')); ?>
</div>


<?php ob_start(); ?>
<script type="text/javascript">
    $(function(){
        $('#gohome').click(function(e){
            window.location.href = '/cg';
        });
    });
</script>

<?php $this->registerJS(ob_get_clean()); ?>
