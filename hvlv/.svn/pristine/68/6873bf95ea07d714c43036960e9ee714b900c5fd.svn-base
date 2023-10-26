<h3> Create Org  In A Batch</h3>
<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'org-create-form',
        'enableAjaxValidation'=>false,
        'htmlOptions'=>['target'=>'org_create_result','class'=>'ifrm-form','enctype' =>'multipart/form-data'],
        'action' => $this->createUrl('org/createOrgInBatch'),
    ));
    ?>
   <div class="row" style="margin-top: 2px;">
        <label for="postw-batch">Org List - <small>.xlsx File</small>(<a href="/ims/PCA_Org_Detail_Template.xlsx" target="_blank">Get template file</a>)</label> <br>
        <input type="file" name="org_detail_file" id="org_detail_file" />
        <input type="hidden" value="1" name="post_flag_field"/>
    </div>
    <br>
    <input id="submit_btn" type="submit" value="Create" />

    <?php $this->endWidget(); ?>
</div>   
<br/>
<br/>
<br/><br/>
<iframe id="org_create_result" name="org_create_result" style="color: green; margin: 10px 0; border: 1px solid black;padding:20px; width: 90%; "></iframe>
<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel=tab.data('panel');nction(){
        var tab = $('#<?=$_GET["tabid"];?>');
       $("#submit_btn",panel).on('click',function(){  
               $("#err_result",panel).contents().find("body").html('');
           });
    });
</script>

