<?php
$podName=($_GET['pod_id']<0)?"All":Org::dptList()[$_GET['pod_id']];
$type=$_GET['consol_type']==20?"Sea":"Air";
?>
<h2><?=$podName.' '.$type;?> - Consol Processing</h2>
<div style="position: absolute; right:80px;">
	<div class="form">
		<div class='row'>
			<div class="rowcol rowleft">
			<a class="tab_link <?=($permissionArr[3]==2)?"dark_link":""?>"  href="<?=$this->createUrl("consolProcess/seaConsolListByStatus", ['pod_id'=>$_GET['pod_id'],'consol_type'=>$_GET['consol_type'],'process_status'=>ConsolProcess::STATE_NEW])?>" title="<?=$podName."-".$type?> New" ><span style="background-position:-48px -688px" class="icon"></span>Documentation---></a>
			</div>

			<div class="rowcol ">
			<a class="tab_link <?=($permissionArr[3]==2)?"dark_link":""?>"  href="<?=$this->createUrl("consolProcess/seaConsolListByStatus", ['pod_id'=>$_GET['pod_id'],'consol_type'=>$_GET['consol_type'],'process_status'=>ConsolProcess::STATE_WAITING_AIRPORT_CKIN])?>" title="<?=$podName."-".$type?> Waiting AirOut/Manifest" ><span style="background-position:-48px -688px" class="icon"></span>Waiting Receipt Outturn/Unpack schedule---></a>

			</div>

			<div class="rowcol">

			<a class="tab_link <?=($permissionArr[3]==1)?"dark_link":""?>"  href="<?=$this->createUrl("consolProcess/seaConsolListByStatus", ['pod_id'=>$_GET['pod_id'],'consol_type'=>$_GET['consol_type'],'process_status'=>ConsolProcess::STATE_WAITING_WH_SCAN])?>" title="<?=$podName."-".$type?> Waiting WH Scan" ><span style="background-position:-48px -688px" class="icon"></span>Waiting WH Scan---></a>

			</div>

			<div class="rowcol">


			<a class="tab_link <?=($permissionArr[3]==1)?"dark_link":""?>"  href="<?=$this->createUrl("consolProcess/seaConsolListByStatus", ['pod_id'=>$_GET['pod_id'],'consol_type'=>$_GET['consol_type'],'process_status'=>ConsolProcess::STATE_WAITING_AIR_MANI])?>" title="<?=$podName."-".$type?> Waiting AirOut/Manifest" ><span style="background-position:-48px -688px" class="icon"></span>Waiting Unpack Outturn/Manifest---></a><!--(will have receipt and unpack ICS status identify)-->
			</div>

			<div class="rowcol">

				<p><a class="tab_link <?=($permissionArr[3]==1)?"dark_link":""?>"  href="<?=$this->createUrl("consolProcess/seaConsolListByStatus", ['pod_id'=>$_GET['pod_id'],'consol_type'=>$_GET['consol_type'],'process_status'=>ConsolProcess::STATE_WAITING_CREATE_INVOICE])?>" title="<?=$podName."-".$type?> Waiting AirOut/Manifest/Create Invoice" ><span style="background-position:-48px -688px" class="icon"></span>Exception/Dehire/Create Invoice---></a></p>
				<a class="tab_link <?=($permissionArr[3]==1)?"dark_link":""?>"  href="<?=$this->createUrl("consolProcess/exceptionShipmentList", ['pod_id'=>$_GET['pod_id'],'consol_type'=>$_GET['consol_type'],'podName'=>$podName,'process_status'=>ConsolProcess::STATE_WAITING_CREATE_INVOICE])?>" title="<?=$podName."-".$type?> Handling Exception Shipments" ><span style="background-position:-48px -688px" class="icon"></span>Handling Exception Shipments(<font class="red" id="<?=$_GET['pod_id'].$_GET['consol_type']?>Exception">0</font>)---></a>
					 <!--(will have receipt and unpack ICS status identify)-->
			</div>

			<div class="rowcol">

			<a class="tab_link <?=($permissionArr[3]==1)?"dark_link":""?>"  href="<?=$this->createUrl("consolProcess/seaConsolListByStatus", ['pod_id'=>$_GET['pod_id'],'consol_type'=>$_GET['consol_type'],'process_status'=>ConsolProcess::STATE_CONSOL_PROCESS_DONE])?>" title="<?=$podName."-".$type?> Process Done" ><span style="background-position:-48px -688px" class="icon"></span>Process Done</a>
			</div>
		</div>
		</div>
</div>
<br>
<br>
<div style="width:50%" id="sea-consols-client-view<?=$_GET['tabid']?>">
	 <?php $this->widget('zii.widgets.grid.CGridView', [
	 	'id'=>'sea-consols-client-list-grid',
	 	'htmlOptions'=>['style'=>'width: 70%'],
	 	'cssFile' => false,
	 	'dataProvider'=>$dataProvider[0],
	 	'filter'=>$dataProvider[1],
	 	'columns'=>[
	 		['name'=>'status','headerHtmlOptions' => ['style' => 'display:none'],'filterHtmlOptions' => ['style' => 'display:none'],
	 			'htmlOptions' => ['style' => 'display:none'],'type'=>'raw'],
	 		['name'=>'status_name','header'=>'Status'],
	 		['name'=>'number', 'header'=>'Number'],
	 		['name'=>'day1','header'=>'<=3 days'],
	 		['name'=>'day2','header'=>'4-6 days'],
	 		['name'=>'day3','header'=>'>=7 days','cssClassExpression' => '$data>0? "cloumn_red_1" : ""'],
	 	],
	 ]); ?>
</div>
<div style="right: 20px;position: absolute;">
<a class="export_search" target="_blank" href="<?=$this->createUrl('consolProcess/exportPreAlertSea', ['pod_id'=>$_GET['pod_id'],'consol_type'=>$_GET['consol_type']]);?>"><div style="background-position:-48px -688px" class="icon"></div> Export Pre Alert</a> 
<a class="export_search" target="_blank" href="<?=$this->createUrl('consolProcess/exportPreAlertSea', ['typ' => '','pod_id'=>$_GET['pod_id'],'hideCusotmer'=>true]);?>"><div style="background-position:-48px -688px" class="icon"></div> Export Pre alert without customer</a> 
</div>
 <div class="row">
	<span> <h2> Available Consols </h2></span>
										<!--<div id="loadingPic"  style="width:20px;height:20px;float:left;"></div>-->
 <div id="consol-list-view">
			<?php
				 $this->renderPartial('sea_sub_consols', [
				 	'consol' => $consol,
				 	//           'name' =>'All'
				 ]);
		?>
	</div>
</div>


<script type="text/javascript">
	$(function(){
		function refreshExN()
		{
			$.ajax({
		              url: "<?=$this->createUrl("consolProcess/getExceptionShipmentNumber", ['pod_id'=>$_GET['pod_id'],'consol_type'=>$_GET['consol_type']])?>",
		              type: "get",
		              processData: false,
		              contentType: false,
		              success: function(r) {
		                  $("#<?=$_GET['pod_id'].$_GET['consol_type']?>Exception").html(r);
		               },
		              error: function(e) {
		                  console.log(e);
		              }
		      });
		}
		$("#<?=$_GET['pod_id'].$_GET['consol_type']?>Exception").everyTime(6e4, function(){
			refreshExN();
		});
		refreshExN();
	});

</script>