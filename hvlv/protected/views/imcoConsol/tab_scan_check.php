<div style="padding-bottom: 5px;">
<table>
<?php
$s = $model->courierSummaryWeight();
$col = [];
foreach ($s as $oid => $data) {
	$ctt = "<td>".$data[0].': '.$data[1]."&nbsp;&nbsp;&nbsp;&nbsp;</td><td>Pkg:".$data[3]."&nbsp;&nbsp;&nbsp;&nbsp;</td><td> Weight:".$data[4]."&nbsp;&nbsp;&nbsp;&nbsp;</td><td> ScanCount:".$data[6]."&nbsp;&nbsp;&nbsp;&nbsp;</td>";
	if (!empty($data[5])) {
		$ls = [];
		foreach ($data[5] as $ltype => $ld) {
			$ls[] = preg_replace('/ Letters| up to|\d{3}g-/', '', ImParcel::$letter_types[$ltype]).': '.$ld;
		}
		$ctt .= '<td>('.implode(', ', $ls).')</td>';
	}else
	{
		$ctt .= '<td></td>';
	}
	$col[] = "<tr>".$ctt."</tr>";
}
echo implode('', $col);
?>
</table>
</div>


<?php
	$imConsolService = new ImConsolService();
	[$bagNumbers,$bagScanned,$bagUnscan] = $imConsolService->getBagScanData($model);
	if($bagNumbers>0)
	{
		echo "<h3>Bag: {$bagNumbers}</h3>";
		echo "<h3>Bag scanned: {$bagScanned}</h3>";
		echo "<h3>Bag unscan: {$bagUnscan}</h3>";
	}
?>

<div class="form">


<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'console-notes-form',
	'enableClientValidation'=>true,
	'action'=>$this->createUrl('imcoConsol/notes', array('id' => $model->id)),
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
));
?>

<!-- <div class="row buttons">
	<?php echo CHtml::submitButton('Save'); ?>
</div> -->

<?php $this->endWidget(); ?>

</div><!-- form -->
<?php
$filtersForm=new FiltersForm;
$imConsolService = new ImConsolService();
$provide = $imConsolService->getConsolBagTagsPrint($model->id,"",true,true,false);
$filteredData=$filtersForm->filter($provide);
$dataProvider=new CArrayDataProvider($filteredData);
$dataProvider->pagination=['pageSize' =>20,];
$sort=new CSort();
$sort->attributes=[
	'bag_tag'=>[
		'asc'=>'bag_tag ASC',
		'desc'=>'bag_tag DESC',
	],
	'total_packages'=>[
		'asc'=>'total_packages ASC',
		'desc'=>'total_packages DESC',
	],
	'scan_status'=>[
		'asc'=>'scan_status ASC',
		'desc'=>'scan_status DESC',
	],
];
$sort->defaultOrder = "status ASC";
$dataProvider->sort=$sort;

$this->widget('zii.widgets.grid.CGridView', [
	'id'=>'tab_scan_check'.$_GET['tabid'],
	'cssFile' => false,
	'dataProvider'=>$dataProvider,
	'filter'=>$filtersForm,
	'columns'=>[
		array('name' => 'bag_tag','header'=>'Bag Tag'),
		array('name' => 'total_packages','header'=>'Total Packages'),
		array('name' => 'scan_status','header'=>'Scan Status')
	],
]);
?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	
	$('form#console-notes-form', panel).on({'success': function(e,r){
			$('#<?=$_GET["tabid"]?>_log-grid', panel).yiiGridView('update');
		},
		'reset': true
	}
	);
});
</script>