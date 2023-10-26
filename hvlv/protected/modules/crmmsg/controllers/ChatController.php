<?php

class ChatController extends Controller {

	/**
	 * Declares class-based actions.
	 */
	protected $nonAjax = array();
	protected $skipAcl = array();

	private $test_account = 'o-C-Zjho9516ToGK9yniP86mPKSI';
	// private $test_account = 'ootT50YCGLFPhpz5Wv-PU7-qxlTk';

	/**
     * @param CAction $action
     * @return bool
     */
	public function beforeAction($action) {
		if(!Yii::app()->request->isAjaxRequest) $this->layout = 'crm';
		return parent::beforeAction($action);
	}

	public function actionIndex() {
		if (!empty($_GET['test']) && $_GET['test'] == '54gvvfbj4fwfg4j4wgcdvbw45w34gh5') {
			$this->render('index', array('test' => '54gvvfbj4fwfg4j4wgcdvbw45w34gh5'));
		} else {
			$this->render('index');
		}
	}

	public function actionList() {
		$this->render('list');
	}

	public function actionRenderPanel() {
		$assigned_cms = CrmMsg::model()->findAll('status = 2');
		foreach ($assigned_cms as $crm_msg) {
			$last_line = $crm_msg->lines[count($crm_msg->lines)-1];
			if (((strtotime(date('Y-m-d H:i:s')) - strtotime($last_line->time)) > 0.5 * 60 * 60) && empty($last_line->mdata['operator_id'])) {
				$crm_msg->status = 1;
				$crm_msg->operator_id = 0;
				$crm_msg->save();
			} else if (((strtotime(date('Y-m-d H:i:s')) - strtotime($last_line->time)) > 3 * 60 * 60) && !empty($last_line->mdata['operator_id'])) {
				$crm_msg->status = 99;
				$crm_msg->operator_id = 0;
				$crm_msg->save();
			}
		}

		$new_cms = CrmMsg::model()->findAll('status = 1');
		foreach ($new_cms as $crm_msg) {
			$last_line = $crm_msg->lines[count($crm_msg->lines)-1];
			if (!empty($last_line->mdata['operator_id']) && $last_line->mdata['operator_id'] == 1) {
				$crm_msg->status = 99;
				$crm_msg->save();
			}
		}

		$type = $_GET['type'];
		if ($type == 1) {
			$crm_msgs = array_merge(CrmMsg::model()->findAll(array('condition' => '((operator_id = :op_id AND status = 2) OR (operator_id = 0 AND status = 1)) AND wechat_id != "' . $this->test_account . '"', 'params' => array(':op_id' => Yii::app()->user->id), 'order' => 'update_time desc')), CrmMsg::model()->findAll(array('condition' => '(operator_id != :op_id AND status = 2) AND wechat_id != "' . $this->test_account . '"', 'params' => array(':op_id' => Yii::app()->user->id), 'order' => 'status, update_time desc')));
		} else if ($type == 2) {
			$crm_msgs = CrmMsg::model()->findAll(
				array(
					'condition' => 'status = 99 AND wechat_id != "' . $this->test_account . '"',
					'order' => 'status, update_time desc',
					'limit' => 20 * (!empty($_GET['panel_len']) ? $_GET['panel_len'] : 1)
				)
			);
		}

		if (!empty($_GET['test']) && $_GET['test'] == '54gvvfbj4fwfg4j4wgcdvbw45w34gh5') {
			$crm_msgs = array_merge(CrmMsg::model()->findAll('wechat_id = :wechat_id', array(':wechat_id' => $this->test_account)), $crm_msgs);
			$this->render('panel', array('crm_msgs' => $crm_msgs, 'test' => $_GET['test']));
		} else {
			$this->render('panel', array('crm_msgs' => $crm_msgs));
		}
	}

	public function actionRenderBox() {
		if (!empty($_GET['id'])) {
			$crm_msg = CrmMsg::model()->findByPk($_GET['id']);
		} else {
			$crm_msg = CrmMsg::model()->find(array('order' => 'update_time desc', 'condition' => 'operator_id = :op_id AND status != :status', 'params' => array(':op_id' => Yii::app()->user->id, ':status' => CrmMsg::STATUS_CLOSED)));
		}

		echo json_encode(array('data' => $this->render('box', array('crm_msg' => $crm_msg), true)));
	}

