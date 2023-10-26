<style type="text/css">
	.clickTheTime
	{
		font-size:1em;
	}
</style>
<div class="content-padded">

	<div>
		<?php

				$columns = array(
				    		//'pickup',
							array('name'=>'datetime','type'=>'raw','value'=>'"<p class=\"clickTheTime\" title=\"".$data["datetime"]."\">".$data["datetime"]."</p>"','filter'=>false)
				         );

				 $this->widget('application.extensions.booster.TbExtendedGridView',array(
				    'fixedHeader'=>true,
				    'id'=>'time_grid_view',
				    'filter'=>$dataProvider[1],
				    'type'=>'striped bordered',
				    'headerOffset'=>40,
				    'responsiveTable'=>true,
				    'dataProvider'=>$dataProvider[0],
				    'template' => "{summary}\n{items}\n{pager}",
				    'afterAjaxUpdate'=>'function(){}',
				    'columns'=>$columns,

				    ),
				    
				); ?>
	</div>

</div>

<script type="text/javascript">

	$('.clickTheTime').on('touchend',function(){
		$time = $(this).attr('title');
		pcadbApp.selectTime($time);
		javascript:window.history.back();

	});

	$('#time_grid_view p').click(function(){
		var btn = this;  
		var event = document.createEvent('Events');
		event.initEvent('touchend', true, true); 
		btn.dispatchEvent(event); 
	});

	pcadbApp.initLink();
</script>
