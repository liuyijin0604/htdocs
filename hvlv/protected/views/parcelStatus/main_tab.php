<h4>Uploaded documents</h4>
	<?php
		$type = 0;
		 if ($_GET['r']=='rec') {
		 	$type = 18;
			$theTypes=[18,19];
		 } elseif ($_GET['r']=='sed') {
		 	$type = 20;
			$theTypes=[19,20];
		 } elseif ($_GET['r']=='inv') {
		 	$type = 21;
			$theTypes=[19,21];
		 } elseif ($_GET['r']=='empp') {
		 	$type = 20;
			$theTypes=[19,20];
		 } elseif ($_GET['r']=='aqis') {
		 	$type = 20;
			$theTypes=[19,20];
		 } else {
			$type=-1;
			$theTypes=[-1];
		 }

			$fr = new FileRepo('search');
			$fr->unsetAttributes();
			$fr->theStatus = [20,30];
			if (empty($_GET['FileRepo'])) {
				$fr->theTypes = $theTypes;
			} else {
				$fr->attributes = $_GET['FileRepo'];
				if (empty($fr->type)) {
					$fr->theTypes = $theTypes;
				}
			}
			$fr->fid = $model->id;
			$mf = Acl::hasAccess('B:org/manageFile');
			$this->widget('zii.widgets.grid.CGridView', [
				'id' => '_import_custom-grid',
				'summaryText' => '',
				'dataProvider' => $fr->search(),
				'filter' => $fr,
				'columns' => [
					['name' => 'name', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->baseUrl."/filerepo/".$data->hash."/".$data->name."\" target=\"_blank\">".$data->name."</a>"'],
					[
						'name' => 'size',
						'value' => '$data->formatSize()',
						'filter' => false,
					],
					'date',
					[
						'class'=>'oButtonColumn',
						'template'=>'{delete}',
						'buttons'=>[
							'delete' => [
								'url'=>'Yii::app()->createUrl("filerepo/delete",array("id"=>$data->id))',
								'imageUrl'=>false,
								'visible'=>'$data->type!=19',
								'options' => ['class' => 'remove_class glyphicon glyphicon-remove', 'label'=>$this->t('Delete'), 'title' => '$data->name'],
							],
						],
					],
				],
			]);
			?>
					<?php if ($_GET['r']=='inv'):?>
					<h3>Invoices:</h3>
					<?php
			 $invoices = Invoice::model()->findAll('pid = :pid AND type in(41,45) AND status NOT IN (10,8)', [':pid' => $model->id]);

			 if(empty($invoices)){
			 	$app_name = Yii::app()->name;
				Yii::app()->name = 'TLA';
				$invoices = Invoice::model()->findAll('pid = :pid AND type in(41,45) AND status NOT IN (10,8)', [':pid' => $model->id]);
				Yii::app()->name = $app_name;
			 }
			 $dp = new CArrayDataProvider($invoices, [
				'id' => 'imconsol_real_invoices-'.$_GET["tabid"]
			 ]);
			$dp->pagination=['pageSize' => 5];
				 $total = 0;
			foreach ($invoices as $invoice) {
				$total += $invoice->total;
			}
			$currency="AUD";
$this->widget('zii.widgets.grid.CGridView', [
	'id'=>$_GET["tabid"].'_ordereport-grid',
	'cssFile' => false,
	'dataProvider' => $dp,// $dp->search(),
	'filter' => null,
	'enableSorting' => false,
	'columns'=>[
		['name' => 'no', 'value' => '$data->no', ],
		['name' => 'bill_to', 'value' => '$data->cust->name', ],
		['name' => 'status', 'value' => '$data->getStatus()', 'footer' => 'Total: ', 'footerHtmlOptions' => ['align' => 'right']],
		['header' => 'Invoice Total', 'value' => '$data->getCurrency().$data->total', 'footer' => $currency . ' ' . AppHelper::money_format("%i", $total)],
		[
			'class'=>'oButtonColumn',
			'template'=>'{view}',
			'buttons'=>[
				'view' => [
					'url' => 'Yii::app()->createURL("parcelStatus/print", array("id" => $data->id,"pid"=>'.$model->id.'))',
					'imageUrl'=>false,
					'options' => ['class' => 'grid_view_btn', 'target' => '_blank'],
				],
			],
		],
	]]);
			
 endif;?>
					<hr>
					<?php
			echo '<br />', CHtml::label($this->t('Upload Files'), 'uploader');
			if ($_GET['r']=='rec') {
				echo '<br/><p>Please upload Invoice and packing list seperately here:</p>';
			} elseif ($_GET['r']=='sed') {
				echo '<br/><p>Please upload Invoice and packing list seperately here:</p>';
			} elseif ($_GET['r']=='inv') {
				echo '<br/><p>Please upload payment receipt here:</p>';
			}
	$pphash = FileRepo::uploadHash($model, $type);
	$this->widget('application.extensions.plupload.PluploadWidget', [
		'config' => [
			'url' => $this->createUrl('filerepo/upload/'.$pphash),
			'max_file_size' => Yii::app()->params['maxFileSize'],
			'unique_names' => true,
			'file_list_height' => 60,
			'visible_header' => false,
			'filters' => [
				['title' => Yii::t('app', 'JPG, PDF, Word, Excel files'), 'extensions' => 'pdf,doc,docx,xls,xlsx,jpg,png'],
			],
			//'resize' => array('width' => 800, 'height' => 800, 'quality' => 80),
			'language' => Yii::app()->language,
			'max_file_number' => 5,
			'autostart' => false,
			'jquery_ui' => false,
			'reset_after_upload' => true,
		],
		'callbacks' => [
			'FileUploaded' => 'function(up,file,response){$("body").trigger("reload_pulfile_grid"); $.fn.yiiGridView.update("_import_custom-grid");}',
		],
		'id' => $_GET['tabid'].'_prodphoto_uploader',
	]);?>
					<br>
				 <script>
							$(function(){
								$('.remove_class').on('click',function(event){
										event.preventDefault();
								});  
								$('body').unbind('reload_cus_process_grid').bind('reload_cus_process_grid', function(){
		$('#_import_custom-grid').yiiGridView('update');
		return false;
					});
							 
									
							});
					</script>