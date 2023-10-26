<h3><?=$this->t('Booking Slot Management');?></h3>
<br />
<br />
<div id="depot-list-tabs">
    <ul>
        <?php
            foreach($depotList as $key => $tab) {
                $href = strpos($tab, '/') === false ? $this->createUrl('pickupBookingSlot/getSlots', array('id' => $key, 'tab' => $tab, 'tabid' => $_GET['tabid'])): $tab;
                echo '<li><a href="'.$href.'">'.$tab.'</a></li>';
            }
        ?>
    </ul>
</div>

<script type="text/javascript">
$(function(){
    var tab = $('#<?=$_GET["tabid"];?>');
    $('#depot-list-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['id'])? 0 : $_GET['id']; ?>, load: function(event,ui){
        myApp.ajaxifyForm(this);
    }});
});
</script>