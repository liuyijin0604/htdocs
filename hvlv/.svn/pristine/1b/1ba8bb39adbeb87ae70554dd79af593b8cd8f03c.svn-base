<?php

class AclController extends Controller{

	/**
	 * Manage Group ACL.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionManage(){
		$this->render('manage');
	}

	public function actionSave(){
		if(isset($_POST['n'])){
			$p = explode('-', $_POST['n']);
			$gid = $p[0] == 'g'? $p[2] : 0;
			$uid = $p[0] == 'u'? $p[2] : 0;
			$a = Acl::model()->find('oid = :oid AND gid = :gid AND uid = :uid', array(':oid' => $p[1], ':gid' => $gid, ':uid' => $uid));
			if(!$a){
				$a = new Acl;
				$a->oid = $p[1];
				$a->gid = $gid;
				$a->uid = $uid;
			}
			$a->p = $_POST['v'];
			$a->save();
		}
	}
}
