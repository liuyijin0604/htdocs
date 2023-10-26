<?php
$fr = new FileRepo('search');
$fr->unsetAttributes();
$fr->type = 89;
$fr->fid = $model->id;
$mf = true;

$this->widget('application.extensions.editablegrid.CEditableGridView', array(
    'id'=>$_GET['tabid'].'_wtfile-grid',
    'cssFile' => false,
    'summaryText'=>'',
    'dataProvider'=> $fr->search(),
    'filter'=>$fr,
    'showQuickBar' => false,
    'columns'=>array(
        array('name' => 'name', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->baseUrl."/filerepo/".$data->hash."/".$data->name."\" target=\"_blank\">".$data->name."</a>"'),
        array(
            'name'=>'size',
            'value'=>'$data->formatSize()',
            'filter' => false,
        ),
        'date',
        array(
            'header' => 'note',
            'value' => '!empty($data->mdata["note"])?$data->mdata["note"]:""',
        ),
        array('name' => 'status', 'type'=>'raw', 'value' => '$data->getStatus()', 'visible' => !$mf,
        'filter'=>CHtml::dropDownList('FileRepo[status]', $fr->status, FileRepo::$states, array('prompt'=>$this->t('All'))) ),
    ),
));

if($mf){
    echo '<br />', CHtml::label($this->t('Upload Files'),'uploader');
    $pphash = FileRepo::uploadHash($model, 89);
    $this->widget('application.extensions.plupload.PluploadWidget', array(
     'config' => array(
         'url' => $this->createUrl('filerepo/upload/'.$pphash),
         'max_file_size' => Yii::app()->params['maxFileSize'],
         'unique_names' => true,
         'file_list_height' => 300,
         'visible_header' => false,
         'filters' => array(
              array('title' => Yii::t('app', 'JPG, PDF, Word, Excel files'), 'extensions' => 'pdf,doc,docx,xls,xlsx,jpg,png'),
          ),
         //'resize' => array('width' => 800, 'height' => 800, 'quality' => 80),
         'language' => Yii::app()->language,
         'max_file_number' => 5,
         'autostart' => true,
         'jquery_ui' => false,
         'reset_after_upload' => true,
     ),
     'callbacks' => array(
         'FileUploaded' => 'function(up,file,response){$("#'.$_GET["tabid"].'").trigger("reload_wtfile_grid");}',
     ),
     'id' => $_GET['tabid'].'_prodphoto_uploader',
    ));
}
?>
<script type="text/javascript">
    $(function(){
        var tab = $("#<?=$_GET['tabid'];?>");
        var panel = tab.data('panel');
        tab.unbind('reload_wtfile_grid').bind('reload_wtfile_grid', function(){
            $('#<?=$_GET["tabid"];?>_wtfile-grid', tab.data('panel')).yiiGridView('update');
            return false;
        });
    });
</script>