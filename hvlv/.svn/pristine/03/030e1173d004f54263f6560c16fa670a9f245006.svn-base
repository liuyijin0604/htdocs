<?php
/* 
 * support Kuaidi 100 API
 * more API details please refer to :
 *  http://www.kuaidi100.com/openapi/api_2_02.shtml
 * normal api url :
 * http://hvlv.local/api/kd?id=eoweuour&com=pcaexpress&nu=PE3334323AU&show=0&muti=1&order=desc
 */
class ApiKdAction extends CAction {

	const SHIPPING_COMPANY = 'pca';
	const KUAIDI100_KEY = 'kd100';

	public function run() {

		// get authorize key
		$key = '';
		if ( isset($_GET['id']) ) {
			$key = $_GET['id'];
		}

		// get kuaidi company code
		// for pca should be pca or others ?
		$company_code = $_GET['com'];

		// get tracking order number
		$tracking_number = $_GET['nu'];

		// get show mode
		// 0 : json
		// only support json currently
		if ( isset($_GET['show']) ) {
			$show_mode = intval( $_GET['show'] );
		}
		if ( $show_mode != 0 ) $show_mode = 0;


		// get multiple line or one line
		// currently only support 1 : means multiple lines
		if ( isset($_GET['muti']) ) {
			$multi_flag = intval( $_GET['muti'] );
		}
		if ( $multi_flag != 1 ) $multi_flag = 1;

		// get order flag
		// desc  or asc
		if ( isset($_GET['order']) ) {
			$order_flag = strtolower( $_GET['order'] );
		}
		if ( $order_flag !== 'desc' or $order_flag !== 'asc'  ) $order_flag = 'desc';


		// check key
		$rt = $this->checkKey($key,$tracking_number);
		if ( !$rt->success ) {
			echo json_encode($rt->result);
			return;
		}

		// check our company name
		$rt = $this->checkCompany($company_code,$tracking_number);
		if ( !$rt->success ) {
			echo json_encode($rt->result);
			return;
		}

		// get all tracking information

		$p = Shipment::model()->find('hbn = :c AND status > 0 AND status < 100', array(':c' => $tracking_number));
		if ( empty($p) ) {
			$o = array(
				'status' => 0,
				'com' => self::SHIPPING_COMPANY,
				'nu' => $tracking_number
			);
		}else{
			/**
			 * @param $order
			 * @return bool|stdClass
			 * kuaidi100 response format:
			 * time 每条跟踪信息的时间
				context 每条跟综信息的描述
				state 快递单当前的状态 ：　
					0：在途，即货物处于运输过程中；
					1：揽件，货物已由快递公司揽收并且产生了第一条跟踪信息；
					2：疑难，货物寄送过程出了问题；
					3：签收，收件人已签收；
					4：退签，即货物由于用户拒签、超区等原因退回，而且发件人已经签收；
					5：派件，即快递正在进行同城派件；
					6：退回，货物正处于退回发件人的途中；
					该状态还在不断完善中，若您有更多的参数需求，欢迎发邮件至  kuaidi@kingdee.com 提出。
				status 查询结果状态：
					0：物流单暂无结果，
					1：查询成功，
					2：接口出现异常，
			**/
			
			$o = new stdClass;
			if (empty($p->tracks)) {
				$o->status = 0;
			} else {
				$o->data = [];

				// order tracking history information
				$size = count($p->tracks);
				$start = 0;
				$end = $size;
				$step = 1;
				if ($order === 'desc') {
					$start = $size - 1;
					$end = -1;
					$step = -1;
				}

				// loop all trackings
				$all_tracks = array_values($p->tracks);
				for ($i = $start; $i != $end; $i += $step) {
					$tracking_info = $all_tracks[$i];
					if ($i == $start) {
						if (isset(Tracking::$kd100_status[$tracking_info['type']])) {
							$o->state = Tracking::$kd100_status[$tracking_info['type']];
						} else {
							$o->state = 2; // 2：疑难，货物寄送过程出了问题；
						}
					}
					$o->data[] = [
						'time' => $tracking_info['dt'],
						'context' => $tracking_info['activity']
					];
				}
				$o->status = 1;
			}
			$o->com = self::SHIPPING_COMPANY;
			$o->nu = $tracking_number;
		}

		echo json_encode($o);
	}

	/**
	 * @param $key
	 * @param $trackingNum
	 * @return stdClass
	 */
	private function checkKey($key,$trackingNum) {
		$resp = new stdClass();
		$resp->success = true;
		if ( empty($key) || $key !== self::KUAIDI100_KEY ) {
			$resp->success = false;
			$resp->result = array(
				'com' => self::SHIPPING_COMPANY,
				'nu' => $trackingNum,
				'status' => 2
			);
		}
		return $resp;
	}

	private function checkCompany($company,$trackingNum) {
		$resp = new stdClass();
		$resp->success = true;
		if ( empty($company) || $company !== self::SHIPPING_COMPANY ) {
			$resp->success = false;
			$resp->result = array(
				'com' => self::SHIPPING_COMPANY,
				'nu' => $trackingNum,
				'status' => 2
			);
		}
		return $resp;
	}

}
