<center>
    <h1><?= ucfirst(@Yii::app()->session['scan_warehouse']) ?> Print Pallet Label</h1>
</center>

<div id="parcel-tabs">
    <ul class="nav nav-tabs">
        <?php
        $tabs = [];

        $tabs[] = ['generate', $this->t('Generate Label'), true];
        //$tabs[] = ['inspection', Yii::t('whscan','Inspection'), true];
        $tabs[] = ['reprint', Yii::t('whscan', 'Reprint Label'), true];

        foreach ($tabs as $key => $tab) {
            $href = strpos($tab[0], '/') === false ? $this->createUrl('warehouseProcess/label', array('tab' => $tab[0], "tabid" => "tab" . $key)) : $tab[0];
            echo '<li><a href="' . $href . '" class="active">' . $tab[1] . '</a></li>';
        }
        ?>
    </ul>
</div>

<script type="text/javascript">
$(function(){
  $('#parcel-tabs').tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
    // posApp.ajaxifyForm(this);
    $("#loading-container").hide();
  },beforeLoad: function(event,ui){
    $("#loading-container").show();
  }});
});
</script>