<h2>Sea Process List</h2>
<div style="position: absolute; right:80px;">
    <a class="tab_link"  href="<?=$this->createUrl("seaProcess/listSpecifyProcess",array('status'=> SeaProcess::STATE_WAITING_PREALERT))?>" title="<?= SeaProcess::$states[SeaProcess::STATE_WAITING_PREALERT]?>" ><span style="background-position:-32px 0px" class="icon"></span><?= SeaProcess::$states[SeaProcess::STATE_WAITING_PREALERT];?></a>
    <a class="tab_link"  href="<?=$this->createUrl("seaProcess/listSpecifyProcess",array('status'=> SeaProcess::STATE_WAITING_REQUEST_MANIFEST))?>" title="<?=SeaProcess::$states[SeaProcess::STATE_WAITING_REQUEST_MANIFEST]?>" ><span style="background-position:-32px 0px" class="icon"></span><?= SeaProcess::$states[SeaProcess::STATE_WAITING_REQUEST_MANIFEST];?></a>
    <a class="tab_link"  href="<?=$this->createUrl("seaProcess/listSpecifyProcess",array('status'=> SeaProcess::STATE_WAITING_DO))?>" title="<?=SeaProcess::$states[SeaProcess::STATE_WAITING_DO]?>" ><span style="background-position:-32px 0px" class="icon"></span><?= SeaProcess::$states[SeaProcess::STATE_WAITING_DO];?></a>
    <a class="tab_link"  href="<?=$this->createUrl("seaProcess/listSpecifyProcess",array('status'=> SeaProcess::STATE_WAITING_OUTTURN))?>" title="<?=SeaProcess::$states[SeaProcess::STATE_WAITING_OUTTURN]?>" ><span style="background-position:-32px 0px" class="icon"></span><?= SeaProcess::$states[SeaProcess::STATE_WAITING_OUTTURN];?></a>
    <a class="tab_link"  href="<?=$this->createUrl("seaProcess/listSpecifyProcess",array('status'=> SeaProcess::STATE_WAITING_DECOMPOSITION))?>" title="<?=SeaProcess::$states[SeaProcess::STATE_WAITING_DECOMPOSITION]?>" ><span style="background-position:-32px 0px" class="icon"></span><?= SeaProcess::$states[SeaProcess::STATE_WAITING_DECOMPOSITION];?></a>
    <a class="tab_link"  href="<?=$this->createUrl("seaProcess/listSpecifyProcess",array('status'=> SeaProcess::STATE_SEA_PROCESS_DONE))?>" title="<?=SeaProcess::$states[SeaProcess::STATE_SEA_PROCESS_DONE]?>" ><span style="background-position:-32px 0px" class="icon"></span><?= SeaProcess::$states[SeaProcess::STATE_SEA_PROCESS_DONE];?></a>
</div>
<div style="width:30%" id="sea-process-overview<?=$_GET['tabid']?>">
   <?php $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'sea-process-overview-grid'.$_GET['tabid'],
        'htmlOptions'=>array('style'=>'width: 70%'),
        'cssFile' => false,
        'dataProvider'=>$dataProvider[0],
        'filter'=>$dataProvider[1],
        'columns'=>array(
           array('name'=>'status','headerHtmlOptions' => array('style' => 'display:none'),'filterHtmlOptions' => array('style' => 'display:none'),
                'htmlOptions' => array('style' => 'display:none'),'type'=>'raw'),
           array('name'=>'status','value'=>'SeaProcess::$states[$data["status"]]'),
            'number',
        ),
    )); ?>
</div>
<div class="form">
    <div class="row">
   <?php  echo CHtml::button('Show All', array('class' => 'show_all'));?>
    </div>
</div>
<div id="sea-shipments-view">
      <?php
         $this->renderPartial('_sub_shipments', array(
           'model' => $model,
           'name'=>$name
            ));
    ?>
</div>

<script type="text/javascript">
    $(function(){
    var tab = $('#<?=$_GET["tabid"];?>');
    var panel=tab.data('panel');
     $('#sea-process-overview<?=$_GET['tabid']?>',panel).on("click", "table tbody td", function(event){
        // get console id
        var status = parseInt($(this).parent().children(':nth-child(1)').html());
        var data = {};
        data['status'] = status;
        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createAbsoluteUrl("SeaProcess/list",array('tabid'=>$_GET['tabid'])) ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                $('#sea-shipments-view',panel).html(resp);
            },
        });
    }); 
    
    $('.show_all',panel).on('click',function(event){
        var data = {};
        data['status'] = 0;
        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createAbsoluteUrl("SeaProcess/list",array('tabid'=>$_GET['tabid'])) ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                $('#sea-shipments-view').html(resp);
            },
        });
        
    });
    })
    
</script>