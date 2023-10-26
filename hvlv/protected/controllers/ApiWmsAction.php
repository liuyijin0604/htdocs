<?php
//opcache_invalidate(__FILE__);
class ApiWmsAction extends CAction
{
	public $ctlr, $mod, $data, $user, $errors = [], $out, $debug = true;

	public function run()
	{
		if ($this->debug) {
			$this->log($_SERVER['REQUEST_METHOD'] . ' ' . strtoupper($_SERVER['REQUEST_METHOD']) == 'GET' ? json_encode($_GET) : file_get_contents('php://input'));
		}
		$this->ctlr = $this->getController();

		if (empty($_SERVER['HTTP_AUTHORIZATION']) || !$this->auth()) {
			throw new CHttpException(401, 'Authentication error, access denied.');
		}

		if (empty($_GET['mdl'])) {
			$this->mod = $this->data['mod'] . '_' . strtolower(@$_SERVER['REQUEST_METHOD']) . '0';
		} else {
			$this->mod = strtolower($_GET['mdl']) . '_' . strtolower(@$_SERVER['REQUEST_METHOD']);
		}

		if (empty($this->mod) || !method_exists($this, $this->mod)) {
			throw new CHttpException(400, 'Bad request, API access only!');
		}

		if (!empty($_GET['data_translator']) && method_exists($this, 'dt_' . strtolower($_GET['data_translator']))) {
			$this->{'dt_' . strtolower($_GET['data_translator'])}();
		}

		$this->{$this->mod}();
		empty($this->errors) ? $this->response($this->out) : $this->errorResponse();
	}

	protected function auth()
	{
		list($user, $hash) = explode(':', base64_decode(preg_replace('/^Basic\s+/i', '', trim($_SERVER['HTTP_AUTHORIZATION']))));
		$api = Api::model()->find('user = :u', [':u' => $user]);
		if (empty($api)) {
			return false;
		}

		if (empty($_GET['mdl'])) {
			if (in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT'])) {
				$data = file_get_contents('php://input');
				$this->data = $this->parse_str($data);
			} else if (in_array($_SERVER['REQUEST_METHOD'], ['DELETE'])) {
				$data = file_get_contents('php://input');
				if (!empty($_GET)) {
					ksort($_GET);
					$this->data = $_GET;
					$data = json_encode($_GET, JSON_UNESCAPED_UNICODE);
				} else if (!empty($data)) {
					$this->data = $this->parse_str($data);
				}
			} else {
				ksort($_GET);
				$this->data = $_GET;
				$data = json_encode($_GET, JSON_UNESCAPED_UNICODE);
			}
		} else {
			if (in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT'])) {
				$data = file_get_contents('php://input');
				$this->data = json_decode($data, true);
			} else if (in_array($_SERVER['REQUEST_METHOD'], ['DELETE'])) {
				$data = file_get_contents('php://input');
				if (!empty($_GET)) {
					ksort($_GET);
					$this->data = $_GET;
					unset($this->data['mdl']);
					$data = json_encode($this->data, JSON_UNESCAPED_UNICODE);
				} else if (!empty($data)) {
					$this->data = $this->parse_str($data);
				}
			} else {
				ksort($_GET);
				$this->data = $_GET;
				unset($this->data['mdl']);
				$data = json_encode($this->data, JSON_UNESCAPED_UNICODE);
			}
		}

		// if (in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT'])) {
		// 	$data = file_get_contents('php://input');
		// 	$this->data = json_decode($data, true);
		// } else {
		// 	ksort($_GET);
		// 	$this->data = $_GET;
		// 	$data = json_encode($_GET);
		// }

		$this->user = User::model()->findByPk($api->user_id);

		if (md5($data . '|' . $api->key) != strtolower($hash)) {
			$this->log('auth failed: ' . md5($data . '|' . $api->key) . ' ' . strtolower($hash));
		}

		// temporary for ufl
		// $user = User::model()->findByPk($api->user_id);
		// if (md5($data.'|'.$api->key) != strtolower($hash) && in_array($user->org_id, Org::$easyships)) {
		// 	return true;
		// }

		return md5($data . '|' . $api->key) == strtolower($hash);
	}

	protected function parse_str($data)
	{
		$data = explode('&', $data);
		$r = [];
		foreach ($data as $item) {
			list($k, $v) = explode('=', $item);
			$r[$k] = $v;
		}
		return $r;
	}

