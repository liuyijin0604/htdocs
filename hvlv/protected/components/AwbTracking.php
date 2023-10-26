<?php
class AwbTracking
{
	private $debug = false;
	private static $config = [
		/*
		'000' => [
			'url' => '',
			'post' => true,
			'awb' => ['field' => '', 'prefix_field' => '', 'no_field' => '', 'two_parts' => false],
			'vvc' => ['field' => '', 'url' => '', 'ttl' => 0],
			'params' => [],
			'copy' => 0,
		],
		*/
		'023' => [
			'url' => 'https://www.fedex.com/apps/fedextrack/',
			'post' => false,
			'awb' => ['field' => 'tracknumbers'],
		],
		'081' => [
			'url' => 'https://freight.qantas.com/online-tracking.html',
			'post' => false,
			'awb' => ['field' => 'airWaybills', 'two_parts' => true],
			// 'copy' => 2,
		],
		'086' => [
			'url' => 'https://www.airnewzealand.co.nz/feeds/cargo-status',
			'post' => false,
			'awb' => ['field' => 'awb'],
			'feed' => true,
		],
		'112' => [
			'url' => 'http://www.eal-ceair.com/service/track.html',
			'post' => true,
			'awb' => ['field' => 'awbNos', 'prefix_field' => '', 'no_field' => '', 'two_parts' => false],
			'vvc' => ['field' => 'verCode', 'url' => 'http://www.eal-ceair.com/authImg?oper=track', 'ttl' => 0],
			'params' => ['tabCode' => 'b2a_cargo_trace'],
		],
		'126' => [
			'url' => 'https://icms.garuda-indonesia.com/HtmlFiles/AWBTracking/AWBTracking.html',
			'post' => false,
			'awb' => ['prefix_field' => 'CarrierCode', 'no_field' => 'AWBNo'],
			'params' => ['BasedOn' => '0'],
		],
		'131' => [
			'url' => 'https://ww4.cargo.jal.co.jp/CargoWebTracing/en/intlTracingResult.do',
			'post' => true,
			'awb' => ['prefix_field' => 'awbNoPrefix1', 'no_field' => 'awbNoSuffix1'],
			'params' => ['searchType' => '00'],
		],
		'157' => [
			'url' => 'https://www.qrcargo.com/trackshipment',
			'post' => false,
			'awb' => ['prefix_field' => 'docPrefix', 'no_field' => 'docNumber'],
			'params' => ['docType' => 'MAWB'],
		],
		'160' => [
			'url' => 'https://www.cathaypacificcargo.com/en-us/manageyourshipment/trackyourshipment.aspx',
			'post' => false,
			'awb' => ['field' => 'SingleAWBNo', 'two_parts' => true],
		],
		'203' => [
			'url' => 'https://cebu.smartkargo.com/FrmAWBTracking.aspx',
			'post' => false,
			'awb' => ['prefix_field' => 'AWBPrefix', 'no_field' => 'AWBNo'],
		],
		'205' => [
			'url' => 'https://cargo.ana.co.jp/anaicoportal/portal/trackshipments',
			'post' => false,
			'awb' => ['field' => 'trkTxnValue', 'two_parts' => true],
		],
		'217' => [
			'url' => 'https://chorus.thaicargo.com/skychain/app',
			'post' => false,
			'awb' => ['prefix_field' => 'awb_pre', 'no_field' => 'awb_no'],
			'params' => ['PID' => 'WEB01-10', 'doc_typ' => 'AWB'],
		],
		'232' => [
			'url' => 'http://www.maskargo.com/online_awb_info/index.php',
			'post' => true,
			'awb' => ['prefix_field' => 'code', 'no_field' => 'awb'],
		],
		'260' => [
			'url' => 'https://freight.qantas.com/online-tracking.html',
			'post' => false,
			'awb' => ['field' => 'airWaybills', 'two_parts' => true],
		],
		'297' => [
			'url' => 'https://cargo.china-airlines.com/CCNetv2/content/manage/ShipmentTracking.aspx',
			'post' => false,
			'awb' => ['prefix_field' => 'AwbPfx', 'no_field' => 'AwbNum'],
			'params' => ['checkcode' => '*7*upHGj'],
		],
		'403' => [
			'url' => 'http://www.polaraircargo.com/TrackAndTraceUI/WebForm1.aspx',
			'post' => false,
			'awb' => ['prefix_field' => 'pe', 'no_field' => 'se'],
			'params' => ['loc' => 'en_US', 'track.x' => '19', 'track.y' => '5'],
		],
		'406' => [
			'url' => 'https://www.ups.com/actrack/track/submit',
			'post' => true,
			'awb' => ['field' => 'awbNum'],
			'params' => ['loc' => 'en_US', 'track.x' => '19', 'track.y' => '5'],
		],
		'618' => [
			'url' => 'https://wheremy.com/awb-air-cargo-tracking/show-tracking-info/',
			'post' => false,
			'awb' => ['field' => 'APPEND_URI'],
		// 	'url' => 'http://www.siacargo.com/ccn/ShipmentTrack.aspx',
		// 	'copy' => 2,
		],
		'731' => [
			'url' => 'https://ecargo.xiamenair.com/Pindex.aspx',
			'post' => false,
			'copy' => 2,
		],
		'738' => [
			'url' => 'https://www.cargoupdate.com/tracktrace/default.aspx',
			'awb' => ['field' => 'awbs', 'two_parts' => true],
			'params' => ['carrier' => 'VN'],
			'post' => false,
		],
		'784' => [
			'url' => 'http://tang.cs-air.com/EN/WebFace/Tang.WebFace.Cargo/AgentAwbBrower.aspx',
			'post' => false,
			'awb' => ['prefix_field' => 'AwbPrefix', 'no_field' => 'AwbNo'],
		],
		'807' => [
			'url' => 'https://airasia.smartkargo.com/FrmAWBTracking.aspx',
			'post' => false,
			'awb' => ['prefix_field' => 'AWBPrefix', 'no_field' => 'AWBNo'],
		],
		'826' => [
			'url' => 'https://www.hnacargo.com/Portal2/AwbSearch.aspx',
			'post' => true,
			'awb' => ['field' => 'hdAwbCode', 'two_parts' => true],
			'vvc' => ['field' => 'hdVerifyCode', 'url' => 'https://www.hnacargo.com/VerifyCode.aspx', 'ttl' => 0],
		],
		'828'=> [
			'url' => 'https://www.hkaircargo.com/track-your-shipment/',
			'post' => false,
			'awb' => ['prefix_field' => 'Code', 'no_field' => 'WaybillNo'],
		],
		'843' => [
			'url' => 'https://airasia.smartkargo.com/FrmAWBTracking.aspx',
			'post' => false,
			'awb' => ['prefix_field' => 'AWBPrefix', 'no_field' => 'AWBNo'],
		],
		'851' => [
			'url' => 'https://www.hkaircargo.com/track-your-shipment/',
			'post' => false,
			'awb' => ['prefix_field' => 'Code', 'no_field' => 'WaybillNo'],
		],
		'876' => [
			'url' => 'http://cargo.sichuanair.com:8000/EN/WebFace/Tang.WebFace.Cargo/AgentAwbBrower.aspx',
			'post' => false,
			'awb' => ['prefix_field' => 'AWBPrefix', 'no_field' => 'AwbNo'],
			'params' => ['menuID' => '1', 'lan' => 'en-us'],
		],
		'880' => [
			'url' => 'https://www.hnacargo.com/Portal2/AwbSearch.aspx',
			'post' => true,
			'awb' => ['field' => 'hdAwbCode', 'two_parts' => true],
			'vvc' => ['field' => 'hdVerifyCode', 'url' => 'https://www.hnacargo.com/VerifyCode.aspx', 'ttl' => 0],
		],
		'932' => [
			'url' => 'https://cargo.virgin-atlantic.com/content/cargo/global/en/track/track-your-cargo.html',
			'post' => false,
			'awb' => ['prefix_field' => 'prefix', 'no_field' => 'number'],
			'params' => ['track' => 'go'],
		],
		'999' => [
			'url' => 'http://www.airchinacargo.com/en/search_order.php',
			'post' => true,
			'awb' => ['prefix_field' => 'orders10', 'no_field' => 'orders0'],
			'vvc' => false,
			'params' => ['orders9' => '78oi', 'section' => '0-0001-0003-0081'],
		],
	];

