<?php

include_once(dirname(__FILE__).'/ediUserMsg.php');

class ediSEACR extends ediUserMsg {

	private $fcode;

	function __construct() {
		parent::__construct();
		$this->fclist = array('O'=>'9', 'C'=>'4', 'W'=>'50');
		$this->tplist = array('O'=>'Original', 'C'=>'Change', 'W'=>'Withdraw');
		$this->Name = 'SEACR';
	}

	private function createUNH() {
		$this->msg[] = "UNH+1+CUSCAR:D:99B:UN";
	}

	private function createBGM() {
		$this->msg[] = "BGM+933:::".$this->Name."+".$this->job['jbNo'].":".$this->ver."+".$this->fcode;
	}


	private function createBillRFF() {

		// house bill
		$hbl = $this->strEscape($this->job['jbHouseNo']);
		if (!empty($hbl)) $this->msg[] = "RFF+BH:$hbl";

		// parent bill
		$pbl = $this->strEscape($this->job['jbParentNo']);
		if (!empty($pbl)) $this->msg[] = "RFF+BM:$pbl";

		// ocean bill
		$mbl = $this->strEscape($this->job['jbMasterNo']);
		if (!empty($mbl)) $this->msg[] = "RFF+MB:$mbl";
		else $this->err[] = 'Missing Master Bill!';

		// payment method
		$pm = ('C' == substr(trim($this->job['jbPayType']), 0, 1))? 'CC' : 'PO';
		$this->msg[] = "RFF+PQ:$pm";
	}


	private function createPartyNAD() {

		// consignee
		$cn = $this->dbcp->read($this->job['jbCnee']);
		if (!empty($cn)) {
			$cnname = substr($this->strEscape($cn['cpName']), 0, 35);
			$cnaddr = empty($cn['cpAddress'])? '***NOT ON FILE***' : $this->smartSplit($this->strEscape($cn['cpAddress']), 35, 3);
			$this->msg[] = "NAD+CN++$cnname::$cnaddr";
		}

		// shipper
		$cz = $this->dbcp->read($this->job['jbCnor']);
		if (!empty($cz)) {
			$czname = substr($this->strEscape($cz['cpName']), 0, 35);
			$czaddr = empty($cz['cpAddress'])? '***NOT ON FILE***' : $this->smartSplit($this->strEscape($cz['cpAddress']), 35, 3);
			$this->msg[] = "NAD+CZ++$czname::$czaddr";
		}

		// responsible party (DS logistics ABN)
		$this->msg[] = "NAD+VW+".$this->RespABN."::95";

		// transport principal's agent
		$tpabn = $this->dbcp->getABN($this->job['jbSCO']);
		if (!empty($tpabn)) $this->msg[] = "NAD+AH+$tpabn::95";
		else $this->err[] = 'Missing Principal ABN!';
	}

	private function createTDT() {
		$llo = $this->strEscape($this->job['vslLloyds']);
		if (empty($llo)) $this->err[] = 'Missing Vessel Lloyds!';
		$voy = $this->strEscape($this->job['voyNo']);
		if (empty($voy)) $this->err[] = 'Missing Voyage number!';
		$this->msg[] = "TDT+20+$voy++11++++$llo::11";
	}

	private function createLOC() {

		// final destination
		$fnd = strtoupper($this->job['jbFND']);
		if (!empty($fnd)) $this->msg[] = "LOC+8+$fnd::6";
		else $this->err[] = 'Missing Final Destination!';

		// port of discharge
		$pod = strtoupper($this->job['jbPOD']);
		if (!empty($pod))$this->msg[] = "LOC+12+$pod::6";
		else $this->err[] = 'Missing Port of Discharge!';

		// port of loading, place of bill issue, Country of origin
		$pol = strtoupper($this->job['jbPOL']);
		if (!empty($pol)) {
			$this->msg[] = "LOC+76+$pol::6";
			$this->msg[] = "LOC+73+$pol::6";
			$this->msg[] = "LOC+27+".substr($pol, 0, 2)."::5";
		} else $this->err[] = 'Missing Port of Loading!';
	}

	private function createGIS() {
		if (!empty($this->job['jbFFInd']))
			$this->msg[] = "GIS+FFO:109:95";
	}

