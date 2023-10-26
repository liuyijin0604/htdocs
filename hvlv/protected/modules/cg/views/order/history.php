<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
    'homeLink'=>CHtml::link('Home / 主页', array('../' . ($this->layout?$this->layout:'cg') . '/site/index')),
    'links' => array(
           'Order History / 订单历史',
    ),
));
?>
<h1>Order History / 订单历史</h1>

<div class="row" style="text-align: center;">
	<div class="form-group">
        <?php if ($tasks) { ?>
    	<table id="items" class="table table-striped table-bordered">
        	<thead>
                <tr class="awb-item">
                    <td>Added Date / 下单日期</td>
                    <td>Delivery Date / 配送日期</td>
                    <td>Amount / 金额</td>
                    <td>Status / 状态</td>
                    <td></td>
                    <td></td>
                </tr>
            </thead>
            <tbody>
            <?php foreach ( $tasks as $task ) : ?>
                <?php switch ($task->getStatus()) {
                    case 'New':
                        $status = '待确认付款 / waiting for confirm payment';
                        break;
                    case 'Scheduled':
                        $status = '等待配送 / waiting for delivery';
                        break;
                    case 'WIP':
                        $status = '配送中 / deliverying';
                        break;
                    case 'Completed':
                        $status = '已完成 / completed';
                        break;
                    default:
                        $status = $task->getStatus();
                        break 2;
                } ?>
                <tr class="awb-item">
                    <td><?php echo $task->schd_time; ?></td>
                    <td><?php echo $task->compl_time; ?></td>
                    <td>$ <?php echo number_format($task->mdata['total'], 2); ?></td>
                    <td><?php echo $status; ?></td>
                    <td>
                        <a class="od-details-link ajax-link" href="<?= str_replace('cg', ($this->layout?$this->layout:'cg'), $this->createUrl('order/details', ['id' => $task->id])); ?>">Details / 详情</a>
                    </td>
                    <td>
                        <a class="od-details-link" target="_" href="<?=$this->createUrl('order/invoice', array('id' => $task->id))?>">Invoice / 账单 <?=InvLine::model()->find('fid = :task_id', array(':task_id' => $task->id))->invoice->no?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        	</tbody>
    	</table>
        <?php } else { ?>
        <h2>No order now</h2>
        <?php } ?>
	</div>
</div>