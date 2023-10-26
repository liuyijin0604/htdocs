<?php

class XeroController extends PController
{

	public function actionAuthCode()
	{
		yii::log(json_encode($_GET), 'warning');
	}

}