<h1><?=$this->t('Create Export Shipment');?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
<?php if (!empty($m)) : ?>
<a class="tab_link"  style="display: none" id="mynewtag1<?=$_GET['tabid'];?>" href="<?=$this->createUrl('exParcel/newtag',array('id'=>$m['id']));?>" title="new tag"><div class="icon" style="background-position:-16px 0"></div> Open new tag</a> &nbsp; 
<?php endif;?>
    <?php if(!empty($m)){
      echo '<div id="myModal'.$_GET['tabid'].'" class="modal" style="position: absolute; top:100px; right:50px;">
      <span class="top" id="close1'.$_GET['tabid'].'" style="color: #aaaaaa;
     font-size: 28px;
     font-weight: bold;">&times;</span>
      <span style="color: #aaaaaa;
     font-size: 28px;
     font-weight: bold;" class="top" 
      id="rotate'.$_GET['tabid'].'">&#8635</span>
      <h4>HBN:'.$this->gethbnbyid($m['id']).'</h4>
      <img src="'. $this->get_url_id($m['id']). '" id="mypic'.$_GET['tabid'].'" style="width:700px; height:500px;">      
      </div>';

}?>
<script type="text/javascript">
$(function(){
     var tab = $('#<?= $_GET["tabid"]; ?>');
     
     $('#ex-parcel-form', tab.data('panel')).data('reset', true);
     var panel=$("#<?= $_GET['tabid'] ?>").data('panel');
     $( '#myModal<?= $_GET["tabid"]; ?>').draggable();
     $( '#mypic<?= $_GET["tabid"]; ?>').resizable();
     $("#close1<?= $_GET["tabid"]; ?>").on('click',function(){
     $("#myModal<?= $_GET["tabid"]; ?>").css('display','none');
      });
      <?php if(!empty($m)):?>
     $("#mypic<?= $_GET["tabid"]; ?>").on('dblclick',function(){
      window.open("<?=$this->createUrl('exParcel/newTag',array('id'=>$m['id']))?>",'image',true); 
     $("#myModal<?= $_GET["tabid"]; ?>").css('display','none');
      });
      <?php endif;?>
     var angel=0;
   $("#rotate<?= $_GET["tabid"]; ?>").on('click',function(){
       angel+=90;
     $("#mypic<?= $_GET["tabid"]; ?>").css('transform','rotate('+angel+'deg)');
   });
//    $('form#ex-parcel-form', tab.data('panel')).on('success', function(e, r){
//		var url = tab.data('url').replace('exParcel/create','exParcel/update/'+r.id);
//		tab.data('url', url).trigger('load');
//     });

    
   });
</script>