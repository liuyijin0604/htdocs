<div class="grid-view">
<table class="items">
<thead>
<tr><th>ACL Object</th><th>Group</th><th>User</th></tr>
</thead>
<tbody>
<?php
function AclTree($pid, $lv=0, $rn=0, $m){
	$rs = AclObj::model()->findAll('pid = :pid ORDER BY name', array(':pid' => $pid));
	$pn = $rn;
	foreach($rs as $r){
		$a = Acl::model()->find('oid = :oid AND uid = :uid', array(':oid' => $r->id, ':uid' => $m->id));
		$g = Acl::model()->find('oid = :oid AND gid = :gid', array(':oid' => $r->id, ':gid' => $m->type));
		$gp = empty($g)? '' : ($g->p == 2? '✔' : ($g->p == 1? '✘' : ''));
		$p = empty($a)? 0 : $a->p;
		echo '<tr data-lv="'.$lv.'" data-pid="'.$pn.'"><td>', $r->name, '</td><td>'.$gp.'</td><td><label><input class="acl_radio" type="radio" name="u-'.$r->id.'-'.$m->id.'" data-p="'.$pid.'" value="2" '.($p==2? 'checked' : '').' /> ✔</label> <label><input class="acl_radio" type="radio"  name="u-'.$r->id.'-'.$m->id.'" data-p="'.$pid.'" value="1" '.($p==1? 'checked' : '').' /> ✘</label></td></tr>';
		$rn++;
		$rn = AclTree($r->id, $lv+1, $rn, $m);
	}
	return $rn;
}

AclTree(0, 0, 0, $model);
?>
</tbody>
</table>
</div>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('table.items', panel).treeTable();
	$('input.acl_radio', panel).on('mousedown', function(){
		if($(this).prop('checked')) $(this).data('on', true);
	}).on('click', function(){
		if($(this).data('on')){ //allow uncheck
			$(this).prop('checked', false).data('on', false);
			$.post('acl/save',{n: $(this).prop('name'), v: 0});
		}else{
			$.post('acl/save',{n: $(this).prop('name'), v: $(this).val()});
		}
	});
	
});
</script>
