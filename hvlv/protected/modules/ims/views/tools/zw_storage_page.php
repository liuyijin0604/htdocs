<style type="text/css">
    .container
    {
        width:100%;
    }



</style>
<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link('Home', array('site/index')),
        'links' => array(
        '海拼登记入仓',
    ),
));
// $form=$this->beginWidget('CActiveForm', array(
//     'id'=>'ad_search_form',
//     'enableAjaxValidation'=>false,
//     ));
?>

<br>
<div class="form-group">
<a class="dash-item ajax-link"
             href="<?=$this->createUrl('tools/updateZWStorage')?>"><?php echo 'New Record'?></a>

<br/>

 <div id="zw-record-list-view">
<?php $this->renderPartial('zw_storage_sub_list',["model"=>$model])?>
</div>
<script type="text/javascript">

    $(function(){
          
        
    });


</script>
    
