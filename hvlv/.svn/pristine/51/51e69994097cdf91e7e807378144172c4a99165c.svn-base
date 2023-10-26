<h3>Filter Manangement</h3>
<style>
	 span.exp{
	border: 1px solid #15538b;
	color: #15538b;
	border-radius: 4px;
	padding: 0 2px;
	cursor: pointer;
	font-weight: bold;
}
span.exp:hover{
		background-color:#15538b;
		color: white;
}   
</style>
<h4>Filters</h4>
<hr/>
<div class="form" id="import-tkt-sum-filter">
		<?php $form=$this->beginWidget('CActiveForm', [
			'id'=>'email-filter-management-form',
			'enableAjaxValidation'=>false,
		]); ?>
		 <input type="hidden" value="" id="filter_meta_items" name="filter_meta_items"/>
		 <div id="sortable">
	 <?php
	 		$mailTypes = ImportsMail::$mailTypes;
	 		if($isWms)
	 		{
	 			$mailTypes = ImportsMail::$wmsMailTypes;
	 		}
			 $rs=ImportsMailFilter::model()->findAll('id>0 and is3pl = :is3pl',[':is3pl'=>$isWms]);
			 $out="";
			 if (!empty($rs)) {
			 	$id=1;
			 	foreach ($rs as $r) {
			 		$out.='<div class="assign "><span class="exp remove" >--</span> <label style="display:inline-block;">From Email:</label><input type="text" name="from_email" id="from_email'.$id++.$_GET['tabid'].'" size=30 value="'.$r->from_email.'"/><label style="display:inline-block;">Subject:</label><input type="text" name="subject" size=30 id="subject_'.$id++.$_GET['tabid'].'." value="'.$r->subject.'" />To Email:</label><input type="text" name="to_email" size=30 id="to_email_'.$id++.$_GET['tabid'].'"  value="'.$r->to_email.'" />CC Email:</label><input type="text" name="cc_email" size=30 id="cc_email'.$id++.$_GET['tabid'].'"  value="'.$r->cc_email.'" /><label style="display:inline-block;">Type:</label>'.CHtml::dropDownList('type', $r->type, $mailTypes, ['id'=>'type'.$id++.$_GET['tabid']]).'<label style="display:inline-block;">Tel:</label><input type="text" name="tel" size=30 id="tel_'.$id++.$_GET['tabid'].'." value="'.$r->tel.'" /><label style="display:inline-block;">Note:</label><input type="text" name="note" size=30 id="note_'.$id++.$_GET['tabid'].'." value="'.$r->note.'" /></div>';
			 	}
			 } else {
			 	$out.='<div class="assign"><span class="exp remove" >--</span> <label style="display:inline-block;">From Email:</label><input type="text" name="from_email" id="from_email_1'.$_GET['tabid'].'" size=30/><label style="display:inline-block;">Subject:</label><input type="text" name="subject" size=30/><label style="display:inline-block;">To Email:</label><input type="text" name="to_email" id="'.$_GET['tabid'].'"  size=30 /><label style="display:inline-block;">CC Email:</label><input type="text" name="cc_email" id="'.$_GET['tabid'].'"  size=30 />Type:</label>'.CHtml::dropDownList('type', '', $mailTypes).'<label style="display:inline-block;">Tel:</label><input type="text" name="tel" size=30/><label style="display:inline-block;">Note:</label><input type="text" name="note" size=30/></div>';
			 }
				$out.='<br/><br/>&nbsp;&nbsp;&nbsp;&nbsp;<span class="add exp"> Add </span>&nbsp;&nbsp;';
		echo $out;
		
	 ?> 
		 </div>
		<br/>
	 <div class="row buttons">
	<?php echo CHtml::submitButton('Save'); ?>
	 </div>
		<?php $this->endWidget(); ?>
