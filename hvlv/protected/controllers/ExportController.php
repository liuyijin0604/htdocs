<?php
class ExportController extends Controller
{
	protected $nonAjax=array('products','org','sales');
	
	public function actionProducts(){
		if(!empty($_POST['p'])){
			function addFolderToZip($dir, $zipArchive, $zipdir = ''){ 
				if (is_dir($dir)){
					if ($dh = opendir($dir)){
						//Add the directory
						if(!empty($zipdir)) $zipArchive->addEmptyDir($zipdir);
						// Loop through all the files
						while (($file = readdir($dh)) !== false){
							//If it's a folder, run the function again!
							if(!is_file($dir . $file)){
								// Skip parent and root directories
								if( ($file !== ".") && ($file !== "..")){
									addFolderToZip($dir . $file . "/", $zipArchive, $zipdir . "/" . $file . "/"); 
								}
							}else{
								// Add the files
								$zipArchive->addFile($dir . $file, $zipdir . $file);
							}
						}
					}
				}
			}
			function unlinkRecursive($dir, $deleteRootToo=true){ 
				 if(!$dh = @opendir($dir)) return;
				 while(false !== ($obj = readdir($dh))){
					 if($obj == '.' || $obj == '..') continue;
					 if (!@unlink($dir . '/' . $obj)) unlinkRecursive($dir.'/'.$obj, true);
				 }

				 closedir($dh); 
				 
				 if($deleteRootToo) @rmdir($dir);
				 
				 return; 
			 } 

			$org = Org::model()->findByPk($_POST['ret']);
			$bd = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'pl'.date('Ymd').DIRECTORY_SEPARATOR;
			mkdir($bd);
			mkdir($bd.'_PriceList');
			foreach($_POST['p'] as $id){
				$p = Product::model()->findByPk($id);
				$pn = str_replace(' ', '_', trim($p->mdata['retname'][$_POST['ret']]));
				$fn = $pn.'.pdf';
				oPDF::renderPDF('price', array('model'=>$p, 'ret'=>$_POST['ret']), 2, $bd.'_PriceList'.DIRECTORY_SEPARATOR.$fn);
				mkdir($bd.$pn);
				
				//add photos line drawing etc.
				$rs = FileRepo::model()->findAll('type IN (20,30,40) AND fid = :id', array(':id' => $id));
				foreach($rs as $r){
					$rname = $r->name;
					if($r->type == 20){
						$rname = $pn.'_LineDrawing.pdf';
					}
					if($r->type == 40){
						$rname = $pn.'_QA_Certificate.pdf';
					}
					copy($r->getFile(), $bd.$pn.DIRECTORY_SEPARATOR.$rname);
				}
			}
			$zip = new ZipArchive;
			$zf = $bd.'pl'.date('Ymd').'.zip';
			$zip->open($zf, ZipArchive::CREATE);
			addFolderToZip($bd, $zip, str_replace(' ', '_', $org->shortName(2)).'_Pack');
			$zip->close();
			header("Cache-Control: maxage=1");
			header("Content-Description: File Transfer");
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"".str_replace(' ', '_', $org->shortName(2))."_Pack_".date('Ymd').".zip\"");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($zf));
			readfile($zf);
			unlinkRecursive($bd);
			exit(0);
		}
		if(empty($_GET['ret'])){
			$this->render('products');
		}else{
			$this->render('products_list', array('ret' => $_GET['ret']));
		}
	}

	public function actionSales(){
		if(!empty($_POST)){
			Yii::app()->end();
		}
		$this->render('sales');
		
	}
	
	public function actionOrg(){
		if(!empty($_POST)){
			$xls = new oExcel;
			$i = 1;
			$xls->addRow($i++, array('ID', 'Type', 'Code', 'Name', 'ABN', 'Address', 'Suburb', 'State', 'Post Code', 'Country', 'Phone', 'Fax', 'Description', 'Sales Manager', 'Active', ''));
			$p = new Org('search');
			$p->type = $_POST['cate'];
			$rs = $p->searchAll();
			foreach($rs as $r){
				$data = array($r->id, $r->getType(), $r->code, $r->name, $r->abn, $r->address, $r->suburb, $r->state, $r->postcode, $r->country, $r->phone, $r->fax, $r->desc, empty($r->sales)? '':$r->sales->getName(), $r->getStatus());
				$xls->addRow($i++, $data);
			}
			$xls->output('PM_Org_Export.xlsx');
		}
		$this->render('org');
	}
}