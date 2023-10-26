
<?php echo '<div id="myModal1'.$_GET['tabid'].'" class="modal" style="z-index:3;" >
      <span class="top" id="close1'.$_GET['tabid'].'" style="color: #aaaaaa; font-size: 28px;font-weight: bold;">&times;</span>
      <span style="color: #aaaaaa;font-size: 28px;font-weight: bold;" class="top" id="rotate'.$_GET['tabid'].'">&#8635</span>
      <h4>HBN:'.$this->gethbnbyid($model['id']).'</h4>
      <img src="'.$this->get_url_id($model['id']).'" id="mypics'.$_GET['tabid'].'" style="width:1000px; height:700px;">      
      </div>';
?>

<script>
    var tab=$("#<?= $_GET['tabid'] . '123' ?>");
     $( function() {
    $( '#myModal1<?= $_GET["tabid"]; ?>').draggable();
  } );
   $( function() {
    $( '#mypics<?= $_GET["tabid"]; ?>').resizable();
  });
     $("#close1<?= $_GET["tabid"]; ?>").on('click',function(){
      $("#myModal1<?= $_GET["tabid"]; ?>").css('display','none');
  });
  var angel=0;
   $("#rotate<?= $_GET["tabid"]; ?>").on('click',function(){
       angel+=90;
     $("#mypics<?= $_GET["tabid"]; ?>").css('transform','rotate('+angel+'deg)');
   });
 
   $('#mypics<?= $_GET["tabid"]; ?>').on('click',function(){
    
       
   });
       
</script>