<h2>Customs List</h2>
<?php if (Yii::app()->user->grp != 72) : ?>
	<div style="position:absolute; right: 80px;top: 25px">
		<a class="jqm_link" href="<?= $this->createUrl("customProcess/assignUser") ?>" title="Assign User"><span class="icon"></span>Assign User</a>
	</div>
	</br>
	<div style="">
		<div style="transform: translateX(45em);">
			<div style="transform: translateX(-1.2em) translateY(1.7em) rotate(-30deg);width:1.5em;">-></div>
			<?php
				foreach (ShipmentProcess::$insstates as $key => $value) {
					$thisId = join('_',explode(' ', strtolower($value)));
					echo '<a class="tab_link" href="'.$this->createUrl("customProcess/getCustomsListByStatus",["status"=>$key]).'" title="'.$value.'"><span class="icon"></span>'.$value.' (<span id="'.$thisId.'">'.ShipmentProcess::getProcessAmount([$key]).'</span>)</a>';
					if($key!=ShipmentProcess::STATE_AQIS_CUSTOMS_DONE)
					{
						echo "->";
					}
				}
			?>
		</div>
		<div  style="transform: translateX(18em);">
			<div style="transform: translateX(-8.5em) translateY(4.2em) rotate(-70deg);width:15em;">---------------------></div>
			<a class="tab_link" href="<?= $this->createUrl("customProcess/aqisMessageCustom") ?>" title="AQIS Message to Custom"><span class="icon"></span>AQIS Message to Custom (<span id='aqis_custom'><?= ShipmentProcess::getProcessAmount([ShipmentProcess::STATE_AQIS_CUSTOM]) ?></span>)</a>->
			<a class="tab_link" href="<?= $this->createUrl("customProcess/getCustomsListByStatus",["status"=>ShipmentProcess::STATE_AQIS_DONOT_MOVE]) ?>" title="AQIS Do not move"><span class="icon"></span>AQIS Donot Move (<span id='aqis_custom'><?= ShipmentProcess::getProcessAmount([ShipmentProcess::STATE_AQIS_DONOT_MOVE]) ?></span>)</a>
		</div>
		<div style="transform: translateX(45em);">
			<div style="transform: translateX(-1.2em) translateY(0.3em) rotate(30deg);width:1.5em;">-></div>
			<?php
				foreach (ShipmentProcess::$disstates as $key => $value) {
					$thisId = join('_',explode(' ', strtolower($value)));
					echo '<a class="tab_link" href="'.$this->createUrl("customProcess/getCustomsListByStatus",["status"=>$key]).'" title="'.$value.'"><span class="icon"></span>'.$value.' (<span id="'.$thisId.'">'.ShipmentProcess::getProcessAmount([$key]).'</span>)</a>';
					if($key!=ShipmentProcess::STATE_DISPOSAL_DONE)
					{
						echo "->";
					}
				}
			?>
		</div>
		<div  style="transform: translateX(18em) translateY(1em);">
			<div style="transform: translateX(-5.5em) translateY(-0.5em) rotate(-45deg);width:15em;">--------></div>
			<a class="tab_link" href="<?= $this->createUrl("customProcess/emppMessageCustom") ?>" title="EMPP Message to Custom"><span class="icon"></span>EMPP Message to Custom (<span id="empp_custom"><?= ShipmentProcess::getProcessAmount([ShipmentProcess::STATE_EMPP_CUSTOM]) ?></span>)</a>
		</div>
	</br>
	</br>
	</br>
		<a class="tab_link" href="<?= $this->createUrl("customProcess/msgSend") ?>" title="Message Send"><span class="icon"></span>Message Send(<span id='msg_send'><?= ShipmentProcess::getProcessAmount([ShipmentProcess::MSG_SENDER, ShipmentProcess::MSG_CONSIGNEE]) ?></span>)</a>

		<div style="width: 18.5em;height:2em;display:inline-block;">
			<a class="tab_link" href="<?= $this->createUrl("customProcess/docConfirmed") ?>" title="Document Confirmed"><span class="icon"></span>Document Confirmed (<span id='doc_wait'><?= ShipmentProcess::getProcessAmount([ShipmentProcess::DOC_CONFIRMED]) ?></span>)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
			<a class="tab_link" href="<?= $this->createUrl("customProcess/waitingConfirm") ?>" title="Document Waiting Confirm"><span class="icon"></span>Document Waiting Confirm (<span id='doc_wait'><?= ShipmentProcess::getProcessAmount([ShipmentProcess::DOC_RECEIVED]) ?></span>)</a>
		</div>



		<a class="tab_link" href="<?= $this->createUrl("customProcess/waitingDeclaration") ?>" title="Waiting for Broker Declaration "><span class="icon"></span>Waiting for Broker Declaration (<span id='msg_brok'><?= ShipmentProcess::getProcessAmount([ShipmentProcess::MSG_BROKER]) ?></span>)</a>
		<a class="tab_link" href="<?= $this->createUrl("customProcess/waitingEntryConfirm") ?>" title="Waiting Entry Confirm "><span class="icon"></span>Waiting for Entry Confirm (<span id='wait_entry'><?= ShipmentProcess::getProcessAmount([ShipmentProcess::ENTRY_SEND]) ?></span>)</a>
		<a class="tab_link" href="<?= $this->createUrl("customProcess/entryConfirmed")  ?>" title="Entry Confirmed "><span class="icon"></span>Entry Confirmed (<span id='entry_confirm'><?= ShipmentProcess::getProcessAmount([ShipmentProcess::ENTRY_CONFIRM]) ?></span>)</a>
		<a class="tab_link" href="<?= $this->createUrl("customProcess/waitingPayment") ?>" title="waiting Payment"><span class="icon"></span>Waiting for Payment (<span id='inform_pay'><?= ShipmentProcess::getProcessAmount([ShipmentProcess::INFROM_CUSTOMER_PAY]) ?></span>)</a>
		<a class="tab_link" href="<?= $this->createUrl("customProcess/paymentReceived") ?>" title="Payment Received"><span class="icon"></span>Payment Received (<span id='payment_rec'><?= ShipmentProcess::getProcessAmount([ShipmentProcess::PAYMENT_RECEIVED]) ?></span>)</a>
		<a class="tab_link" href="<?= $this->createUrl("customProcess/paymentConfirmed") ?>" title="Payment Confirmed"><span class="icon"></span>Payment Confirmed (<span id='confirm_pay'><?= ShipmentProcess::getProcessAmount([ShipmentProcess::CONFIRM_PAYMENT, ShipmentProcess::INFORM_BROKER_PAY]) ?></span>)</a>
		<a class="tab_link" href="<?= $this->createUrl("customProcess/brokerPaymentConfirmed") ?>" title="Broker Paid"><span class="icon"></span>Broker Paid (<span id='broker_paid'><?= ShipmentProcess::getProcessAmount([ShipmentProcess::BROKER_PAID]) ?></span>)</a>
		<a class="tab_link" href="<?= $this->createUrl("customProcess/customsDone") ?>" title="Customs Done"><span class="icon"></span>Customs Done</a>
	</div>
	<br />
	<style>
		.cloumn_red_1 {
			color: red;
			font-weight: bold;
		}

		#custom_summary table.chart1 td {
			text-align: center;
		}
	</style>
	<table>
		<tr>
			<td width="60%">
				<div style="width:100%" id="custom-client-view">
					<?php
					$this->widget('zii.widgets.grid.CGridView', [
						'id' => 'custom-client-list-grid',
						'htmlOptions' => ['style' => 'width: 90%'],
						'afterAjaxUpdate' => 'function(r,s){$("#custom_summary").html($(s).find("#custom_summary").html());}',
						'cssFile' => false,
						'dataProvider' => $dataProvider[0],
						'filter' => $dataProvider[1],
						'columns' => [
							[
								'name' => 'agent_id', 'headerHtmlOptions' => ['style' => 'display:none'], 'filterHtmlOptions' => ['style' => 'display:none'],
								'htmlOptions' => ['style' => 'display:none'], 'type' => 'raw'
							],
							['name' => 'owner', 'type' => 'raw'],
							'number',
							['name' => 'day1', 'header' => '0-3 Days', 'type' => 'raw'],
							['name' => 'day2', 'header' => '3-5 Days', 'type' => 'raw'],
							['name' => 'day3', 'header' => '5-7 Days', 'type' => 'raw', 'cssClassExpression' => '$data>0? "cloumn_red_1" : ""'],
							['name' => 'day4', 'header' => '7+ Days', 'type' => 'raw', 'cssClassExpression' => '$data>0? "cloumn_red_1" : ""'],
						],
					]);
					?>
				</div>
			</td>
			<td width="40%" valign="top">
				<div style="width:100%;" id="custom-client-view-detail">
					<div style="clear:both;"></div>
					<div class="col">
						<div id="custom_summary">
							<p>Daily Shipments(Average):<?= $info['totalShipment'] ?></p>
							<table class="chart chart1">
								<tr>
									<th>User</th>
									<th>Type</th>
									<th>Total</th>
									<th>Percent of Daily's Shipments</th>
									<th>Percent</th>
									<th>Open Today</th>
									<th>Close Today</th>
								</tr>
								<?php
								$tot = $totPercentOfTotalShipment = $totPercent = $totOpen = $totClose = $totDaiyClose = $totDailyClosePercent = 0;
								foreach ($info['tableTwoInfo'] as $user_id => $oneUserInfo) {
									if (!array_key_exists($user_id, ImportsMail::$importscs_list_id)) {
										continue;
									}
									$username = User::getUserName($user_id);
									if (empty($username)) {
										$username = $user_id;
									}
									$colums = sizeof($oneUserInfo);
									if ($colums > 1) {
										$colums += 1;
									}
									$subTotal = 0;
									$subPercentOfTotalShipment = $subPercnet = $subOpen = $subClose = $subDaiyClose = $subDailyClosePercent = 0;
									echo "<tr ><td  rowspan='" . $colums . "'>$username</td>";
									foreach ($oneUserInfo as $type_id => $oneTypeInfo) {
										$typeName = ShipmentProcess::$the_types[$type_id];
										$total = intval(@$oneTypeInfo['total']);
										$subTotal += $total;
										$percentOfTotalShipment = @$oneTypeInfo['totalPercent'];
										$subPercentOfTotalShipment += $percentOfTotalShipment;
										$percent = @$oneTypeInfo['percent'];
										$subPercnet += $percent;
										$open = intval(@$oneTypeInfo['open']);
										$subOpen += $open;
										$close = intval(@$oneTypeInfo['close']);
										$subClose += $close;
										$daily_avg_close = intval(@$oneTypeInfo['close_avg']);
										$subDaiyClose += $daily_avg_close;
										$close_percent = $oneTypeInfo['close_avg_percent'] . "%";
										if ($type_id != 8) {
											$tot += $total;
											$totPercentOfTotalShipment += $percentOfTotalShipment;
											$totPercent += $percent;
											$totOpen += $open;
											$totClose += $close;
											$totDaiyClose += $daily_avg_close;
										}
										echo "<td>$typeName</td><td align=center>$total</td><td align=center>$percentOfTotalShipment%</td><td>$percent%</td><td>$open</td><td>$close</td></tr>";
									}
									$subDailyClosePercent = $subDaiyClose > 0 ? number_format(($subClose / $subDaiyClose * 100), 2, '.', '') : 0;
									if ($colums > 1) {
										echo "<td>SubTotal</td><td >$subTotal</td><td>$subPercentOfTotalShipment%</td><td>$subPercnet%</td><td>$subOpen</td><td>$subClose</td></tr>";
									}
								}
								$totDailyClosePercent = $totDaiyClose > 0 ? number_format(($totClose / $totDaiyClose * 100), 2, '.', '') : 0;
								echo "<tr><td></td><th>Total</th><th>$tot</th><th>$totPercentOfTotalShipment%</th><th>$totPercent%</th><th>$totOpen</th><th>$totClose</th></tr>";
								?>
							</table>
						</div>
					</div>
					<div class="row">
						<div style="clear:both;"></div>
						<div class="col">
							<div id="custom_summary">
								<p>General Report</p>
								<table class="chart chart1">
									<tr>
										<th></th>
										<th>Total Jobs</th>
										<th>Today</th>
										<th>0-5 Days</th>
										<th>5-10 Days</th>
										<th>10-15 Days</th>
										<th><a style="color:red;">15+ Days</a></th>
									</tr>
									<?php
									if (!empty($eta)) {
										for ($i = 0; $i <= 3; $i++) {
											echo "<tr><th> " .$eta[$i][0]. "</th><th>" . 
											($eta[$i][1][0] + $eta[$i][2][0] + $eta[$i][3][0] + $eta[$i][4][0] + $eta[$i][5][0]) . 
											"</th><th>" . 
											CHtml::link($eta[$i][1][0],$this->createUrl('customProcess/showShipmentList',["no"=>join(',',$eta[$i][1][1])]),['class'=>'jqm_link']) . 
											"</th><th>" . 
											CHtml::link($eta[$i][2][0],$this->createUrl('customProcess/showShipmentList',["no"=>join(',',$eta[$i][2][1])]),['class'=>'jqm_link']) . 
											"</th><th>" . 
											CHtml::link($eta[$i][3][0],$this->createUrl('customProcess/showShipmentList',["no"=>join(',',$eta[$i][3][1])]),['class'=>'jqm_link']). 
											"</th><th>" . 
											CHtml::link($eta[$i][4][0],$this->createUrl('customProcess/showShipmentList',["no"=>join(',',$eta[$i][4][1])]),['class'=>'jqm_link']) . 
											"</th><th><a style='color:red;''>" . 
											CHtml::link($eta[$i][5][0],$this->createUrl('customProcess/showShipmentList',["no"=>join(',',$eta[$i][5][1])]),['class'=>'jqm_link red_link']) . 
											"</a></th></tr>";
										}
									}
									?>
								</table>
							</div>
						</div>
					</div>
					<?php
					$subDoneTotal = $dailydone['EMPPClose'] + $dailydone['AQISClose'] + $dailydone['HVClose'];
					$subMonthlyTotalDone = $monthlyRecord['EMPPClose'] + $monthlyRecord['AQISClose'] + $monthlyRecord['HVClose'];
					$subMonthlyTotalOpen = $monthlyRecord['EMPPOpen'] + $monthlyRecord['AQISOpen'] + $monthlyRecord['HVOpen'];
					//$subPrecent = round($subDoneTotalPoint / 60 * 100, 2) . "%";

					?>

				</div>
			</td>
		</tr>
	</table>
	<div class="row">
		
	</div>
	<br />
	<div class="form">
		<div class="row">
			<?php echo CHtml::button('Show All', ['class' => 'show_all']); ?>
		</div>
	</div>
