<h2>ExCRM tickets Report</h2>
<style type="text/css">
#ex-crm-tkt-sum{
	list-style: none;
	margin: 0;
	padding: 0;
	line-height: 25px;
}
#ex-crm-tkt-sum ul{
	list-style: none;
	margin: 0;
	padding-left: 20px;
}

#ex-crm-tkt-sum .grid-view table.items {
    display: flex;
    flex-flow: column;
    height: 100%;
    width: 100%;
}
#ex-crm-tkt-sum .grid-view table.items thead, #ex-crm-tkt-sum .grid-view table.items tfoot {
    /* head takes the height it requires, 
    and it's not scaled when table is resized */
    flex: 0 0 auto;
    width: calc(100% - 1.15em);
}
#ex-crm-tkt-sum .grid-view table.items tbody {
    /* body takes all the remaining available space */
    flex: 1 1 auto;
    display: block;
	max-height: 400px;
    overflow-y: scroll;
    overflow-x: hidden;
}
#ex-crm-tkt-sum .grid-view table.items tbody tr {
    width: 100%;
}
#ex-crm-tkt-sum .grid-view table.items thead, #ex-crm-tkt-sum .grid-view table.items tfoot, #ex-crm-tkt-sum .grid-view table.items tbody tr {
    display: table;
    table-layout: fixed;
}

#ex-crm-tkt-sum li:hover>p{
	color: #090;
	font-weight: bold;
}
#ex-crm-tkt-sum span.exp{
	border: 1px solid #15538b;
	color: #15538b;
	border-radius: 4px;
	padding: 0 2px;
	cursor: pointer;
	font-weight: bold;
}
#ex-crm-tkt-sum span.exp:hover{
	color: #fff;
}
#ex-crm-tkt-sum span.exp:before{
	content: "+";
}
#ex-crm-tkt-sum span.exp.in{
	padding: 0 4px;
}
#ex-crm-tkt-sum span.exp.in:before{
	content: "-";
}
</style>
<h4>Tickets Overview</h4>
<div class="form">
	<div id="export_sum_result" style="margin: 10px 0; border: 1px solid;padding:20px; display: none">
	</div>

	<ul id="ex-crm-tkt-sum" style="margin-top: 20px; list-style: none;font-size: 1.2em;"></ul>

</div><!-- form -->
<br/>
<hr>
<br/>
<h4>Task Assign</h4>
<div class="form">
    <?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'excrm-task-assign-form',
	'enableAjaxValidation'=>false,
)); ?>
    <input type="hidden" value="" id="the_meta_items" name="the_meta_items"/>
    <div id="assignment">
   <?php 
     foreach (ExCrm::$types as $k=>$v){
         $users=CrmManage::model()->findAll('type=20 AND tid=:tid',array(':tid'=>$k));
         $out='<div class="row" ><label>'.$v.':</label>';
         if(empty($users)){
             $out.='<div class="assign"><input type="hidden" name="'.$k.'"/><input type="text" class="complete" /></div>';
         }else{
             foreach ($users as $i=>$vi){
               $out.='<div class="assign"><input type="hidden" value="'.$vi->uid.'" name="'.$k.'"/><input type="text" value="'.User::getUserName($vi->uid).'" class="complete"/></div>';
             }
         }
         $out.=' <span class="add">Add</span>&nbsp;&nbsp;<span class="remove">Remove</span></div>';
         echo $out;
             }
   ?>
    </div>
    <div class="row buttons">
		<?php echo CHtml::submitButton('Save'); ?>
   </div>
    <?php $this->endWidget(); ?>
</div>
<br>
<hr>
<h4>View Tickets-Manager</h4>

