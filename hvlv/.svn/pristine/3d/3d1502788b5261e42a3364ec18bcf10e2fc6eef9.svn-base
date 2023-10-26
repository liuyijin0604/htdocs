<?php 
class WarehouseService extends Service
{

	public function __construct()
	{
		
	}

	protected function setResult()
	{
		$r->color = 'red';
		$r->sound = 'not_found';
		$r->stop = 0;
		$r->msg = 'NOT FOUND';
		$r->status = 'UNKNOWN';
		$r->nosound = 0;
		$r->found = 0;
		$this->groupSound($r);
	}
	

	public function getDptId()
	{
		$tempWareHouse=106;
		if (!empty(Yii::app()->session['scan_warehouse']) && Yii::app()->session['scan_warehouse']=='melbourne') {
			$tempWareHouse=218;
		}else if (!empty(Yii::app()->session['scan_warehouse']) && Yii::app()->session['scan_warehouse']=='brisbane') {
			$tempWareHouse=530;
		}
		return $tempWareHouse;
	}

	protected function groupSound(&$r)
	{
		$ss = [];
		foreach(['blueLabel','sound', 'amazon', 'area', 'label','dg','cfire', 'sorting','special'] as $k){
			if(!empty($r->{$k})){
				$ss[] = $r->{$k};
			}
			unset($r->{$k});
		}
		$r->sounds = $ss;
	}

}
?>