	public function __construct($debug = false)
	{
		$this->debug = $debug;
	}

	public static function prepAwb($awb, $ra = true)
	{
		$awb = preg_replace('/[^\d]+/', '', $awb);
		$valid = strlen($awb) == 11 && (substr($awb, 3, -1) % 7) == substr($awb, -1);
		return $ra? [$awb, substr($awb, 0, 3), substr($awb, 3), $valid]: $awb;
	}

	public static function canTrack($awb)
	{
		$awb = self::prepAwb($awb);
		return $awb[3] && isset(self::$config[$awb[1]]);
	}

	public static function trackForm($awb)
	{
		$awb = self::prepAwb($awb);
		$config = self::$config[$awb[1]];
		if(!empty($config['feed'])) return self::showFeed($awb, $config);

		$f = '';
		if (!empty($config['preload'])) {
			$f .= '<iframe src="'.$config['preload'].'" style="border:none; width:1px; height:1px;"></iframe>';
		}
		$f .= '<form class="awb-tracking-form ifrm-form" action="'.$config['url'].(!empty($config['awb']['field']) && 'APPEND_URI' == $config['awb']['field']? $awb[0] : '').'" method="'.($config['post']? 'post' : 'get').'" target="_blank">';
		if (!empty($config['awb'])) {
			if (empty($config['awb']['field'])) {
				$f .= '<input type="hidden" name="'.$config['awb']['prefix_field'].'" value="'.$awb[1].'" /><input type="hidden" name="'.$config['awb']['no_field'].'" value="'.$awb[2].'" />';
			} elseif('APPEND_URI' != $config['awb']['field']) {
				$f .= '<input type="hidden" name="'.$config['awb']['field'].'" value="'.(empty($config['awb']['two_parts'])? $awb[0] : $awb[1].'-'.$awb[2]).'" />';
			}
		}
		if (!empty($config['copy'])) {
			$f .= '<input class="copy" type="text" name="" value="'.($config['copy'] == 1? $awb[0] : $awb[2]).'" size="10" /><br />';
		}
		if (!empty($config['vvc'])) {
			$f .= '<div><label>Verification</label><input class="vvc_txt" type="text" name="'.$config['vvc']['field'].'" size="10" style="font-size:1.5em;vertical-align:baseline" /> <img class="vvc" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAAAZBAMAAAA1cJZ4AAAAA3NCSVQICAjb4U/gAAAALVBMVEUsMjPd3d2Eh4hHTE3BwsJpbW6jpaUxNzjMzMx1eHlWW1yTlpays7Q4PT7X2NiOERoyAAAACXBIWXMAAAsSAAALEgHS3X78AAAAHHRFWHRTb2Z0d2FyZQBBZG9iZSBGaXJld29ya3MgQ1M26LyyjAAAABZ0RVh0Q3JlYXRpb24gVGltZQAwOC8zMS8xOMXuDcEAAAB5SURBVDiNYxAkGTCMasEBbOeQrEWBwZAULd5A7MBOki28QCxgoEiCFmkWICEpzEiCFuFCECnGQooWQxApQmMtUmBfSHOQoEWMDUSKF5KgRfBCI5AISCRFSwPQG6Ks2OVwaJG4cEmVYSJJWgTFFhvPwyE14PllYLUAAAObWqQjXYMKAAAAAElFTkSuQmCC" data-vu="'.$config['vvc']['url'].'" data-pfx="'.$awb[1].'" data-ttl="'.$config['vvc']['ttl'].'" alt="vvc ↺" style="display:inline-block; vertical-align:middle;" /></div><br />';
		}
		if (!empty($config['params'])) {
			foreach ($config['params'] as $k => $v) {
				$f .= '<input type="hidden" name="'.$k.'" value="'.$v.'" />';
			}
		}

		if (!empty($config['copy']) || !empty($config['vvc'])){
			$f .='<input type="submit" value="'.(empty($config['copy'])? '':'Copy &amp; ').'Track" />';
		}
		return $f.'</form>';
	}

	public static function showFeed($awb, $config){
		$data = empty($config['params'])? [] : $config['params'];
		$data[$config['awb']['field']] = empty($config['awb']['two_parts'])? $awb[0] : $awb[1].'-'.$awb[2];
		$c = new curl($config['url']);
		if($config['post']){
			$c->setopt(CURLOPT_CUSTOMREQUEST, 'POST');
			$c->setopt(CURLOPT_POSTFIELDS, $data);
		}else{
			$c->setopt(CURLOPT_URL, $config['url'].'?'.$c->asPostString($data));
		}
		$c->setopt(CURLOPT_RETURNTRANSFER, true);
		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
		$c->setopt(CURLOPT_TIMEOUT, 20);
		if (!$c->exec()) {
			echo 'Request Error';
			throw new Exception('cUrl Error: '.$c->err);
		}
		return Yii::app()->controller->renderPartial('feed_'.$awb[1], ['data' => json_decode($c->result)]);
	}

	//end of class
}
