
        <?php echo CHtml::label('Weight Map','weight_map');?>
        <?php 
        foreach ($weightMapArr as $key => $weightMap) {
           echo CHtml::radioButton('weight_map[]',false,array('class'=>'radio_label','value'=>$key)),"&nbsp;&nbsp{$weightMap}";
           echo CHtml::button('X', ["class"=>"deleteZoneMapImport","onClick"=>"deleteZoneMapImport({$key},1);"]),"&nbsp;&nbsp";
        }
        ?>