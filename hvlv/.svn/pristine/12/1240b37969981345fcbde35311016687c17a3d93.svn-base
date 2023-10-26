
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
    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'bwtrunk-grid',
        'cssFile' => false,
        'dataProvider'=>$dataprovider,
        'filter'=>$filtersForm,
        'columns' => array(
            ['name' => 'MAWB_number'],
            ['name'=>'Customer'],
            ['name'=>'Air Type'],
            ['name' => 'CTO_finish'],
            ['header' => 'WH_note','type'=>'raw','value'=>'"<div style=\"width:5em;word-wrap: break-word;\">".$data["wh_note"]."</br>".CHtml::link("edit",Yii::app()->createURL("warehouseProcess/editConsolWhNote")."?id=".$data["id"],["class"=>"grid_edit_btn edit_wh_note"])."</div>"']

            
        )
    ));

}else
{
    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'bwtrunk-grid',
        'cssFile' => false,
        'dataProvider'=>$dataprovider,
        'filter'=>$filtersForm,
        'columns' => array(
            ['name' => 'MAWB_number'],
            ['name'=>'Airport'],
            ['name'=>'Air Type'],
            ['name'=>'Customer'],
            ['name'=>'Weight'],
            ['name' => 'pieces_pick_up'],
            ['name' => 'CTO_start'],
            ['name' => 'CTO_finish'],
            ['name' => 'Client_start'],
            ['name' => 'Client_finish'],
            ['header' => 'WH_note','type'=>'raw','value'=>'"<div style=\"width:5em;word-wrap: break-word;\">".$data["wh_note"]."</br>".CHtml::link("edit",Yii::app()->createURL("warehouseProcess/editConsolWhNote")."?id=".$data["id"],["class"=>"grid_edit_btn edit_wh_note"])."</div>"']
        )
    ));

}

?>
</div>

<script type="text/javascript">
    $(function(){

        $('body').off('click', 'a.edit_wh_note').on('click', 'a.edit_wh_note', function(e){
            $('#modal-message').modal();
            $('#modal-message .modal-body').load($(this).attr('href'));
            e.preventDefault();
        });

    })
</script>


