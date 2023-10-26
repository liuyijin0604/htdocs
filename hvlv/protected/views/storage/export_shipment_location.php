<h2>Export Shipment Location</h2>



 
<div style="right: 20px;position: absolute;">

<a href="#" class="export_search" target="_blank" data-baseurl="<?=$this->createUrl('storage/exportShipmentLocation')."?export=1";?>"><div style="background-position:-48px -688px" class="icon"></div> Export Current Search</a> 
</div>
 <div class="row">
                    <!--<div id="loadingPic"  style="width:20px;height:20px;float:left;"></div>-->
 <div id="export-shipment-location-view">
      <?php
         $this->renderPartial('_sub_shipment_location', [
            'model' => $model
         ]);
    ?>
  </div>
</div>
<script>
$(function(){
    var tab = $('#<?=$_GET["tabid"];?>');
    var panel=tab.data('panel');
    
     $('a.export_search', panel).on('mousedown', function(){
        var q = $('.filters input, .filters select', panel).serialize();
        $(this).attr('href', $(this).data('baseurl') + '&' + q);
    });
})
  
</script>