<style>
input[type=text]{
    width: 500px;
}

textarea{
    width: 500px;
}
.min_height_300{
    min-height: 300px;
}

</style>

<div>
    <h1>Email Schedule</h1>

    <form id="form_email">

    <div class="form">
        <form id="form_email">
            <input type="hidden" name="id" value="<?=$model->id?>" />
            <div class="row rowcol rowleft">
                <?php echo CHtml::label('Schedule Time','schedule_time'); ?>
                <?php echo CHtml::textField( 'schedule_time',$model->date,['class' => 'datetime_input']); ?>
            </div>
            <?php
                $strSql = 'select distinct `group` from marketing_address';
            	$listRows =Yii::app()->db->createCommand($strSql)->queryAll();
                if(!empty($listRows)){
                    echo '<div class="row rowcol rowleft">';
                    echo CHtml::label('Group','group');
                    $listGroup = [];
                    foreach($listRows as $objRow){
                        $listGroup[$objRow['group']] = $objRow['group'];
                    }
                    $listSelect = [];
                    if(!empty($model->mdata['Group'])){
                        $listSelect = $model->mdata['Group'];
                    }
                    echo CHtml::checkBoxList('group',$listSelect,$listGroup,['labelOptions'=>['class'=>'radio_label'],'separator'=>'&nbsp;&nbsp']);
                    echo '</div><br/><br/>';
                }
            ?>
        </form>
        <div class="row rowcol rowleft">
            <input type="button" value="Save Schedule Time" onclick="funcSave()"/>
            <input type="button" value="Cancel Schedule" onclick="funcCancelSchedule()"/>
        </div>
        <br/><br/><br/>
        <form id="form_test">
            <input type="hidden" name="id" value="<?=$model->id?>" />
            <div class="row rowcol rowleft">
                <?php echo CHtml::label('Online View','Online View'); ?>
                <?php 
                $strUrl = ' http://'.$_SERVER['HTTP_HOST'].'/ims/customerService/marketingEmailOnlineView?id='.$model->id;
                if($_SERVER['HTTP_HOST'] == 'ims.toplogistics.com.au'){
                    $strUrl = ' https://'.$_SERVER['HTTP_HOST'].'/customerService/marketingEmailOnlineView?id='.$model->id;
                }
                echo $strUrl;
                ?>
            </div>
        </form>
        <form id="form_test">
            <input type="hidden" name="id" value="<?=$model->id?>" />
            <div class="row rowcol rowleft">
                <?php echo CHtml::label('Test Address','test_address'); ?>
                <?php echo CHtml::textField( 'test_address',''); ?>
            </div>
        </form>
        <div class="row rowcol rowleft">
            <input type="button" value="Send Test Email" onclick="funcSendTestEmail()"/>
        </div>

        <div class="row rowcol rowleft min_height_300">
            <?php
            echo CHtml::label('History','history');
            foreach( $model->mdata['History'] as $strHistory){
                echo $strHistory.'<br/>';
            }
            ?>
            
        </div>

    </div>



</div>

<script type="text/javascript">
    function funcSave() {
        setTimeout(() => {
            listData = $('#form_email').serializeArray();
            htmlobj = $.ajax({
                type:"POST",
                url: "/marketingTool/scheduleEmail",
                data: listData,
                async: false
            });
            obj = JSON.parse(htmlobj.responseText);
            if(obj.isSuccess){
                $('#jqmw_<?=$_GET["tabid"];?>').jqmHide();
                $('#<?=$_GET["tabid"]?>_email-grid').yiiGridView('update');
                myApp.notice('success', 5000);
            }
        }, 0);
    }

    function funcCancelSchedule(){
        setTimeout(() => {
            listData = $('#form_email').serializeArray();
            htmlobj = $.ajax({
                type:"POST",
                url: "/marketingTool/cancelSchedule",
                data: listData,
                async: false
            });
            obj = JSON.parse(htmlobj.responseText);
            if(obj.isSuccess){
                $('#jqmw_<?=$_GET["tabid"];?>').jqmHide();
                $('#<?=$_GET["tabid"]?>_email-grid').yiiGridView('update');
                myApp.notice('success', 5000);
            }
        }, 0);
    }

    function funcSendTestEmail(){
        setTimeout(() => {
            listData = $('#form_test').serializeArray();
            htmlobj = $.ajax({
                type:"POST",
                url: "/marketingTool/sendTestEmail",
                data: listData,
                async: false
            });
            obj = JSON.parse(htmlobj.responseText);
            if(obj.isSuccess){
                $('#jqmw_<?=$_GET["tabid"];?>').jqmHide();
                $('#<?=$_GET["tabid"]?>_email-grid').yiiGridView('update');
                myApp.notice('success', 5000);
            }
        }, 0);
    }
</script>