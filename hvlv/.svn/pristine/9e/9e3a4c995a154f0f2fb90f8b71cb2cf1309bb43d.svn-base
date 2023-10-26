
<h1>Check In List</h1>

<div class="row" style="text-align: center;">

	<div class="form-group">
	<table id="items" class="table table-striped table-bordered" style="font-size: xx-large;">
	<tbody>

<?php foreach ( $consoles as $console ) : ?>
    <tr class="awb-item"><td><a href="<?= $this->createUrl('shipment/scan',['id' => $console->consol_id,'op' => 'checkin']); ?>"><?php echo $console->consol->awb; ?></td></a></tr>
    <?php endforeach; ?>
	</tbody>


	</table>
	</div>


    <table style="text-align: center;width:100%;">
        <tfoot>
        <tr><td><?php echo CHtml::button('Home', array('class' => 'gohome','style'=> 'text-align:center;font-size:xx-large')); ?></td></tr>
        </tfoot>
    </table>

</div>

<?php ob_start(); ?>
<script type="text/javascript">
    $(document).ready(function(){

        $('.awb-item').click(function(e){
           // alert('good');
        });

        $('.gohome').click(function(e){
            window.location.href = '/whscan';
        });

    });

</script>


<?php $this->registerJS(ob_get_clean()); ?>