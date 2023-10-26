<h3><?=$typeName?></h3>
<div style="max-width: 300px;" >    
    <?php  
    $this->widget('zii.widgets.grid.CGridView',array(
            'id'=>'consol_main_process_type'.$_GET['tabid'],
            'cssFile' => false,
             'dataProvider'=>$dataprovider,
            'columns'=>array(
             array('name'=>'type','header'=>'Type','cssClassExpression' =>'"type_row"','type'=>'raw',
             'value'=>'"<a href=\"".Yii::app()->createUrl($data["consol_type"]==15?"imcoConsol/list":"dmawbConsol/list",array("delivery_type"=>$_GET["delivery_type"],"consol_type"=>$data["consol_type"]))."\"  class=\"tab_link\" title=\"".($_GET["delivery_type"]==10?"Air":"Sea")."-".$data["type"]."\" >".$data["type"]."</a>"'
                    
                    )),
    ));
    ?>
</div>
<script>
       $(function(){
        var tab=$('#<?=$_GET['tabid']?>');
        var panel=tab.data('panel');
        $('.summary',panel).html('');
        })
</script>