<?php endif; ?>
<div style="right: 20px;position: absolute;">
	<a class="jqm_link grid_edit_btn" href="<?= $this->createUrl('customProcess/exportHistoryCustomKPIReport'); ?>"> Export KPI Report</a>
	<a class="jqm_link grid_edit_btn" href="<?= $this->createUrl('customProcess/exportHistoryCustomKPIDetailReport'); ?>"> Export KPI Detail Report</a>
	<!-- <a href="#" class="export_search" target="_blank" data-baseurl="<?= $this->createUrl('customProcess/exportHistoryCustomKPIReport', ['typ' => '']); ?>"><div style="background-position:-48px -688px" class="icon"></div> Export Custom Process</a> -->
	<a href="#" class="export_search" target="_blank" data-baseurl="<?= $this->createUrl('customProcess/export', ['typ' => '']); ?>">
		<div style="background-position:-48px -688px" class="icon"></div> Export Current Search
	</a>
	<?php if (Acl::hasAccess('C:imParcel/seizedShipmentManagement')) : ?>
	<a class="tab_link grid_edit_btn" title="Seized Management" href="<?= $this->createUrl('imParcel/seizedShipmentManagement'); ?>">Seized Shipment Management</a>
	<?php endif; ?>
</div>
<br />
<div class="row">
	<div id="custom-shipments-view">
		<?php
		$this->renderPartial('_sub_shipments', [
			'shipment_model' => $modelShipment,
			'agentId' => $agentId,
			'value' => $value,
			'list' => $list
		]);
		?>
	</div>
