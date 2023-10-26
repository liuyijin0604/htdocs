
<div>
    <h2>Basic Inventory Statistic</h2>
    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'cgi-inventory-report-grid',
        'htmlOptions'=>array('style'=>'width: 70%'),
        'cssFile' => false,
        'dataProvider'=>$inventory,
        'columns'=>array(
            array('header' => 'Name','name' => 'name'),
            array('header' => 'Inventory','name' => 'total'),
            array('header' => 'Avg Consume(based 3mons)','name' => 'avg'),
            array('header' => 'Alert','name' => 'alert','type' => 'raw', 'value' => '$data->alert')

        ),
    )); ?>

</div>
<br/>
<!--
<div>

    <h2>Consume Trend - Recent Three Months</h2>

    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'cgi-trends-report-grid',
        'htmlOptions'=>array('style'=>'width: 70%'),
        'cssFile' => false,
        'dataProvider'=>$trend,
        'columns'=>array(
            array('header' => 'Month','name' => 'month'),
            array('header' => 'Name','name' => 'name'),
            array('header' => 'Consumed','name' => 'total','type' => 'raw','value' => 'abs($data->total)')
        ),
    )); ?>

</div>
-->

<br/>

<div>
<h1> By Customer - Recent Three Months </h1>

    <div class="row">
    <div class="rowcol rowleft">
        <?php echo CHtml::label('Customer','sgr_customer'); ?>
        <?php echo CHtml::hiddenField('cid', 0,array('data-ov' => 0));
        $acname1 = empty($_GET["tabid"])? 'agent_ac' : $_GET["tabid"].'_agent_ac';
        $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
            'name' => $acname1,
            'sourceUrl' => array('org/ownerSuggest'),
            'value' => '',
            'options' => array(
                'showAnim' => 'fold',
                'minLength' => 2,
                'delay' => 200,
                'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]).trigger("change"); return false; }',
                'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
            ),
            'htmlOptions' => array(
                'size' => '30',
            ),
        ));
        ?>

        <?php  echo CHtml::button('View',['id' => 'view-by-customer']) ?>

    </div>


        <div id="inventory-bycustomer-data">

        </div>

    </div>
</div>

<script type="text/javascript">

    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
        console.log('debug');

        tab.bind('onOpen', function(){
           // $('#cg-orders-grid', panel).yiiGridView('update');
        });

        $('#view-by-customer',panel).click(function(e){
            var cid = $('#cid',panel).val();
            if ( cid <= 0 ) {
                alert('please select a customer');
            } else {
                var data = {'cid' : cid};
                $.ajax({
                    type : 'POST',
                    url : '<?php echo Yii::app()->createAbsoluteUrl("cgoods/inventoryByCustomer") ;?>',
                    data: data,
                    dataType: 'html',
                    success:function(resp){
                        $('#inventory-bycustomer-data',panel).html(resp);
                    }
                });
            }
        });
    });

</script>
