<div style="position: absolute; right: 20px;">
<a href="#" id="myBtn"><div style="background-postion:-48px -688px" class="icon"></div>Open</a> &nbsp; <a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div style="background-position:-48px -688px" class="icon"></div> Export</a>
<a class="jqm_link"  id="new_tickets_<?=$_GET['tabid'];?>" href="<?=$this->createUrl('exCrm/createTickets',array('id'=>$model->id));?>" title="new tag"><div class="icon" style="background-position:-16px 0"></div>New CRM Ticket</a> &nbsp; 
<br />
<a class="tab_link" style="display:none;" id="mynewtag<?=$_GET['tabid'];?>" href="<?=$this->createUrl('exParcel/newtag',array('id'=>$this->geteid($model->hbn)));?>" title="new tag"><div class="icon" style="background-position:-16px 0"></div> Open new tag</a> &nbsp;
<a class="jqm_link"  id="err_record<?=$_GET['tabid'];?>" href="<?=$this->createUrl('exParcel/UpdateErrorRecord',array('id'=>$model->id));?>" title="Update Error"><div class="icon" style="background-position:-16px 0"></div>Error Record</a> &nbsp; 

<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><a href="<?=$this->createUrl('exParcel/label', array('id'=>$model->id))?>" target="_blank">Label</a></li>
	<?php if($model->consol_id > 0 || in_array($model->agent_id, [672])): ?>
		<li><a href="<?=$this->createUrl('exParcel/transLabel', array('id'=>$model->id))?>" target="_blank">派送面单</a></li>
		<li><a href="<?=$this->createUrl('exParcel/transLabel', array('id'=>$model->id, 'type' => 'pdf'))?>" target="_blank">派送面单 PDF</a></li>
		<li><a href="<?=$this->createUrl('exParcel/receipt', array('id'=>$model->id));?>" target="_blank">小票</a></li>
		<li><a href="<?=$this->createUrl('exParcel/receipt', array('id'=>$model->id, 'photo' => 1));?>" target="_blank">小票照片</a></li>
		<li><a href="<?=$this->createUrl('exParcel/transLabel', array('id'=>$model->id, 'type'=> '3in1'));?>" target="_blank">3 合 1</a></li>
	<?php endif; ?>
		<li><a href="<?=$this->createUrl('exParcel/cusReceipt', array('id'=>$model->id));?>" class="jqm_link">定制小票</a></li>
		<li><a href="<?=$this->createUrl('exParcel/authLetter', array('id'=>$model->id));?>" target="_blank">授权信</a></li>
	</ul>
</div>
</div>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
		
<!-- The Modal -->

<script>
$(function(){
	var rotate_angle = 0;
	var url=$("#mypic<?=$_GET['tabid']?>").attr('src');
	var tab=$("#<?=$_GET['tabid']?>");
	var panel = tab.data('panel');

	if($("#Cnee_address", panel).val() ==''|| $("#Cnee_tel", panel).val()=='') show();

	$("#myBtn", panel).on('click',function(){
		show();
		$('#myclick<?= $_GET["tabid"]; ?>').trigger('click');
		return false;
	});
	
	$("#close<?=$_GET["tabid"];?>").on('click',function(){
		$("#myModal<?=$_GET["tabid"];?>").hide();
		return false;
	});
	
	var angel=0;
	$("#rotate<?=$_GET['tabid']?>").on('click',function(){
		angel+=90;
		$("#mypic<?=$_GET['tabid']?>").css('transform','rotate('+angel+'deg)');
		return false;
	});

	$("#mypic<?=$_GET['tabid']?>").on('dblclick',function(){
		window.open("<?=$this->createUrl('exParcel/newTag',array('id'=>$this->geteid($model->hbn)))?>",'image');
		$("#myModal<?=$_GET["tabid"];?>").hide();
		return false;
	});
	
	function show(){
		$('#myModal<?= $_GET["tabid"]; ?>').show();
		$('#myModal<?= $_GET["tabid"]; ?>').draggable();
		$('#mypic<?= $_GET["tabid"]; ?>').resizable();
	}
});
</script>