	public function order_post()
	{
		if (empty($this->data['orders'])) {
			$this->errors[] = ['no' => '', 'code' => 110, 'message' => 'Order info incomplete'];
			return;
		}
		$this->out = ['orders' => []];
		$errs = [];

		$odr_job_type_map = function ($t) {
			if ($t == 20 || $t == 40) return 10;
			else return 20;
			// return in_array($t, [20, 30]) ? 10 : 30;
		};

		$odr_att_type_map = function ($t) {
			$rt = 84;
			switch ($t) {
				case 10:
					$rt = 85;
					break;
				case 20:
					$rt = 86;
					break;
				case 30:
					$rt = 87;
					break;
			}
			return $rt;
		};

		foreach ($this->data['orders'] as $odr) {
			$temp_err = [];
			if (empty($odr['no'])) {
				$errs[] = ['no' => '', 'code' => 110, 'message' => 'Missing Order No'];
				continue;
			}
			if ($this->user->org_id == Org::ORGID_3PL_XCSOURCE && empty($odr['courier'])) {
				$errs[] = ['no' => '', 'code' => 110, 'message' => 'Missing 渠道'];
				continue;
			}

			//pick/create job
			$job = WmsJob::model()->find('org_id = :org_id AND type = :t AND date(`created`) = :day', [':org_id' => $this->user->org_id, ':t' => $odr_job_type_map($odr['type']), ':day' => date('Y-m-d')]);

			if (empty($job)) {
				$job = new WmsJob;
				$job->org_id = $this->user->org_id;
				$job->type = $odr_job_type_map($odr['type']);
				$job->status = 10;
				$job->ref = '3PL_' . date('Y-m-d');
				$job->save();
			}

			//group item
			$items = [];
			$not_found = [];
			foreach ($odr['items'] as $itm) {
				$prod = null;
				// find product
				if (empty($itm['ean']) && empty($itm['sku'])) {
					$this->errors[] = ['no' => '', 'code' => 110, 'message' => 'Order EAN / SKU info incomplete'];
					return;
				}

				// find ean first
				if (!empty($itm['ean'])) {
					$prod = WmsProd::model()->find('ean = :ean AND status = 1', [':ean' => $itm['ean']]);
				}
				// then find sku
				if (!empty($itm['sku'])) {
					$wpo = WmsProdOrg::model()->with('prod')->find('prod.status = 1 AND t.org_id = :org_id AND (t.sku = :sku)', [':org_id' => $this->user->org_id, ':sku' => $itm['sku']]);
					if (!empty($wpo)) {
						$prod = $wpo->prod;
					}
				}

				// life style barcode and sku is mixed up, need to check stock
				if ($this->user->org_id == Org::ORGID_3PL_LIFESTYLE) {
					$have_stock = false;
					$temp_prod = null;
					if (!empty($itm['sku'])) {
						// find sku in sku
						$wpo = WmsProdOrg::model()->with('prod')->find('prod.status = 1 AND t.org_id = :org_id AND (t.sku = :sku)', [':org_id' => $this->user->org_id, ':sku' => $itm['sku']]);
						if (!empty($wpo->prod)) {
							$prod = $wpo->prod;
							$temp_prod = $prod;
							$stock = WmsStock::model()->find(['select' => 'SUM(qty) AS qty, SUM(qty_res) AS qty_res', 'condition' => 'prod_id = :prod_id AND org_id = :org_id', 'params' => [':prod_id' => $prod->id, ':org_id' => $this->user->org_id]]);
							if ($stock->qty - $stock->qty_res > $itm['qty']) {
								$have_stock = true;
							}
						}
						// find sku in ean
						if ($have_stock == false || empty($prod)) {
							$prod = WmsProd::model()->find('ean = :ean AND status = 1', [':ean' => $itm['sku']]);
							if (!empty($prod)) {
								$temp_prod = $prod;
								$stock = WmsStock::model()->find(['select' => 'SUM(qty) AS qty, SUM(qty_res) AS qty_res', 'condition' => 'prod_id = :prod_id AND org_id = :org_id', 'params' => [':prod_id' => $prod->id, ':org_id' => $this->user->org_id]]);
								if ($stock->qty - $stock->qty_res > $itm['qty']) {
									$have_stock = true;
								}
							}
						}
					}
					if ($have_stock == false && !empty($itm['ean'])) {
						// find ean in ean
						$prod = WmsProd::model()->find('ean = :ean AND status = 1', [':ean' => $itm['ean']]);
						if (!empty($prod)) {
							$temp_prod = $prod;
							$stock = WmsStock::model()->find(['select' => 'SUM(qty) AS qty, SUM(qty_res) AS qty_res', 'condition' => 'prod_id = :prod_id AND org_id = :org_id', 'params' => [':prod_id' => $prod->id, ':org_id' => $this->user->org_id]]);
							if ($stock->qty - $stock->qty_res > $itm['qty']) {
								$have_stock = true;
							}
						}
						// find ean in sku
						if ($have_stock == false || empty($prod)) {
							$wpo = WmsProdOrg::model()->with('prod')->find('prod.status = 1 AND t.org_id = :org_id AND (t.sku = :sku)', [':org_id' => $this->user->org_id, ':sku' => $itm['ean']]);
							if (!empty($wpo->prod)) {
								$prod = $wpo->prod;
								$temp_prod = $prod;
								$stock = WmsStock::model()->find(['select' => 'SUM(qty) AS qty, SUM(qty_res) AS qty_res', 'condition' => 'prod_id = :prod_id AND org_id = :org_id', 'params' => [':prod_id' => $prod->id, ':org_id' => $this->user->org_id]]);
								if ($stock->qty - $stock->qty_res > $itm['qty']) {
									$have_stock = true;
								}
							}
						}
					}
					if (empty($prod)) {
						$prod = $temp_prod;
					}
				}

				if (empty($itm['ean'])) {
					$itm['ean'] = $itm['sku'];
				}
				if (empty($prod)) {
					$errs[] = ['no' => $odr['no'], 'code' => 201, 'message' => 'Product not found EAN ' . $itm['ean'] . (!empty($itm['sku']) ? ' / SKU ' . $itm['sku'] : '')];
					$temp_err[] = ['no' => $odr['no'], 'code' => 201, 'message' => 'Product not found EAN ' . $itm['ean'] . (!empty($itm['sku']) ? ' / SKU ' . $itm['sku'] : '')];
					if (empty($not_found[$itm['ean']])) $not_found[$itm['ean']] = 0;
					$not_found[$itm['ean']] += intval($itm['qty']);
				} else {
					if (!isset($items[$prod->id])) {
						$items[$prod->id] = 0;
					}

					if ($prod->type != WmsProd::WMS_PROD_KIT) {
						$items[$prod->id] += intval($itm['qty']);
					} else if ($prod->type == WmsProd::WMS_PROD_KIT) {
						foreach ($prod->items as $item) {
							if (empty($items[$item->item->id])) $items[$item->item->id] = 0;
							$items[$item->item->id] += intval($itm['qty']) * $item->qty;
						}
					}
				}
			}

			// stock out
			if ($odr_job_type_map($odr['type']) == 20) {
				//check stock
				$new_items = [];
				foreach ($items as $pid => $qty) {
					$stock = WmsStock::model()->find(['select' => 'SUM(qty) AS qty, SUM(qty_res) AS qty_res', 'condition' => 'prod_id = :prod_id AND org_id = :org_id', 'params' => [':prod_id' => $pid, ':org_id' => $this->user->org_id]]);
					if ($stock->qty - $stock->qty_res < $qty) {
						$prod = WmsProd::model()->findByPk($pid);
						$errs[] = ['no' => $odr['no'], 'code' => 203, 'message' => $prod->ean . ' not enough stock (' . $qty . ' < ' . ($stock->qty - $stock->qty_res) . ')'];
						$temp_err[] = ['no' => $odr['no'], 'code' => 203, 'message' => $prod->ean . ' not enough stock (' . $qty . ' < ' . ($stock->qty - $stock->qty_res) . ')'];
						$new_items[] = [
							'si' => '',
							'sn' => $prod->name,
							'pq' => '',
							'cq' => '',
							'uq' => $qty,
							'pli' => '',
							'pl' => '',
							'nt' => '',
							'pi' => $prod->id,
						];
					} else {
						$stocks = WmsStock::model()->findAll('prod_id = :prod_id AND org_id = :org_id AND qty - qty_res > 0', [':prod_id' => $pid, ':org_id' => $this->user->org_id]);
						foreach ($stocks as $stock) {
							if ($qty <= 0) {
								break;
							}
							$uq = min($qty, $stock->availQty());
							$new_items[] = [
								'si' => $stock->id,
								'sn' => $stock->prod->name,
								'pq' => '',
								'cq' => '',
								'uq' => $uq,
								'pli' => '',
								'pl' => '',
								'nt' => '',
								'pi' => $stock->prod->id,
							];
							$qty -= $uq;
						}
					}
				}
				foreach ($not_found as $ean => $qty) {
					$new_items[] = [
						'si' => '',
						'sn' => $ean,
						'pq' => '',
						'cq' => '',
						'uq' => $qty,
						'pli' => '',
						'pl' => '',
						'nt' => '',
						'pi' => '',
					];
				}

				$create = true;
				if (!empty($temp_err)) {
					if (empty($this->data['api_hook'])) {
						$create = false;
					} else {
						// easyship if stock not enough still create, but do not occupy stock
						foreach ($new_items as $k => $item) {
							$new_items[$k]['si'] = '';
						}
					}
				}

				if (empty($odr['to']['country'])) {
					$odr['to']['country'] = 'AU';
				} else if ($odr['to']['country'] == '中国') {
					$odr['to']['country'] = 'CN';
				}

				if (!in_array($this->user->org_id, Org::$easyships)) {
					if (!empty($odr['to']) && $odr['to']['country'] == 'AU' && !Postcode::validateAddress($odr['to']['suburb'], $odr['to']['state'], $odr['to']['postcode'], $odr['to']['country'])) {
						$errs[] = ['no' => $odr['no'], 'code' => 301, 'message' => 'Receiver info suburb, state, postcode not match'];
						$create = false;
					}
					$odr['to']['phone'] = trim($odr['to']['phone']);
					if (!preg_match('/[\+]+\d{2}\s+\d*/', $odr['to']['phone']) && !preg_match('/\d*/', $odr['to']['phone'])) {
						$errs[] = ['no' => $odr['no'], 'code' => 301, 'message' => 'Receiver phone has to be number'];
						$create = false;
					}
				}

				$transaction = Yii::app()->db->beginTransaction();
				if ($create) {
					//create main task
					$task = WmsTask::model()->with('job')->find('t.ref = :ref AND job.org_id = :org_id', [':org_id' => $this->user->org_id, ':ref' => $odr['no']]);
					if (empty($task)) {
						$task = new WmsTask;
						$task->job_id = $job->id;
						$task->type = WmsTask::TYPE_PICK_UNIT;
						$task->is_request = 1;
						$task->op_id = 0;
						$task->status = empty($temp_err) ? WmsTask::STATUS_SCHEDULED : WmsTask::STATUS_NEW;
						$task->ref = $odr['no'];
						$task->new_items = $new_items;
						if (!empty($this->data['api_hook'])) {
							$task->mdata['api_hook'] = $this->data['api_hook'];
							if (!empty($this->data['api_meta'])) {
								$task->mdata['api_meta'] = $this->data['api_meta'];
							}
						}
						if (!empty($odr['sourceLinkId'])) {
							$task->mdata['sourceLinkId'] = $odr['sourceLinkId'];
							if (!empty($odr['orderSPT'])) {
								$task->mdata['orderSPT'] = $odr['orderSPT'];
							}
						}
						$task->mdata['errs'] = $temp_err;
						if (!empty($odr['warehouse'])) {
							$task->mdata['api_warehouse'] = $odr['warehouse'];
						}
						if (!empty($odr['type'])) {
							$task->mdata['api_type'] = $odr['type'];
						}
						if (!empty($odr['customerId'])) {
							$task->mdata['api_customerId'] = $odr['customerId'];
						}
						if (!empty($odr['items'])) {
							$task->mdata['api_items'] = $odr['items'];
						}
						$task->save();

						$task->pickupTask->type = WmsTask::TYPE_DELIVERY;
						$delivery_task = $task->pickupTask;
						$delivery_task->ref = @$odr['connote_no'];
						$delivery_task->mdata['cnee'] = $odr['to'];
						$delivery_task->mdata['cnee']['tel'] = $odr['to']['phone'];
						if (empty($delivery_task->mdata['cnee']['tel'])) {
							$delivery_task->mdata['cnee']['tel'] = @$task->job->customer->extra['3pl_cnee_tel'];
						}
						// customer choose mix or letter
						if (!empty($odr['courier']) && (preg_match('/^MIX$/i', $odr['courier']) || preg_match('/^LETTER$/i', $odr['courier']))) {
							$delivery_task->mdata['customer_choose_courier'] = @$odr['courier'];
						}
						$delivery_task->save();

						//attachments
						if (!empty($odr['attachments'])) {
							foreach ($odr['attachments'] as $att) {
								FileRepo::storeBase64File($att['content'], $att['name'], $odr_att_type_map($att['type']), $task->id);
							}
						}

						$this->out['orders'][] = ['no' => $odr['no'], 'id' => $task->id, 'status' => $task->status];
					} else {
						$errs[] = ['no' => $odr['no'], 'code' => 101, 'message' => 'Order already exists'];
					}
				}
				$transaction->commit();
			} else {
				$new_items = [];
				foreach ($items as $pid => $qty) {
					$prod = WmsProd::model()->findByPk($pid);
					$new_items[] = [
						'pli' => '',
						'pl' => '',
						'gi' => $prod->id,
						'gn' => $prod->name,
						'cq' => '',
						'uq' => $qty,
						'ex' => '',
						'bn' => '',
						'nt' => '',
					];
				}

				if (empty($temp_err)) {
					$task = WmsTask::model()->with('job')->find('t.ref = :ref AND job.org_id = :org_id', [':org_id' => $this->user->org_id, ':ref' => $odr['no']]);
					if (!empty($task)) {
						$errs[] = ['no' => $odr['no'], 'code' => 101, 'message' => 'Order already exists'];
					} else {
						$task = new WmsTask;
						$task->job_id = $job->id;
						$task->type = 1010;
						$task->is_request = 1;
						$task->op_id = 0;
						$task->status = 20;
						$task->ref = $odr['no'];
						$task->new_items = $new_items;
						if (!empty($odr['warehouse'])) {
							$task->mdata['api_warehouse'] = $odr['warehouse'];
						}
						if (!empty($odr['type'])) {
							$task->mdata['api_type'] = $odr['type'];
						}
						if (!empty($odr['customerId'])) {
							$task->mdata['api_customerId'] = $odr['customerId'];
						}
						if (!empty($odr['items'])) {
							$task->mdata['api_items'] = $odr['items'];
						}
						$task->save();

						$this->out['orders'][] = ['no' => $odr['no'], 'id' => $task->id, 'status' => $task->status];
					}
				}
			}
		}

		if (!empty($errs)) {
			$this->out['errors'] = $errs;
		}
	}

