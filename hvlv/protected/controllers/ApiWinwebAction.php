<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class ApiWinwebAction extends CAction {

	public function run() {
		$wwapi = new WinWebAPI;
		$wwapi->auth();
		$r = $wwapi->getTracking($_GET['awbno']);
	}

}
