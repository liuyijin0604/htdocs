<div  style="width: 50%">
            <?php
            $this->widget('zii.widgets.grid.CGridView', array(
                'id' => 'shipment_amazon_booking-grid',
                'htmlOptions' => array('style' => 'width: 70%'),
                'cssFile' => false,
                'dataProvider' => $dataProvider[0],
                'filter' => $dataProvider[1],
                'columns' => array(
                    array('name' => 'id', 'headerHtmlOptions' => array('style' => 'display:none'), 'filterHtmlOptions' => array('style' => 'display:none'),
                        'htmlOptions' => array('style' => 'display:none'), 'type' => 'raw'),
                    array('name'=>'ref','type'=>'raw','value'=> '"<a href=\"".Yii::app()->createURL("".lcfirst($data["model"])."/update", array("id" => $data["fid"]))."\" class=\"tab_link\" title=\"".$data["no"]."\">".$data["no"]."</a>"'),
                    "ref",
                    array('name'=>'booking_ref'),
                    array('name'=>'amazon_booking_time'),
                    array('name' => 'wt_percent', 'header' => 'Weight %'),
                    array('name' => 'consol'),
                ),
            ));
            ?>
        </div>
