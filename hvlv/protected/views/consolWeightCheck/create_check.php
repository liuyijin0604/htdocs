<h1><?=$this->t('Create Consol Weight Check');?></h1>


<div class="form">

	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'consol-weight-check-form',
		'enableAjaxValidation'=>false,
	)); ?>

	<div class="row">
		<label for="postw-batch">Load Source Data(<a href="/template/consol_check_weight.xlsx" target="_blank">Get template file</a>)</label>
		<input type="file" name="cwc_file" id="cwc_file" />
		<?php echo CHtml::submitButton('Import',['id' => 'btn-import']); ?>
	</div>

	<div class="row" id="step2">
	</div>
	<br/>
	<div class="rowcol" id="overwrite_div">
		<?php echo $this->t('<b>In case overwrite old one , please tick it</b> '), CHtml::checkBox('overwrite') . ' Overwrite'; ?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Save',['id' => 'btn-save']); ?>
	</div>
	<?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
	$(function(){
		var tab_id = '<?=$_GET["tabid"];?>';
		var tab = $('#'+tab_id);
		var panel = tab.data('panel');

		$('#btn-save', panel).hide();
		$('#overwrite_div', panel).hide();

		$('form#consol-weight-check-form', panel).data('custom_success', function(r){
			if ( r.success == 1 ) {
				if (r.step == 1){
					$('#step2', panel).empty();
					for(i in r.err){
						$('#step2', panel).append('<p class="error">' + r.err[i]+ '</p>');
						$('#btn-save', panel).hide();
						$('#overwrite_div', panel).hide();
					}
					h = '<table style="width:100%; text-align:center;"><tr><th rowspan="2">No</th><th rowspan="2">AWB</th><th rowspan="2">ETD</th><th rowspan="2">Channel</th><th rowspan="2">Invoice</th><th colspan="3">Wt./Awb Wt.</th><th colspan="3">Pks</th><th colspan="3">Clearance</th><th colspan="3">Delivery</th><th colspan="3">Duty</th><th colspan="3">Others</th><th colspan="3">Total</th></tr><tr><th style="color:rgba(0,0,200,0.4)">ACCR</th><th style="color:rgba(200,0,0,0.4)">ACT</th><th>Diff</th><th style="color:rgba(0,0,200,0.4)">ACCR</th><th style="color:rgba(200,0,0,0.4)">ACT</th><th>Diff</th><th style="color:rgba(0,0,200,0.4)">ACCR</th><th style="color:rgba(200,0,0,0.4)">ACT</th><th>Diff</th><th style="color:rgba(0,0,200,0.4)">ACCR</th><th style="color:rgba(200,0,0,0.4)">ACT</th><th>Diff</th><th style="color:rgba(0,0,200,0.4)">ACCR</th><th style="color:rgba(200,0,0,0.4)">ACT</th><th>Diff</th><th style="color:rgba(0,0,200,0.4)">ACCR</th><th style="color:rgba(200,0,0,0.4)">ACT</th><th>Diff</th><th style="color:rgba(0,0,200,0.4)">ACCR</th><th style="color:rgba(200,0,0,0.4)">ACT</th><th>Diff</th></tr>';
					totcost = {'wt' : 0, 'awb wt' : 0, 'pks' : 0, 4 : 0, 5 : 0, 6 : 0, 9 : 0};
					totbill = {'wt' : 0, 'pks' : 0, 4 : 0, 5 : 0, 6 : 0, 9 : 0};
					for(i in r.data){
						d = r.data[i];
						adjust_rate = d.consol.awb_weight / d.consol.weight;
						h += '<tr style="background: '+((i%2==0)?'white':'#DEDEDE')+'"><td><a href="ExcoConsol/update/'+d.consol.id+'" class="tab_link" title="'+d.consol.no+'">'+d.consol.no+'</a></td><td>'+d.consol.awb+'</td><td>'+d.consol.etd+'</td><td>'+d.consol.poc+'</td><td>'+d.bill.ref+'</td><td>'+d.consol.weight+'/'+d.consol.awb_weight+'</td><td>'+d.bill.weight+'</td><td>'+round(d.consol.awb_weight-d.bill.weight)+'</td><td>'+d.consol.qty+'</td><td>'+d.bill.qty+'</td><td>'+round(d.consol.qty-d.bill.qty)+'</td><td>'+round(d.cost[4]*adjust_rate)+'</td><td>'+d.bill[4]+'</td><td>'+round(d.cost[4]*adjust_rate-d.bill[4])+'</td><td>'+round(d.cost[5]*adjust_rate)+'</td><td>'+d.bill[5]+'</td><td>'+round(d.cost[5]*adjust_rate-d.bill[5])+'</td><td>'+round(d.cost[6]*adjust_rate)+'</td><td>'+d.bill[6]+'</td><td>'+round(d.cost[6]*adjust_rate-d.bill[6])+'</td><td>'+d.bill[9]+'</td><td>'+round(d.cost[9]*adjust_rate)+'</td><td>'+round(d.cost[9]*adjust_rate-d.bill[9])+'</td><td>'+round((Number(d.cost[4])+Number(d.cost[5])+Number(d.cost[6])+Number(d.cost[9]))*adjust_rate)+'</td><td>'+round(Number(d.bill[4])+Number(d.bill[5])+Number(d.bill[6])+Number(d.bill[9]))+'</td><td>'+round((Number(d.cost[4])+Number(d.cost[5])+Number(d.cost[6])+Number(d.cost[9]))*adjust_rate-Number(d.bill[4])-Number(d.bill[5])-Number(d.bill[6])-Number(d.bill[9]))+'</td></tr>';
						totcost['wt'] += Number(d.consol.weight);
						totcost['awb wt'] += Number(d.consol.awb_weight);
						totcost['pks'] += Number(d.consol.qty);
						totcost[4] += Number(d.cost[4])*adjust_rate;
						totcost[5] += Number(d.cost[5])*adjust_rate;
						totcost[6] += Number(d.cost[6])*adjust_rate;
						totcost[9] += Number(d.cost[9])*adjust_rate;
						totbill['wt'] += Number(d.bill.weight);
						totbill['pks'] += Number(d.bill.qty);
						totbill[4] += Number(d.bill[4]);
						totbill[5] += Number(d.bill[5]);
						totbill[6] += Number(d.bill[6]);
						totbill[9] += Number(d.bill[9]);
						for (k in d.dup) {
							t = d.dup[k];
							t['tot'] = Number(t[4]?t[4]:0)+Number(t[5]?t[5]:0)+Number(t[6]?t[6]:0)+Number(t[9]?t[9]:0);
							h += '<tr style="background: '+((i%2==0)?'white':'#DEDEDE')+'; color: red;"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>'+t.ref+'</td><td>'+d.consol.weight+'/'+d.consol.awb_weight+'</td><td>'+(t[2]?t[2]:'-')+'</td><td>'+(t[2]?round(d.consol.awb_weight-t[2]):'-')+'</td><td>'+d.consol.qty+'</td><td>'+(t[1]?t[1]:'-')+'</td><td>'+(t[1]?round(d.consol.qty-t[1]):'-')+'</td><td>'+round(d.cost[4]*adjust_rate)+'</td><td>'+(t[4]?t[4]:'-')+'</td><td>'+(t[4]?round(d.cost[4]*adjust_rate-t[4]):'-')+'</td><td>'+round(d.cost[5]*adjust_rate)+'</td><td>'+(t[5]?t[5]:'-')+'</td><td>'+(t[5]?round(d.cost[5]*adjust_rate-t[5]):'-')+'</td><td>'+round(d.cost[6]*adjust_rate)+'</td><td>'+(t[6]?t[6]:'-')+'</td><td>'+(t[6]?round(d.cost[6]*adjust_rate-t[6]):'-')+'</td><td>'+round(d.cost[9]*adjust_rate)+'</td><td>'+(t[9]?t[9]:'-')+'</td><td>'+(t[9]?round(d.cost[9]*adjust_rate-t[9]):'-')+'</td><td>'+round((Number(d.cost[4])+Number(d.cost[5])+Number(d.cost[6])+Number(d.cost[9]))*adjust_rate)+'</td><td>'+round(t['tot'])+'</td><td>'+round((Number(d.cost[4])+Number(d.cost[5])+Number(d.cost[6])+Number(d.cost[9]))*adjust_rate-t['tot'])+'</td></tr>';
						}
						for(j in d.hist){
							t = d.hist[j];
							t['tot'] = Number(t[4]?t[4]:0)+Number(t[5]?t[5]:0)+Number(t[6]?t[6]:0)+Number(t[9]?t[9]:0);
							h += '<tr style="background: '+((i%2==0)?'white':'#DEDEDE')+'"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>'+t.ref+'</td><td>-</td><td>'+(t[2]?t[2]:'-')+'</td><td>'+(t[2]?-t[2]:'-')+'</td><td>-</td><td>'+(t[1]?t[1]:'-')+'</td><td>'+(t[1]?-t[1]:'-')+'</td><td>-</td><td>'+(t[4]?t[4]:'-')+'</td><td>'+(t[4]?-t[4]:'-')+'</td><td>-</td><td>'+(t[5]?t[5]:'-')+'</td><td>'+(t[5]?-t[5]:'-')+'</td><td>-</td><td>'+(t[6]?t[6]:'-')+'</td><td>'+(t[6]?-t[6]:'-')+'</td><td>-</td><td>'+(t[9]?t[9]:'-')+'</td><td>'+(t[9]?-t[9]:'-')+'</td><td>-</td><td>'+round(t['tot'])+'</td><td>'+-round(t['tot'])+'</td></tr>';
							totbill['wt'] += Number(t[2]?t[2]:0);
							totbill['pks'] += Number(t[1]?t[1]:0);
							totbill[4] += Number(t[4]?t[4]:0);
							totbill[5] += Number(t[5]?t[5]:0);
							totbill[6] += Number(t[6]?t[6]:0);
							totbill[9] += Number(t[9]?t[9]:0);
						}
					}
					h += '<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>TOTAL</td><th>'+round(totcost['wt'])+'/'+round(totcost['awb wt'])+'</th><th>'+round(totbill['wt'])+'</th><th>'+round(totcost['awb wt']-totbill['wt'])+'</th><th>'+round(totcost['pks'])+'</th><th>'+round(totbill['pks'])+'</th><th>'+round(totcost['pks']-totbill['pks'])+'</th><th>'+round(totcost[4])+'</th><th>'+round(totbill[4])+'</th><th>'+round(totcost[4]-totbill[4])+'</th><th>'+round(totcost[5])+'</th><th>'+round(totbill[5])+'</th><th>'+round(totcost[5]-totbill[5])+'</th><th>'+round(totcost[6])+'</th><th>'+round(totbill[6])+'</th><th>'+round(totcost[6]-totbill[6])+'</th><th>'+round(totcost[9])+'</th><th>'+round(totbill[9])+'</th><th>'+round(totcost[9]-totbill[9])+'</th><th>'+round(totcost[4]+totcost[5]+totcost[6]+totcost[9])+'</th><th>'+round(totbill[4]+totbill[5]+totbill[6]+totbill[9])+'</th><th>'+round(totcost[4]+totcost[5]+totcost[6]+totcost[9]-totbill[4]-totbill[5]-totbill[6]-totbill[9])+'</th></tr></table>';
					$('#step2', panel).append(h);
					$('#btn-import', panel).removeAttr('disabled');
					if (r.data.length > 0) {
						if (d.dup.length > 0) {
							$('#overwrite_div', panel).show();
							$('#btn-save', panel).show();
							$('#overwrite', panel).off('click').on('click', function() {
								if ($('#overwrite', panel).prop('checked')) {
									$('#btn-save', panel).removeAttr('disabled');
								} else {
									$('#btn-save', panel).attr('disabled', 'disabled');
								}
							});
						} else {
							$('#overwrite_div', panel).hide();
							$('#btn-save', panel).show();
							$('#btn-save', panel).removeAttr('disabled');
						}
					}
				}
			} else {
				$('#btn-import', panel).removeAttr('disabled');
				myApp.notice(r.msg);
			}

			return true;
		});

	function round(val) {
		return Math.round(parseFloat(val)*100)/100;
	}
	});
</script>