	public function order_put()
	{
		if (empty($this->data['orders'])) {
			$this->errors[] = ['no' => '', 'code' => 110, 'message' => 'Order info incomplete'];
			return;
		}
		$this->out = ['orders' => []];
		$errs = [];

		$odr_job_type_map = function ($t) {
			if ($t == 20) return 10;
			else return 20;
			// return in_array($t, [20, 30]) ? 10 : 30;
		};

		foreach ($this->data['orders'] as $odr) {
			if (empty($odr['no'])) {
				$errs[] = ['no' => '', 'code' => 110, 'message' => 'Missing Order No'];
				continue;
			}

			if (!empty($odr['no'])) {
				$task = WmsTask::model()->with('job')->find('t.ref = :no AND job.org_id = :org_id', [':no' => $odr['no'], ':org_id' => $this->user->org_id]);
			} else if (!empty($odr['id'])) {
				$task = WmsTask::model()->with('job')->find('t.id = :id AND job.org_id = :org_id', [':id' => $odr['id'], ':org_id' => $this->user->org_id]);
			}

			if (empty($task)) {
				$errs[] = ['no' => $odr['no'], 'id' => $odr['id'], 'code' => 104, 'message' => 'Unable to find Order'];
			} else {
				if (!in_array($task->status, [10, 20])) {
					$map = array(
						25 => 'Dispatch Booked',
						30 => 'Picked',
						40 => 'Packed',
						90 => 'Dispatched',
						100 => 'Cancelled',
					);
					$errs[] = ['no' => $task->ref, 'id' => $task->id, 'code' => 102, 'message' => $map[$task->status] . ' order cannot be updated'];
				} else {
					$transaction = Yii::app()->db->beginTransaction();

					foreach ($odr['items'] as $itm) {
						//find product
						if (empty($itm['ean']) && !empty($itm['sku'])) {
							$itm['ean'] = $itm['sku'];
						}
						if (!empty($itm['ean'])) {
							$prod = WmsProd::model()->find('ean = :ean AND status = 1', [':ean' => $itm['ean']]);
							if (empty($itm['sku'])) {
								$itm['sku'] = $itm['ean'];
							}

							if (empty($prod) && !empty($itm['sku'])) { // try sku
								$wpo = WmsProdOrg::model()->with('prod')->find('prod.status = 1 AND t.org_id = :org_id AND (t.sku = :sku OR t.sku = :ean)', [':org_id' => $this->user->org_id, ':sku' => $itm['sku'], ':ean' => $itm['ean']]);
								if (!empty($wpo)) {
									$prod = $wpo->prod;
								}
							}
							if (empty($prod)) {
								$errs[] = ['no' => $odr['no'], 'code' => 201, 'message' => 'Product not found EAN ' . $itm['ean'] . (!empty($itm['sku']) ? ' / SKU ' . $itm['sku'] : '')];
							} else {
								if (!isset($items[$prod->id])) {
									$items[$prod->id] = 0;
								}

								if ($prod->type != WmsProd::WMS_PROD_KIT) {
									$items[$prod->id] += intval($itm['qty']);
								} else if ($prod->type == WmsProd::WMS_PROD_KIT) {
									foreach ($prod->items as $item) {
										$items[$item->item->id] += intval($itm['qty']) * $item->qty;
									}
								}
							}
						}
					}
					foreach ($task->items as $item) {
						$item->realaseReserve();
					}

					// stock in change items
					if ($odr_job_type_map($odr['type']) == 10) {
						$new_items = [];
						foreach ($items as $pid => $qty) {
							$prod = WmsProd::model()->findByPk($pid);
							$new_items[] = [
								'pli' => '',
								'pl' => '',
								'gi' => $prod->id,
								'gn' => $prod->name,
								'cq' => '',
								'uq' => $qty,
								'ex' => '',
								'bn' => '',
								'nt' => '',
							];
						}

						$task->new_items = $new_items;
						$task->save();

						$this->out['orders'][] = ['no' => $odr['no'], 'id' => '', 'status' => $task->status];
					} else {
						//check stock
						$new_items = [];
						foreach ($items as $pid => $qty) {
							$stock = WmsStock::model()->find(['select' => 'SUM(qty) AS qty, SUM(qty_res) AS qty_res', 'condition' => 'prod_id = :prod_id AND org_id = :org_id', 'params' => [':prod_id' => $pid, ':org_id' => $this->user->org_id]]);
							if ($stock->qty - $stock->qty_res < $qty) {
								$prod = WmsProd::model()->findByPk($pid);
								$errs[] = ['no' => $odr['no'], 'code' => 203, 'message' => $prod->ean . ' not enough stock (' . $qty . ' < ' . ($stock->qty - $stock->qty_res) . ')'];
								$new_items[] = [
									'si' => '',
									'sn' => $prod->name,
									'pq' => '',
									'cq' => '',
									'uq' => $qty,
									'pli' => '',
									'pl' => '',
									'nt' => '',
									'pi' => $prod->id,
								];
							} else {
								$stocks = WmsStock::model()->findAll('prod_id = :prod_id AND org_id = :org_id AND qty - qty_res > 0', [':prod_id' => $pid, ':org_id' => $this->user->org_id]);
								foreach ($stocks as $stock) {
									if ($qty <= 0) {
										break;
									}
									$uq = min($qty, $stock->availQty());
									$new_items[] = [
										'si' => $stock->id,
										'sn' => $stock->prod->name,
										'pq' => '',
										'cq' => '',
										'uq' => $uq,
										'pli' => '',
										'pl' => '',
										'nt' => '',
										'pi' => $stock->prod->id,
									];
									$qty -= $uq;
								}
							}
						}

						$update = true;
						if (empty($odr['to']['country'])) {
							$odr['to']['country'] = 'AU';
						} else if ($odr['to']['country'] == '中国') {
							$odr['to']['country'] = 'CN';
						}

						if (!empty($odr['to']) && $odr['to']['country'] == 'AU' && !Postcode::validateAddress($odr['to']['suburb'], $odr['to']['state'], $odr['to']['postcode'], $odr['to']['country'])) {
							$errs[] = ['no' => $odr['no'], 'code' => 301, 'message' => 'Receiver info suburb, state, postcode not match'];
							$create = false;
						}
						$odr['to']['phone'] = trim($odr['to']['phone']);
						if (!preg_match('/[\+]+\d{2}\s+\d*/', $odr['to']['phone']) && !preg_match('/\d*/', $odr['to']['phone'])) {
							$errs[] = ['no' => $odr['no'], 'code' => 301, 'message' => 'Receiver phone has to be number'];
							$create = false;
						}

						if (!empty($errs)) {
							$update = false;
						}
						if ($update) {
							$task->new_items = $new_items;
							$task->save();

							$task->deliveryTask->mdata['cnee'] = $odr['to'];
							$task->deliveryTask->mdata['cnee']['tel'] = $odr['to']['phone'];
							$task->deliveryTask->save();

							$transaction->commit();
							$this->out['orders'][] = ['no' => $odr['no'], 'id' => $task->id, 'status' => $task->status];
						} else {
							$transaction->rollback();
							$errs[] = ['no' => $odr['no'], 'code' => 102, 'message' => 'Order cannot be updated'];
						}
					}
				}
			}
		}

		if (!empty($errs)) {
			$this->out['errors'] = $errs;
		}
	}