</div>
<br/>
<hr/>
<h3>Users Binding</h3>
<div class="form" id="imports-mail-map-user">
		<?php $form=$this->beginWidget('CActiveForm', [
			'id'=>'email-map-user-management-form',
			'enableAjaxValidation'=>false,
			'action'=>'importsMail/manageUserMap'
		]); ?>
		<input type="hidden" value="<?=$isWms?>" id="isWms" name="isWms"/>
			<input type="hidden" value="" id="inports_mail_map_user_items" name="inports_mail_map_user_items"/>
		<?php
			$importscs_list_id = ImportsMail::$importscs_list_id;
			if($isWms)
	 		{
	 			$importscs_list_id = ImportsMail::$wmscs_list_id;
	 		}

			foreach ($mailTypes as $key=>$value) {
				$records= ImportsMailUserMap::model()->findAll('type_id=:type_id', [':type_id'=>$key]);
				$output='<div class="row" ><label>'.$value.':</label>';
				if (!empty($records)) {
					$id=1;
					foreach ($records as $r) {
						$output.='<div class="user_assign"><span class="exp remove_user" >--</span> '.CHtml::dropDownList($key, $r->user_id, $importscs_list_id, ['prompt'=>'SELECT','id'=>'user'.$key.$id++.$_GET['tabid']]).'<label style="display:inline-block;">Tel:</label><input type="text" name="tel" id="tel'.$key.($id++).$_GET['tabid'].'" size=30 value="'.$r->tel.'"/></div>';
					}
				} else {
					if(empty($id))
					{
						$id=1;
					}
					$output.='<div class="user_assign"><span class="exp remove_user" >--</span> '.CHtml::dropDownList($key, '', $importscs_list_id, ['prompt'=>'SELECT','id'=>'user1'.$key.$_GET['tabid']]).'<label style="display:inline-block;">Tel:</label><input type="text" name="tel" id="telt'.$key.($id++).$_GET['tabid'].'" size=30 /></div>';
				}
				$output.='<br/>&nbsp;&nbsp;&nbsp;&nbsp;<span class="add_user exp"> Add User </span>&nbsp;&nbsp;</div>';
				echo $output;
			}
		 ?>
	 <br/>
	 <div class="row buttons">
	<?php echo CHtml::submitButton('Save'); ?>
	 </div>
	 <?php $this->endWidget();?>
</div>

		<script>
		$(function(){
	var tab = $("#<?=$_GET['tabid']?>");
	var panel = tab.data('panel');
				
				$("#sortable",panel).sortable({
					 placeholder:"ui-state-highlight"         
								});
				$( "#sortable",panel ).disableSelection();
				var type=<?= json_encode($mailTypes)?>;
				 var element='<select name="type">';
				for( var typeno in type){
						element+="<option value="+typeno+">"+type[typeno]+"</option>";
				 }
				element+="</select>";
				var id=1;
			 $('.add',panel).on('click',function(){
				 $(this).prev().prev().prev().after('<div class="assign"><span class="exp remove" >--</span> <label style="display:inline-block;">From Email:</label><input type="text" name="from_email" id="from_email_j'+(id++)+'" size=30/><label style="display:inline-block;">Subject:</label><input type="text" name="subject" id="subject_j'+(id++)+'" size=30/><label style="display:inline-block;">To Email:</label><input type="text" name="to_email" id="to_email_j'+(id++)+'" size=30 /><label style="display:inline-block;">To Email:</label><input type="text" name="cc_email" id="cc_email_j'+(id++)+'" size=30 /><label style="display:inline-block;">Type:</label>'+element+'<label style="display:inline-block;">Tel:</label><input type="text" name="tel" id="tel_j'+(id++)+'" size=30 /><label style="display:inline-block;">Note:</label><input type="text" name="note" id="note_j'+(id++)+'" size=30 /></div>');
				 });   
				 
			$('#import-tkt-sum-filter', panel).on('click', 'span.remove', function(){
					 if($('#import-tkt-sum-filter', panel).find('.assign').length>1){
							$(this).parent().remove();
					}
			});    
			
			
			var users=<?=json_encode($importscs_list_id)?>;
			$('.add_user',panel).on('click',function(){
				 var type=$(this).parent().find('select').attr('name');
				 var element='<select name="'+type+'"><option>SELECT</option>'
				 for(var user_id in users){
								element+="<option value="+user_id+">"+users[user_id]+"</option>";
					 }
						element+="</select>";
				 $(this).prev().prev().after('<div class="user_assign"><span class="exp remove_user" >--</span> '+element+'<label style="display:inline-block;">Tel:</label><input type="text" name="tel" id="tel_j'+type+(id++)+'" size=30 />');
			 }); 
			 $('#imports-mail-map-user', panel).on('click', 'span.remove_user', function(){
					 if($(this).parent().parent().find('.user_assign').length>1){
							$(this).parent().remove();
					}
			});
			

					//prepare the data when submit
		$('#email-filter-management-form').on('beforeSerialize', function(){
		var ia = [];
							$('.assign',panel).each(function(){
											 var o = {};
			$('input[type=text]', this).each(function(){
				o[$(this).attr('name')] = $(this).val();
			});
												$('select[name=type]',this).each(function(){
														o[$(this).attr('name')] = $(this).val();
												})
			ia.push(o);
		});
								console.log(ia);
		$('#filter_meta_items',panel).val(JSON.stringify(ia));
		return true;
	});
				
				
				
					//prepare the data when submit
		$('#email-map-user-management-form').on('beforeSerialize', function(){
		var ia = [];
							$('.user_assign',panel).each(function(){
											 var o = {};
												$('select',this).each(function(){
														 o[$(this).attr('name')]={};
														o[$(this).attr('name')]['uid'] = $(this).val();
														o[$(this).attr('name')]['tel']=$('input[name=tel]', $(this).parent()).val();
												});
			ia.push(o);
		});
		$('#inports_mail_map_user_items',panel).val(JSON.stringify(ia));
		return true;
	});

		})    
		</script>