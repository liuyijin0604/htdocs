<style>
    .cloumn_red{
        background-color:red;
    }
</style>
<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link('Home', array('shipment/heldList')),
    'links' => array(
           'Upload Customs List',
    ),
));
?>
 <?php
  echo '<br /><h3>', CHtml::label($this->t('Upload Customs Files(Filename: HBN-filename.fileType)'), '</h3>uploader');

  $this->widget('application.extensions.plupload.PluploadWidget', [
      'config' => [
          'url' => $this->createUrl('shipment/uploadCustomsFiles/'),
          'max_file_size' => Yii::app()->params['maxFileSize'],
          'unique_names' => true,
          'file_list_height' => 300,
          'visible_header' => false,
          'filters' => [
              ['title' => Yii::t('app', 'JPG, PDF, Word, Excel files'), 'extensions' => 'pdf,doc,docx,xls,xlsx,jpg,png,txt'],
          ],
          //'resize' => array('width' => 800, 'height' => 800, 'quality' => 80),
          'language' => Yii::app()->language,
          'max_file_number' => 200,
          'autostart' => false,
          'jquery_ui' => false,
          'reset_after_upload' => true,
      ],
      'callbacks' => [
          'FileUploaded' => 'function(up,file,response){$("#_excofile-grid").yiiGridView("update");alert("Upload Customs Files Success");}',
      ],
      'id' => 'excofile_uploader_1',
  ]);
  ?>

<?php
 echo '<br /><h3>', CHtml::label($this->t('Uploaded Customs File List'), '</h3>');

$fr = new FileRepo('search');
$fr->unsetAttributes();
if (empty($_GET['FileRepo'])) {
  $fr->theTypes = [20];
} else {
  $fr->attributes=$_GET['FileRepo'];
  if (empty($fr->theTypes)) {
    $fr->theTypes = [20];
  }
}
$fr->org_id = User::currentUserOrgId();
$fr->status=[20];
$mf = Acl::hasAccess('B:org/manageFile');

$this->widget('zii.widgets.grid.CGridView', [
  'id'=>'_excofile-grid',
  'cssFile' => false,
  'summaryText'=>'',
  'dataProvider'=> $fr->search(),
  'filter'=>$fr,
  'columns'=>[
    ['name' => 'name', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->baseUrl."/filerepo/".$data->hash."/".$data->name."\" target=\"_blank\">".$data->name."</a>"'],
    [
      'name'=>'size',
      'value'=>'$data->formatSize()',
      'filter' => false,
    ],
    'date'
  ],
]);
?>


<div class="modal fade" id="modal-tracking" tabindex="-1" role="dialog" aria-labelledby="modal-tracking-label" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" id='modal_close' class="btn btn-default" data-dismiss="modal">////<?=$this->t('Close');?></button>
      </div>
    </div>
  </div>
</div>
</div>
<script type="text/javascript">
        function exportHeld()
    {
          var q = $('.filters input, .filters select').serialize();
            $(this).attr('href', '<?=$this->createUrl('shipment/exportHeld');?>'+ '?' + q);
            return true;
    }
    </script>

 <?php ob_start(); ?>
<script type="text/javascript">
$(function(){

    $('body').off('click', 'a.tracking-modal-link').on('click', 'a.tracking-modal-link', function(e){
        $('#modal-tracking').modal();
        $('#modal-tracking .modal-body').load($(this).attr('href'));
        e.preventDefault();
    });
    $('#export_search').on('mousedown', function(){
        var q = $('.filters input, .filters select').serialize();
        $(this).attr('href', '<?=$this->createUrl('shipment/exportHeldList');?>'+ '?' + q);
        return true;
    });
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>