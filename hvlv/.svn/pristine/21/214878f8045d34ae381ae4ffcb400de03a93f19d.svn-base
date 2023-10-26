
<h1> Import customer final Check weight</h1>
<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'client-check-import-form',
        'enableAjaxValidation'=>false,
        'htmlOptions'=>['target'=>'err_result','class'=>'ifrm-form','enctype' =>'multipart/form-data'],
//        'action' => $this->createUrl('uploadMani/createByMani'),
    ));
    ?>
   <div class="row" style="margin-top: 2px;">
       <label for="postw-batch">Consignments List - <small>.xlsx File</small>(<a href="/ims/weight_gap_check_template.xlsx" target="_blank">Tempalte file</a>)</label> <br>
        <input type="file" name="client_check_file" id="client_check_file" />
    </div>
    <br>
    <input id="submit_btn" type="submit" value="Upload" />

    <?php $this->endWidget(); ?>
</div>


<br/><br/>
<iframe id="err_result" name="err_result" style="color: green; margin: 5px 0; border: 1px solid black;padding:10px; width: 90%; "></iframe>


<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
         $("#submit_btn",panel).on('click',function(){
               $("#err_result",panel).contents().find("body").html('');
           });
   });

</script>

