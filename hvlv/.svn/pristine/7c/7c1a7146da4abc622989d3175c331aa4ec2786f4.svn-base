
<style type="text/css">
  .row-status-TOTAL {
    background-color: rgb(103,167,205);
}
.grid-container {
    overflow: auto;
    max-height: 800px; /* Set a maximum height for the container to enable scrolling */
}

.grid-view thead {
    position: sticky;
    top: 0;
    background-color: #f2f2f2; /* Adjust the background color of the fixed header */
    z-index: 1; /* Ensure the header stays above the content */
}
</style>
<div class="grid-container">
<?php
  if($width<800)
  {
    $this->widget('zii.widgets.grid.CGridView', [
      'id'=>$_GET["tabid"].'_consol_process_grid',
      'cssFile' => false,
      'dataProvider' => $dataprovider,
      'filter' => $filtersForm,
      'columns'=>[
        ["name"=>"mawb","header"=>"MAWB"],
        ["name"=>"customer","header"=>"Customer"],
        ["name"=>"air_type","header"=>"Air Type"],
        ["name"=>"eta","header"=>"ETA"],
        ["name"=>"weight","header"=>"Weight"] 
      ],
        'rowCssClassExpression' =>function($row, $data) {
            return $data["status"]=="TOTAL"?"row-status-TOTAL":($row % 2 === 0?"even" : "odd");
        }
    ]);


  }else
  {
    $this->widget('zii.widgets.grid.CGridView', [
    'id'=>$_GET["tabid"].'_consol_process_grid',
    'cssFile' => false,
    'dataProvider' => $dataprovider,
    'filter' => $filtersForm,
    'columns'=>[
      ["name"=>"date","header"=>"Date"],
      ["name"=>"doAgent","header"=>"Airport"],
      ["name"=>"air_type","header"=>"Air Type"],
      ["name"=>"customer","header"=>"Customer"],
      ["name"=>"mawb","header"=>"MAWB"],
      ["name"=>"eta","header"=>"ETA"],
      ["name"=>"status","type"=>"raw","header"=>"Status","value"=>'$data["status"]=="Checked In"?"<font color=\"red\" style=\"font-weight:bold\">".$data["status"]."</font>":$data["status"]'],
      ["name"=>"pcs","header"=>"PCS"],
      ["name"=>"weight","header"=>"Weight"],
      ["name"=>"note","header"=>"Note"]          
    ],
      'rowCssClassExpression' =>function($row, $data) {
          return $data["status"]=="TOTAL"?"row-status-TOTAL":($row % 2 === 0?"even" : "odd");
      }
  ]);
}

?>
</div>