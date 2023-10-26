<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class ApiStorageAction extends CAction
{
	public $ctlr;
	public $debug;
	public $user;
	public static $currencies = [
		'AUD' => '1',
		'USD' => '2',
		'RMB' => '3',
	];

	public function run()
	{
		$this->ctlr = $this->getController();
		$this->debug = !empty($_POST['test']);
		$this->user = empty($this->ctlr->user)? false : User::model()->findByPk($this->ctlr->user);

		if (!empty($_POST['method']) && method_exists($this, $_POST['method'])) {
			if (!in_array($_POST['method'], ['get'])) {
				$this->log(json_encode($_POST));
			}
			$this->{$_POST['method']}();
		} else {
			throw new CHttpException(400, 'API method not found!');
		}
	}

	public function log($l)
	{
		$tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'storageapi' . DIRECTORY_SEPARATOR ;
		file_put_contents($tmp.'shipment_api_'  . date('Y-m-d') . '.log', date('Y-m-d H:i:s').' '.$l."\n", FILE_APPEND);
	}

	public function get()
	{
		$apiService = new ApiService();
		if ($this->debug) {
			$o = new stdClass;
			$o->status = 1;
			$o->msg = 'Success';
			echo json_encode($o);
			return true;
		}

		$d = $this->ctlr->data;
		
		$o = $apiService->getZWStorage($d);
		$this->log(json_encode($o));
		echo json_encode($o);
	}

	public function update()
	{
		$apiService = new ApiService();
		if ($this->debug) {
			$o = new stdClass;
			$o->status = 1;
			$o->msg = 'Success';
			echo json_encode($o);
			return true;
		}

		$d = $this->ctlr->data;

		if (!empty($d->storages)) {
			$o = [];
			$trans = Yii::app()->db->beginTransaction();
			try {
				foreach ($d->storages as $s) {
					$o[] = $apiService->updateZWStorage($s);
				}
				$trans->commit();
			} catch (Exception $ex) {
				$trans->rollback();
				throw $ex;
			}
		} elseif (is_object($d)) {
			$o = $apiService->updateZWStorage($d);
		} else {
			$o=['status' => 0, 'msg' => 'Json data malformatted'];
		}
		$this->log(json_encode($o));
		echo json_encode($o);
	}

}