	public function actionRenderChatbox() {
		if (!empty($_GET['id'])) {
			$crm_msg = CrmMsg::model()->findByPk($_GET['id']);
		} else {
			$crm_msg = CrmMsg::model()->find(array('order' => 'update_time desc', 'condition' => 'operator_id = :op_id AND status != :status', 'params' => array(':op_id' => Yii::app()->user->id, ':status' => CrmMsg::STATUS_CLOSED)));
		}

		// refresh last lines
		if ($crm_msg) {
			$last_lines = CrmMsgLine::model()->findAll('crm_msg_id = :crm_msg_id AND meta not like :op_id order by time asc', array(':crm_msg_id' => $crm_msg->id, ':op_id' => '%"' . Yii::app()->user->id . '"%'));
			foreach ($last_lines as $line) {
				$line->mdata['read'][] = Yii::app()->user->id;
				$line->save();
			}
		}

		echo json_encode(array('data' => $this->render('chatbox', array('crm_msg' => $crm_msg), true)));
	}

	public function actionRenderTickets() {
		if (!empty($_GET['id'])) {
			$crm_msg = CrmMsg::model()->findByPk($_GET['id']);
		} else {
			$crm_msg = CrmMsg::model()->find(array('order' => 'update_time desc', 'condition' => 'operator_id = :op_id AND status != :status', 'params' => array(':op_id' => Yii::app()->user->id, ':status' => CrmMsg::STATUS_CLOSED)));
		}

		$tickets = array();
		if (!empty($crm_msg->mdata['tickets'])) {
			foreach ($crm_msg->mdata['tickets'] as $ticket) {
				$tickets[] = ExCrm::model()->findByPk($ticket);
			}
		}

		echo json_encode(array('data' => $this->render('tickets', array('tickets' => $tickets), true)));
	}

	public function actionRefreshBox($id) {
		$crm_msg = CrmMsg::model()->findByPk($id);
		$last_lines = CrmMsgLine::model()->findAll('crm_msg_id = :crm_msg_id AND meta not like :op_id order by time asc', array(':crm_msg_id' => $id, ':op_id' => '%"' . Yii::app()->user->id . '"%'));

		$lines = array();
		foreach ($last_lines as $line) {
			if (empty($line->mdata['operator_id'])) {
				if (!empty($line->mdata['text'])) {
					$lines[] = array('wechat_name' => $crm_msg->wechat_name, 'text' => $line->mdata['text'], 'time' => date('H:i', strtotime($line->time)));
				} else if (!empty($line->mdata['pic'])) {
					$lines[] = array('wechat_name' => $crm_msg->wechat_name, 'pic' => $line->mdata['pic'], 'time' => date('H:i', strtotime($line->time)));
				} else if (!empty($line->mdata['voice'])) {
					$lines[] = array('wechat_name' => $crm_msg->wechat_name, 'voice' => $line->mdata['voice'], 'time' => date('H:i', strtotime($line->time)));
				}
			} else {
				if ($line->type == CrmMsgLine::TYPE_AUTO) {
					$lines[] = array('auto' => true, 'time' => date('H:i', strtotime($line->time)));
				}
			}

			$line->mdata['read'][] = Yii::app()->user->id;
			$line->save();
		}

		if ($lines) {
			echo json_encode($lines);
		} else {
			return $lines;
		}
	}

