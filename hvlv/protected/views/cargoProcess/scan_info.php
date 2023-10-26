<div style="line-height: 150%; clear: both; white-space: nowrap">
 <ul style="width: 30%">
     <li ><h3>Total Pacels: <span style="color: green;"><?=$model->totShipments();?></span></h3></li>
     <li ><h3>Total Packs: <span style="color: green;"><?=$model->totPacks();?></span></h3></li>
     <li ><h3>Scaned Packs: <span style="color: green;"> <?=$model->totScanPacks();?></span></h3></li>
     <li ><h3>Left Packs:<span style="color: red;"><?=$model->totLeftPacks();?></span></h3></li>
     <li ><h3>Total cleared: <span style="color: green;"> <?=$model->totClearShipments();?></span></h3></li>
     <li ><h3>Total held: <span style="color: green;"> <?=$model->totHeldShipments();?></span></h3></li>
     <li ><h3>EMPP held: <span style="color: green;"> <?=$model->totEMPPShipments();?></span></h3></li>
     <li ><h3>>12 hours AQIS held: <span style="color: green;"> <?=$model->totAQISShipments();?></span></h3></li>
</ul>
</div>

