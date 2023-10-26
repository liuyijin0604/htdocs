<h1 style="text-align: center;">Welcome - <?= Yii::app()->user->name; ?></h1>
<br>

<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link"
                                                         href="<?=$this->createUrl('cartage/task',array('op'=>'manage'))?>"><span
            class="glyphicon glyphicon-tasks"></span><br/>Task List</a></div>
<!-- <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link"
                                                         href="<?=$this->createUrl('cartage/takeList',array('op'=>'manage'))?>"><span
            class="glyphicon glyphicon-th-list"></span><br/>Accepted List</a></div> -->
<!-- <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link"
                                                         href="<?=$this->createUrl('cartage/finishList',array('op'=>'manage'))?>"><span
            class="glyphicon glyphicon-ok"></span><br/>Finished Work</a></div> -->
 <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item"
                                                         href="<?=$this->createUrl('site/logout');?>"><span
            class="glyphicon glyphicon-log-out"></span><br/>Log out</a></div>