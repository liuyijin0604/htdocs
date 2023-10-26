<?php

class MassmsgController extends Controller {

	/**
	 * Declares class-based actions.
	 */
	protected $nonAjax = array();
	protected $skipAcl = array();

	public function actionList() {
		$model = new CrmMsgLine('search');
		$model->unsetAttributes();
		$model->type = CrmMsgLine::TYPE_MASS;

		if (isset($_GET['CrmMsgLine'])) {
			$model->attributes = $_GET['CrmMsgLine'];
		}

		$this->render('list', array('model' => $model));
	}

	public function actionView($id) {
		$this->render('view', array('model' => $this->loadModal($id)));
	}

	public function actionCreate() {
		if (empty($_POST['ck_content'])) {
			$model = new CrmMsgLine;
			$model->crm_msg_id = 0;
			$model->wechat_msg_id = 0;
			$model->type = CrmMsgLine::TYPE_MASS;
			$model->time = date('Y-m-d H:i:s');
			$model->mdata['operator_id'] = Yii::app()->user->id;
			$model->mdata['content'] = '';
			$model->mdata['title'] = '';
			$model->save();
			$this->render('create', array(
				'model' => $model
			));
		} else {
			$model = CrmMsgLine::model()->findByPk($_GET['id']);
			$model->mdata['content'] = $_POST['ck_content'];
			$model->mdata['title'] = $_POST['title'];

			if (!empty($_FILES)) {
				// read file
				$file_origin = fopen($_FILES['thumb']['tmp_name'], 'r');
				$fres = fread($file_origin, $_FILES['thumb']['size']);
				$type = substr($_FILES['thumb']['type'], 6);

				// write and upload file
				$f = tempnam(Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR, 'pp');
				file_put_contents($f, $fres);

				if (!FileRepo::sameFile($f)) {
					$fid = FileRepo::storeFile($f, 'P' . date('YmdHis') . '.' . $type, 101, $id);
					$file = FileRepo::model()->findByPk($fid);

					$weObj = new WechatAPI();
					$weObj->checkAuth();
					$data = array(
						'media' => '@' . $file->getFile(),
						'mime' => $file->mime,
						'postname' => $file->name
					);
					$result = $weObj->uploadForeverMedia($data, 'image');
					$file->mdata['media_id'] = $result['media_id'];
					$file->save();
				} else {
					$fid = FileRepo::model()->find('hash = :h', array(':h' => hash_file('crc32b', $f).hash('crc32b', filesize($f))))->id;
				}
				fclose($file_origin);
				unlink($f);
			}

			if (!empty($fid)) {
				$model->mdata['thumb'] = $fid;
			}

			$model->save();
			$this->ajaxResult($model);
		}
	}

	public function actionUpdate($id) {
		$model = CrmMsgLine::model()->findByPk($id);

		if (!empty($_POST)) {
			$model->mdata['content'] = $_POST['ck_content'];
			$model->mdata['title'] = $_POST['title'];

			if (!empty($_FILES)) {
				// read file
				$file_origin = fopen($_FILES['thumb']['tmp_name'], 'r');
				$fres = fread($file_origin, $_FILES['thumb']['size']);
				$type = substr($_FILES['thumb']['type'], 6);

				// write and upload file
				$f = tempnam(Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR, 'pp');
				file_put_contents($f, $fres);

				if (!FileRepo::sameFile($f)) {
					$fid = FileRepo::storeFile($f, 'P' . date('YmdHis') . '.' . $type, 101, $id);
					$file = FileRepo::model()->findByPk($fid);

					$weObj = new WechatAPI();
					$weObj->checkAuth();
					$data = array(
						'media' => '@' . $file->getFile(),
						'mime' => $file->mime,
						'postname' => $file->name
					);
					$result = $weObj->uploadForeverMedia($data, 'thumb');
					$file->mdata['media_id'] = $result['media_id'];
					// $result = $weObj->uploadMedia($data, 'thumb');
					// $file->mdata['media_id'] = $result['thumb_media_id'];
					$file->save();
				} else {
					$fid = FileRepo::model()->find('hash = :h', array(':h' => hash_file('crc32b', $f).hash('crc32b', filesize($f))))->id;
				}
				fclose($file_origin);
				unlink($f);
			}

			if (!empty($fid)) {
				$model->mdata['thumb'] = $fid;
			}

			$model->save();
			$this->ajaxResult($model);
		} else {
			$this->render('update', array(
				'model' => $model
			));
		}
	}

