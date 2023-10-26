<?php

class FilerepoController extends PController
{
	protected $skipAcl=['upload', 'download', 'downloadPodValidate'];
	protected $nonAjax=['upload','download','downloadTempFile', 'downloadPodValidate'];

	public function actionDownload($hash, $file)
	{
		$f = FileRepo::model()->find('hash = :hash', [':hash' => $hash]);
		if (!$f) {
			throw new CHttpException(404, 'Page not found!');
		}

		if(preg_match('/^AUCustoms[ _\-]+Entry[ ]*Print.+\.pdf$/i', $f->name)){//$f->type == 19 && 
			$pdf = new modiPDF($f->getFile());
			if(preg_match('/Email Cover Sheet/i', $pdf->rawText()) && $pdf->pageCount() > 1) $pdf->removePage(1);
			if($pdf->replace(['FYN Logistics Australia Pty Lt', 'Mega-Top Cargo Pty Limited  ', 'MASTER LOGISTICS PTY LTD', 'Master Logistics Pty Ltd    ', 'Compliant Customs Pty Ltd   ','Autumn Customs Services Pty Lt'], ['TLA Appointed Broker  ', 'TLA Appointed Broker', 'TLA Appointed Broker', 'TLA Appointed Broker', 'TLA Appointed Broker', 'TLA Appointed Broker'])){
				$pdf->output(1, $f->name);
				Yii::app()->end();
			}
		}
		$f->download();
	}

	public function actionUpload($hash)
	{
		$fr = new FileRepo;
		$fr->store($_FILES['file'], $hash);
		echo 'DONE';
	}
	
	public function actionDelete($id)
	{
		$fr = FileRepo::model()->findByPk($id);
		$fr->status = 0;
		$fr->save();
	}
	
	public function actionArchive($id)
	{
		$fr = FileRepo::model()->findByPk($id);
		$fr->status = 90;
		$fr->save();
	}
	
	public function actionType()
	{
		if (!empty($_POST['id'])) {
			$fr = FileRepo::model()->findByPk($_POST['id']);
			$fr->type = $_POST['type'];
			$fr->save();
			if (in_array($_POST['type'], [20,40])) {
				Yii::app()->db->createCommand()->update('filerepo', ['active'=>9], 'type = :t AND fid = :fid AND id != :id', [':t' => $_POST['type'], ':fid' => $fr->fid, ':id' => $fr->id])->execute();
			}
		}
	}

	public function loadModel($id)
	{
		$model=FileRepo::model()->findByPk($id);
		if ($model===null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}
	
	/**
	 * Lists and search.
	 */
	public function actionList()
	{
		$model=new FileRepo('search');
		$model->unsetAttributes();  // clear any default values
		if (isset($_GET['FileRepo'])) {
			$model->attributes=$_GET['FileRepo'];
		}

		$this->render('list', [
			'model'=>$model,
		]);
	}
	
	public function ajaxResult($model, $attr=[], $msg='')
	{
		$es = $model->getErrors();
		$r = new stdClass;
		$r->done = false;
		$r->msg = '';
		if (isset($model->isCreate)) {
			$r->isCreate = $model->isCreate;
		}
		foreach ($attr as $k=>$a) {
			if (is_string($k) && !empty($a)) {
				$r->{$k} = $a;
			}
			if (!is_array($a) && isset($model->{$a})) {
				$r->{$a} = $model->{$a};
			}
		}
		if (empty($es)) {
			$r->done = true;
			$r->msg = empty($msg)? $this->t('{model} Saved Successfully', ['{model}' => get_class($model)]) : $msg;
		} else {
			foreach ($es as $e) {
				foreach ($e as $el) {
					$r->msg .= $el.'<br />';
				}
			}
		}
		echo json_encode($r);
		Yii::app()->end();
	}

	public function actionDownloadTempFile()
	{
		$filename = $_GET['filename'];
		$tempfile = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.$filename;
		header("Cache-Control: maxage=1");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Disposition: attachment;filename=\"".urldecode(basename($tempfile)).'"');
		header("Content-Transfer-Encoding: binary");
		readfile($tempfile);
		unlink($tempfile);
		Yii::app()->end();
	}

	public function actionCnl($id){
		
	}

	public function actionDownloadPodValidate()
	{
		if (!empty($_GET['ref']) && !empty($_GET['hash'])) {
            $ref = $_GET['ref'];
			$hash = $_GET['hash'];
            $p = ImParcel::model()->find('ref = :ref', [":ref" => $ref]);
			$file = FileRepo::model()->findByAttributes(array('hash' => $hash));
			if (empty(Yii::app()->user) || (!empty(Yii::app()->user) && Yii::app()->user->guestName == 'Guest')) {
				echo "<script>function postcodeVeryfi() {
					var postcode;
					var systempostcode = '{$p->cnee->postcode}'
					postcode = prompt('Enter Your Postcode.')
					if (systempostcode == postcode) {
						window.location.href='https://os.toplogistics.com.au/filerepo/" . $hash . "/" . $file->name . "';
					} else {
						return false;
					}
					return false;
				}
				postcodeVeryfi();</script>";
			} else {
				// for hvlv or ims, no need for postcode validate
				echo "<script>function postcodeVeryfi() {
					window.location.href='https://os.toplogistics.com.au/filerepo/" . $hash . "/" . $file->name . "';
					return false;
				}
				postcodeVeryfi();</script>";
			} 
        }
	}
}
