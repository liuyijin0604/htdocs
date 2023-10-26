<?php
if ($result === false) { 
	echo "<div class=\"alert alert-danger\" style=\"text-align:center; font-size:32px; margin-top:2px\">" . $text . "</div>";
	echo "<script>$('#input').val(''); $('#input').focus(); </script>";
} else {
?>

	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapseProduct">订单商品详情</a>
					</h3>
				</div>
				<div id="collapseProduct" class="panel-collapse collapse in" style="min-height: 400px; max-height: 400px; overflow-y: scroll;">
					<div class="panel-body">
						<div class="row">
							<div class="col-xs-12">
								<table class="table table-striped" style="font-size: 20px;">
									<thead>
										<tr>
											<th class="col-xs-2">条形码</th>
											<th class="col-xs-5">名称</th>
											<th class="col-xs-2">备注</th>
											<?php
											$total = 0;
											foreach ($products as $product) {
												$total += $product['uq'];
											}
											?>
											<th class="col-xs-3">数量 <span style="color: red">(总数<?=$total?>)</span></th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($products as $product) { ?>
										<tr id="tr_<?=$product["ean"]?>" class="success">
											<td><?=$product["ean"]?></td>
											<td><?=$product["name_zh"] ? $product["name_zh"] . " - " . $product["name"] : $product["name"]?></td>
											<td><?=$product["note"]?></td>
											<?php $extraQuantity = $product["pq"] ? $product["pq"] . "板" : ($product["cq"] ? $product["cq"] . "箱" : ""); ?>
											<td><?=$product["uq"] . "件" . ($extraQuantity ? "(" . $extraQuantity . ")" : "") ?></td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<script>
				setTimeout(function() {
					$('#taskid').html('<?=$task->getNo() . "&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;" . $task->ref?>' + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class="btn btn-danger hold-btn" style="position: relative; top: -4px" onclick="pack.holdTask();">Hold订单</button>');
				}, 2e2);
				pack.products = [];
				pack.labelstatus = '<?=$labelstatus?>';
				if (pack.labelstatus == 'null') {
					$('#label').text('该订单客人自取，无需打印面单');
				} else if (pack.labelstatus == 'pca') {
					$('#label').text('需要打印面单');
				} else {
					$('#label').text('预打印面单 ' + pack.labelstatus);
					pack.labelstatus = 'other';
				}
				pack.markstatus = '<?=$markstatus?>';
				if (pack.markstatus != 'null') {
					$('#mark').text(pack.markstatus);
				}
				<?php foreach($products as $product) { ?>
					pack.products['<?=$product["ean"]?>'] = {'name':'<?=addslashes($product["name_zh"]) ? addslashes($product["name_zh"]) . " - " . addslashes($product["name"]) : addslashes($product["name"])?>', 'quantity':'<?=$product["uq"]?>', 'scanquantity':0};
					pack.products.length ++;
				<?php } ?>
				<?php
				$dim = false;
				if (!empty($task->mdata['note']) && preg_match('/TNT/i', $task->mdata['note'])) {
					$dim = true;
				} else if (in_array($task->job->org_id, [Org::ORGID_3PL_PEAKCARE])) {
					$dim = true;
				} else if (in_array($task->job->org_id, [Org::ORGID_3PL_ELEKZON]) && !empty($task->deliveryTask) && (empty($task->deliveryTask->mdata['shopify_standard']))) {
					$dim = true;
				}
				if ($dim == true) {
					echo "$('#dim').show(); pack.dimstatus = true;";
				}
				?>
			</script>
		</div>

		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h3 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapseParcel">已完成包裹</a>
							</h3>
						</div>
						<div id="collapseParcel" class="panel-collapse collapse in" style="min-height: 400px; max-height: 400px; overflow-y: scroll;">
							<div class="panel-body">
								<table class="table table-striped" style="font-size: 20px;">
									<thead>
										<th class="col-xs-2">包裹号</th>
										<!-- <th class="col-xs-4">扫描时间</th> -->
										<th class="col-xs-2">重量</th>
										<th class="col-xs-5" colspan="5">三边</th>
										<th class="col-xs-2">耗材</th>
										<th class="col-xs-1"></th>
									</thead>
									<tbody id="parcellist">
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<script>
						pack.parcels = [];
						pack.lsfile = '<?=$lsfile?>';
						<?php foreach ($parcels as $parcel) { if (empty($parcel['id'])) continue; ?>
							pack.parcels['<?=$parcel["id"]?>'] = {'id':'<?=$parcel["id"]?>', 'taskid':'<?=$parcel["taskid"]?>', 'date':'<?=$parcel["date"]?>', 'weight':'<?=$parcel["weight"]?>', 'material':'<?=$parcel["material"]?>', 'height':'<?=$parcel["height"]?>', 'width':'<?=$parcel["width"]?>', 'depth':'<?=$parcel["depth"]?>'};
							pack.parcels.length ++;
							$('#parcellist').append('<tr class="active"><td><?=substr($parcel["id"], -1)?></td><td><?=$parcel["weight"]?> kg</td><td><?=$parcel["depth"]?> cm</td><td> X </td><td><?=$parcel["width"]?> cm</td><td> X </td><td><?=$parcel["height"]?> cm</td><td><?=$parcel["material"]?></td><td><span class="glyphicon glyphicon-trash delete" id="<?=$parcel["id"]?>"></span></td></tr>');
						<?php } ?>
						$('#collapseParcel').animate({ scrollTop: $('#collapseParcel').prop("scrollHeight")}, 1000);
						$('#collapseParcel').off('click', '.glyphicon.glyphicon-trash.delete').on('click', '.glyphicon.glyphicon-trash.delete', function() {
							var obj = $(this);
							$.ajax({
								'url': '/pack/pack/delete?id=' + $(this).attr('id'),
								success: function(r) {
									r = JSON.parse(r);
									if (r.result == true) {
										obj.parent().parent().empty();
									}
								},
							});
						});
					</script>
				</div>

				<!-- <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h3 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapsePhoto">拍照记录</a>
							</h3>
						</div>
						<div id="collapsePhoto" class="panel-collapse collapse in">
							<div id="cam_live" style="margin: 10px auto;"></div>
							<script>
								$(document).ready(function() {
									// Webcam.set({
									//     width: 380,
									//     height: 260,
									//     image_format: 'jpeg',
									//     jpeg_quality: 90,
									//     constraints: {
									//         optional: [ {minWidth: 360} ]
									//     }
									// });
									// Webcam.attach('#cam_live');
								});

								camApp.taskid = '<?=$task->id?>';
								camApp.url = '<?=$this->createUrl("pack/picture")?>';
							</script>
						</div>
					</div>
				</div> -->
			</div>
		</div>
	</div>
<?php } ?>