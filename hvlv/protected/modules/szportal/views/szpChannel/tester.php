<h1><?=$this->t('Tester');?></h1>
<div class="grid-view" style="min-height: 400px">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'ex-channel-tester-form',
	'enableAjaxValidation'=>false,
));
$states = ['北京市', '天津市', '河北省', '山西省', '内蒙古自治区', '辽宁省', '吉林省', '黑龙江省', '上海市', '江苏省', '浙江省', '安徽省', '福建省', '江西省', '山东省', '河南省', '湖北省', '湖南省', '广东省', '广西壮族自治区', '海南省', '重庆市', '四川省', '贵州省', '云南省', '西藏自治区', '陕西省', '甘肃省', '青海省', '宁夏回族自治区', '新疆维吾尔自治区'];
?>
<table class="items">
<thead>
	<tr><th>Agent ID</th><th>Service</th><th>State</th><th>Weight</th><th>Goods Type</th><th>Goods Name</th><th>Goods Qty</th><th>Channel</th></tr>
	<tr><td><?php echo CHtml::textField('a', '', ['size' => '5']); ?></td>
		<td><?php echo CHtml::dropdownList('st', 0, ExParcel::$styps, ['empty' => 'Select One']); ?></td>
		<td><?php echo CHtml::dropdownList('s', '北京市', array_combine($states, $states), ['empty' => 'Select One']); ?></td>
		<td><?php echo CHtml::textField('w', '3.6', ['size' => '5']); ?></td>
		<td><?php echo CHtml::dropdownList('t', 'B', ['B' => 'B', 'M' => 'M', 'O' => 'O'], ['empty' => 'Select One']); ?></td>
		<td><?php echo CHtml::textField('g', '婴儿奶粉1段', ['size' => '20']); ?></td>
		<td><?php echo CHtml::textField('q', '3', ['size' => '5']); ?></td>
		<td><?php echo CHtml::submitButton('Test'); ?></td></tr>
</thead>
<tbody>
	
</tbody>
</table>
<?php $this->endWidget(); ?>
</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	var randState = function(){
		var states = <?=json_encode($states);?>;
		return states[Math.floor(Math.random()*states.length)];
	}
	var tests = [
		{'a': null, 'st': 0, 's': randState(), 'w': 3.6, 't': 'B', 'g': '婴儿奶粉1段', 'q': 3},
		{'a': null, 'st': 0, 's': randState(), 'w': 3.6, 't': 'B', 'g': '婴儿奶粉3段', 'q': 3},
		{'a': null, 'st': 0, 's': randState(), 'w': 3.6, 't': 'B', 'g': '婴儿羊奶粉4段', 'q': 3},
		{'a': null, 'st': 0, 's': randState(), 'w': 7.2, 't': 'B', 'g': '婴儿奶粉2段', 'q': 6},
		{'a': null, 'st': 0, 's': randState(), 'w': 6.5, 't': 'M', 'g': '成人奶粉', 'q': 6},
		{'a': null, 'st': 0, 's': randState(), 'w': 8.5, 't': 'M', 'g': '成人奶粉', 'q': 8},
		{'a': null, 'st': 0, 's': randState(), 'w': 6.5, 't': 'M', 'g': '雅培小安素奶粉 850g', 'q': 6},
		{'a': null, 'st': 0, 's': randState(), 'w': 1.5, 't': 'O', 'g': 'UGG鞋', 'q': 1},
		{'a': null, 'st': 0, 's': randState(), 'w': 3.2, 't': 'O', 'g': '羊奶皂', 'q': 8},
		{'a': null, 'st': 0, 's': randState(), 'w': 0.5, 't': 'O', 'g': 'Panadol', 'q': 2},
		{'a': null, 'st': 0, 's': randState(), 'w': 0.8, 't': 'O', 'g': '洗脸仪', 'q': 1},
		{'a': 462, 'st': 0, 's': randState(), 'w': 1.2, 't': 'O', 'g': '蜂蜜', 'q': 2},
		{'a': 1010, 'st': 0, 's': randState(), 'w': 0.8, 't': 'O', 'g': '面霜', 'q': 3},
		{'a': 1010, 'st': 0, 's': randState(), 'w': 4.5, 't': 'M', 'g': '成人奶粉', 'q': 4},
	];

	var test = function(tests){
		$.post('exChannel/tester', {tests: tests}, function(r){
			if(r.done){
				$('table.items tbody', win).prepend(r.grid);
			}else{
				myApp.alert(r.msg);
			}
		}, 'json');
	};

	test(tests);

	$('#ex-channel-tester-form', win).on('submit', function(){
		test([$(this).formSerialize()]);
		return false;
	});
});
</script>