<?php
/* @var $this TaskController */

$this->breadcrumbs=array(
	'Task'=>array('/task'),
	'List',
);
?>

<h1>Task Manager</h1>


<div class="form">

    <div class="row">
        <?php echo CHtml::label('Warehouse','forme'); ?>
        <?php echo CHtml::dropDownList('warehouse-list','Select Warehouse',ErpProductRoutes::warehouseList());?>
    </div>

    <div class="row rowcol">
        <?php echo CHtml::label('Driver','forme'),
            CHtml::hiddenField('driver-id','0',array('data-ov' => '0'));
            $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
            'name' => 'did',
            'sourceUrl' => array('ErpProductRoute/driverSuggest'),
            'value' => '',
            'options' => array(
                'showAnim' => 'fold',
                'minLength' => 2,
                'delay' => 200,
                'autoFocus' => true,
                'select' => 'js:function(evt, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]);  getagentinfo(); return false; }',
                'change' => 'js:function(evt, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
            ),
            'htmlOptions' => array(
                'class' => 'required',
                'size' => '25',
            ),
        ));
        ?>
    </div>


    <div class="row">
        <div class="row rowcol" id="driver-agents">
            <div class="row">
                <?php echo CHtml::label('Agent','forme'),
                CHtml::hiddenField('agent-id','0',array('data-ov' => '0'));
                $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                    'name' => 'agent_name',
                    'sourceUrl' => array('ErpProductRoute/ToSuggest'),
                    'value' => '',
                    'options' => array(
                        'showAnim' => 'fold',
                        'minLength' => 2,
                        'delay' => 200,
                        'autoFocus' => true,
                        'select' => 'js:function(evt, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
                        'change' => 'js:function(evt, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
                    ),
                    'htmlOptions' => array(
                        'class' => 'required',
                        'size' => '25',
                    ),
                ));
                ?>
                <?php echo CHtml::button('Add',array('id' => 'add-agent')); ?>
            </div>
            <div class="row" style="weight:90%;height: 220px; overflow-y: auto;">
                <ul class="task-driver-agent-list">
                 <ul>
            </div>
        </div>

        <div class="row rowcol">
            <span id="selected-agent-name">Please select one agent</span>

            <br>
            <div>
                <br>
                <?php echo  CHtml::label('Pickup time:','forme'); ?>
                From:<?php echo  CHtml::textField('pickup-time-from','08:00'); ?>
                To:<?php echo  CHtml::textField('pickup-time-to','19:00'); ?>
            </div>

            <div id="repeat-box">
                <br>
                <?php echo  CHtml::label('Repeat:','forme'); ?>
                <?php echo CHtml::checkBox('Mon',false,array('value' => 1) ); ?> Mon &nbsp; <?php echo CHtml::checkBox('Tue',false,array('value' => 2)); ?> Tue&nbsp;
                <?php echo CHtml::checkBox('Wed',false,array('value' => 4)); ?> Wed &nbsp;<?php echo CHtml::checkBox('Thu',false,array('value' => 8)); ?> Thu&nbsp;
                <?php echo CHtml::checkBox('Fri',false,array('value' => 16)); ?> Fri &nbsp;<?php echo CHtml::checkBox('Sat',false,array('value' => 32)); ?> Sat&nbsp;
                <?php echo CHtml::checkBox('Sun',false,array('value' => 64)); ?> Sun  <br>
                <br>
                <?php echo CHtml::checkBox('Monthly',false,array('value' => 128)); ?> Monthly&nbsp;<?php echo CHtml::checkBox('By Year',false,array('value' => 256)); ?> By Year
            </div>


            <div>
                <br>
                <?php echo  CHtml::label('Notes:','forme'); ?>
                <?php echo CHtml::textArea('agent-notes' ,'', array('rows' => 4, 'cols' => 50)); ?>
            </div>
            <br>
            <div class="row buttons">
                <?php echo CHtml::submitButton('Save',array('id' => 'btn-save')); ?>
            </div>
        </div>

    </div>

</div>

