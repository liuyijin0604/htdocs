<div class="container">
    <?php
         $invoices = Invoice::model()->findAll('pid = :pid AND type in(41,45) AND status NOT IN (10,8)',[':pid' => $model->id]);
         $dp = new CArrayDataProvider($invoices,array(
                'id' => 'imconsol_real_invoices-'.$_GET["tabid"]
         ));
        $dp->pagination=array('pageSize' => 5);
           $total = 0;
        foreach ($invoices as $invoice) {
                $total += $invoice->total;
        }
        $currency="AUD";
        $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>$_GET["tabid"].'_ordereport-grid-online',
	'cssFile' => false,
	'dataProvider' => $dp,// $dp->search(),
	'filter' => null,
	'enableSorting' => false,
	'columns'=>array(
		array('name' => 'no', 'value' => '$data->no', ),
		array('name' => 'bill_to', 'value' => '$data->cust->name', ),
                array('name' => 'status', 'value' => '$data->getStatus()', 'footer' => 'Total: ', 'footerHtmlOptions' => array('align' => 'right')),
		array('header' => 'Invoice Total', 'value' => '$data->getCurrency().$data->total', 'footer' => $currency . ' ' . AppHelper::money_format("%i", $total)),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{view}',
			'buttons'=>array
			(
				'view' => array(
					'url' => 'Yii::app()->createURL("parcelStatus/print", array("id" => $data->id,"pid"=>'.$model->id.'))',
					'imageUrl'=>false,
					'options' => array('class' => 'grid_view_btn', 'target' => '_blank'),
				),
			),
		),
)));
          
?>
    <h3>Online Payment</h3>
    <div class="row">
        <div class="col-md-7">
        <a style="cursor: pointer;" class="list-group-item clearfix" href="#" id="poly_payment">
            <div class="pull-left" style="width: 60%">
             <h4 class="list-group-item-heading">POLI</h4>
              <p class="list-group-item-text">POLI support most Australia's Online Bank.1% surcharge(GAP:$3)</p>
            </div>
             <span class="pull-right">
                    <img src="<?=Yii::app()->request->baseUrl.'/'.'images'.'/'.'poli.png'?>" alt="POLI" class="img-responsive" style="max-height: 50px;">
              </span>
          </a>
           <a style="cursor: pointer;" class="list-group-item clearfix" href="#" id="wechat_payment">
            <div class="pull-left" style="width: 60%">
             <h4 class="list-group-item-heading">Wechat Pay</h4>
              <p class="list-group-item-text">Wechat Pay. 3% surcharge</p>
            </div>
             <span class="pull-right">
                    <img src="<?=Yii::app()->request->baseUrl.'/'.'images'.'/'.'wechatpay.png'?>" alt="POLI" class="img-responsive" style="max-height: 50px;">
              </span>
          </a>
          <a style="cursor: pointer;" class="list-group-item clearfix" href="#" id="alipay_payment">
            <div class="pull-left" style="width: 60%">
             <h4 class="list-group-item-heading">ALIPay</h4>
              <p class="list-group-item-text">AlIPay. 3.4% surcharge</p>
            </div>
             <span class="pull-right">
                    <img src="<?=Yii::app()->request->baseUrl.'/'.'images'.'/'.'alipay.png'?>" alt="POLI" class="img-responsive" style="max-height: 50px;">
              </span>
          </a>
       </div>
   </div>
</div>
<br/>
<script type="text/javascript">
    $(function(){
        $("#poly_payment").on('click',function(event){
            event.preventDefault();
            var id=<?=$model->id?>;
           if(confirm('Are you sure to Process the Payment with Poli')){
              $.ajax({
                  url:'<?=Yii::app()->createUrl('importsPay/MakeSupay',array('id'=>$model->id,'act'=>'Poli'));?>',
                  success:function(e){
                      e = JSON.parse(e);
                     if(e.success==true){
                        window.open(e.url,'_blank');
                      }else{
                        Alert('Failed!');  
                        }
                  }
              });
           }
        });
         $("#wechat_payment").on('click',function(event){
            event.preventDefault();
           var id=<?=$model->id?>;
           if(confirm('Are you sure to process the Payment with Wechat')){
              $.ajax({
                  url:'<?=Yii::app()->createUrl('importsPay/MakeSupay',array('id'=>$model->id,'act'=>'Wechat'));?>',
                  success:function(e){
                      e = JSON.parse(e);
                     if(e.success==true){
                        window.open(e.url,'_blank');
                      }else{
                        Alert('Failed!');  
                        }
                  }
              });
           }
        });
         $("#alipay_payment").on('click',function(event){
          event.preventDefault();
           var id=<?=$model->id?>;
           if(confirm('Are you sure to process the Payment with Alipay')){
              $.ajax({
                  url:'<?=Yii::app()->createUrl('importsPay/MakeSupay',array('id'=>$model->id,'act'=>'Alipay'));?>',
                  success:function(e){
                      e = JSON.parse(e);
                     if(e.success==true){
                        window.open(e.url,'_blank');
                      }else{
                        Alert('Failed!');  
                        }
                  }
              });
           }
        });
    });
</script>