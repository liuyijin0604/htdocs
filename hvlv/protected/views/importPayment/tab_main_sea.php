<table class="table table-hover">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Goods Available</th>
      <th scope="col">Storage Start</th>
      <th scope="col">Unit</th>
      <th scope="col">Rate</th>
      <th scope="col">Charge</th>
      <th scope="col">Paid</th>
      <th scope="col">PayTo</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">1</th>
        <?php $seaInfo=$model->getSeaParcelInfo();?>
      <td><?=@$seaInfo['available_date']?></td>
      <td><?=@$seaInfo['storage_start_date']?></td>
      <td><?=@$seaInfo['unit']?> <?=ImParcel::$sea_storage_charge_type[@$seaInfo['pay_type']]?></td>
      <td>$<?=@$seaInfo['rate']."/".ImParcel::$sea_storage_charge_type[@$seaInfo['pay_type']]?></td>
      <td>$<?=@intval($seaInfo['unit'])*@floatval($seaInfo['rate'])?></td>
      <td></td>
      <td><div ><input class="form-control" type="text" id="datepicker" size="4"/></div></td>
    </tr>
  </tbody>
</table>
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
              <p class="list-group-item-text">Wechat Pay. 1% surcharge</p>
            </div>
             <span class="pull-right">
                    <img src="<?=Yii::app()->request->baseUrl.'/'.'images'.'/'.'wechatpay.png'?>" alt="POLI" class="img-responsive" style="max-height: 50px;">
              </span>
          </a>
          <a style="cursor: pointer;" class="list-group-item clearfix" href="#" id="alipay_payment">
            <div class="pull-left" style="width: 60%">
             <h4 class="list-group-item-heading">ALIPay</h4>
              <p class="list-group-item-text">AlIPay. 1.4% surcharge</p>
            </div>
             <span class="pull-right">
                    <img src="<?=Yii::app()->request->baseUrl.'/'.'images'.'/'.'alipay.png'?>" alt="POLI" class="img-responsive" style="max-height: 50px;">
              </span>
          </a>
       </div>
   </div>
<script>
    $(function(){
        $( "#datepicker" ).datepicker({
             dateFormat: "yy-mm-dd",
             minDate: 0,
        }); 
    });
</script>