	public function order_get()
	{
		$data = $this->data;
		$tasks = [];
		foreach (['id', 'no'] as $key) {
			if (empty($data[$key])) {
				continue;
			}

			if (is_string($data[$key]) || is_numeric($data[$key])) {
				$data[$key] = [$data[$key]];
			}

			if (is_array($data[$key])) {
				foreach ($data[$key] as $n) {
					$task = $key == 'id' ? WmsTask::model()->findByPk($n) : WmsTask::model()->with('job')->find(['condition' => 't.ref = :ref AND job.org_id = :org_id', 'params' => [':ref' => $n, ':org_id' => $this->user->org_id], 'order' => 't.id desc']);

					if (!empty($task)) {
						$tasks[] = $task;
					}
				}
			}
		}

		$results = [];
		if (!empty($tasks)) {
			foreach ($tasks as $task) {
				$tracking_no = [];
				$connote_no = [];
				if (!empty($task->deliveryTask) && !empty($task->deliveryTask->mdata['shipment_id'])) {
					foreach ($task->deliveryTask->mdata['shipment_id'] as $sid) {
						$shipment = Shipment::model()->findByPk($sid);
						$tracking_no[] = $shipment->ref;
						$connote_no[] = $shipment->hbn;
					}
				}
				if (!empty($task->deliveryTask->mdata['shipment_courier_id'])) {
					if ($task->deliveryTask->mdata['shipment_courier_id'] == Org::ORGID_COURIER_FASTWAY) {
						$courier = 'Fastway';
					} else if ($task->deliveryTask->mdata['shipment_courier_id'] == Org::ORGID_COURIER_AUPOST) {
						$courier = 'Auspost';
					} else if ($task->deliveryTask->mdata['shipment_courier_id'] == Org::ORGID_COURIER_TNT) {
						$courier = 'Tnt';
					} else if ($task->deliveryTask->mdata['shipment_courier_id'] == Org::ORGID_COURIER_SENDLE) {
						$courier = 'Sendle';
					} else {
						$courier = '';
					}
				}
				if ($task->type == 1010) {
					$type = 10;
				} else if (in_array($task->type, [3020,3030])) {
					$type = 20;
				} else {
					$type = 0;
				}
				$items = [];
				foreach ($task->items as $item) {
					if ($task->type == 1010 && !empty($item->mdata['gi'])) {
						$prod = WmsProd::model()->findByPk($item->mdata['gi']);
					} else if (in_array($task->type, [3020,3030]) && !empty($item->mdata['si'])) {
						$prod = WmsProd::model()->with('stocks')->find('stocks.id = :si', [':si' => $item->mdata['si']]);
					}
					$temp = ['ean' => $prod->ean, 'sku' => $prod->getSku($this->user->org_id), 'name' => $prod->name, 'qty' => $item->mdata['uq']];
					if (in_array($task->type, [3020,3030]) && !empty($item->mdata['si'])) {
						$serials = WmsSerialNo::model()->findAll('task_id = :task_id AND stock_id = :stock_id', [':task_id' => $task->actionTask->id, ':stock_id' => $item->mdata['si']]);
						foreach ($serials as $serial) {
							$temp['serial_number'][] = $serial->sn;
						}
					}
					$items[] = $temp;
				}
				$results[] = [
					'id' => $task->id,
					'no' => $task->ref,
					'type' => $type,
					'warehouse' => @$task->mdata['api_warehouse'],
					'confirmed' => $task->status >= 20 ? true : false,
					'items' => $items,
					'to' => @$task->deliveryTask->mdata['cnee'],
					'freight_type' => 10,
					'freight_co' => $courier,
					'connote_no' => $tracking_no,
					'attachments' => [],
					'note' => @$task->mdata['note'],
					'packing_note' => @$task->packTask->ref,
					'shipping_note' => @$task->deliveryTask->ref,
					'status' => $task->status,
					'tracking_no' => $tracking_no,
					'tracking_courier' => $courier,
				];
			}
		}
		if (empty($results)) {
			$this->out = ['msg' => 'No task found'];
		} else {
			$this->out = $results;
		}
	}

	public function order_delete()
	{
		if (!empty($this->data['no'])) {
			foreach ($this->data['no'] as $no) {
				$this->data['orders'][] = ['no' => $no];
			}
		}
		if (empty($this->data['orders'])) {
			$this->errors[] = ['no' => '', 'code' => 110, 'message' => 'Order info incomplete'];
			return;
		}
		$this->out = ['orders' => []];
		$errs = [];

		foreach ($this->data['orders'] as $odr) {
			if (empty($odr['no']) && empty($odr['id'])) {
				$errs[] = ['no' => '', 'code' => 110, 'message' => 'Missing Order No and ID'];
				continue;
			}

			if (!empty($odr['no'])) {
				$task = WmsTask::model()->with('job')->find('t.ref = :no AND job.org_id = :org_id', [':no' => $odr['no'], ':org_id' => $this->user->org_id]);
			} else if (!empty($odr['id'])) {
				$task = WmsTask::model()->with('job')->find('t.id = :id AND job.org_id = :org_id', [':id' => $odr['id'], ':org_id' => $this->user->org_id]);
			}

			if (empty($task)) {
				$errs[] = ['no' => $odr['no'], 'id' => $odr['id'], 'code' => 104, 'message' => 'Unable to find Order'];
			} else {
				if (in_array($task->status, [10, 20])) {
					$task->status = 100;
					$task->update('status');

					$this->out['orders'][] = ['no' => $task->ref, 'id' => $task->id, 'status' => 100];
				} else {
					$map = array(
						25 => 'Dispatch Booked',
						30 => 'Picked',
						40 => 'Packed',
						90 => 'Dispatched',
						100 => 'Cancelled',
					);
					$errs[] = ['no' => $task->ref, 'id' => $task->id, 'code' => 103, 'message' => $map[$task->status] . ' order cannot be cancelled'];
				}
			}
		}

		if (!empty($errs)) {
			$this->out['errors'] = $errs;
		}
	}

	public function product_post()
	{
		if (empty($this->data['products'])) {
			$this->errors[] = ['ean' => '', 'name' => '', 'code' => 110, 'message' => 'Product info incomplete'];
			return;
		}
		$this->out = ['products' => []];
		$errs = [];

		foreach ($this->data['products'] as $pdt) {
			if (empty($pdt['ean'])) {
				$errs[] = ['ean' => '', 'code' => 110, 'message' => 'Product ean is empty'];
				continue;
			} else if (empty($pdt['name'])) {
				$errs[] = ['name' => '', 'code' => 110, 'message' => 'Product name is empty'];
				continue;
			}

			$prod = WmsProd::model()->find('ean = :ean', [':ean' => $pdt['ean']]);
			if (!empty($prod)) {
				$errs[] = ['ean' => $prod->ean, 'name' => $prod->name, 'code' => 101, 'message' => 'Product already exists'];
				continue;
			}

			$prod = new WmsProd;
			$prod->status = 1;
			$prod->type = WmsProd::WMS_PROD_PHYSICAL;
			$prod->ean = $pdt['ean'];
			$prod->name = $pdt['name'];
			$prod->name_zh = @$pdt['name_zh'];
			$prod->brand = @$pdt['brand'];
			$prod->model = @$pdt['model'];
			$prod->weight = @$pdt['weight'];
			$prod->dim = !empty($pdt['dim']) ? json_encode($pdt['dim']) : '';
			if (!$prod->save()) {
				$errs[] = ['ean' => $pdt['ean'], 'name' => $pdt['name'], 'code' => 110, 'message' => 'Product info incomplete'];
				continue;
			}

			$this->out['products'][] = ['ean' => $prod->ean, 'name' => $prod->name];

			if (!empty($pdt['sku'])) {
				$org = WmsProdOrg::model()->find('prod_id = :pid AND org_id = :oid', [':pid' => $prod->id, ':oid' => $this->user->org_id]);
				if (empty($org)) {
					$org = new WmsProdOrg;
					$org->prod_id = $prod->id;
					$org->org_id = $this->user->org_id;
					$org->sku = $pdt['sku'];
					$org->save();
				}
			}

			if (!empty($pdt['packages'])) {
				foreach ($pdt['packages'] as $pkg) {
					if (empty($pkg['type']) || empty($pkg['qty']) || empty($pkg['weight'])) {
						$errs[] = ['ean' => $prod->ean, 'name' => $prod->name, 'code' => 201, 'message' => 'Package info incomplete'];
						continue;
					}

					$pack = WmsProdPack::model()->find('type = :type AND qty = :qty AND prod_id = :prod_id', [':type' => $pkg['type'], ':qty' => $pkg['qty'], ':prod_id' => $prod->id]);
					if (empty($pack)) {
						$pack = new WmsProdPack;
						$pack->prod_id = $prod->id;
						$pack->type = $pkg['type'];
						$pack->qty = $pkg['qty'];
					}
					$pack->weight = $pkg['weight'];
					$pack->barcode = @$pkg['barcode'];
					$pack->dim = !empty($pkg['dim']) ? json_encode($pkg['dim']) : '';
					$pack->save();
				}
			}
		}

		if (!empty($errs)) {
			$this->out['errors'] = $errs;
		}
	}

	public function product_put()
	{
		if (empty($this->data['products'])) {
			$this->errors[] = ['ean' => '', 'name' => '', 'code' => 110, 'message' => 'Product info incomplete'];
			return;
		}
		$this->out = ['products' => []];
		$errs = [];

		foreach ($this->data['products'] as $pdt) {
			if (empty($pdt['ean'])) {
				$errs[] = ['ean' => '', 'code' => 110, 'message' => 'Product ean is empty'];
				continue;
			} else if (empty($pdt['name'])) {
				$errs[] = ['name' => '', 'code' => 110, 'message' => 'Product name is empty'];
				continue;
			}

			$prod = WmsProd::model()->find('ean = :ean', [':ean' => $pdt['ean']]);
			if (empty($prod)) {
				$errs[] = ['ean' => $pdt['ean'], 'name' => $pdt['name'], 'code' => 104, 'message' => 'Unable to find Product'];
				continue;
			}

			$prod->name_zh = @$pdt['name_zh'];
			$prod->brand = @$pdt['brand'];
			$prod->model = @$pdt['model'];
			$prod->weight = @$pdt['weight'];
			$prod->dim = !empty($pdt['dim']) ? json_encode($pdt['dim']) : '';
			if (!$prod->save()) {
				$errs[] = ['ean' => $pdt['ean'], 'name' => $pdt['name'], 'code' => 110, 'message' => 'Product info incomplete'];
				continue;
			}

			$this->out['products'][] = ['ean' => $prod->ean, 'name' => $prod->name];

			if (!empty($pdt['sku'])) {
				$org = WmsProdOrg::model()->find('prod_id = :pid AND org_id = :oid', [':pid' => $prod->id, ':oid' => $this->user->org_id]);
				if (empty($org)) {
					$org = new WmsProdOrg;
					$org->prod_id = $prod->id;
					$org->org_id = $this->user->org_id;
				}
				$org->sku = $pdt['sku'];
				$org->save();
			}

			if (!empty($pdt['packages'])) {
				foreach ($pdt['packages'] as $pkg) {
					if (empty($pkg['type'])) $pkg['type'] = 10;
					if (empty($pkg['qty'])) $pkg['qty'] = 1;
					if (empty($pkg['type']) || empty($pkg['qty'])) {
						$errs[] = ['ean' => $prod->ean, 'name' => $prod->name, 'code' => 201, 'message' => 'Package info incomplete'];
						continue;
					}

					$pack = WmsProdPack::model()->find('type = :type AND prod_id = :prod_id', [':type' => $pkg['type'], ':prod_id' => $prod->id]);
					if (empty($pack)) {
						$pack = new WmsProdPack;
						$pack->prod_id = $prod->id;
						$pack->type = $pkg['type'];
					}
					$pack->qty = @$pkg['qty'];
					$pack->weight = @$pkg['weight'];
					$pack->barcode = @$pkg['barcode'];
					$pack->dim = !empty($pkg['dim']) ? json_encode($pkg['dim']) : '';
					$pack->save();
				}
			}
		}

		if (!empty($errs)) {
			$this->out['errors'] = $errs;
		}
	}

