<h1 style="text-align: center;">Gatepass Signature-Welcome - <?= Yii::app()->user->name; ?></h1>
<br>
<div class="col-md-4"><a class="dash-item ajax-link"
             href="<?=$this->createUrl('gatePass/index')?>?direct=101"><span
            class="glyphicon glyphicon-tasks"></span><br/>AusPost eParcel</a>
</div>
<div class="col-md-4"><a class="dash-item ajax-link"
             href="<?=$this->createUrl('gatePass/index')?>?direct=115"><span
            class="glyphicon glyphicon-tasks"></span><br/>Fastway</a>
</div>
<div class="col-md-4"><a class="dash-item ajax-link"
             href="<?=$this->createUrl('gatePass/index')?>?direct=976"><span
            class="glyphicon glyphicon-tasks"></span><br/>TNT</a>
</div>
<div class="col-md-4"><a class="dash-item ajax-link"
             href="<?=$this->createUrl('gatePass/index')?>?direct=toll"><span
            class="glyphicon glyphicon-tasks"></span><br/>Toll</a>
</div>
<div class="col-md-4"><a class="dash-item ajax-link"
             href="<?=$this->createUrl('gatePass/index')?>?direct=2619"><span
            class="glyphicon glyphicon-tasks"></span><br/>4PX</a>
</div>
<div class="col-md-4"><a class="dash-item ajax-link"
             href="<?=$this->createUrl('gatePass/index')?>?direct=3590"><span
            class="glyphicon glyphicon-tasks"></span><br/>SF</a>
</div>
<div class="col-md-4"><a class="dash-item ajax-link"
             href="<?=$this->createUrl('gatePass/index')?>?direct=TP"><span
            class="glyphicon glyphicon-tasks"></span><br/>Truck delivery/PICKUP</a>
</div>
<div class="col-md-4"><a class="dash-item ajax-link"
             href="<?=$this->createUrl('gatePass/index')?>"><span
            class="glyphicon glyphicon-tasks"></span><br/>GPass Signature</a>
</div>
<div class="col-md-4"><a class="dash-item ajax-link"
             href="<?=$this->createUrl('gatePass/list')?>"><span
            class="glyphicon glyphicon-tasks"></span><br/>GPass List</a>
</div>
<div class="col-md-4"><a class="dash-item ajax-link"
             href="<?=$this->createUrl('gatePass/needProcessParcelList')?>"><span
            class="glyphicon glyphicon-tasks"></span><br/>Prepare Truck/Pickup Parcel</a>
</div>
 <div class="col-md-4"><a class="dash-item "
                                                         href="<?=$this->createUrl('site/logout');?>"><span
            class="glyphicon glyphicon-log-out"></span><br/>Log out</a></div>