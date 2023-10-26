<div class="form">
 <?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'image_bind_form_'.$_GET['tabid'],
	'enableAjaxValidation'=>false,
        'action'=>'exParcel/link',
         'method'=>'get',
       
)); ?>

     <div class="row" >
         <h3>Barcode:</h3>
	<textarea name="hbn" rows="4" cols="40"><?=$model->hbn?></textarea>
        <div>
            <input name="id" type="hidden" value=<?=$model->id?>>
            <input name="tabid" type="hidden" value=<?=$_GET['tabid']?>>
        <div class="row buttons">
		<?php echo CHtml::submitButton('Bind'); ?>
	</div>
           
        </div>
        </div>
    <?php $this->endWidget(); ?>
</div>
<?php echo '<div id="myModal" class="modal" style="z-index:3;" >
      <span class="top" id="close" style="color: #aaaaaa; font-size: 28px;font-weight: bold;">&times;</span>
      <span style="color: #aaaaaa;font-size: 28px;font-weight: bold;" class="top" id="rotate">&#8635</span>
      <img src="'.$this->get_url_id($model['id']).'" id="mypics" style="width:700px; height:500px;">      
      </div>';
?>


<script>
    $(function(){
        var tab=$('#<?=$_GET["tabid"]?>');
        var win = $('#jqmw_<?=$_GET["tabid"];?>');
        win.css('background-color','rgba(0,0,0,0.1)');
        win.css('width','60em');
        win.css('border','none');
        win.draggable();
        var angel=0;
        $('#myModal',win).draggable();
        $( '#mypics',win).resizable();
        $('#close',win).on('click',function(){
           $('#myModal',win).css('display','none');
        });
        $("#rotate",win).on('click',function(){
             angel+=90;
             $("#mypics",win).css('transform','rotate('+angel+'deg)');
        });
        $('#image_bind_form_<?=$_GET['tabid']?>').on('success',function(r,msg){
            var title=$('textarea[name=hbn]',win).val()||'Update';
            if(msg.create==2){
               if(confirm('The shipment not create Yet? Do you want to create it?')){
                    win.jqmHide();
                    myApp.tabs.CreateTab({
                        title:'Create',
                        url:'<?=Yii::app()->createUrl('exParcel/tcreate',array('id'=>$model->id))?>',
                        bg:false,
                    });
		return false;
              }
            }else if(msg.create==1){
                 var r=msg.id;
                  win.jqmHide();
                  myApp.tabs.CreateTab({
		        title:title,
			url:'exParcel/update/'+r,
		   });
              tab.trigger('reload_image_grid');
              return false;
            
            }
            win.jqmHide();
            tab.trigger('reload_image_grid');
        })
        
    });
       
</script>