	public function product_get()
	{
		if (empty($this->data['products'])) {
			$this->errors[] = ['ean' => '', 'name' => '', 'code' => 110, 'message' => 'Product info incomplete'];
			return;
		}
		$this->out = ['products' => []];
		$errs = [];

		foreach ($this->data['products'] as $pdt) {
			if (empty($pdt['ean'])) {
				$errs[] = ['ean' => '', 'code' => 110, 'message' => 'Product ean is empty'];
				continue;
			} else if (empty($pdt['name'])) {
				$errs[] = ['name' => '', 'code' => 110, 'message' => 'Product name is empty'];
				continue;
			}

			$prod = WmsProd::model()->find('ean = :ean', [':ean' => $pdt['ean']]);
			if (empty($prod)) {
				$errs[] = ['ean' => $pdt['ean'], 'name' => $pdt['name'], 'code' => 104, 'message' => 'Unable to find Product'];
				continue;
			}

			$result = ['ean' => $prod->ean, 'name' => $prod->name, 'name_zh' => $prod->name_zh, 'brand' => $prod->brand, 'model' => $prod->model, 'weight' => $prod->weight, 'dim' => $prod->dim, 'packages' => []];

			$org = WmsProdOrg::model()->find('prod_id = :pid AND org_id = :oid', [':pid' => $prod->id, ':oid' => $this->user->org_id]);
			if (!empty($org)) {
				$result['sku'] = $org->sku;
			}

			$packs = WmsProdPack::model()->findAll('prod_id = :prod_id', [':prod_id' => $prod->id]);
			foreach ($packs as $pack) {
				$result['packages'][] = ['type' => $pack->type, 'qty' => $pack->qty, 'barcode' => $pack->barcode, 'weight' => $pack->weight, 'dim' => $pack->dim];
			}

			$this->out['products'][] = $result;
		}

		if (!empty($errs)) {
			$this->out['errors'] = $errs;
		}
	}

	public function stock_get()
	{
		$this->out = ['stocks' => []];
		$errs = [];

		if (empty($this->data['ean']) && empty($this->data['sku'])) {
			$errs[] = ['ean' => '', 'sku' => '', 'code' => 110, 'message' => 'Product ean and sku is empty'];
		}

		if (empty($this->data['date'])) {
			$todate = date('Y-m-d');
		} else {
			$todate = $this->data['date'];
		}
		$todate = date('Y-m-d 00:00:00', strtotime($todate . ' + 1 day'));

		if (empty($this->data['warehouse'])) {
			$this->data['warehouse'] = 'all warehouse';
		}

		$prods = [];
		if (!empty($this->data['ean'])) {
			foreach ($this->data['ean'] as $ean) {
				$prod = WmsProd::model()->find('ean = :ean', [':ean' => $ean]);
				if (empty($prod)) {
					$errs[] = ['ean' => $ean, 'code' => 101, 'message' => 'Product not found'];
				} else {
					$prods[$prod->id] = $prod;
				}
			}
		}
		if (!empty($this->data['sku'])) {
			foreach ($this->data['sku'] as $sku) {
				$temp_prods = WmsProd::model()->with('orgs')->findAll('orgs.sku = :sku AND orgs.org_id = :oid AND t.status = 1', [':sku' => $sku, ':oid' => $this->user->org_id]);
				if (empty($temp_prods)) {
					$errs[] = ['sku' => $sku, 'code' => 101, 'message' => 'Product not found'];
				} else {
					foreach ($temp_prods as $prod) {
						$prods[$prod->id] = $prod;
					}
				}
			}
		}

		foreach ($prods as $prod) {
			if ($prod->type != WmsProd::WMS_PROD_KIT) {
			$stocks = WmsStock::model()->findAll('prod_id = :pid AND t.org_id = :oid', [':pid' => $prod->id, ':oid' => $this->user->org_id]);
			$qty = 0;
			foreach ($stocks as $stock) {
				$ledgers = WmsStockLedger::model()->findAll('ts > :todate AND stock_id = :sid AND location_id > 10', [':todate' => $todate, ':sid' => $stock->id]);
				foreach ($ledgers as $ledger) {
					if ($ledger->qty_in > 0) {
						$stock->qty -= $ledger->qty_in;
					} else if ($ledger->qty_out > 0) {
						$stock->qty += $ledger->qty_out;
					}
				}

				$ledgers = WmsStockLedger::model()->findAll('ts > :todate AND stock_id = :sid AND location_id = 4', [':todate' => $todate, ':sid' => $stock->id]);
				foreach ($ledgers as $ledger) {
					if ($ledger->qty_in > 0) {
						$stock->qty_res -= $ledger->qty_in;
					} else if ($ledger->qty_out > 0) {
						$stock->qty_res += $ledger->qty_out;
					}
				}

				if ($stock->qty <= 0) continue;
				$sku = WmsProdOrg::model()->find('org_id = :oid AND prod_id = :pid', [':oid' => $this->user->org_id, ':pid' => $stock->prod->id]);
				if (empty($stock->prod->mdata['expiry_span'])) {
					$proddate = '';
				} else {
					$proddate = date('Y-m-d', strtotime($stock->expiry . ' - ' . intval($stock->prod->mdata['expiry_span']) . ' year'));
				}
				$this->out['stocks'][] = [
					'ean' => $stock->prod->ean,
					'sku' => @$sku->sku,
					'name' => $stock->prod->name,
					'expiry' => $stock->expiry,
					'prodDate' => $proddate,
					'batch' => $stock->batch,
					'date' => date('Y-m-d', strtotime($todate . ' - 1 day')),
					'warehouse' => [
						'code' => $this->data['warehouse'],
						'stock' => $stock->qty - $stock->qty_res,
						'lock' => $stock->qty_res,
						'total' => $stock->qty,
					]
				];
				$qty += $stock->qty;
			}

			if (empty($qty)) {
				$errs[] = ['ean' => $prod->ean, 'sku' => $prod->getSku($this->user->org_id), 'code' => 301, 'message' => 'No stock at this date'];
			}
			} else {
				$qty = 0;
				$stocks = WmsStock::model()->findAll('prod_id = :pid AND t.org_id = :oid', [':pid' => $prod->id, ':oid' => $this->user->org_id]);
				foreach ($stocks as $stock) {
					$this->out['stocks'][] = [
						'ean' => $stock->prod->ean,
						'sku' => $stock->prod->getSku($this->user->org_id),
						'name' => $stock->prod->name,
						'expiry' => '',
						'prodDate' => '',
						'batch' => '',
						'date' => date('Y-m-d'),
						'warehouse' => [
							'code' => $this->data['warehouse'],
							'stock' => $stock->getQty(),
							'lock' => 0,
							'total' => $stock->getQty(),
						]
					];
					$qty += $stock->getQty();
				}

				if (empty($qty)) {
					$errs[] = ['ean' => $prod->ean, 'sku' => $prod->getSku($this->user->org_id), 'code' => 301, 'message' => 'No stock at this date'];
				}
			}
		}

		if (!empty($errs)) {
			$this->out['errors'] = $errs;
		}
	}

