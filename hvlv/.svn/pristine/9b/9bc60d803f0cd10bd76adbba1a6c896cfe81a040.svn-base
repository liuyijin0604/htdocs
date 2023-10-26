<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
    'homeLink'=>CHtml::link('Home / 主页', array('site/index')),
    'links' => array(
           'Orders / 配送中订单',
    ),
));
?>
<h1>Orders / 配送中订单</h1>
<div class="container">

<div class="row" style="text-align: center;">

	<div class="form-group">
        <?php if ($tasks) { ?>
    	<table id="items" class="table table-striped table-bordered">
        	<thead>
            <tr class="awb-item">
                <th class="col-xs-2">ID / 商家编号</th>
                <th class="col-xs-3">Name / 商家名称</th>
                <th class="col-xs-7">Details / 商品详情</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ( $tasks as $task ) : 
                $org = Org::model()->findByPk($task->mdata['org']);
            ?>
                <tr class="awb-item">
                    <td><?=$org->id?></td>
                    <td><?=$org->name?></td>
                    <?php
                        $detail = "";
                        foreach ($task->getItemsArray() as $item) {
                            $detail .= $item['sn'] . ": " . $item['uq'] . "个&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                        }
                    ?>
                    <td><?=$detail?></td>
                </tr>
            <?php endforeach; ?>
        	</tbody>
    	</table>
        <?php } else { ?>
        <h2>No order now</h2>
        <?php } ?>
	</div>

</div>
</div>