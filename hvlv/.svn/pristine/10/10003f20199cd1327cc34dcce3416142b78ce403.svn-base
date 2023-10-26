
        <?php echo CHtml::label('Step 3: Choose TLA Test Zone For Exporting Average Cost Metrix','pca_zone_test_label',["style"=>"font-size:1.5em;"]);?>
        <?php 
        foreach ($pcaZoneTestArr as $key => $pcaZoneTest) {
           echo CHtml::radioButton('pca_zone_test[]',false,array('class'=>'radio_label','value'=>$key)),"&nbsp;&nbsp{$pcaZoneTest}";
           echo CHtml::button('X', ["class"=>"deleteZoneMapImport","onClick"=>"deleteZoneMapImport({$key},0);"]),"&nbsp;&nbsp";
        }
        ?>