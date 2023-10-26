<?php

class ZwStorageController extends Controller
{
	protected $nonAjax=[];
	public $user;
	public $chargecode;
	public function beforeAction($action)
	{
		$this->user=empty(Yii::app()->user->id)? false : User::model()->findByPk(Yii::app()->user->id);
		return parent::beforeAction($action);
	}

	public function actionList()
	{
		$model = new ImportZwStorage();
		if(!empty($_GET['ImportZwStorage']))
		{
			$model->setAttributes($_GET['ImportZwStorage']);
			$model->hbns = @$_GET['ImportZwStorage']['hbns'];
			$model->org_name = @$_GET['ImportZwStorage']['org_name'];
			$model->consols = @$_GET['ImportZwStorage']['consols'];
			if(User::getCurrentUser()->org_id>1)
			{
				$model->org_id = User::getCurrentUser()->org_id;
			}
			$this->render('zw_storage_sub_list',["model"=>$model]);
			return;
		}
		$this->render('zw_storage_page',["model"=>$model]);
	}

	public function actionUpdateZWStorage()
	{
		$id = @$_GET['id'];
		$model = new ImportZwStorage();
		if(!empty($id))
		{
			$model = ImportZwStorage::model()->findByPk($id);
		}

		if(!empty($_POST['ImportZwStorage']))
		{
			$transaction = Yii::app()->db->beginTransaction();
			try 
			{
				if(empty($model->putcode))
				{
					$oldPutcode = $model->putcode;
					$model->setAttributes($_POST['ImportZwStorage']);
					$user = User::getCurrentUser();
					if(!empty($_POST['ImportZwStorage']['org_id']))
					{
						$model->org_id = $_POST['ImportZwStorage']['org_id'];
					}else
					{
						$model->org_id = $user->org_id;
					}
					if(empty($model->tlano ))
					{
						$model->tlano = ImportZwStorage::generateTlaNo();
					}
					$model->updated = date('Y-m-d H:i:s');

					if(empty($model->created))
					{
						$model->client_id = $user->id;
						$model->memberid = ImportZwStorage::$memberid;
						$model->arrival_city = ImportZwStorage::$storeCode[$model->store_code];
						$model->created = date('Y-m-d H:i:s');
					}

					if(!isset($model->status))
					{
						$model->status = ImportZwStorage::STATE_NEW;
					}

					if(!empty($_POST['ImportZwStorage']['putcode'])&&($_POST['ImportZwStorage']['putcode']!=$oldPutcode))
					{
						if($model->status==ImportZwStorage::STATE_NEW)
						{
							$model->status = ImportZwStorage::STATE_MANUAL;
						}
					}

					$model->mdata['service']=$_POST['ImportZwStorage']['service'];
					$model->channel_code = ImportZwStorage::$channelCode[$model->mdata['service']];
					$model->transport_type = ImportZwStorage::$transportTypeRe[$model->mdata['service']];
					foreach($_POST['items'] as $k1 => $colv)
					{
						$v2 = [];
						foreach($colv as $v)
						{
							$v2[] = $v;
						}
						$_POST['items'][$k1] = $v2;
					}
					$model->eitems = $_POST['items'];

				}

				$model->remark=@$_POST['ImportZwStorage']['remark'];
				$model->mdata['hbns']=$_POST['ImportZwStorage']['hbns'];
				if(!empty($_POST['ImportZwStorage']['notPush']))
				{
					$model->mdata['notPush'] = $_POST['ImportZwStorage']['notPush'];
				}else
				{
					$model->mdata['notPush'] = 0;
				}


				$model->save();

				if(empty($model->relations)||$model->mdata['hbns']!=$_POST['ImportZwStorage']['hbns'])
				{
					$model->hbns=$_POST['ImportZwStorage']['hbns'];
					$model->mdata['hbns']=$_POST['ImportZwStorage']['hbns'];
					$hbnArray = [];
					foreach (preg_split("/[;,]/i", $model->hbns) as $hbn) {
						if(!empty($hbn))
						{
							$hbnArray[] = trim($hbn);
						}
					}
					if(!empty($hbnArray))
					{
						$shipments = ImParcel::model()->findAll("hbn in ('".join("','",$hbnArray)."') or ref in ('".join("','",$hbnArray)."')");
						$sids = array_column($model->relations, 'shipment_id');
						ImportZwStorageRelation::model()->deleteAll("shipment_id in ('".join("','",$sids)."')");
						foreach ($shipments as $key => $s) {
							$izsr = new ImportZwStorageRelation();
							$izsr->shipment_id = $s->id;
							$izsr->parent_id = $model->id;
							$izsr->save();
						}
					}
				}
				$transaction->commit();
			} catch (Exception $ex) {
				$transaction->rollback();
				throw $ex;
			}

			if(!empty($model->id))
			{
				if(empty($model->putcode))
				{
					$zwStorageService = new ZwStorageService();
					$putcode = $zwStorageService->getZWStorage($model);
				}
			}

			echo "done";
			return;
		}

		if(!empty($model->putcode))
		{
			$zwStorageService = new ZwStorageService();
			$model = $zwStorageService->checkZwStorageRealData($model);
		}

		$this->render('zw_storage_form',["model"=>$model]);
	}

	public function actionCancelZWStorage()
	{
		$id = @$_GET['id'];
		$model = new ImportZwStorage();
		if(!empty($id))
		{
			$model = ImportZwStorage::model()->findByPk($id);
			$model->updated = date('Y-m-d H:i:s');
			$model->status = ImportZwStorage::STATE_CANCEL;
			$model->save();
			echo '<script type="text/javascript">javascript:history.go(-1);</script>';
		}
	}

	public function actionLog($id)
	{
		$model = ImportZwStorage::model()->findByPk($id);
		if ($model==null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		$this->render('log', [
			'model'=>$model,
		]);
	}

	public function actionNotes($id)
	{
		$model = ImportZwStorage::model()->findByPk($id);
		ImportZwStorageLog::add($model, Log::LOG_TYPE_NOTES, ['notes' => $_POST['notes']]);
		$this->ajaxResult($model);
	}

}
