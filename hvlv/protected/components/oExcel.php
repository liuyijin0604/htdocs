<?php
class oExcel
{

	public $xls, $format;
	public $err = array();

	function __construct($format = 'Excel2007')
	{
		spl_autoload_unregister(array('YiiBase', 'autoload'));
		require 'E:\xampp\htdocs\hvlv\vendor\autoload.php';
		spl_autoload_register(array('YiiBase', 'autoload'));
		$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
		$cacheSettings = array('memoryCacheSize' => '128MB');
		$this->xls = new PHPExcel();
		$this->format = $format;
		//$this->defaultStyle();
	}

	public static function getAllData($f, $fn = false, $allsheets = false)
	{
		if (!is_file($f)) {
			return false;
		}

		$xls = new self;
		if (!$xls->supported(empty($fn) ? $f : $fn)) {
			return false;
		}

		$xls->load($f);
		if (!$allsheets) {
			return $xls->getAll();
		}

		$dd = [];
		$sheetsArray = $xls->xls->getAllSheets();
		foreach ($sheetsArray as $n => $sheet) {
			$xls->goSheet($n);
			$dd = array_merge($dd, $xls->getAll());
		}
		return $dd;
	}

	public static function getSheetsFileData($f, $fn = false, $allsheets = false)
	{
		if (!is_file($f)) {
			return false;
		}

		$xls = new self;
		if (!$xls->supported(empty($fn) ? $f : $fn)) {
			return false;
		}

		$xls->load($f);
		if (!$allsheets) {
			return $xls->getAll();
		}

		$data = [];
		$i = 0;
		$sheetsArray = $xls->xls->getAllSheets();
		foreach ($sheetsArray as $n => $sheet) {
			$xls->goSheet($n);
			$data[$i] = $xls->getAll();
			$i++;
		}
		return $data;
	}

	private function defaultStyle()
	{
		$this->xls->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial');
		$this->xls->getActiveSheet()->getDefaultStyle()->getFont()->setSize(9);
		//$this->xls->getActiveSheet()->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
	}

	public function getExcel()
	{
		return $this->xls;
	}

	public function getActiveSheet()
	{
		return $this->xls->getActiveSheet();
	}

	public function createSheet($n = '')
	{
		$s = $this->xls->createSheet();
		if (!empty($n)) {
			$s->setTitle($n);
		}

		return $s;
	}

	public function setTitle($t)
	{
		$sheet = $this->xls->getActiveSheet();
		$sheet->setTitle($t);
	}

	public function setFont($range, $style = array())
	{
		return $this->getActiveSheet()->getStyle($range)->applyFromArray(array('font' => $style));
	}

