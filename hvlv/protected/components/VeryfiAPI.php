<?php

class VeryfiAPI{

	public $debug, $result, $err;

	private $secret = 'MVSyebiF4xH8VxW68wvEdZeEisD4wDUPosaap7xinwZOfXKNI9dAxxkrU2irKeiuFSUCfA0aUeLdmxmWJn3t2KfDCE2M0azX3YINCcWZ47PSiRLoI9trZVDO5bTf4huo';

	private $url = 'https://api.veryfi.com/api/v7/partner/documents/';

	public function __construct($debug = false){
		$this->debug = $debug;
	}

	public function ocr($file, $compact = true, $file_name = ''){
		$r = $this->request('POST', ['file_name' => empty($file_name)? basename($file) : $file_name, 'file_data' => 'application/pdf;base64,'.base64_encode(file_get_contents($file))]);
		return $compact? $this->compactInfo($r) : $r;
	}

	public function compactInfo($d){
		if(empty($d->invoice_number)) return $d;
		$inv = [
			'no' => $d->invoice_number,
			'date' => substr($d->date, 0, 10),
			'due' => substr($d->due_date, 0, 10),
			'currency' => $d->currency_code,
			'discount' => $d->discount,
			'total' => $d->total,
			'subtotal' => $d->subtotal,
			'tax' => $d->tax,
			'term' => $d->payment_terms,
			'vendor' => [
				'name' => $d->vendor->raw_name,
				'address' => $d->vendor->address,
			],
			'lines' => [],
		];
		foreach($d->line_items as $line){
			$inv['lines'][] = [
				'desc' => $line->description,
				'price' => $line->price,
				'qty' => $line->quantity,
				'total' => $line->total,
				'tax' => $line->tax,
			];
		}

		return json_decode(json_encode($inv));
	}

	public function request($method = 'POST', $data = [])
	{
		$this->result = false;
		$c = new curl($this->url);
		$c->setopt(CURLOPT_CUSTOMREQUEST, strtoupper($method));
		$c->setopt(CURLOPT_RETURNTRANSFER, true);
		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
		$c->setopt(CURLOPT_TIMEOUT, 600);
		$hdr = ['CLIENT-ID: vrfovrzMVnLzWaIVp9zZ9Mw7xZyvjl8dygofLpW', 
'AUTHORIZATION: apikey frank11:4bfb9c35cb41e183d119364b922c050c', 'Content-Type: application/json', 'Accept: application/json'];
		if (!empty($data)) {
			$json = json_encode($data);
			$c->setopt(CURLOPT_POSTFIELDS, $json);
			$hdr[] = 'Content-Length: ' . strlen($json);
			$c->setopt(CURLOPT_HTTPHEADER, $hdr);
		}

		if (!$c->exec()) {
			throw new Exception('cUrl Error: '.$c->err);
		}

		if ($this->debug) {
			if(isset($data['file_data'])) $data['file_data'] = '...';
			$this->log2file('Request: '.json_encode($data)."\nResponse: ".$c->result."\n");
		}
		$this->result = json_decode($c->result);

		if (!empty($this->result->status) && $this->result->status == 'fail') {
			$this->err = $this->result->message;
			return false;
		}
		return $this->result;
	}
	
	protected function log2file($m)
	{
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR  . 'veryfi_api.log';
		return file_put_contents($lf, 'Time: '.date('Y-m-d H:i:s')."\n".$m."\n", FILE_APPEND);
	}
}