	public function stock_put()
	{
		if (empty($this->data['to'])) {
			$this->errors[] = ['to' => !empty($this->data['to']) ? $this->data['to'] : '', 'code' => 201, 'message' => 'Transfer organization info incomplete'];
			return;
		}
		$allow = [
			Org::ORGID_AIRSEA_WGAU,
			Org::ORGID_AIRSEA_HOUPU,
			Org::ORGID_AIRSEA_MINENSSEY,
			Org::ORGID_AIRSEA_WTMANAGE,
		];
		if (!in_array($this->data['to'], $allow)) {
			$this->errors[] = ['to' => !empty($this->data['to']) ? $this->data['to'] : '', 'code' => 201, 'message' => 'Transfer organization info incomplete'];
			return;
		}
		if (empty($this->data['warehouse'])) {
			$this->data['warehouse'] = 'all warehouse';
		}

		$this->out = ['to' => $this->data['to'], 'warehouse' => $this->data['warehouse'], 'items' => []];
		$errs = [];

		if (empty($this->data['items'])) {
			$errs[] = ['ean' => '', 'sku' => '', 'name' => '', 'expiry' => '', 'batch' => '', 'qty' => '', 'code' => 110, 'message' => 'Product info incomplete'];
		}

		foreach ($this->data['items'] as $item) {
			if (empty($item['ean']) && empty($item['sku'])) {
				$errs[] = ['ean' => !empty($item['ean']) ? $item['ean'] : '', 'sku' => !empty($item['sku']) ? $item['sku'] : '', 'name' => !empty($item['name']) ? $item['name'] : '', 'expiry' => !empty($item['expiry']) ? $item['expiry'] : '', 'batch' => !empty($item['batch']) ? $item['batch'] : '', 'qty' => !empty($item['qty']) ? $item['qty'] : '', 'code' => 110, 'message' => 'Product info incomplete'];
				continue;
			}
			if (empty($item['qty'])) {
				$errs[] = ['ean' => !empty($item['ean']) ? $item['ean'] : '', 'sku' => !empty($item['sku']) ? $item['sku'] : '', 'name' => !empty($item['name']) ? $item['name'] : '', 'expiry' => !empty($item['expiry']) ? $item['expiry'] : '', 'batch' => !empty($item['batch']) ? $item['batch'] : '', 'qty' => '', 'code' => 110, 'message' => 'Product info incomplete'];
				continue;
			}

			if (!empty($item['ean'])) {
				$prod = WmsProd::model()->find('ean = :ean', [':ean' => $item['ean']]);
			}
			if (!empty($item['sku']) && empty($prod)) {
				$prod = WmsProd::model()->with('orgs')->find('orgs.sku = :sku AND org_id = :oid', [':sku' => $item['sku'], ':oid' => $this->user->org_id]);
			}
			if (empty($prod)) {
				$errs[] = ['ean' => !empty($item['ean']) ? $item['ean'] : '', 'sku' => !empty($item['sku']) ? $item['sku'] : '', 'name' => !empty($item['name']) ? $item['name'] : '', 'expiry' => !empty($item['expiry']) ? $item['expiry'] : '', 'batch' => !empty($item['batch']) ? $item['batch'] : '', 'qty' => !empty($item['qty']) ? $item['qty'] : '', 'code' => 101, 'message' => 'Product not found'];
				continue;
			}

			$cond = 'prod_id = :pid AND org_id = :oid AND qty - qty_res > 0';
			$params = [':pid' => $prod->id, ':oid' => $this->user->org_id];
			if (!empty($item['expiry'])) {
				$cond .= ' AND expiry = :ex';
				$params[':ex'] = $item['expiry'];
			}
			if (!empty($item['batch'])) {
				$cond .= ' AND batch = :bn';
				$params[':bn'] = $item['batch'];
			}

			$count = 0;
			$stocks = WmsStock::model()->findAll($cond, $params);
			foreach ($stocks as $stock) {
				$count += $stock->qty - $stock->qty_res;
			}

			if ($count < $item['qty']) {
				$errs[] = ['ean' => !empty($item['ean']) ? $item['ean'] : '', 'sku' => !empty($item['sku']) ? $item['sku'] : '', 'name' => !empty($item['name']) ? $item['name'] : '', 'expiry' => !empty($item['expiry']) ? $item['expiry'] : '', 'batch' => !empty($item['batch']) ? $item['batch'] : '', 'qty' => $item['qty'], 'code' => 102, 'message' => 'Request qty exceed limt, only have ' . $count];
				continue;
			}

			// transfer from
			$job = WmsJob::model()->find(['condition' => 'org_id = :org_id AND type = 40', 'params' => [':org_id' => $this->user->org_id], 'order' => 'id desc']);
			if (empty($job)) {
				$job = new WmsJob;
				$job->org_id = $this->user->org_id;
				$job->type = 40;
				$job->status = 10;
				$job->save();
			}

			$task = new WmsTask;
			$task->job_id = $job->id;
			$task->type = 3030;
			$task->is_request = 1;
			$task->op_id = $this->user->id;
			$task->status = 20;
			$task->ref = 'stock transfer to ' . Org::model()->findByPk($this->data['to'])->shortName() . ' ' . date('Y-m-d');
			$task->bwf = 16;
			$items = [];
			$qty = $item['qty'];
			foreach ($stocks as $stock) {
				if ($qty <= 0) break;
				foreach ($stock->locs as $loc) {
					$witem = array(
						'si' => $stock->id,
						'sn' => $stock->prod->name,
						'pq' => '',
						'cq' => '',
						'uq' => min($qty, $loc->qty),
						'pli' => '',
						'pl' => $loc->loc->code,
						'nt' => '',
					);
					$items[] = $witem;
					$qty -= $witem['uq'];
				}
			}
			$task->new_items = $items;

			$transfers = [];
			if ($task->save()) {
				$ac_task = $task->actionTask;
				foreach ($task->items as $witem) {
					$itm = new WmsTaskItem;
					$itm->task_id = $ac_task->id;
					$loc = WmsLocation::model()->find('name = :name', [':name' => $witem->mdata['pl']]);
					$itm->mdata = array(
						'si' => $witem->mdata['si'],
						'sn' => $witem->mdata['sn'],
						'uq' => $witem->mdata['uq'],
						'pli' => $loc->id,
						'pl' => $loc->code,
					);
					$itm->save();
					$qty -= $witem->mdata['uq'];
					$transfers[] = $itm;
				}
				$ac_task->save();
				$task->status = array_search('WIP', WmsTask::$states);
				$task->save();
			}

			// transfer to
			$job = WmsJob::model()->find(['condition' => 'org_id = :org_id AND type = 40', 'params' => [':org_id' => $this->data['to']], 'order' => 'id desc']);
			if (empty($job)) {
				$job = new WmsJob;
				$job->org_id = $this->data['to'];
				$job->type = 40;
				$job->status = 10;
				$job->save();
			}

			$task = new WmsTask;
			$task->job_id = $job->id;
			$task->type = 1010;
			$task->is_request = 1;
			$task->op_id = $this->user->id;
			$task->status = 20;
			$task->ref = 'stock transfer from ' . Org::model()->findByPk($this->user->org_id)->shortName() . ' ' . date('Y-m-d');
			$task->bwf = 16;
			$items = [];
			foreach ($transfers as $witem) {
				$stock = WmsStock::model()->findByPk($witem->mdata['si']);
				$items[] = array(
					'gi' => $stock->prod->id,
					'gn' => $witem->mdata['sn'],
					'uq' => $witem->mdata['uq'],
				);
			}
			$task->new_items = $items;

			if ($task->save()) {
				$ac_task = $task->actionTask;
				foreach ($transfers as $witem) {
					$stock = WmsStock::model()->findByPk($witem->mdata['si']);
					$itm = new WmsTaskItem;
					$itm->task_id = $ac_task->id;
					$itm->mdata = array(
						'gi' => $stock->prod->id,
						'gn' => $stock->prod->name,
						'uq' => $witem->mdata['uq'],
						'pl' => $witem->mdata['pl'],
						'cq' => '',
						'ex' => $stock->expiry,
						'bn' => $stock->batch,
						'nt' => '',
					);
					$itm->save();
				}
				$ac_task->save();
				$task->status = array_search('WIP', WmsTask::$states);
				$task->save();
			}

			$this->out['items'][] = ['ean' => !empty($item['ean']) ? $item['ean'] : '', 'sku' => !empty($item['sku']) ? $item['sku'] : '', 'name' => !empty($item['name']) ? $item['name'] : '', 'expiry' => !empty($item['expiry']) ? $item['expiry'] : '', 'batch' => !empty($item['batch']) ? $item['batch'] : '', 'qty' => $item['qty']];
		}

		if (!empty($errs)) {
			$this->out['errors'] = $errs;
		}
	}

	public function errorResponse()
	{
		$this->response(['errors' => $this->errors]);
	}

	public function response($o)
	{
		echo json_encode($o);
		$this->log(json_encode($o));
		Yii::app()->end();
	}

	public function log($l)
	{
		if (empty($l)) {
			return;
		}

		$tmp = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'wmsapi' . DIRECTORY_SEPARATOR;
		file_put_contents($tmp . 'wms_api_' . date('Y-m-d') . '.log', date('Y-m-d H:i:s') . ' ' . $l . "\n\n", FILE_APPEND);
	}

	protected function _mapFields(&$o, &$t, $map)
	{
		foreach ($map as $k => $mk) {
			if (isset($o[$k])) {
				$t[$mk] = $o[$k];
			}
		}
	}

	//data translators for clients
	public function dt_ufl()
	{
		$d = ['api_hook' => 'ufl', 'api_meta' => ['customer' => $this->data['customer']]];

		if (in_array($this->mod, ['order_post', 'order_put'])) {
			$mapFileType = function ($t) {
				switch ($t) {
					case 'ShippingLabel':
						$r = 10;
						break;
					case 'CommercialInvoice':
						$r = 20;
						break;
					case 'PackingSlip':
						$r = 30;
						break;
					default:
						$r = 90;
						break;
				}
				return $r;
			};

			if (!empty($this->data['orders'])) {
				$d['orders'] = [];
				foreach ($this->data['orders'] as $odr) {
					$od = [
						'no' => $odr['orderNo'],
						'type' => 10,
						'confirmed' => true,
						'items' => [],
						'to' => [
							'name' => @$odr['orderConsigneeName'],
							'company' => @$odr['orderConsigneeCompany'],
							'state' => @$odr['orderConsigneeState'],
							'suburb' => @$odr['orderConsigneeCity'],
							'postcode' => @$odr['orderConsigneePostalCode'],
							'address' => @$odr['orderConsigneeAddr1'] . (empty($odr['orderConsigneeAddr2']) ? '' : ' ' . $odr['orderConsigneeAddr2']),
							'phone' => @$odr['orderConsigneePhone'],
							'email' => @$odr['orderConsigneeEmail'],
							'country' => @$odr['orderConsigneeCountry'],
						],
						'attachments' => [],
					];

					if (!empty($odr['sourceLinkId'])) {
						$od['sourceLinkId'] = $odr['sourceLinkId'];
						if (!empty($odr['orderSPT'])) {
							$od['orderSPT'] = $odr['orderSPT'];
						}
					}

					$this->_mapFields($odr, $od, ['courierName' => 'freight_co', 'courierBillNo' => 'connote_no', 'remarkforDelivery' => 'shipping_note', 'orderTotalAmt' => 'total']);

					//items
					foreach ($odr['parts'] as $p) {
						$this->_mapFields($p, $od['items'][], ['partNo' => 'ean', 'vendorCode' => 'sku', 'vendorName' => 'name', 'partQty' => 'qty', 'PartUnitPrice' => 'price']);
					}

					//attachments
					if (!empty($odr['orderFiles'])) {
						foreach ($odr['orderFiles'] as $f) {
							$od['attachments'][] = [
								'type' => $mapFileType(@$f['usage']),
								'name' => empty($f['fileName']) ? @$f['usage'] . '.' . @$f['format'] : $f['fileName'],
								'content' => @$f['baseContent'],
							];
						}
					}
					$d['orders'][] = $od;
				}
				$this->data = $d;
			}
		}
	}

