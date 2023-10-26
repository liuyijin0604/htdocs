
        <?php echo CHtml::label('Test Couriers','test_couriers');?>
        <?php 
        foreach ($couriersTestArr as $key => $couriersTest) {
           echo CHtml::checkBox('test_couriers[]',false,array('class'=>'radio_label','value'=>$key)),"&nbsp;&nbsp{$couriersTest}";
           echo CHtml::button('X', ["class"=>"deleteTestCouriers","onClick"=>"deleteTestCouriers({$key});"]),"&nbsp;&nbsp";
        }
        ?>