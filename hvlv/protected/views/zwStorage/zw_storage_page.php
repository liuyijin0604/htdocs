
<br>
<div class="form-group">
<a class="jqm_link" data-win-class= 'XL'
             href="<?=$this->createUrl('zwStorage/updateZWStorage')?>"><?php echo 'New Record'?></a>

<br/>

 <div id="zw-record-list-view">
<?php $this->renderPartial('zw_storage_sub_list',["model"=>$model])?>
</div>
<script type="text/javascript">

    $(function(){
          
        
    });


</script>
    
