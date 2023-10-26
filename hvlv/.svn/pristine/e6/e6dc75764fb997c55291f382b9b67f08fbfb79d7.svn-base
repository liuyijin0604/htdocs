<?php
class oWord {
	public $wrd, $actSec;
	public $err = array();

	function __construct() {
		spl_autoload_unregister(array('YiiBase','autoload'));
		include_once('PHPWord/PHPWord.php');
		include_once('PHPWord/PHPWord/IOFactory.php');
		spl_autoload_register(array('YiiBase','autoload'));
		//$cacheMethod = PHPWord_CachedObjectStorageFactory::cache_to_phpTemp;
		//$cacheSettings = array( 'memoryCacheSize' => '128MB');
		$this->wrd = new PHPWord();
		//$this->defaultStyle();
	}

	public function createSection(){
		return $this->wrd->createSection();
	}

	public function getActiveSection(){
		return empty($this->actSec)? $this->wrd->_sectionCollection[sizeof($this->wrd->_sectionCollection) - 1] : $this->actSec;
	}

	public function addTable($data, $sec = null){
		$sec = empty($sec)? $this->getActiveSection() : $sec;
		$table = $this->cur_sec->addTable();

		foreach($data as $ri => $row){
			$table->addRow();
			foreach($row as $ci => $cell){
				$c = $table->addCell($cell['width']);
				if(empty($cell['style'])) $cell['style'] = [];
				if(!empty($cell['txt'])) $c->addText($cell['txt'], $cell['style']);
				if(!empty($cell['img'])) $c->addImage($cell['img'], $cell['style']);
			}
		}
	}

	public function output($filename, $download = true) {
		$objWriter = PHPWord_IOFactory::createWriter($this->wrd, 'Word2007');
		if($download){
			header("Cache-Control: maxage=1");
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			header("Content-Disposition: attachment;filename=".urldecode(basename($filename)));
			header("Content-Transfer-Encoding: binary ");
			$objWriter->save('php://output');
			Yii::app()->end();
		}elseif(!empty($filename)){
			$objWriter->save($filename);
		}else{
			ob_start();
			$objWriter->save('php://output');
			$data = ob_get_clean();
			return $data;
		}
	}

} // end of class
