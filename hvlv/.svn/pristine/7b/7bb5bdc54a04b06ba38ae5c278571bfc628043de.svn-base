<div class="pane">
    <div class="form">
  <?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'custom-user-assign-form',
	'enableAjaxValidation'=>false,
      )); ?>
    <h3>Assign User</h3>
    <?php 
    $user_maps=[];
    $aumAll = 0;
       $rs =TypeMapUser::model()->findAll('type=:type and status=1',array(':type'=> TypeMapUser::TYPE_CUSTOMER_SERVICE));
       foreach($rs as $oneMap){
           $user_maps[$oneMap['map_type']][]=$oneMap['user_id'];
       }
      $faqList = CsFaq::model()->findAll("  status = 0  ");

     foreach ($faqList as $key => $value): ?>
      <?php $thisKey = $value->id;?>
        <div class="row" id="<?="faqAss".$thisKey?>">
            <label><?=$value->content?></label>
            <?php if(isset($user_maps[$thisKey]))
            {
              foreach ($user_maps[$thisKey] as $keyuis => $userIds)
              {
                $aumAll++;
                $thisId = $thisKey."_".$aumAll;
                echo "<div id ='audiv{$thisId}'>";
              echo CHtml::dropDownList($thisId,@$userIds, ImportsMail::$importscs_list_id,array('prompt'=>'SELECT'));
              echo CHtml::button('X', ["class"=>"deleteCustomerServiceUser","onClick"=>"deleteCustomerServiceUser('{$thisId}');"]),"&nbsp;&nbsp";
              echo "</div>";

              } 
            }else
            {
              $aumAll++;
              echo "<div id ='audiv{$thisKey}'>";
               echo CHtml::dropDownList($thisKey,@$user_maps[$thisKey], ImportsMail::$importscs_list_id,array('prompt'=>'SELECT'));
               echo CHtml::button('X', ["class"=>"deleteCustomerServiceUser","onClick"=>"deleteCustomerServiceUser({$thisKey});"]),"&nbsp;&nbsp";
                echo "</div>";

            }
            ?>
          
           <?=CHtml::button('+', ["class"=>"addCustomerServiceUser","onClick"=>"addCustomerServiceUser({$thisKey});"]),"&nbsp;&nbsp";?>
        </div>
    <?php endforeach; ?>
    <div class="row button">
        <?php echo CHtml::submitButton('Save');?>
    </div>
    <?php $this->endWidget(); ?>
     </div>
     <div id="hidedList">
        <?php  echo CHtml::dropDownList("","",ImportsMail::$importscs_list_id,array('id'=>'hidedDownList','prompt'=>'SELECT'));?>
     </div>
</div>
<script>
  var aumAll = <?=$aumAll?>;
    $(function(){
        var win = $('#jqmw_<?=$_GET["tabid"];?>');
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = $('#<?=$_GET["tabid"];?>').data('panel');
        function deleteZoneMapImport(id,type)
        {
            if(confirm("Are you sure to delete the Test Data?"))
            {
                var tab = $('#<?=$_GET["tabid"];?>');
                var panel = tab.data('panel');
                $.ajax({
                    type : 'POST',
                    url : '<?php echo Yii::app()->createAbsoluteUrl("importsCost/deleteZoneMapImport") ;?>',
                    dataType: 'html',
                    data:{ 'id' : id,'type':type},
                    success:function(resp){
                      if(type ==0)
                      {
                       $("#pcaTestZone",panel).html(resp);
                      }else
                      {
                        $("#weightZone",panel).html(resp);
                      }
                    }
                });
            }
        }
      });

      function deleteCustomerServiceUser(id)
        {
          $("#audiv"+id).html("");
        }
        function addCustomerServiceUser(id)
        {
          aumAll++;
          $hideId = "hidedDownList";
          let thisDiv= $("#hidedDownList");
          thisDiv.attr("name",id+"_"+aumAll);
          thisDiv.attr("id","");
          let addHtml = $("#hidedList").html();
          thisDiv.attr("id","hidedDownList");
          let myHtml = $("#faqAss"+id).html();
          $("#faqAss"+id).html(myHtml+addHtml);
        }
</script>

