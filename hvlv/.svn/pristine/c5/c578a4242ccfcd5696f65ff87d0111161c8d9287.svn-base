<?php

class CnZipController extends Controller{
	protected $skipAcl = ['area', 'city', 'suggestCity', 'suggestSuburb'];

	public function actionArea(){
		$rs = CnArea::model()->findAll('city_id = :pid ORDER BY init', array(':pid' => $_GET['s']));
		echo '<option value="">Select One</option>';
		foreach($rs as $r){
			echo '<option value="'.$r->name.'" data-zip="'.$r->zip.'">'.$r->init.' - '.$r->name.'</option>';
		}
		Yii::app()->end();
	}

	public function actionCity(){
		$p = CnProvince::model()->find('id = :n', [':n' => $_GET['s']]);
		$rs = CnCity::model()->findAll('pid = :pid ORDER BY weight,init', array(':pid' => $p->id));
		echo '<option value="">Select One</option>';
		foreach($rs as $r){
			echo '<option value="'.$r->name.'" data-id="'.$r->id.'">'.$r->init.' - '.$r->name.'</option>';
		}
		Yii::app()->end();
	}

    public function actionProvince(){
        $rs = CnProvince::model()->findAll('1 ORDER BY weight,init');
        echo '<option value="">Select One</option>';
        foreach($rs as $r){
            echo '<option value="'.$r->name.'" data-id="'.$r->id.'">'.$r->init.' - '.$r->name.'</option>';
        }
        Yii::app()->end();
    }


    public function actionSuggestCity(){
		$rs = CnCity::model()->findAll([
			'condition' => 'pid = :pid AND (name LIKE :t OR init LIKE :t)',
			'order' => 'weight,init', 
			'params' => [':pid' => $_GET['sid'], ':t' => '%'.$_GET['term'].'%']
			]);
		$a = [];
		foreach($rs as $r){
			$p = ['id' => $r->id, 'value' => $r->name, 'zip' => $r->getZip(), 'label' => $r->init.' - '.$r->name];
			$a[] = $p;
		}

		if(!empty($_GET['term'])){
			$rs = CnArea::model()->with('city')->findAll([
				'condition' => 'city.pid = :pid AND (t.name LIKE :t OR t.init LIKE :t)',
				'order' => 'city.init,t.init',
				'params' => [':pid' => $_GET['sid'], ':t' => '%'.$_GET['term'].'%']
				]);
			foreach($rs as $r){
				$c = $r->city;
				$p = ['id' => $c->id, 'aname' => $r->name, 'zip' => $r->zip, 'value' => $c->name, 'label' => $c->init.' - '.$c->name.' > '.$r->init.' - '.$r->name];
				$a[] = $p;
			}
		}
		echo json_encode($a);
	}

	public function actionSuggestSuburb(){
		$rs = CnArea::model()->findAll([
			'condition' => 'city_id = :cid AND (name LIKE :t OR init LIKE :t)',
			'order' => 'init',
			'params' => [':cid' => $_GET['cid'], ':t' => '%'.$_GET['term'].'%']
			]);
		$a = [];
		foreach($rs as $r){
			$p = ['value' => $r->name, 'zip' => $r->zip, 'label' => $r->init.' - '.$r->name];
			$a[] = $p;
		}
		echo json_encode($a);
	}

}