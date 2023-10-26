<?php Yii::app()->clientScript->registerCss('mycss',"
#myImg {
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
}

#myImg:hover {opacity: 0.7;}

/* The Modal (background) */
.modal {
   /* Hidden by default */
    display:none;
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    padding-top: 100px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0); /* Black w/ opacity */
}

/* Modal Content (image) */
.modal-content {
    margin: auto;
    display: block;
    width: 80%;
    max-width: 700px;
}

/* Caption of Modal Image */
#caption {
    margin: auto;
    display: block;
    width: 80%;
    max-width: 700px;
    text-align: center;
    color: #ccc;
    padding: 10px 0;
    height: 150px;
}

/* Add Animation */
.modal-content, #caption {    
    -webkit-animation-name: zoom;
    -webkit-animation-duration: 0.6s;
    animation-name: zoom;
    animation-duration: 0.6s;
}

@-webkit-keyframes zoom {
    from {-webkit-transform:scale(0)} 
    to {-webkit-transform:scale(1)}
}

@keyframes zoom {
    from {transform:scale(0)} 
    to {transform:scale(1)}
}

/* The Close Button */
.close {
    position: absolute;
    top: 15px;
    right: 35px;
    color: #f1f1f1;
    font-size: 40px;
    font-weight: bold;
    transition: 0.3s;
}

.close:hover,
.close:focus {
    color: #bbb;
    text-decoration: none;
    cursor: pointer;
}

/* 100% Image Width on Smaller Screens */
@media only screen and (max-width: 700px){
    .modal-content {
        width: 100%;
    }
}
  
")?>

<?php Yii::app()->clientScript->registerScript('test', "

  
var modalImg1 = document.getElementById('img02');
var captionText = document.getElementById('caption');
 $('body').on('click','img',function(){
     $('#myModala').css('display','block');
    modalImg1.src = this.src;
    captionText.innerHTML = this.alt;
     $( function() {
    $( '#myModala' ).draggable();
  } );
});
//


$('#myModala').on('click',function(){
      
      $('#myModala').css('display','none');
    
});


");?>

<?php 
    
    echo 'HBN: '.$_GET['fhbn'];
$this->widget('zii.widgets.grid.CGridView',array(
   
        'id'=>'ex-image-grid',
	
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
              
            'hbn',
            'date',
            'pdf_number',
            'islinked',
                array(
                    'header'=>'Image',
                    'type'=>'raw',
                                    
                                    'value'=>'CHtml::image($data->get_url_id($data->id),
                                     "",
                                     array(\'width\'=>100, \'height\'=>60))',
                    
                 
                    ),
              
		array(
			'class'=>'QCAjaxButtonColumn',
			'template'=>'{link}{unlink}',
			'buttons'=>array
			(  
                            'link'=>array( 
                                'label'=>'link',
                                 'ajax'=>true,
                                 'url'=>'Yii::app()->createUrl("exParcel/link1",array("id"=>$data->id,"hbn"=>$_GET["fhbn"]))',
                                  'imageUrl'=>false,
                                ),
                                'unlink'=>array( 
                                'label'=>'Unlink',
                                 'ajax'=>true,
                                 'url'=>'Yii::app()->createUrl("exParcel/unlink",array("id"=>$data->id,"hbn"=>$_GET["fhbn"]))',
                                  'imageUrl'=>false,
                                  
                                             
                                
                                
                         ),
           
			
			),
		),
          
	),
    
))?>
<div id="myModala" class="modal">
  <img class="modal-content" id="img02" style="width:700px; height:500px;">
  <div id="caption"></div>
</div>
<script>
  
  
</script>