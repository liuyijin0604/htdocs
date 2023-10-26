<?php
  $wmsProd = new WmsProd('search');
  $wmsProd->unsetAttributes();
  $wmsProd->orgs = $model;

  $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'wms-org-prod-grid',
    'cssFile' => false,
    'dataProvider'=>$wmsProd->search(),
    'filter'=>$wmsProd,
    'columns'=>array(
      array('name' => 'name', 'value' => 'empty($data) ? "" : $data->name . ($data->name_zh ? " - " . $data->name_zh : "")'),
      array('name' => 'ean', 'type' => 'raw', 'value' => 'empty($data)? "" : "<a href=\"".Yii::app()->createUrl("wmsProd/update", ["id" => $data->id])."\" class=\"tab_link\" title=\"Product ".$data->name."\">".$data->ean."</a>"'),
      array(
        'class'=>'oButtonColumn',
        'template'=>'{view}',
        'buttons'=>array
        (
          'view' => array(
            'imageUrl'=>false,
            'url'=>'Yii::app()->createUrl("wmsProd/view", ["id" => $data->id])',
            'options' => array('class' => 'jqm_link grid_view_btn', 'data-win-class' => 'L'),
          ),
        ),
      ),
    ),
  ));
?>