	private function createCntrLine($c) {

		$this->msg[] = "CNI++:::I";

		// cntr no.
		$cntrno = strtoupper(trim($c['ctnNo']));
		if (!empty($cntrno)) $this->msg[] = "RFF+AAQ:".$cntrno;
		else $this->err[] = 'Missing Container Number!';
		$this->msg[] = "GID+1";

		// cntr size
		if (!empty($c['ctpEdiSize'])) $this->msg[] = "RFF+ACC:".$c['ctpEdiSize'];
		else $this->err[] = 'Missing Container Size!';
		$this->msg[] = "GID+1";

		// cntr seal
		$seal = strtoupper(trim($c['ctnSeal']));
		if (!empty($seal)) { $this->msg[] = "RFF+SN:$seal"; $this->msg[] = "GID+1"; }

		// general indicator
		if (!empty($c['ctnSOC']))     { $this->msg[] = "RFF+ZZZ:1"; $this->msg[] = "GIS+SOC:109:95"; $this->msg[] = "GID+1"; }
		if (!empty($c['ctnHAZ']))     { $this->msg[] = "RFF+ZZZ:1"; $this->msg[] = "GIS+HAZ:109:95"; $this->msg[] = "GID+1"; }
		if (!empty($c['ctnFum']))     { $this->msg[] = "RFF+ZZZ:1"; $this->msg[] = "GIS+FUM:109:95"; $this->msg[] = "GID+1"; }
		if (!empty($c['ctnPsnal']))   { $this->msg[] = "RFF+ZZZ:1"; $this->msg[] = "GIS+PER:109:95"; $this->msg[] = "GID+1"; }
		if (!empty($c['ctnTimb']))    { $this->msg[] = "RFF+ZZZ:1"; $this->msg[] = "GIS+TMB:109:95"; $this->msg[] = "GID+1"; }
		if (!empty($c['ctnPerish']))  { $this->msg[] = "RFF+ZZZ:1"; $this->msg[] = "GIS+PSH:109:95"; $this->msg[] = "GID+1"; }
		if (!empty($c['ctnSlfAssd'])) { $this->msg[] = "RFF+ZZZ:1"; $this->msg[] = "GIS+SAC:109:95"; $this->msg[] = "GID+1"; }

		// number of packages
		if (!empty($c['ctnPkg'])) $this->msg[] = "PAC+".$c['ctnPkg'];
		else $this->err[] = 'Missing Number of Packages!';

		// cargo load type
		if (!empty($this->job['jbLoadType'])) $this->msg[] = "PAC+++".$this->job['jbLoadType'].":67:95";
		else $this->err[] = 'Missing Cargo Load Type!';

		// container type
		if (!empty($c['ctpEdiType'])) $this->msg[] = "PAC+++".$c['ctpEdiType'].":121:95";
		else $this->err[] = 'Missing Container Type!';

		// package type
		if (!empty($c['ctnPkgTp'])) $this->msg[] = "PAC+++".$c['ctnPkgTp'].":185:95";
		else $this->err[] = 'Missing Package Type!';

		// cargo description
		$cargo = $this->smartSplit($this->strEscape($c['ctnCargo']), 512, 5);
		if (!empty($cargo)) $this->msg[] = "FTX+AAA+++$cargo";
		else $this->err[] = 'Missing Cargo Description!';

		// weight
		if (!empty($c['ctnNwt'])) {
			$this->msg[] = "MEA+AAE+AAL+KG:".$c['ctnNwt'];
			$this->msg[] = "MEA+AAE+G+KG:".$c['ctnNwt'];
		} else $this->err[] = 'Missing Cargo Weight!';

		// volume
		if (!empty($c['ctnVol'])) $this->msg[] = "MEA+AAE+ABJ+CU:".$c['ctnVol'];

		// marks
		$marks = $this->smartSplit($this->strEscape($c['ctnMark']), 35, 10);
		if (!empty($marks)) $this->msg[] = "PCI+28+$marks";

	}

	private function createUNT() {
		$num = count($this->msg) + 1;
		$this->msg[] = "UNT+$num+1";
	}

	public function prepareMessage($jid, $fc='O') {
		$this->msg = array();
		$this->err = array();
		$this->job = $this->dbjb->read($jid);
		if (empty($this->job)) return false;

		$this->fcode = empty($this->fclist[$fc])? $this->fclist['O'] : $this->fclist[$fc];
		$this->Type = empty($this->tplist[$fc])? $this->tplist['O'] : $this->tplist[$fc];

		// create record in db and get version
		$this->prepare($jid);

		$this->createUNH();
		$this->createBGM();
		$this->createBillRFF();
		$this->createPartyNAD();
		$this->createTDT();
		$this->createLOC();
		$this->createGIS();

		foreach ($this->dbct->getEdiContainer($jid) as $c) {
			$this->createCntrLine($c);
		}
		$this->createUNT();

		return $this->formatMessage();
	}

} // end of class
?>