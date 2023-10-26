<h1><?=$this->t(' Suplus Tabs');?></h1>
</br>
</br>
</br>
<div id="imco-consol-tabs">
  <ul>
 <?php
 $tabs = array(
	array('daipost',$this->t('DAIPOST'), true),
  array('others',$this->t('others'), true)
 );
 foreach($tabs as $tab){
	if(Acl::hasAccess($this->CaName.'/'.$tab[0]) && $tab[1]){
		$href = strpos($tab[0], '/') === false? $this->createUrl('consolProcess/surplusShipmentList',array('tab'=>$tab[0], "tabid" => $_GET["tabid"],'pod_id'=>$_GET['pod_id'],'consol_type'=>$_GET['consol_type'],'podName'=>$_GET['podName'] )) : $tab[0];
		echo '<li><a href="'.$href.'">'.$tab[1].'<span style="color:red" ></span>'.'</a></li>';
	}
 }
 ?>
  </ul>
</div>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	$('#imco-consol-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
		myApp.ajaxifyForm(this);
	}});

 //oTlaTask
$.oTlaTask = {
  opt: {cid: 'oTlaTask-container',
    noticeUrl: myApp.config.baseURL + 'tlaTask/notice',
    type: 'notify',
    msg: '',
    id: 0,
    from: '',
    ajax: false,
    sajx: false,
    timestamp:""
  },
  update: function(id,o) {
    if(o>0)
    {
      o="*";
    }else
    {
      o ="";
    }
    if($(id).html()=="")
    {
      $(id).html(o);
    }
    return "";
  },
  clean: function(id) {
   $(id).html("");
    return "";
  },
   chkAjax: function(){
    if($.oTlaTask.opt.ajax) $.oTlaTask.opt.ajax.abort();
    return !$.oTlaTask.opt.sjax;
  },
  getNotice: function(){
    if($.oTlaTask.chkAjax()){
      var thisUrl = $.oTlaTask.opt.noticeUrl+"?timestamp="+$.oTlaTask.opt.timestamp;
      // $.oTlaTask.opt.ajax = $.post($.oTlaTask.opt.url, {'ids': $.oTlaTask.serialize()}, function(r){
      //  for(var i in r){
      //    $.oTlaTask.add(r[i]);
      //  }
      // }, 'json');
      $.ajax({
              url: thisUrl,
              type: "get",
              processData: false,
              contentType: false,
              success: function(r) {
                  var valueList = r.split(",");
              $.oTlaTask.update('#my_tla_task_tab_tla_task_list',valueList[0]);
              $.oTlaTask.update('#my_tla_task_tab_closed_by_assigned_user',valueList[1]);
              $.oTlaTask.opt.timestamp = valueList[3];
               },
              error: function(e) {
                  console.log(e);
              }
      }); 
    }
  }
};
  $.oTlaTask.getNotice();

    $(window).everyTime(6e4, $.oTlaTask.getNotice);

});
</script>
