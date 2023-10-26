<?php

class AccountsController extends Controller{
    /**
     * Declares class-based actions.
     */
    protected $nonAjax=array('export');
    protected $skipAcl = array('resetpassword','finish');

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex(){
        if(!Yii::app()->request->isAjaxRequest) $this->layout = 'cg';
        $this->render('index');
    }


    public function actionUpdate(){
        $model = Org::model()->findByPk(Yii::app()->user->org);
        $own = $model->id == Yii::app()->user->org;
        $user =empty($model->users[0])? new User:$model->users[0];
              
        $_GET['tabid']=12321112;
        if(isset($_POST['Org'])){
            if($own){
                unset($_POST['Org']['status']);
                unset($_POST['Org']['type']);
            }
            $model->attributes=$_POST['Org'];
            $model->sync_xero = false;
            $model->save();

            if (!empty($_POST['password']) && !empty($_POST['password_again'])) {
                if ($_POST['password'] == $_POST['password_again']) {
                    if (!empty($model->extra['password'])) {
                        if (!empty($_POST['current_password']) && $model->extra['password'] == md5($_POST['current_password'])) {
                            $model->extra['password'] = md5($_POST['password']);
                            $model->sync_xero = false;
                            $model->save();
                        } else {
                            $model->addError('', '当前密码输入错误');
                        }
                    } else {
                        $model->extra['password'] = md5($_POST['password']);
                        $model->sync_xero = false;
                        $model->save();
                    }
                } else {
                    $model->addError('', '两次密码不匹配');
                }
            }

            if (!empty($model->address) && !empty($model->suburb) && !empty($model->state) && !empty($model->postcode) && !empty($model->phone) && !empty($model->email) && !empty($model->extra['password'])) {
                Yii::app()->user->setState('incomplete', false);
            }

            $this->ajaxResult($model);
        }

        $this->render('index',array('model'=>$model, 'own' => $own,'user'=>$user));
    }

    public function actionChangePassword() {
        if (Yii::app()->user->isGuest) {
            $this->redirect($this->createUrl('site/index'));
        } else {
            $model = Org::model()->findByPk(Yii::app()->user->id);
            if (!empty($_POST['password']) && !empty($_POST['password_again'])) {
                if ($_POST['password'] == $_POST['password_again']) {
                    if (!empty($model->extra['password'])) {
                        if (!empty($_POST['current_password']) && $model->extra['password'] == md5($_POST['current_password'])) {
                            $model->extra['password'] = md5($_POST['password']);
                            $model->sync_xero = false;
                            $model->save();
                            $this->ajaxResult($model);
                        } else {
                            $model->addError('', '当前密码输入错误');
                            $this->ajaxResult($model);
                        }
                    } else {
                        $model->extra['password'] = md5($_POST['password']);
                        $model->sync_xero = false;
                        $model->save();
                        $this->ajaxResult($model);
                    }
                } else {
                    $model->addError('', '两次密码不匹配');
                    $this->ajaxResult($model);
                }
            } else {
                $this->render('changepassword', array('model' => $model));
            }
        }
    }

    public function actionResetPassword() {
        if (empty($_POST['id']) && empty($_POST['email'])) {
            Yii::app()->user->setState('reset', true);
            $this->render('resetpassword');
        } else {
            $org = Org::model()->findByPk($_POST['id']);
            if (empty($org)) {
                Yii::app()->user->setState('reset', true);
                echo json_encode(array('done' => false, 'msg' => '代理号不存在'));
                return;
            }
            if ($org->email != $_POST['email']) {
                Yii::app()->user->setState('reset', true);
                echo json_encode(array('done' => false, 'msg' => '邮箱和代理号不匹配'));
                return;
            } else {
                Yii::app()->user->setState('reset', true);

                $user = User::model()->find('fname = "Jerry" AND lname = "Fang"');

                // send email
                $elog = new Emailog;
                $elog->from_id = $user->id;
                $elog->to_id = $org->id;
                $elog->status = 10;
                $elog->type = Emailog::CG_CHANGE_PASSWORD;
                $elog->fid = $_POST['id'];
                $elog->mdata['from'] = $user->email;
                $elog->mdata['from_name'] = $user->getName();
                $elog->save();
                $elog->prepTemplate();
                $elog->subject = $elog->tpl->subject;
                $elog->body = $elog->tpl->getContent();
                $elog->save();
                $elog->send();

                echo json_encode(array('done' => true, 'msg' => '重置成功，新密码已发送您的邮箱'));
                return;
            }
        }
    }

    public function actionFinish($result) {
        $this->render('finish', array('result' => $result));
    }

}