	public function actionPicBrowser() {
		$ids = [];
		$crm_msg_lines = CrmMsgLine::model()->findAll(array('select' => array('id'), 'condition' => 'type = 8'));
		foreach ($crm_msg_lines as $line) {
			$ids[] = $line->id;
		}
		$files = FileRepo::model()->findAll('type = :type AND fid in (' . implode(',', $ids) . ')', array(':type' => 101));
		$this->renderPartial('browser', array('files' => $files));
	}

	public function actionPicUploader() {
		$ckfile = $_FILES['upload'];
		$cb = $_GET['CKEditorFuncNum'];
		$id = $_GET['id'];

		$type = substr($ckfile['type'], 6);
		if (!in_array($type, array('png', 'jpg', 'jpeg'))) {
			echo '<script>window.parent.CKEDITOR.tools.callFunction(' . $cb . ', "", "图片格式只接受png, jpg, jpeg");</script>';
		} else {
			// read file
			$file_origin = fopen($ckfile['tmp_name'], 'r');
			$fres = fread($file_origin, $ckfile['size']);

			// write file
			$f = tempnam(Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR, 'pp');
			file_put_contents($f, $fres);

			if (!FileRepo::sameFile($f)) {
				$fid = FileRepo::storeFile($f, 'P' . date('YmdHis') . '.' . $type, 101, $id);
				$file = FileRepo::model()->findByPk($fid);
			} else {
				$file = FileRepo::model()->find('hash = :h', array(':h' => hash_file('crc32b', $f).hash('crc32b', filesize($f))));
			}
			fclose($file_origin);
			unlink($f);

			echo '<script>window.parent.CKEDITOR.tools.callFunction(' . $cb . ', "' . $file->getUrl() . '", "");</script>';
		}
	}

	public function actionPost($id) {
		$model = CrmMsgLine::model()->findByPk($id);

		if (empty($_POST)) {
			$this->render('post', array('model' => $model));
		} else {
			$weObj = new WechatAPI();
			$weObj->checkAuth();

			if (empty($model->mdata['mediaid'])) {
				$thumb = FileRepo::model()->findByPk($model->mdata['thumb']);
				$data = array(
					'articles' => array(
						array(
							'title' => $model->mdata['title'],
							'thumb_media_id' => $thumb->mdata['media_id'],
							'author' => 'PCA Express',
							'show_cover_pic' => true,
							'content' => str_replace('src="', 'src="' . str_replace('\\', '\/', $_SERVER['HTTP_HOST']) . '/protected', $model->mdata['content']),
							'content_source_url' => 'https://www.pcaexpress.com.au/zh/首页/'
						)
					)
				);
				$result = $weObj->uploadForeverArticles($data);

				if (!empty($result['media_id'])) {
					$model->mdata['mediaid'] = $result['media_id'];
					$model->save();
				} else {
					$model->addError('id', 'Post failed');
				}
			}

			// $data = array(
			// 	'filter' => array(
			// 		'is_to_all' => true
			// 	),
			// 	'msgtype' => 'mpnews',
			// 	'mpnews' => array(
			// 		'media_id' => $model->mdata['mediaid']
			// 	)
			// );
			// $result = $weObj->sendGroupMassMessage($data);

			$data = array(
				'touser' => array(
					'o-C-Zjho9516ToGK9yniP86mPKSI',
					'o-C-ZjokAuY6thLNrlmQtSOJplqc'
				),
				'msgtype' => 'mpnews',
				'mpnews' => array(
					'media_id' => $model->mdata['mediaid']
				)
			);
			$result = $weObj->sendMassMessage($data);
			yii::log(json_encode($result), 'warning');

			$this->ajaxResult($model);
		}
	}

	private function loadModal($id) {
		$model = CrmMsgLine::model()->findByPk($id);
		if ($model === null)
			throw new CHttpException(404, 'The requested page does not exist.');
		return $model;
	}
}