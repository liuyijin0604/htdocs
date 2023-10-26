<?php
$crm = new Crm('search');

$crm->unsetAttributes();
$crm->parcel_id = $model->id;
$crm->due = date('Y-m-d');

if ( !empty($_GET['Crm']) ) {
    $crm->attributes = $_GET['Crm'];
}

$this->widget('zii.widgets.grid.CGridView', array(
			'id'=>$_GET["tabid"].'_crm-grid',
			'cssFile' => false,
			'summaryText'=>'',
			'dataProvider'=> $crm->search(),
            'filter' => $crm,
			'columns'=>array(

               array( 'name' => 'id',
                   'type' => 'raw',
                   'value' => '"<a href=\"".Yii::app()->createURL("crm/updateCrm", array("id" => $data->id))."\" class=\"tab_crm_link\" title=\"".$data->id."\">".$data->id."</a>"'),

                array(
                    'name'=>'type',
                    'value'=>'$data->getType()',
                    'filter'=>CHtml::dropDownList('Crm[type]', $crm->type,$crm->getTypes() , array('prompt'=>$this->t('All')))
                ),

                array(
                    'name'=>'level',
                    'value'=>'$data->getLevel()',
                    'filter'=>CHtml::dropDownList('Crm[level]', $crm->level, $crm->getLevels() , array('prompt'=>$this->t('All')))
                ),

                array(
                    'name'=>'source',
                    'value'=>'$data->getSource()',
                    'filter'=>CHtml::dropDownList('Crm[source]', $crm->source, $crm->getSources() , array('prompt'=>$this->t('All')))
                ),


				array(
                    'header' => 'Last Note',
                    'value' => '$data->getLastNote()',
                ),
                array(
		            'name'=>'status',
                    'type' => 'raw',
		            'value'=>'$data->getStatus()',
                    'filter'=>CHtml::dropDownList('Crm[status]', $crm->status, Crm::$states , array('prompt'=>$this->t('All')))
		        ),

                array(
                    'name'=>'operator_id',
                    'value'=>'$data->getUser()',
                ),

                array(
                    'header' => 'create_time',
                    'value' => '$data->create_time'
                ),

                'due',
                array(
                    'class'=>'oButtonColumn',
                    'template'=>'{view}',
                    'buttons'=>array
                    (
                        'view' => array(
                            'imageUrl'=>false,
                            'options' => array('class' => 'jqm_link grid_view_btn'),
                            'url' => 'CHtml::normalizeUrl(array("crm/viewCrm/","id" => "$data->id"))'
                        )
                    ),
                ),

			),
		));
?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'shipment-crmnotes-form',
	'enableClientValidation'=>true,
	'action'=>$this->createUrl('crm/crmNotes', array('id' => $model->id)),
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
));
?>
<h2>Create New Ticket</h2>

<?php echo $form->errorSummary($model); ?>

    <?php
    // get CRM type and level
    // const 1 means CRM type parent id in oList table
    // const 5 means CRM level parent id  in oList table
    $types = $crm->getTypes();
    $levels = $crm->getLevels();
    $sources = $crm->getSources();

    ?>

    <div class="row">
        <span>Type:</span>
        <?php
        $index = 0;
        foreach ( $types as $k => $v ) {
            echo  CHtml::radioButton('crm_type',($index == 0), array(
                    'value'=> $k,
                    'id'=>'crm_type_' . $k,
                    'uncheckValue'=>null
                )) . $v . ' &nbsp;';
            $index++;
        }
        ?>

    </div>

    <div class="row">
        <span>Level:</span>
        <?php
        $index = 0;
        foreach ( $levels as $k => $v ) {
           echo  CHtml::radioButton('crm_level',($index == 0), array(
                'value'=> $k,
                'id'=>'crm_level_' . $k,
                'uncheckValue'=>null
            )) . $v . ' &nbsp;';
            $index++;
        }
        ?>

    </div>

    <div class="row">
        <span>Source:</span>
        <?php
        $index = 0;
        foreach ( $sources as $k => $v ) {
            echo  CHtml::radioButton('crm_source',($index == 0), array(
                    'value'=> $k,
                    'id'=>'crm_source_' . $k,
                    'uncheckValue'=>null
                )) . $v . ' &nbsp;';
            $index++;
        }
        ?>

    </div>


<div class="row">
	<?php echo CHtml::textArea('notes', '', array('rows'=>4, 'cols' => 60)); ?>
</div>

    <div class="row rowcol">
        <span>Due:</span>
        <?php echo $form->textField($crm,'due', ['size' => '12', 'class' => 'date_input']); ?>

    </div>

    <div class="row buttons">
	<?php echo CHtml::submitButton('Create'); ?>
</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	
	$('form#shipment-crmnotes-form', panel).on({'success': function(e,r){
			$('#<?=$_GET["tabid"]?>_crm-grid', panel).yiiGridView('update');
		},
		'reset': true
	}
	);
});
</script>