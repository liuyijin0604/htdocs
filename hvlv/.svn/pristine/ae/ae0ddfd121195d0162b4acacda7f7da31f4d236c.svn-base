<div style="text-align:right;">
<a href="<?=$this->createUrl('storage/export', ['id' => $model->id]);?>" target="_blank"><div style="background-position:-48px -688px" class="icon"></div> Export Report</a>
</div>
<h1><?=$this->t('View Storage');?> <?php echo $model->name; ?></h1>

<?php
foreach($model->items as $it){
	if(!empty($it->out_dt)) continue;
	switch($it->model){
		case 'ImParcel':
			$r = ImParcel::model()->findByPk($it->fid);
			echo '<p><a href="'.$this->createUrl('imParcel/update', array('id' => $r->id)).'" class="tab_link" title="'.$r->hbn.'">'.$r->hbn.'</a> to '.$r->cnee->name.' '.$r->cnee->state.' &nbsp; <a href="'.$this->createUrl('storage/moveout', array('id' => $it->id)).'" class="move_out">move out</a></p>';
		break;
		case 'ExParcel':
			$r = ExParcel::model()->findByPk($it->fid);
			echo '<p><a href="'.$this->createUrl('exParcel/update', array('id' => $r->id)).'" class="tab_link" title="'.$r->hbn.'">'.$r->hbn.'</a> to '.$r->cnee->name.' '.$r->cnee->state.' &nbsp; <a href="'.$this->createUrl('storage/moveout', array('id' => $it->id)).'" class="move_out">move out</a></p>';
		break;
	}
}
?>
<script type="text/javascript">
$(function(){
var win = $('#jqmw_<?=$_GET["tabid"];?>');
$('a.move_out', win).click(function(){
	if(window.confirm("Are you sure to move this item out?")){
		var that = $(this);
		$.get($(this).attr('href'), function(){
			that.parents('p').remove();
		});
	}
	return false;
});
});
</script>