<script type="text/javascript">
$(function(){
	var tab_id = '<?=$_GET["tabid"];?>';
	var tab = $('#'+tab_id);
	var panel = tab.data('panel');
	var brl = '<?=Yii::app()->createAbsoluteUrl("exCrm/ajaxTicketsSum");?>';
       $('#ex-crm-tkt-sum', panel).addClass('loading').load(brl +'?path=1', function(){ $('#ex-crm-tkt-sum', panel).removeClass('loading')});
 $('#ex-crm-tkt-sum', panel).on('click', 'span.exp', function(){
		var li = $(this).parent().parent();
		var tl = li.find('>ul');
		var me = $(this);
		if($(this).data('loaded') == 1){
			if($(this).hasClass('in')){
				tl.slideUp();
				$(this).removeClass('in');
			}else{
				tl.slideDown();
				$(this).addClass('in');
			}
		}else{
			li.addClass('loading');
			tl.load(brl +'?path='+$(this).data('path'), function(){
				me.addClass('in').data('loaded', 1);
				li.removeClass('loading');
				afterLoading(tl);
			});
		}
		return false;
	}).on('click', 'a.ogrpt', function(){
		var os = [];
		$(this).parent().find('input.ogcb:checked').each(function(){
			os.push($(this).val());
		});
		$(this).attr('href', $(this).data('ub')+os.join(','));
	});
        
     //get the data
        <?php  $items=[];
              foreach (ExCrm::$types as $k=>$v){
               $rs= CrmManage::model()->findAll('type=20 and tid=:tid',array(':tid'=>$k));
               foreach ($rs as $r){
                   $items[$k][]=$r->uid;
               }
              }
         ?>
                 
    var items=<?=json_encode($items)?>||[];
    var addItem = function(add){
		var tb = $('#items tbody', panel);
		var id = $('tr', tb).length;
		var add = add || 1;
		while(add-- > 0){
			tb.append('<tr class="'+(id%2==0? 'even' : 'odd')+'"><td>'+(id+1)+'</td><td><input type="text" name="items[g]['+id+']" size="40" value="'+(pitems.g && pitems.g[id]? pitems.g[id] : '')+'"'+(pitems.g && pitems.g[id] === false? ' class="error"' : '')+' /></td><td><input type="text" class="item_g_zh'+(pitems.g_zh && pitems.g_zh[id] === false? ' error' : '')+'" name="items[g_zh]['+id+']" size="40" value="'+(pitems.g_zh && pitems.g_zh[id]? pitems.g_zh[id] : '')+'" /></td><td><input type="text" name="items[hs]['+id+']" size="20" value="'+(pitems.hs && pitems.hs[id]? pitems.hs[id] : '')+'" /></td><td><input type="text" class="item_qty'+(pitems.q && pitems.q[id] === false? ' error' : '')+'" name="items[q]['+id+']" size="5" value="'+(pitems.q && pitems.q[id]? pitems.q[id]: '')+'" /></td><td><input type="text" class="item_uv'+(pitems.v && pitems.v[id] === false? ' error' : '')+'" name="items[v]['+id+']" size="10" value="'+(pitems.v && pitems.v[id]? pitems.v[id]: '')+'" /></td><td class="item_tot">&nbsp</td><td><span class="exp show_add">+</span></td></tr>');
			id++;
		}
                
	};
     $('.add',panel).on('click',function(){
         var type=$(this).prev().find('input').attr('name');
         $(this).prev().after('<div class="assign"><input type="hidden" name="'+type+'"/><input type="text" class="complete"/></div>');
         });   
         
    $('.remove',panel).on('click',function(){
          if($(this).parent().find('.assign').length>1){
              $(this).prev().prev().remove();
          }
          
    });  
    
    var ac_select = function(me, ui){
        me.val(ui.label);
	me.prevAll("input[type=hidden]").val(ui.value).data("ov",ui.value);
	};
    $('#assignment').on('focus','input.complete',function(){
        var me = $(this);
        if(me.data('acinit') == 1) return;
        me.autocomplete({
            'source': "<?=$this->createUrl('user/suggest')?>",
            'showAnim': 'fold',
	    'minLength': 2,
	    'delay': 200,
            'select': function(event, ui){
				ac_select(me, ui.item);
				return false;
			},
            'response': function(evt, ui){
                      console.log(ui);
                     $(this).prevAll("input[type=hidden]").val(0);
				if(ui.content.length == 1){
					ui.item = ui.content[0];
					ac_select($(this), ui.item);
				}else if(ui.content.length == 0){
					me.prevAll("input[type=hidden]").val(me.prevAll("input[type=hidden]").data("ov"));
                                        me.val('');
				}
				return false;
			},
        }).data('acinit',1);
        
    });//bind to 'acinit'
    //prepare the data when submit
    $('#excrm-task-assign-form').on('beforeSerialize', function(){
		var ia = [];
            	$('.assign',panel).each(function(){
                       var o = {};
			$('input[type=hidden]', this).each(function(){
				o[$(this).attr('name')] = $(this).val();
			});
			ia.push(o);
		});
                console.log(ia);
		$('#the_meta_items',panel).val(JSON.stringify(ia));
		$('tbody input',panel).prop('disabled', true);
              
		return true;
	});

});

</script>