	public function setBG($range, $color)
	{
		$this->getActiveSheet()->getStyle($range)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => $color)));
	}

	public function setNumber($range)
	{
		$this->getActiveSheet()->getStyle($range)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
	}

	public function setCurrency($range, $f = '$#,##0.00')
	{
		$this->getActiveSheet()->getStyle($range)->getNumberFormat()->setFormatCode($f);
	}

	public function setPercentage($range, $f = '0.00%;[Red]-0.00%')
	{
		$this->getActiveSheet()->getStyle($range)->getNumberFormat()->setFormatCode($f);
	}

	public function setColWidth($w)
	{
		foreach ($w as $i => $cw) {
			$this->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setWidth($cw);
		}
	}
	public function setColWidthString($w)
	{
		foreach ($w as $i => $cw) {
			$this->getActiveSheet()->getColumnDimension($i)->setWidth($cw);
		}
	}

	public function setLineHeight($f, $t, $h)
	{
		for ($i = $f; $i <= $t; $i++) {
			$this->getActiveSheet()->getRowDimension($i)->setRowHeight($h);
		}
	}

	public function setTextWrap($range)
	{
		$this->getActiveSheet()->getStyle($range)->getAlignment()->setWrapText(true);
	}

	public function centerAlignment($range)
	{
		return $this->getActiveSheet()->getStyle($range)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	}

	public function leftAlignment($range)
	{
		return $this->getActiveSheet()->getStyle($range)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	}

	public function verticalAlignment($range)
	{
		return $this->getActiveSheet()->getStyle($range)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	}

	public function wrapAlignment($range)
	{
		return $this->getActiveSheet()->getStyle($range)->getAlignment()->setWrapText(true);
	}

	public function mergeCells($range)
	{
		$this->getActiveSheet()->mergeCells($range);
	}

	public function setUrl($col, $row, $url)
	{
		$this->getActiveSheet()->getCellByColumnAndRow($col, $row)->getHyperLink()->setUrl($url);
	}

	public function supported($filename)
	{
		$pi = pathinfo($filename);
		if (strtolower($pi['extension']) == 'xls') {
			$this->format = 'Excel5';
		} else if (strtolower($pi['extension']) == 'xlsx') {
			$this->format = 'Excel2007';
		} else if (strtolower($pi['extension']) == 'csv') {
			$this->format = 'CSV';
		} else {
			$this->err[] = 'File format not supported';
			$this->format = false;
		}
		return $this->format;
	}

	public function getError($show = false)
	{
		if ($show) {
			echo '<p style="color:#c00">' . implode('<br />', $this->err) . '</p>';
		} else {
			return $this->err;
		}
	}

	public function load($filename, $ro = true)
	{
		$xlsReader = PHPExcel_IOFactory::createReader($this->format);
		if ($ro) {
			$xlsReader->setReadDataOnly(true);
		}

		$this->xls = $xlsReader->load($filename);
		if (!$ro) {
			$this->defaultStyle();
		}

		return true;
	}

	public function output($filename, $format = null, $download = true)
	{
		$xlsWriter = PHPExcel_IOFactory::createWriter($this->xls, empty($format) ? $this->format : $format);
		if ($download) {
			header("Cache-Control: maxage=1");
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			header("Content-Disposition: attachment;filename=\"" . urldecode(basename($filename)) . '"');
			header("Content-Transfer-Encoding: binary");
			$xlsWriter->save('php://output');
			Yii::app()->end();
		} else if (!empty($filename)) {
			$xlsWriter->save($filename);
		} else {
			ob_start();
			$xlsWriter->save('php://output');
			$data = ob_get_clean();
			return $data;
		}
	}

	public function goSheet($i)
	{
		$this->xls->setActiveSheetIndex($i);
		return $this->getActiveSheet();
	}

	public function setValidator($cell, $sFormula, $allowBlank = true, $error = [], $prompt = [])
	{
		$sheet = $this->xls->getActiveSheet();
		$oVal = $sheet->getDataValidation();
		$oVal->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
		$oVal->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
		$oVal->setAllowBlank($allowBlank);
		$oVal->setShowDropDown(true);
		if (!empty($error)) {
			$oVal->setShowErrorMessage(true);
			$oVal->setErrorTitle($error['title']);
			$oVal->setError($error['msg']);
		}
		if (!empty($prompt)) {
			$oVal->setShowInputMessage(true);
			$oVal->setPromptTitle($prompt['title']);
			$oVal->setPrompt($prompt['msg']);
		}
		$oVal->setFormula1($sFormula);
		$sheet->setDataValidation($cell, $oVal);
	}

	public function getValidator($cell)
	{
		$sheet = $this->xls->getActiveSheet();
		return $sheet->getCell($cell)->getDataValidation();
	}

	public function addRow($rn, $data = array(), $sheet = null)
	{
		if (empty($sheet)) {
			$sheet = $this->xls->getActiveSheet();
		}
		$i = 0;
		foreach ($data as $cell) {
			$sheet->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($i) . $rn, $cell, PHPExcel_Cell_DataType::TYPE_STRING);
			$i++;
		}

	}

	public function setCell($cell, $val, $sheet = null)
	{
		if (empty($sheet)) {
			$sheet = $this->xls->getActiveSheet();
		}
		$sheet->setCellValue($cell, $val);
	}

	public function getRow($r, $sheet = null)
	{
		if (empty($sheet)) {
			$sheet = $this->xls->getActiveSheet();
		}
		$hc = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());
		$row = array();
		for ($i = 1; $i <= $hc; $i++) {
			$row[$i] = trim($sheet->getCellByColumnAndRow($i - 1, $r)->getCalculatedValue());
		}

		return $row;
	}

	public function getCell($c, $sheet = null)
	{
		if (empty($sheet)) {
			$sheet = $this->xls->getActiveSheet();
		} else {
			$sheet = $this->goSheet($sheet);
		}
		return trim($sheet->getCell($c)->getCalculatedValue());
	}

	public function getAll($sheet = null)
	{
		if (empty($sheet)) {
			$sheet = $this->xls->getActiveSheet();
		}
		$hr = $sheet->getHighestRow();
		$data = array();
		for ($i = 1; $i <= $hr; $i++) {
			$data[$i] = $this->getRow($i, $sheet);
		}

		return $data;
	}

	public static function toDate($n)
	{
		if (empty($n)) {
			return '';
		} else if (strpos($n, '/') > 0) { //string date (AU format)
			$d = explode('/', $n);
			return $d[2] . '-' . $d[1] . '-' . $d[0];
		} else if (preg_match('/\d{2}-\d{2}-\d{4}/', $n)) {
			$d = explode('-', $n);
			return $d[2] . '-' . $d[1] . '-' . $d[0];
		} else if (preg_match('/\d{4}-\d{2}-\d{2}/', $n)) {
			return $n;
		} else if (preg_match('/\d{2}-\w*-\d{2}/', $n)) {
			return date('Y-m-d', strtotime($n));
		} else if (is_float(floatval($n))) {
			$d = intval($n);
			$s = round(86400 * ($n - $d));
			return date('Y-m-d H:i:s', strtotime('1899-12-30 +' . $d . ' days +' . $s . ' seconds'));
		} else if (is_numeric($n)) { //excel date field
			return date('Y-m-d', strtotime('1899-12-30 +' . $n . ' days'));
		} else {
			return $n;
		}
	}

	public static function getFileData($fileFullName)
	{
		if (!preg_match('/xml|csv|xls|xlsx/i', $fileFullName)) 
		{
			return [];
		}
		if (preg_match('/csv/i', $fileFullName)) 
		{
			$xls = new oExcel('CSV');
			$xls->load($fileFullName);
		} elseif(preg_match('/xml/i', $fileFullName))
		{
			$xml = simplexml_load_file($fileFullName);
			$invoiceCount = count($xml->Details);
			if($invoiceCount<=0)
			{
				$data = [];
			}else
			{
				$data = [$xml->Details];
				return $data;
			}
		}else {
			$xls = new oExcel;
			$format = $xls->supported($fileFullName);
			$xls = new oExcel($format);
			$xls->load($fileFullName);
		}
		$data = [];
		$i = 0;
		$sheetsArray = $xls->xls->getAllSheets();
		foreach ($sheetsArray as $n => $sheet) {
			$xls->goSheet($n);
			$data[$i] = $xls->getAll();
			$i++;
		}
		return $data;
	}

} // end of class
