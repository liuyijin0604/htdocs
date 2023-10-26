<div class="portlet<?=($wid['collapse'])? ' collapsed':''?>" id="wid_<?=$name?>">
	<div class="portlet-header"><?php
	if(empty($wid['url'])){
		echo $wid['title'];
	}else{
		echo '<a class="tab_link" title="'.$wid['title'].'" href="'.$this->createUrl($wid['url']).'">'.$wid['title'].'</a>';
	}
	?></div>
	<div class="portlet-content">
	<?php $wid['collapse']? '' : $this->actionWidget($name); ?>
	</div>
</div>