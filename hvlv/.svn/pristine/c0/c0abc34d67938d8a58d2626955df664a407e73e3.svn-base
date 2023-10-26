<!--
@Author:Nero Wang
@Date:2021/5/18
@Description: ORG add user tab
-->
<?php 
  $user=new User('search');
  $user->unsetAttributes();
  if(!empty($_GET['User'])) {
    $user->setAttributes($_GET['User']);  
   }
  $user->org_id = $model->id;
   
    $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'user-grid',
        'cssFile' => false,
        'dataProvider'=>$user->search(),
        'filter'=>$user,
        'columns'=>array(
            array(
                'name'=>'type',
                'value'=>'$data->getType()',
                'filter'=>CHtml::dropDownList('User[type]', $user->type, $this->t(User::$types), array('prompt'=>$this->t('All'))),
            ),
            array('name' => 'dpt_id', 'value' => '$data->getBranch()', 'filter' => CHtml::dropDownList('User[dpt_id]', $user->dpt_id, Org::dptList(), ['prompt' => $this->t('All')]),),
            'title',
            'fname',
            'lname',

            //'user',
            array(
                'name' => 'email',
                'type' => 'raw',
                'value' => 'CHtml::link($data->email,"mailto:".$data->email)',
            ),
            'phone',
    
            array(
                'name'=>'org_search',
                'value'=>'@$data->org->name',
            ),
    
            array(
                'name'=>'active',
                'value'=>'$data->getActive()',
                'filter'=>CHtml::dropDownList('User[active]', $user->active, $this->t(['1' => 'Yes', 0 => 'No']), array('prompt'=>$this->t('All'))),
            ),
    
            array(
                'class'=>'CButtonColumn',
                'template'=>'{update}',
                'buttons'=>array(
                    'update' => array(
                        'imageUrl'=>false,
                        'url'=>'Yii::app()->createUrl("user/update", ["id" => $data->id])',
                        //'visible'=>'$data->id > 1',
                        'options' => array('class' => 'tab_link grid_edit_btn', 'title'=>$this->t('Update User')),
                    ),
                ),
            ),
        ),
    ));
 ?>
 