<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'currency-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'currency',
                 array('name'=>'type','header'=>'type','value'=>'$data->type==0?"USD":"CNY"','filter'=>Chtml::dropDownList('Currency[type]',$model->type, Currency::$currency_type,array('prompt'=>'All'))),
		'date',
		
	),
)); ?>