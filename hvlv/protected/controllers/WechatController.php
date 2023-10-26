<?php

class WechatController extends PController {

	public function actionIndex() {
		$weObj = new WechatAPI();
		$result = $weObj->valid();

		$type = $weObj->getRev()->getRevType();
		switch ($type) {
			case WechatAPI::MSGTYPE_TEXT:
				$this->receiveMsg($weObj, 'text');
				break;
			case WechatAPI::MSGTYPE_IMAGE:
				$this->receiveMsg($weObj, 'pic');
				break;
			case WechatAPI::MSGTYPE_VOICE:
				$this->receiveMsg($weObj, 'voice');
				break;
			case WechatAPI::MSGTYPE_EVENT:
				$this->receiveEvent($weObj);
				break;
			default:
				$this->receiveOther($weObj);
				break;
		}

		return $result;
	}

	private function receiveMsg($weObj, $type) {
		$crm_msg = CrmMsg::model()->find('wechat_id = :wechat_id', array(':wechat_id' => $weObj->getRevFrom()));
		if (empty($crm_msg)) {
			$crm_msg = new CrmMsg;
			$crm_msg->wechat_id = $weObj->getRevFrom();
			$crm_msg->wechat_name = $weObj->getUserInfo($crm_msg->wechat_id)['nickname'];
			$crm_msg->status = CrmMsg::STATUS_ACTIVE;
			$crm_msg->create_time = date('Y-m-d H:i:s', $weObj->getRevCtime());
			$crm_msg->save();
		}
		if ($crm_msg->status == CrmMsg::STATUS_CLOSED) {
			$crm_msg->status = CrmMsg::STATUS_ACTIVE;
		}
		$crm_msg->update_time = date('Y-m-d H:i:s', $weObj->getRevCtime());
		$crm_msg->save();

		// eliminate dup
		$dup_crm_msg = CrmMsg::model()->find('wechat_id = :wechat_id', array(':wechat_id' => $weObj->getRevFrom()));
		$crm_msg = CrmMsg::model()->findByPk($crm_msg->id);
		if (!empty($dup_crm_msg) && ($crm_msg->id != $dup_crm_msg->id)) {
			foreach ($crm_msg->lines as $line) {
				$line->crm_msg_id = $dup_crm_msg->id;
				$line->save();
			}
			$crm_msg->status = CrmMsg::STATUS_DUP;
			$crm_msg->save();
		}

		$crm_msg_line = CrmMsgLine::model()->find('wechat_msg_id = :wechat_msg_id AND crm_msg_id = :crm_msg_id', array(':wechat_msg_id' => $weObj->getRevID(), ':crm_msg_id' => $dup_crm_msg->id));
		if (!empty($crm_msg_line)) return;
		$crm_msg_line = new CrmMsgLine;
		$crm_msg_line->crm_msg_id = $dup_crm_msg->id;
		$crm_msg_line->time = date('Y-m-d H:i:s', $weObj->getRevCtime());
		$crm_msg_line->type = array_search($type, CrmMsgLine::$types);
		$crm_msg_line->wechat_msg_id = $weObj->getRevID();
		$crm_msg_line->mdata['wechat'] = $weObj->getRevFrom();
		$crm_msg_line->save();

		if ($type == 'text') {
			$crm_msg_line->mdata['text'] = $weObj->getRevContent();
			if (preg_match_all('/(EAU\d+|DAU\d+|PE\d+|PV\d+)/i', $crm_msg_line->mdata['text'], $matches)) {
				$nos = $matches[1];
				$reply_msg = '您好，';
				foreach ($nos as $no) {
					$shipment = Shipment::model()->find('hbn = :hbn', array(':hbn' => $no));
					if (empty($shipment)) {
						$reply_msg .= '快递单号 ' . $no . ' 不存在。';
					} else {
						$reply_msg .= '点击查询快递信息 <a href="https://www.pcaexpress.com.au/tracking/?c=' . $no . '">' . $no . '</a>。';
					}
				}
				$weObj->text($reply_msg)->reply();
				$auto_crm_msg_line = new CrmMsgLine;
				$auto_crm_msg_line->crm_msg_id = $dup_crm_msg->id;
				$auto_crm_msg_line->time = date('Y-m-d H:i:s', $weObj->getRevCtime());
				$auto_crm_msg_line->type = CrmMsgLine::TYPE_AUTO;
				$auto_crm_msg_line->mdata['operator_id'] = 1;
				$auto_crm_msg_line->save();
			}
		} else if ($type == 'pic') {
			$crm_msg_line->mdata['mediaid'] = $weObj->getRevPic()['mediaid'];

			// get media
			$weObj = new WechatAPI();
			$weObj->checkAuth();
			$file = $weObj->getMedia($crm_msg_line->mdata['mediaid']);

			// get type
			$bin = substr($file,0,2);
			$strInfo = @unpack("C2chars", $bin);
			$typeCode = intval($strInfo['chars1'] . $strInfo['chars2']);
			switch ($typeCode) {
				case 255216:
					$fileType = 'jpg';
					break;
				case 7173:
					$fileType = 'gif';
					break;
				case 6677:
					$fileType = 'bmp';
					break;
				case 13780:
					$fileType = 'png';
					break;
			}

			// save pic
			$f = tempnam(Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR, 'pp');
			file_put_contents($f, $file);
			$id = FileRepo::storeFile($f, 'P' . date('YmdHis') . '.' . $fileType, 101, $crm_msg_line->id);
			$file = FileRepo::model()->findByPk($id);
			unlink($f);

			$crm_msg_line->mdata['pic'] = $file->getUrl();
		} else if ($type == 'voice') {
			$crm_msg_line->mdata['mediaid'] = $weObj->getRevVoice()['mediaid'];

			// get media
			$weObj = new WechatAPI();
			$weObj->checkAuth();
			$file = $weObj->getMedia($crm_msg_line->mdata['mediaid']);

			// convert
			$file = $crm_msg_line->amr2m4a($file);

			// save audio
			$f = tempnam(Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR, 'pp');
			file_put_contents($f, $file);
			$id = FileRepo::storeFile($f, 'P' . date('YmdHis') . '.m4a', 101, $crm_msg_line->id);
			$file = FileRepo::model()->findByPk($id);
			unlink($f);

			$crm_msg_line->mdata['voice'] = $file->getUrl();
		}

		$crm_msg_line->save();
	}

	private function receiveEvent($weObj) {
		$event = $weObj->getRevEvent();

		if ($event['key'] == 'AU_PHONE') {
			$weObj->text('+61299257100')->reply();
		} else if ($event['key'] == 'CN_PHONE') {
			$weObj->text('95040315891')->reply();
		} else {
			$weObj->text('Welcome')->reply();
		}
	}

	private function receiveOther($weObj) {
		$weObj->text('Welcome')->reply();
	}
}