	public function actionAddMsg($id, $type) {
		$crm_msg = CrmMsg::model()->findByPk($id);
		$crm_msg->status = CrmMsg::STATUS_ASSIGNED;
		$crm_msg->update_time = date('Y-m-d H:i:s');
		$crm_msg->operator_id = Yii::app()->user->id;

		$weObj = new WechatAPI();
		$weObj->checkAuth();

		$crm_msg_line = new CrmMsgLine;
		$crm_msg_line->crm_msg_id = $crm_msg->id;
		$crm_msg_line->time = $crm_msg->update_time;
		$crm_msg_line->type = array_search($type, CrmMsgLine::$types);
		$crm_msg_line->wechat_msg_id = 0;
		if ($type == 'text') {
			$crm_msg_line->mdata = array('operator_id' => $crm_msg->operator_id, 'text' => $_POST['text'], 'read' => array(Yii::app()->user->id));
			$crm_msg_line->save();
			$crm_msg->save();

			$data = array(
				'touser' => $crm_msg->wechat_id,
				'msgtype' => 'text',
				'text' => array('content' => $_POST['text'])
			);
			$weObj->sendCustomMessage($data);

			echo json_encode(array('operator' => User::model()->findByPk($crm_msg->operator_id)->getName(), 'text' => $_POST['text'], 'time' => date('H:i', strtotime($crm_msg->update_time))));
		} else if ($type == 'pic') {
			// save pic
			$f = tempnam(Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR, 'pp');
			preg_match('/data:image\/(\w+);base64,/', $_POST['pic'], $matches);
			$type = $matches[1];
			file_put_contents($f, base64_decode(preg_replace('/data:image\/' . $type . ';base64,/', '', $_POST['pic'])));
			$id = FileRepo::storeFile($f, 'P' . date('YmdHis') . '.' . $type, 101, $crm_msg->id);
			$file = FileRepo::model()->findByPk($id);
			unlink($f);

			// upload pic
			$data = array(
				'media' => '@' . $file->getFile(),
				'mime' => $file->mime,
				'postname' => $file->name
			);
			$result = $weObj->uploadMedia($data, 'image');
			$crm_msg_line->mdata = array('operator_id' => $crm_msg->operator_id, 'pic' => $file->getUrl(), 'mediaid' => $result['media_id'], 'read' => array(Yii::app()->user->id));
			$crm_msg_line->save();
			$crm_msg->save();

			// send pic
			$data = array(
				'touser' => $crm_msg->wechat_id,
				'msgtype' => 'image',
				'image' => array('media_id' => $result['media_id'])
			);
			$weObj->sendCustomMessage($data);

			echo json_encode(array('operator' => User::model()->findByPk($crm_msg->operator_id)->getName(), 'pic' => $_POST['pic'], 'time' => date('H:i', strtotime($crm_msg->update_time))));
		} else if ($type == 'ticket') {
			$ticket = ExCrm::model()->find('no = :no', array(':no' => $_POST['ticket_no']));
			$crm_msg_line->mdata = array('operator_id' => $crm_msg->operator_id, 'ticket' => $ticket->id, 'no' => $ticket->no, 'status' => $_POST['status'], 'read' => array(Yii::app()->user->id));
			$crm_msg_line->save();

			if (empty($crm_msg->mdata['tickets'])) {
				$crm_msg->mdata['tickets'] = [];
			}

			if (!in_array($ticket->id, $crm_msg->mdata['tickets'])) {
				$crm_msg->mdata['tickets'][] = $ticket->id;
			}

			$crm_msg->save();

			echo json_encode(array('operator' => User::model()->findByPk($crm_msg->operator_id)->getName(), 'ticket_no' => $ticket->no, 'time' => date('H:i', strtotime($crm_msg->update_time))));
		}
	}

	public function actionClose($id) {
		$crm_msg = CrmMsg::model()->findByPk($id);
		$crm_msg->operator_id = 0;
		$crm_msg->status = CrmMsg::STATUS_CLOSED;
		$crm_msg->save();

		echo json_encode(array('success' => true));
	}

	public function actionTicket($id) {
		$crm_msg = CrmMsg::model()->findByPk($id);
		if (!empty($_GET['ticket_id'])) {
			$crm = ExCrm::model()->findByPk($_GET['ticket_id']);
			echo json_encode(array('data' => $this->render('ticket', array('crm_msg' => $crm_msg, 'crm' => $crm), true)));
		} else {
			echo json_encode(array('data' => $this->render('ticket', array('crm_msg' => $crm_msg), true)));
		}
	}

}