<script type="text/javascript">

    var tasks = {};

    function addNewAgent(agentId,agentName,noalert){

        if ( typeof agentId == 'undefined' || typeof agentName == 'undefined' ) {
            return;
        }

        // existing do nothing
        var existing = false;
        $('ul.task-driver-agent-list li').each(function(){
            var agent_id = $(this).data('aid');
            if ( agent_id == agentId ) {
                existing = true;
                return false;
            }
        });

        if ( existing  ) {
            if ( !noalert ) {
                alert('selected agent is existing already');
            }
            return;
        }

        $('.agent-info-div').removeClass('active');

        // add new one
        var new_agent = '<li data-aid="' + agentId +  '"><div class="agent-info-div active">';
        new_agent += '<span class="agent-name">' + agentName +'</span><br><span class="agent-id">' + agentId+ '</span>';
        new_agent +=  '<span class="close">X</span> </div> </li>';

        $('#selected-agent-name').html( agentName + ' - ' + agentId);

        $('ul.task-driver-agent-list').append(new_agent);

    }

    function getagentinfo(){
        // get driver id
        var driver_id = $('#driver-id').val();

        // get task information
        $.ajax({
            url: 'Task/AjaxGetTaskInfo',
            dataType: 'json',
            type: 'post',
            data : {'did' : driver_id},
            success: function(r){
                if ( r.success == 1 ) {
                    //alert(r.tasks);
                    for ( var i in r.tasks ) {
                        addNewAgent(r.tasks[i].agent_id, r.tasks[i].agent_name,true);
                    }

                    // save for selected then show task details
                    tasks = r.tasks;

                    // show last selected task details
                    $('.task-driver-agent-list li:last-child').trigger('click');

                }
            },
            error: function(r, e){ alert(e); }
        });

    }

    function formatTime(time) {
        var result = false, m;
        var re = /^\s*([01]?\d|2[0-3]):?([0-5]\d)\s*$/;
        if ((m = time.match(re))) {
            result = (m[1].length === 2 ? "" : "0") + m[1] + ":" + m[2];
        }

        return result;
    }


    function showRepeats(repeat){
        // clear all previous
        var repeat_check_ids = ['#Mon','#Tue','#Wed','#Thu','#Fri','#Sat','#Sun','#Monthly','#By_Year'];
        var repeat_check_vals = [1,2,4,8,16,32,64,128,256];
        for ( var i in repeat_check_ids ) {
            var dest = $(repeat_check_ids[i]);
            dest.prop('checked',false);
            if ( (repeat_check_vals[i] & repeat) > 0 ) {
                dest.prop('checked',true);
            }
        }
    }

    $(function(){
        var win = $('#jqmw_<?=$_GET["tabid"];?>');

        $('#btn-save').click(function(e){

            // check driver
            var driver_id = $('#driver-id').val();
            if ( typeof driver_id == 'undefined' || driver_id.length <= 0 || driver_id <= 0) {
                alert('please select one driver');
                return;
            }

            // check agent
            var agent_id = $('#agent-id').val();
            if ( typeof agent_id == 'undefined' || agent_id.length <= 0 || agent_id <= 0 ) {
                alert('please select one agent');
                return;
            }

            // check all repeat
            var time_from = $('#pickup-time-from').val();
            var time_to = $('#pickup-time-to').val();

            if ( !formatTime(time_from) ) {
                alert('invalid pickup from time');
                return;
            }

            if ( !formatTime(time_to) ) {
                alert('invalid pickup to time');
                return;
            }

            // from time should be big than to time
            if ( time_to < time_from ) {
                alert('To time should exceed the From time');
                return;
            }

            // check repeat data
            $repeat = 0;
            $("#repeat-box input[type=checkbox]").each(function(){
                if ( $(this).prop('checked') ) {
                    $repeat += parseInt($(this).val());
                }
            });
            if ( $repeat <= 0 ) {
                alert('please select at least one repeat mode');
                return;
            }

            // get text notes
            var notes = $('#agent-notes').val();

            // save them now
            // refresh all product inventories
            $.ajax({
                url: 'Task/ajaxSave',
                dataType: 'json',
                type: 'post',
                data : {'did' : driver_id,'aid':agent_id,'tf':time_from,'tt':time_to,'r':$repeat,'n':notes},
                success: function(r){
                    if ( r.success == 1 ) {
                        alert('The agent information has been saved successfully!');

                        // update to local
                        tasks.push(r.task);
                    }
                },
                error: function(r, e){ alert(e); }
            });

        });


        $('.task-driver-agent-list').on('click','li span.close',function(e){
            if ( confirm('are you sure delete the agent?') ) {
                var parent = $(this).parent().parent();
                var agent_id = parent.data('aid');
                var driver_id = $('#driver-id').val();
                parent.remove();

                $.ajax({
                    url: 'Task/ajaxRemove',
                    dataType: 'json',
                    type: 'post',
                    data : {'did' : driver_id,'aid':agent_id},
                    success: function(r){
                        if ( r.success == 1 ) {
                            alert('The agent information has been deleted successfully!');
                        }
                    },
                    error: function(r, e){ alert(e); }
                });

            }
        });

        $('.task-driver-agent-list').on('click','li',function(e){
            $('.agent-info-div').removeClass('active');
            var dest =  $(this).find('.agent-info-div');
            dest.addClass('active');
            var agent_name = dest.find('.agent-name').html();
            var agent_id = dest.find('.agent-id').html();

            $('#agent-id').val(agent_id);

            for ( var i in tasks ) {
                if ( tasks[i].agent_id == agent_id ) {
                    $('#agent-notes').val(tasks[i].notes);
                    $('#pickup-time-from').val(tasks[i].from_time);
                    $('#pickup-time-to').val(tasks[i].to_time);

                    showRepeats( parseInt(tasks[i].repeat));

                    break;
                }
            }
            $('#selected-agent-name').html(agent_name + ' - ' + agent_id );
        });

        $('#add-agent').click(function(e){
            var agent_name = $('#agent_name').val();
            var agent_id = $('#agent-id').val();

            addNewAgent(agent_id,agent_name,true);

        });
    });
</script>
