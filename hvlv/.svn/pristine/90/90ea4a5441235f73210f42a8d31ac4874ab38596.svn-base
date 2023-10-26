<h3>Related Consignee</h3>
   <?php
    $provide=$model->findRelatedConsignee();
    $dataprovider=new CArrayDataProvider($provide); 
    $dataprovider->pagination=array('pageSize' =>5);
    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'custom-client-list-grid',
        'htmlOptions' => array('style' => 'width: 70%'),
        'afterAjaxUpdate'=>'function(r,s){$("#custom_summary").html($(s).find("#custom_summary").html());}',
        'cssFile' => false,
        'dataProvider' => $dataprovider,
        'columns' => array(
            array('name'=>'ref', 'type'=>'raw','value'=>'"<a href=\"".Yii::app()->createURL("imParcel/update",array("id"=>$data["id"]))."\" class=\"tab_link\" title=\"".$data["ref"]."\">".$data["ref"]."</a>"'),
            'name',
            'tel',
            array('header' => 'Email', 'type' => 'raw',   'value'=>function($data){return CHtml::tag('div', array('title'=>@$data["email"],),substr(@$data["email"],0,15));},),
            array('name' => 'loa_name', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->baseUrl."/filerepo/".$data["loa_hash"]."/".$data["loa_name"]."\" target=\"_blank\">".$data["loa_name"]."</a>"'),
            array(
			'class'=>'CButtonColumn',
			'template'=>'{and}',
			'buttons'=>array
			(
        'and' => array(
                                        'label'=>'cpy email&loa',
                                        'url'=>'Yii::app()->createUrl("customProcess/copyEmailAndLoa",array("eid"=>$data["id"],"efid"=>'.$model->id.',"lid"=>$data["loa_id"],"lfid"=>'.$model->shipment->id.'))',
          'imageUrl'=>false,
          'visible'=>'true',
          'options' => array('class' => 'grid_edit_btn cpy_email_class'),
        )
    //     ),
				// 'email' => array(
    //                                     'label'=>'cpy email',
    //                                     'url'=>'Yii::app()->createUrl("customProcess/copyEmail",array("id"=>$data["id"],"fid"=>'.$model->id.'))',
				// 	'imageUrl'=>false,
				// 	'visible'=>'true',
				// 	'options' => array('class' => 'grid_edit_btn cpy_email_class'),
				// ),
    //                          'loa' => array(
    //                                     'label'=>'cpy Loa',
				// 	'imageUrl'=>false,
				// 	'url'=>'Yii::app()->createUrl("customProcess/copyLoa", array("id"=>$data["loa_id"],"fid"=>'.$model->shipment->id.'))',
    //                                      'options' => array('class' => 'grid_edit_btn cpy_loa_class')
    //                           ),
			),
		),
        ),
    ));
   
     ?>
<script type="text/javascript">
    $(function(){
        var win = $('#jqmw_<?=$_GET["tabid"];?>');
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = $('#<?=$_GET["tabid"];?>').data('panel');
        $('.cpy_email_class',win).off('click').on('click',function(event){
            event.preventDefault();
            if(confirm('Are you sure to copy the email to this parcel?')){
         
               $.get($(this).attr('href'),function(r){
                  if(r=='done'){
                         myApp.notice('Done', 5000);
                  }else{
                         myApp.alert(r, false);
                  }
               });
            }
        });
           $('.cpy_loa_class',win).off('click').on('click',function(event){
            event.preventDefault();
            if(confirm('Are you sure to copy Loa to this parcel?')){
               $.get($(this).attr('href'),function(r){
                  if(r=='done'){
                         myApp.notice('Done', 5000);
                  }else{
                         myApp.alert(r, false);
                  }
               });
            }
        });
    })
</script>