</div>
<script>
	$(function() {
		var tab = $('#<?= $_GET["tabid"]; ?>');
		var panel = tab.data('panel');
		tab.unbind('reload_update_tag').bind('reload_update_tag', function() {
			$.ajax({
				url: '<?= Yii::app()->createURL('customProcess/getProcessAmount') ?>',
				data: {
					'status': [<?= ShipmentProcess::STATE_EMPP_CUSTOM ?>]
				},
				method: 'POST',
				success: function(msg) {
					var reply = JSON.parse(msg)
					for (var p in reply) {
						$('#' + p, tab.data('panel')).html(reply[p]);
					}
				}
			});
			return false;
		});

		$('#custom-client-list-grid', panel).on('updated', function(r) {
			console.log(1);
		});

		$('#custom-client-view', panel).on("click", "table tbody td", function(event) {
			// get console id
			var agentId = parseInt($(this).parent().children(':nth-child(1)').html());
			var data = {};
			data['agent_id'] = agentId;
			$.ajax({
				type: 'GET',
				url: '<?php echo Yii::app()->createAbsoluteUrl("customProcess/customList", ['tabid' => $_GET['tabid']]); ?>',
				data: data,
				dataType: 'html',
				success: function(resp) {
					$('#custom-shipments-view').html(resp);
				},
			});
			tab.trigger('reload_update_tag');
		});

		$('.show_all', panel).on('click', function(event) {
			var data = {};
			data['agent_id'] = 0;
			$.ajax({
				type: 'GET',
				url: '<?php echo Yii::app()->createAbsoluteUrl("customProcess/customList", ['tabid' => $_GET['tabid']]); ?>',
				data: data,
				dataType: 'html',
				success: function(resp) {
					$('#custom-shipments-view').html(resp);
				},
			});
		});

		$('a.export_search', panel).on('mousedown', function() {
			var id = $('#agent_id', panel).val();
			var q = $('.filters input, .filters select', panel).serialize() + '&agent_id=' + id;
			$(this).attr('href', $(this).data('baseurl') + '&' + q);
		});

		$('#custom-client-view-detail', panel).on("click", "table tbody td", function(event) {
			var columnIndex = $(this).index();
			var type = parseInt($(this).parent().children(':nth-child(1)').html());
			if (parseInt($(this).html()) <= 0) return false;
			if (columnIndex < 2) return false;
			myApp.tabs.CreateTab({
				title: 'Custom Process Detail',
				url: "customProcess/customDetail?type=" + type + "&index=" + columnIndex,
				bg: false
			});
			return false;
		});

	})
</script>