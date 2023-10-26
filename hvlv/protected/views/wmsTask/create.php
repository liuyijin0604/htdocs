<h1><?=$model->job->no;?> - <?=$model->getType();?></h1>


<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>