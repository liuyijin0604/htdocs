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

<h3>Dispute Task Users Binding</h3>
<div class="form" id="cs-faq-map-user">
		<?php $form=$this->beginWidget('CActiveForm', [
			'id'=>'cs-faq-user-management-form',
			'enableAjaxValidation'=>false,
			'action'=>'tlaTask/manageUserMap'
		]); ?>
			<input type="hidden" value="" id="cs_faq_map_user_items" name="cs_faq_map_user_items"/>
		<?php
			$importscs_list_id = ImportsMail::$importscs_list_id;

			foreach ($typeList as $key=>$value) {
				$records= CsFaqUserMap::model()->findAll('cs_faq_id=:cs_faq_id', [':cs_faq_id'=>$key]);
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
				var type=<?= json_encode($typeList)?>;
				 var element='<select name="type">';
				for( var typeno in type){
						element+="<option value="+typeno+">"+type[typeno]+"</option>";
				 }
				element+="</select>";
				var id=1;
			 $('.add',panel).on('click',function(){
				 $(this).prev().prev().prev().after('<div class="assign"><span class="exp remove" >--</span> <label style="display:inline-block;">From Email:</label><input type="text" name="from_email" id="from_email_j'+(id++)+'" size=30/><label style="display:inline-block;">Subject:</label><input type="text" name="subject" id="subject_j'+(id++)+'" size=30/><label style="display:inline-block;">To Email:</label><input type="text" name="to_email" id="to_email_j'+(id++)+'" size=30 /><label style="display:inline-block;">To Email:</label><input type="text" name="cc_email" id="cc_email_j'+(id++)+'" size=30 /><label style="display:inline-block;">Type:</label>'+element+'<label style="display:inline-block;">Tel:</label><input type="text" name="tel" id="tel_j'+(id++)+'" size=30 /><label style="display:inline-block;">Note:</label><input type="text" name="note" id="note_j'+(id++)+'" size=30 /></div>');
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
			 $('#cs-faq-map-user', panel).on('click', 'span.remove_user', function(){
					 if($(this).parent().parent().find('.user_assign').length>1){
							$(this).parent().remove();
					}
			});
			
				
				
					//prepare the data when submit
		$('#cs-faq-user-management-form').on('beforeSerialize', function(){
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
		$('#cs_faq_map_user_items',panel).val(JSON.stringify(ia));
		return true;
	});

		})    
		</script>