	//version 0 functions, to be deprecated
	public function order_post0()
	{
		$data = json_decode(urldecode($this->data['data']), true);

		$errors = [];
		if (empty($data['product'])) {
			$this->out = ['msg' => 'Product is empty'];
		} else if (empty($data['cnee'])) {
			$this->out = ['msg' => 'Cnee is empty'];
		} else if (empty($data['ref'])) {
			$this->out = ['msg' => 'Ref is empty'];
		} else {
			$products = [];
			foreach ($data['product'] as $v) {
				if (empty($v['ean'])) {
					$errors[] = 'ean is empty';
					continue;
				} else if (empty($v['qty'])) {
					$errors[] = 'qty is empty';
					continue;
				}

				$product = WmsProd::model()->find('ean = :ean AND status = 1', [':ean' => $v['ean']]);
				if (empty($product)) {
					$errors[] = $v['ean'] . ' is not found';
					continue;
				}

				if (empty($products[$product->id])) {
					$products[$product->id] = 0;
				}
				$products[$product->id] += $v['qty'];
			}

			$items = [];
			foreach ($products as $id => $qty) {
				$stock = WmsStock::model()->find(['select' => 'SUM(qty) AS qty, SUM(qty_res) AS qty_res', 'condition' => 'prod_id = :prod_id AND org_id = :org_id', 'params' => [':prod_id' => $id, ':org_id' => $this->user->org_id]]);
				if ($stock->qty - $stock->qty_res < $qty) {
					$errors[] = $stock->prod->ean . ' qty is not enough';
				} else {
					$stocks = WmsStock::model()->findAll('prod_id = :prod_id AND org_id = :org_id AND qty - qty_res > 0', [':prod_id' => $id, ':org_id' => $this->user->org_id]);
					foreach ($stocks as $stock) {
						if ($qty <= 0) {
							break;
						}
						$items[] = array(
							'si' => $stock->id,
							'sn' => $stock->prod->name,
							'pq' => '',
							'cq' => '',
							'uq' => min($qty, $stock->availQty()),
							'pli' => '',
							'pl' => '',
							'nt' => '',
						);
						$qty -= min($qty, $stock->availQty());
					}
				}
			}

			$cnee = [];
			if (empty($data['cnee']['name'])) {
				$errors[] = 'Cnee name is empty';
			}
			if (empty($data['cnee']['tel'])) {
				$errors[] = 'Cnee tel is empty';
			}
			if (empty($data['cnee']['address'])) {
				$errors[] = 'Cnee address is empty';
			}
			if (empty($data['cnee']['suburb'])) {
				$errors[] = 'Cnee suburb is empty';
			}
			if (empty($data['cnee']['state'])) {
				$errors[] = 'Cnee state is empty';
			}
			if (empty($data['cnee']['postcode'])) {
				$errors[] = 'Cnee postcode is empty';
			}
			if (!Postcode::validateAddress($data['cnee']['suburb'], $data['cnee']['state'], $data['cnee']['postcode'])) {
				$errors[] = 'Error Address';
			}

			if (!empty($errors)) {
				$this->out = ['success' => false, 'msg' => $errors];
			} else {
				$job = WmsJob::model()->find('org_id = :org_id AND type = 30 AND week(`created`, 1) = :week', [':org_id' => $this->user->org_id, ':week' => intval(date('W'))]);
				if (empty($job)) {
					$job = new WmsJob;
					$job->org_id = $this->user->org_id;
					$job->type = 30;
					$job->status = 10;
					$job->ref = '3PL_' . date('Y-m-d');
					$job->save();
				}

				$task = WmsTask::model()->find('ref = :ref', [':ref' => $data['ref']]);
				if (empty($task)) {
					$task = new WmsTask;
					$task->job_id = $job->id;
					$task->type = 3030;
					$task->is_request = 1;
					$task->op_id = 0;
					$task->status = 20;
					$task->ref = $data['ref'] . ' API test';
					$task->new_items = $items;
					$task->save();

					$task->pickupTask->type = 2120;
					$delivery_task = $task->pickupTask;
					$delivery_task->mdata['cnee'] = $data['cnee'];
					$delivery_task->save();
					$this->out = ['success' => true, 'order_id' => $task->id];
				} else {
					$errors[] = 'Task with ref:' . $task->ref . ' has been created';
					$this->out = ['success' => false, 'order_id' => $task->id, 'msg' => $errors];
				}
			}
		}
	}

	public function order_get0()
	{
		$data = json_decode($this->data['data'], true);
		$tasks = [];
		foreach (['order_id', 'ref'] as $key) {
			if (empty($data[$key])) {
				continue;
			}

			if (is_string($data[$key]) || is_numeric($data[$key])) {
				$data[$key] = [$data[$key]];
			}

			if (is_array($data[$key])) {
				foreach ($data[$key] as $n) {
					$task = $key == 'order_id' ? WmsTask::model()->findByPk($n) : WmsTask::model()->find('ref = :ref', [':ref' => $n]);

					if (!empty($task)) {
						$tasks[] = $task;
					}
				}
			}
		}

		$results = [];
		if (!empty($tasks)) {
			foreach ($tasks as $task) {
				$tracking_no = [];
				if (!empty($task->deliveryTask) && !empty($task->deliveryTask->mdata['shipment_id'])) {
					foreach ($task->deliveryTask->mdata['shipment_id'] as $sid) {
						$shipment = Shipment::model()->findByPk($sid);
						$tracking_no[] = $shipment->ref;
					}
				}
				if (!empty($task->deliveryTask->mdata['shipment_courier_id'])) {
					if ($task->deliveryTask->mdata['shipment_courier_id'] == 115) {
						$courier = 'Fastway';
					} else if ($task->deliveryTask->mdata['shipment_courier_id'] == 101) {
						$courier = 'Auspost';
					}
				}
				$results[] = ['order_id' => $task->id, 'ref' => $task->ref, 'status' => WmsTask::$states[$task->status], 'tracking_no' => $tracking_no, 'tracking_courier' => @$courier];
			}
		}
		if (empty($results)) {
			$this->out = ['msg' => 'No task found'];
		} else {
			$this->out = $results;
		}
	}

	public function stock_get0()
	{
		$data = json_decode($this->data['data'], true);
		$stocks = [];
		foreach (['ean', 'name'] as $key) {
			if (empty($data[$key])) {
				continue;
			}

			if (is_string($data[$key])) {
				$data[$key] = [$data[$key]];
			}

			if (is_array($data[$key])) {
				foreach ($data[$key] as $n) {
					$ss = $key == 'ean' ? WmsStock::model()->with('prod', 'prod.orgs')->findAll('prod.status = 1 AND (prod.ean = :n OR (orgs.sku = :n AND orgs.org_id = :org_id))', [':n' => $n, ':org_id' => $this->user->org_id]) : WmsStock::model()->with('prod')->find('prod.status = 1 AND (prod.name = :name OR prod.name_zh = :name', [':name' => $n]);

					if (!empty($ss)) {
						$stocks = array_merge($stocks, $ss);
					}
				}
			}
		}
		// search all
		if (empty($stocks)) {
			$stocks = WmsStock::model()->findAll('org_id = :org_id', [':org_id' => $this->user->org_id]);
		}
		// process result
		$results = [];
		if (!empty($stocks)) {
			foreach ($stocks as $stock) {
				$product_org = WmsProdOrg::model()->find('prod_id = :prod_id AND org_id = :org_id', [':prod_id' => $stock->prod_id, ':org_id' => $this->user->org_id]);
				$results[] = ['name' => $stock->prod->name, 'name_zh' => $stock->prod->name_zh, 'ean' => $stock->prod->ean, 'sku' => @$product_org->sku, 'qty' => $stock->qty, 'qty_res' => $stock->qty_res, 'expiry' => $stock->expiry, 'batch' => $stock->batch];
			}
		}
		if (empty($results)) {
			$this->out = ['msg' => 'No stock found'];
		} else {
			$this->out = $results;
		}
	}

