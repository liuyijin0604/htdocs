<div class="form">
   <?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'consol-form',
	'enableAjaxValidation'=>false,
)); ?>
    	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>30)); ?>
	</div>
        <div class="row rowcol">
		<?php echo $form->labelEx($model,'telephone'); ?>
		<?php echo $form->textField($model,'telephone',array('size'=>20)); ?>
	</div>
       <div class="row rowcol">
		<?php echo $form->labelEx($model,'type'); ?>
		<?php echo $form->dropDownList($model, 'type', ExCrm::$types_cn, array('disabled' => 'disabled')); ?>
		 <div style="background-position:-240px -416px" class="icon"></div>
	</div>
          <div class="row rowcol">
		<?php echo $form->labelEx($model,'assign_to'); ?>
		<?php 
                $users=[];
                $theusers=[];
                foreach ($model->notes as $r){
                 $users[$r->operator_id]=$r->operator_id;
                }
                $cUsers=User::model()->findAll('lname="国内客服"');
                foreach ($cUsers as $ur){
                    $users[$ur->id]=$ur->id;
                }
                foreach ($users as $uid){
                   if(in_array($uid, array(465,238))) continue;
                  $user=User::model()->findByPk($uid);
                  if(empty($user))     continue;
                  $theusers[$uid]=$user->fname.' '.$user->lname;
                }
                echo $form->dropDownList($model, 'assign_to', (ExCrm::$assign_to+$theusers), array('disabled' => 'disabled')); ?>
		 <div style="background-position:-240px -416px" class="icon"></div>
     </div>
    <div class="row buttons">
        <?php echo CHtml::submitButton('Update')?>
    </div>
 <?php $this->endWidget(); ?>
</div>
<h3>Customer Description</h3>
<p style="border: 1px solid black; width: 40%;  min-height: 50px; margin: 10px 0px;padding: 5px;"><?=@$model->mdata["desc"]?></p>

<h3>Notes</h3>
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' =>$_GET['tabid'].'_crm-ticket-view',
    'cssFile' => false,
    'summaryText' => '',
    'dataProvider' => $crmLog->search(),
    'columns' => array(
        array(
            'name' =>  'operator_id',
            'value' => '$data->getUser()'
            ),
        'time',
        array('name'=>'process_method','value'=>'$data->getProcessMethod()'),
        'note'
    )
));
?>
<br/>
<ul>
    <li><a class="jqm_link grid_email_btn "  style="margin-left:0px;" href="<?=$this->createUrl("exCrm/sendSms",array("id"=>$model->id))?>"  title="Edit Email">Send SMS</a></li>
    <li><a class="jqm_link grid_email_btn theclick"  style="margin-left:0px;" href="<?=$this->createUrl("exCrm/sendEmail",array("id"=>$model->id))?>"  title="Edit Email">Send Email</a></li>
</ul>

<div id="grid-note-view">
<?php if (!in_array($model->status,array(90))) : ?>

<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'crm-notes-form',
        'enableClientValidation'=>true,
        'action'=>$this->createUrl('exCrm/crmMoreNotes', array('id' => $crmLog->crm_id)),
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
        ),
    ));
    ?>
    <h2>Add Notes</h2>

    <?php echo $form->errorSummary($crmLog); ?>
    <div class="row" style="font-size: 1.2em">
        <?php echo CHtml::label('Method','method');?>
        <?php echo CHtml::radioButtonList('process_method',0, CrmLog::$method_types,array('labelOptions'=>array('class'=>'radio_label'),'separator'=>'&nbsp;&nbsp;'));?>
    </div>   
    <div class="row">
        <?php echo CHtml::textArea('notes', '', array('rows'=>4, 'cols' => 60)); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Add'); ?>
        <?php echo CHtml::button('Clost Ticket',array('id' => 'btn-close-ticket','data-ticketid' => $crmLog->crm_id) ); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->
<?php else : ?>
   <div class="row buttons">
        <?php echo CHtml::button('Reopen',array('id' => 'btn-reopen-ticket','data-ticketid' => $crmLog->crm_id) ); ?>
    </div>
<?php endif;?>
</div>
<script type="text/javascript">
    $(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = $('#<?=$_GET["tabid"];?>').data('panel');
       $('form#crm-notes-form',panel).on(
            {'success': function(e, r){
                $('#<?=$_GET['tabid']?>_crm-ticket-view',panel).yiiGridView('update');
            },
                'reset': true
            }
        );
     $('#ExCrm_type', panel).next().on('dblclick', function(){
		if(window.confirm('Are you sure to override status?')){
			$(this).prev().attr('disabled', false);
		}
	});
          $('#ExCrm_assign_to', panel).next().on('dblclick', function(){
		if(window.confirm('Are you sure to override status?')){
			$(this).prev().attr('disabled', false);
		}
	});
    $('form#crm-notes-form',panel).on('submit',function(){
        var a=$("input:radio[name='process_method']:checked",panel).val();
        var b=$("textArea[name='notes']",panel).val();
        if(!a){
            alert('Please Choose Method');
            return false;
        }
        if(!b){
         alert("Please add some note");
         return false;
        }
    });

        // client close ticket
        $('#btn-close-ticket',panel).click(function(){
          // post close ticket request to server
            var action_url = 'crm/closeCrm/'+ $(this).data('ticketid') + '.app';
            var notes = $('textarea#notes').val();
             var data = $('#crm-notes-form').serialize()

            $.ajax({
                url: action_url,
                dataType: 'json',
                type: 'post',
                data : data,
                success: function(r){
                    if ( r.success == 1 ) {
                     $.get('<?=Yii::app()->createUrl('exCrm/update',array('id'=>$model->id,'tab'=>'overview','tabid'=>$_GET['tabid']))?>',function(s){
                       $('#grid-note-view',panel).html($(s).find('#grid-note-view').html());
                     });
                    }
                },
                error: function(r, e){ alert(e); }
            });

        });
           $('#btn-reopen-ticket').click(function () {

            // post close ticket request to server
            var action_url = 'crm/reopenCrm/' + $(this).data('ticketid') + '.app';

            $.ajax({
                url: action_url,
                dataType: 'json',
                type: 'post',
                success: function (r) {
                    if (r.success == 1) {
                    $.get('<?=Yii::app()->createUrl('exCrm/update',array('id'=>$model->id,'tab'=>'overview','tabid'=>$_GET['tabid']))?>',function(s){
                       $('#grid-note-view',panel).html($(s).find('#grid-note-view').html());
                   });
                    } else {
                        alert(r.error);
                    }
                },
                error: function (r, e) {
                    alert(e);
                }
            });

        });
    });
</script>