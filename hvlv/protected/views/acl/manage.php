<h1><?=$this->t('Manage ACL');?></h1>
<p><select class="ugs" name="group">
<?php
if(empty($_GET['grp'])) $_GET['grp'] = 10;
foreach(User::$types as $g=>$t){
	if($g == 0) continue;
	echo '<option value="'.$g.'"'.($g == $_GET['grp']? 'selected' : '').'>'.$t.'</option>';
}
?>
</select></p>
<div class="grid-view" style="max-width:400px;">
<table class="items">
<thead>
<tr><th>ACL Object</th>
<?php
foreach(User::$types as $g=>$t){
	if($g != $_GET['grp']) continue;
	echo '<th width="40%">'.$t.'</th>';
}
?>
</tr>
</thead>
<tbody>
<?php
function AclTree($pid, $lv=0, $rn=0){
	$rs = AclObj::model()->findAll('pid = :pid ORDER BY name', array(':pid' => $pid));
	$pn = $rn;
	foreach($rs as $r){
		echo '<tr data-lv="'.$lv.'" data-pid="'.$pn.'"><td>', $r->name, '</td>';
		foreach(User::$types as $g=>$t){
			if($g != $_GET['grp']) continue;
			$a = Acl::model()->find('oid = :oid AND gid = :gid', array(':oid' => $r->id, ':gid' => $g));
			$p = empty($a)? 0 : $a->p;
			echo '<td><label><input class="acl_radio" type="radio" name="g-'.$r->id.'-'.$g.'" data-p="'.$pid.'" value="2" '.($p==2? 'checked' : '').' /> ✔</label> <label><input class="acl_radio" type="radio"  name="g-'.$r->id.'-'.$g.'" data-p="'.$pid.'" value="1" '.($p==1? 'checked' : '').' /> ✘</label></td>';
		}
		echo '</tr>';
		$rn++;
		$rn = AclTree($r->id, $lv+1, $rn);
	}
	return $rn;
}

AclTree(0);
?>
</tbody>
</table>
</div>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

	$('select.ugs', panel).on('change', function(){
		$('table.items', panel).fadeOut();
		$.get('acl/manage.app?tabid=<?=$_GET["tabid"];?>&grp='+$(this).val(), function(r){
			$('table.items', panel).replaceWith($('table.items', r)).show();
			$('table.items', panel).trigger('prep');
		});
	});

	$(panel).off('zebra', 'table.items').on('zebra', 'table.items', function(){
		$('table.items tr:visible:odd', panel).css('background', '#E5F1F4');
		$('table.items tr:visible:even', panel).css('background', '#F8F8F8'); 
	}).off('prep', 'table.items').on('prep', 'table.items', function(){
		$(this).treeTable().trigger('zebra');
	}).off('click', 'table.items tr td:first-child').on('click', 'table.items tr td:first-child', function(){
		$('table.items', panel).trigger('zebra');
	}).off('mousedown', 'input.acl_radio').on('mousedown', 'input.acl_radio', function(){
		if($(this).prop('checked')) $(this).data('on', true);
	}).off('click', 'input.acl_radio').on('click', 'input.acl_radio', function(){
		if($(this).data('on')){ //allow uncheck
			$(this).prop('checked', false).data('on', false);
			$.post('acl/save',{n: $(this).prop('name'), v: 0});
		}else{
			$.post('acl/save',{n: $(this).prop('name'), v: $(this).val()});
		}
	});

	$('table.items', panel).trigger('prep');
	
	// tab.off('onOpen').on('onOpen', function(){
	// 	tab.load();
	// });
	
});
</script>
