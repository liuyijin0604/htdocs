

    <span> Total : Revenue <span style="font-weight: bold;"> <?php echo $total['revenue']; ?></span>
        Cost <span style="font-weight: bold;"> <?php echo $total['cost']; ?> </span>
            GP <span style="font-weight: bold;"> <?php echo $total['gp']; ?></span>
          Profit <span style="font-weight: bold;"> <?php echo $total['profit']; ?></span>  </span>
    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'ex-pl-sum-report-grid',
        'htmlOptions'=>array('style'=>'width: 70%'),
        'cssFile' => false,
        'dataProvider'=>$model,
       'filter'=>$filter,
        'columns'=>array(
            array('name'=>'no','header'=>'No','type' => 'raw','value'=>'"<a href=\"".Yii::app()->createURL(($data["type"]==15)?"excoConsol/update":(($data["type"]==80)?"elmsConsol/update":"dmawbConsol/update" ), array("id" =>$data["id"]))."\" class=\"tab_link\" title=\"".$data["no"]."\">".$data["no"]."</a>"'),
            'eta',
            'owner',
            'shipments',
            'weight',
            'revenue',
            'cost',
            'gp',
            array('name'=>'profit_rate','value'=>'$data["profit_rate"]."%"'),
           
        ),
    )); ?>

<!-- array('name' => 'no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL(($data->type==15)?"excoConsol/update":(($data->type==80)?"elmsConsol/update":"dmawbConsol/update" ), array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>"',),
            'eta',
            array('header' => 'Shipments', 'value' => '$data->totShipments()'),
            array('header' => 'Weight', 'value' => '$data->totWeight()'),
            array('header' => 'Revenue', 'value' => '$data->getTotalInvoice()'),
            array('header' => 'Cost', 'value' => '$data->totCost()'),
            array('header' => 'GP', 'value' => '$data->getProfitGP()'),
            array('header' => 'Profit Rate', 'value' => '$data->getProfitRate()'),-->