<h2>Billing</h2>
<?php if (empty($model->mdata['edi_job'])) { ?>
	<a href="<?=$this->createUrl('ediJob/createJob', array('org' => 1084, 'cnlorder' => $model->id))?>" title="New Air Feight Job" class="tab_link"><div style="background-position:-16px 0" class="icon"></div> New EdiJob</a>
	<a href="<?=$this->createUrl('ediJob/linkJob', array('cnlorder' => $model->id))?>" title="Link Air Feight Job" class="jqm_link"><div style="background-position:-16px 0" class="icon"></div> Link EdiJob</a>
<?php } else { ?>
	<a href="<?=$this->createUrl('ediJob/update', array('id' => $model->mdata['edi_job']))?>" title="<?=EdiJob::model()->findByPk($model->mdata['edi_job'])->no?>" class="tab_link"><div style="background-position:-176px -544px" class="icon"></div> <?=EdiJob::model()->findByPk($model->mdata['edi_job'])->no?></a>
<?php } ?>