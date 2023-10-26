<style type="text/css">
ul.cv_list { list-style: none; clear: both; }
ul.cv_list li { padding: 5px 15px; float: left; }
</style>
<ul class="cv_list ui-tabs-nav">
<li class="ui-state-default"><a class="search" href="/lsx/search.php" target="cv_ifm">查询</a></li>
<li class="ui-state-default"><a href="/lsx/stat.php" target="cv_ifm">报告</a></li>
<li class="ui-state-default"><a class="fetch" href="/lsx/update.php" target="cv_ifm">推送</a></li>
<li class="ui-state-default"><a href="/lsx/exits.php" target="cv_ifm">出口</a></li>
<li class="ui-state-default"><a href="exParcel/refMap" target="cv_ifm">单号绑定</a></li>
<li class="ui-state-default"><a href="/lsx/users.php" target="cv_ifm">用户</a></li>
</ul>
<iframe name="cv_ifm" id="cv_ifm" src="/lsx/search.php" border="0" style="width: 100%; min-height: 500px; border: 1px solid;">
</iframe>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('a.fetch', panel).on('click', function(){
		if((window.prompt('请输入"yes"确认推送流水线') || 'no').toLowerCase() == 'yes'){
			$('iframe#cv_ifm', panel).attr('src', '/lsx/please_wait.html');
			window.alert('流水线推送需要几分钟时间，请耐心等待结果显示');
			return true;
		}
		return false;
	});
	$('ul.cv_list a', panel).on('click', function(){
		$('ul.cv_list li', panel).removeClass('ui-tabs-active ui-state-active');
		$(this).parent().addClass('ui-tabs-active ui-state-active');
	});
	$('a.search', panel).trigger('click');

});
</script>