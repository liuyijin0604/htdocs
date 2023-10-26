
<div class="row">
    <div class="hvlv-crm-report-item"> <span class="title">Total:</span><span class="item-value"><?php echo $total;?></span></div>
    <div class="hvlv-crm-report-item"> <span class="title">Open:</span><span class="item-value"><?php echo $totalOpen;?></span></div>
    <div class="hvlv-crm-report-item"> <span class="title">Closed:</span><span class="item-value"><?php echo $totalClosed;?></span></div>
</div>

<div class="row" style="margin-top:20px;">
    <div class="xpanel xpanel-default" style="width:300px;float:left;margin-right:10px;">
        <div class="xpanel-heading">Type</div>
        <div class="xpanel-body">
            <div class="xpanel-body-item">
                <?php foreach ( $types as $key => $type) : ?>
                <div class="hvlv-crm-report-item"> <span class="title"><?php echo $key; ?>:</span><span class="item-value"><?php echo $type;?></span></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="xpanel xpanel-default" style="width:300px;float:left;margin-right:10px;">
        <div class="xpanel-heading">Level</div>
        <div class="xpanel-body">
            <div class="xpanel-body-item">
                <?php foreach ( $levels as $key => $level) : ?>
                    <div class="hvlv-crm-report-item"> <span class="title"><?php echo $key; ?>:</span><span class="item-value"><?php echo $level;?></span></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="xpanel xpanel-default" style="width:300px;float:left;margin-right:10px;">
        <div class="xpanel-heading">Source</div>
        <div class="xpanel-body">
            <div class="xpanel-body-item">
                <?php foreach ( $sources as $key => $source) : ?>
                    <div class="hvlv-crm-report-item"> <span class="title"><?php echo $key; ?>:</span><span class="item-value"><?php echo $source;?></span></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</div>

<div class="clearfix"></div>
<br/>

<div class="form">
        <div class="row" style="margin-top:20px;">
            <span style="font-size: 20px; font-weight: bold;"> Search By Operator</span>
            <div id="crm-report-by-search">
            <?php $this->renderPartial('_op_reports',['model' => $model ]); ?>
            </div>
        </div>
</div>

<script type="text/javascript">

    function crm_op_search_send(){
        var data = $('#crm-search-report-form').serialize();

        $.ajax({
            type : 'POST',
            url : '<?php echo Yii::app()->createAbsoluteUrl("crm/ajaxSearchReport") ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                $('#crm-report-by-search').html(resp);
            }
        });
    }

$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

	tab.bind('onOpen', function(){
		$('#<?=$_GET["tabid"]?>_crm-grid', panel).yiiGridView('update');
	});




});

</script>