	public function product_get0()
	{
		$data = json_decode($this->data['data'], true);
		// search through name
		if (!empty($data['name'])) {
			$products = WmsProd::model()->findAll('(name = :name OR name_zh = :name) AND status = 1', [':name' => $data['name']]);
		}
		// search through ean
		if (empty($products) && !empty($data['ean'])) {
			$products = WmsProd::model()->with('orgs')->findAll('(t.ean = :ean OR (orgs.sku = :ean AND orgs.org_id = :org_id)) AND t.status = 1', [':ean' => $data['ean'], ':org_id' => $this->user->org_id]);
		}
		// search all
		if (empty($products)) {
			$product_orgs = WmsProdOrg::model()->findAll('org_id = :org_id', [':org_id' => $this->user->org_id]);
			foreach ($product_orgs as $product) {
				$products[$product->prod->id] = $product->prod;
			}
			// $products = [];
			// $stocks = WmsStock::model()->findAll('org_id = :org_id', [':org_id' => $this->user->org_id]);
			// foreach ($stocks as $stock) {
			//     $products[$stock->prod_id] = $stock->prod;
			// }
		}
		// process result
		$results = [];
		if (!empty($products)) {
			foreach ($products as $product) {
				$product_org = WmsProdOrg::model()->find('prod_id = :prod_id AND org_id = :org_id', [':prod_id' => $product->id, ':org_id' => $this->user->org_id]);
				$results[] = ['name' => $product->name, 'name_zh' => $product->name_zh, 'ean' => $product->ean, 'sku' => @$product_org->sku];
			}
		}
		// return
		if (empty($results)) {
			$this->out = ['msg' => 'No product found'];
		} else {
			$this->out = $results;
		}
	}

	public function return_post()
	{
		foreach ($this->data['returns'] as $return) {
			$job = WmsJob::model()->find('t.org_id = :org_id AND date(`created`) = :day', [':org_id' => $this->user->org_id, ':day' => date('Y-m-d')]);
			if (empty($job)) {
				$job = new WmsJob;
				$job->org_id = $this->user->org_id;
				$job->type = 10;
				$job->status = 10;
				$job->ref = '3PL_' . date('Y-m-d');
				$job->save();
			}

			$task = WmsTask::model()->with('job')->find('job.org_id = :org_id AND t.ref = :ref AND t.type = :type', [':org_id' => $this->user->org_id, ':ref' => $return['ref'], ':type' => WmsTask::TYPE_RETURN]);
			if (!empty($task)) {
				$errs[] = ['no' => '', 'code' => 110, 'message' => 'Reference No. already exists'];
				continue;
			}

			if (empty($return['ref'])) {
				$errs[] = ['no' => '', 'code' => 110, 'message' => 'Reference is missing'];
				continue;
			}

			if (empty($return['action'])) {
				$errs[] = ['no' => '', 'code' => 110, 'message' => 'Action is missing'];
				continue;
			}

			if (empty($return['from'])) {
				$errs[] = ['no' => '', 'code' => 110, 'message' => 'Sender is missing'];
				continue;
			}

			if (empty($return['items'])) {
				$errs[] = ['no' => '', 'code' => 110, 'message' => 'Items is missing'];
				continue;
			}

			if (empty($return['weight'])) {
				$errs[] = ['no' => '', 'code' => 110, 'message' => 'Weight is missing'];
				continue;
			}

			$task = new WmsTask;
			$task->job_id = $job->id;
			$task->type = WmsTask::TYPE_RETURN;
			$task->is_request = 1;
			$task->status = 10;
			$task->ref = $return['ref'];
			$task->mdata['note'] = @$return['note'];
			$task->mdata['return_option'] = $return['action'];
			$task->mdata['weight'] = $return['weight'];
			if ($task->save()) {
				foreach ($return['items'] as $item) {
					$wti = new WmsTaskItem;
					$wti->task_id = $task->id;
					$wti->mdata = [
						'sn' => @$item['name'],
						'sku' => @$item['sku'],
						'uq' => @$item['qty'],
						'ean' => @$item['ean'],
						'price' => @$item['price'],
					];
					$wti->save();
				}
				$delivery = $task->deliveryTask;
				$delivery->mdata['cnee'] = $return['from'];
				if ($delivery->save()) {
					$delivery->createReturnLabel(explode(',', $return['weight']));

					if (!empty($delivery->mdata['return_shipment_id'])) {
						foreach ($delivery->mdata['return_shipment_id'] as $rsi) {
							$r = Shipment::model()->findByPk($rsi);
							$this->out['returns'][] = ['ref' => $task->ref, 'id' => $task->id, 'connote' => $r->ref];
						}
					}
				}
			}
		}

		if (!empty($errs)) {
			$this->out['errors'] = $errs;
		}
	}

	public function return_put()
	{
		foreach ($this->data['returns'] as $return) {
			$task = WmsTask::model()->with('job')->find('job.org_id = :org_id AND t.ref = :ref AND t.type = :type', [':org_id' => $this->user->org_id, ':ref' => $return['ref'], ':type' => WmsTask::TYPE_RETURN]);
			if (empty($task)) {
				$errs[] = ['no' => '', 'code' => 110, 'message' => 'Reference No. does not exist'];
				continue;
			}

			if (!empty($task->deliveryTask->mdata['return_shipment_id'])) {
					foreach ($task->deliveryTask->mdata['return_shipment_id'] as $rsi) {
						$r = Shipment::model()->findByPk($rsi);
						if (preg_match('/\[M\]/', $r->getStatus())) {
							$errs[] = ['no' => '', 'code' => 110, 'message' => 'Shipment has already manifested. Task can\'t be updated'];
							continue 2;
						}
					}
			}

			if (empty($return['action'])) {
				$errs[] = ['no' => '', 'code' => 110, 'message' => 'Action is missing'];
				continue;
			}

			if (empty($return['from'])) {
				$errs[] = ['no' => '', 'code' => 110, 'message' => 'Sender is missing'];
				continue;
			}

			if (empty($return['items'])) {
				$errs[] = ['no' => '', 'code' => 110, 'message' => 'Items is missing'];
				continue;
			}

			if (empty($return['weight'])) {
				$errs[] = ['no' => '', 'code' => 110, 'message' => 'Weight is missing'];
				continue;
			}

			$task->mdata['note'] = @$return['note'];
			$task->mdata['return_option'] = $return['action'];
			$task->mdata['weight'] = $return['weight'];
			if ($task->save()) {
				foreach ($task->items as $item) {
					$item->del = 1;
					$item->save();
				}

				foreach ($return['items'] as $item) {
					$wti = new WmsTaskItem;
					$wti->task_id = $task->id;
					$wti->mdata = [
						'sn' => @$item['name'],
						'sku' => @$item['sku'],
						'uq' => @$item['qty'],
						'ean' => @$item['ean'],
						'price' => @$item['price'],
					];
					$wti->save();
				}
				$delivery = $task->deliveryTask;
				$delivery->mdata['cnee'] = $return['from'];
				$delivery->mdata['return_shipment_id'] = [];
				if ($delivery->save()) {
					$delivery->createReturnLabel(explode(',', $return['weight']));

					if (!empty($delivery->mdata['return_shipment_id'])) {
						foreach ($delivery->mdata['return_shipment_id'] as $rsi) {
							$r = Shipment::model()->findByPk($rsi);
							$this->out['returns'][] = ['ref' => $task->ref, 'id' => $task->id, 'connote' => $r->ref];
						}
					}
				}
			}
		}

		if (!empty($errs)) {
			$this->out['errors'] = $errs;
		}
	}

	public function return_get()
	{
		if (!empty($this->data['id'])) {
			$tasks = WmsTask::model()->findAll('id IN (' . implode(',', $this->data['id']) . ')');
		} else if (!empty($this->data['ref'])) {
			$tasks = WmsTask::model()->findAll('ref IN ("' . implode('","', $this->data['ref']) . '")');
		} else if (!empty($this->data['connote'])) {
			$shipments = Shipment::model()->findAll('ref IN ("' . implode('","', $this->data['connote']) . '")');
		} else {
			$errs[] = ['no' => '', 'code' => 110, 'message' => 'ID and Ref are missing'];
		}

		$temp = [];
		if (!empty($tasks)) {
			foreach ($tasks as $task) {
				if (empty($task->deliveryTask->mdata['return_shipment_id'])) continue;

				foreach ($task->deliveryTask->mdata['return_shipment_id'] as $rsi) {
					$r = Shipment::model()->findByPk($rsi);
					$temp[$task->ref] = $r;
				}
			}
		}

		if (!empty($shipments)) {
			foreach ($shipments as $shipment) {
				$task = WmsTask::model()->find(ltrim($shipment->cref, 'T'));
				$temp[$task->ref] = $shipment;
			}
		}

		foreach ($temp as $ref => $shipment) {
			if (!empty($this->data['link'])) {
				$this->out['returns'][] = ['ref' => $ref, 'connote' => $shipment->ref, 'link' => "https://www.pcaexpress.com.au/client/return/" . hash('crc32b', $shipment->ref . '#pca3plReturn$') . "/" . $shipment->ref . ".html"];
			} else if (!empty($this->data['size']) && $this->data['size'] == 'A4') {
				$this->out['returns'][] = ['ref' => $ref, 'connote' => $shipment->ref, 'pdf' => base64_encode(oPDF::renderPDF('label_A4', ['rs' => [$shipment]], 0))];
			} else {
				$this->out['returns'][] = ['ref' => $ref, 'connote' => $shipment->ref, 'pdf' => base64_encode(oPDF::renderPDF('label_A6', ['rs' => [$shipment]], 0))];
			}
		}

		if (!empty($errs)) {
			$this->out['errors'] = $errs;
		}
	}

}
