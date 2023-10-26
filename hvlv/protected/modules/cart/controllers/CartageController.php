<?php

class CartageController extends Controller
{
  
  /**
   * @return array action filters
   */

  public function actionIndex()
  {
    $this->render('site/index');
  }
  
  //list the task for each drivers
  public function actionTask()
  {
    $model=new Cartage('search');
    $model->unsetAttributes();
    if (isset($_GET['Cartage'])) {
      $model->attributes=$_GET['Cartage'];
    }
    $this->render('task_list', ['model'=>$model]);
  }
  public function actionCreate()
  {
    $model=new Cartage;
    $resp=['success'=>1,'msg'=>'successfully'];
    if (isset($_POST['Cartage'])) {
      $model->attributes=$_POST['Cartage'];
      if (!empty($_POST['fad'])) {
        $model->from_addr=$_POST['fad'];
        $model->from_contact=$_POST['fcontact'];
      }
      if (!empty($_POST['tad'])) {
        $model->to_addr=$_POST['tad'];
        $model->to_contact=$_POST['tcontact'];
      }
      
      $model->org_id=empty($_POST['org_id'])?'':$_POST['org_id'];
      $model->op_id=Yii::app()->user->id;
      $model->type= Cartage::gettypes($_POST['types']);

      $model->from_id=$_POST['fid'];
      $model->to_id=$_POST['tid'];
      $model->ref=$_POST['reference'];
      //           $model->rego=$_POST['rego'];
      $model->kg=empty($_POST['weight'])?'':$_POST['weight'];
      $model->cbm=empty($_POST['cbm'])?'':$_POST['cbm'];
      $model->mdata['notes']['op']=empty($_POST['notes'])?'':$_POST['notes'];
      $user= User::model()->find('id=:id', [':id'=>Yii::app()->user->id]);
      $model->mdata['log']['create']=$user->id.'+'.$user->fname.' '.$user->lname.' create task at '.date('Y-m-d h:i:s');
      $model->status=10;   //new
      $model->scd_time=$_POST['scd_date'];
      if (!empty($_POST['awb'])) {
        $model->mdata['awb'] = $_POST['awb'];
      }
      if (!empty($_POST['flight'])) {
        $model->mdata['flight'] = $_POST['flight'];
      }
      if (!empty($_POST['cbm'])) {
        $model->mdata['cbm'] = $_POST['cbm'];
      }
      if (!empty($_POST['weight'])) {
        $model->mdata['weight'] = $_POST['weight'];
      }
    
      if ($model->save()) {
        $this->ajaxResult($model, ['id' => $model->id]);
      } else {
        $this->ajaxResult($model);
      }
    }
    $this->render('create_task', ['model'=>$model]);
  }

  public function actionUpdate($id)
  {
    if ($id == 'site') {
      return;
    }
    $model = Cartage::model()->findByPk($id);
    if (!isset($_POST['Cartage'])) {
      $this->render('update_task', ['model' => $model]);
    } else {
      $model->attributes=$_POST['Cartage'];
      if (!empty($_POST['fad'])) {
        $model->from_addr=$_POST['fad'];
        $model->from_contact=$_POST['fcontact'];
      }
      if (!empty($_POST['tad'])) {
        $model->to_addr=$_POST['tad'];
        $model->to_contact=$_POST['tcontact'];
      }
      
      $model->org_id=empty($_POST['org_id'])?'':$_POST['org_id'];
      $model->op_id=Yii::app()->user->id;
      $model->type= Cartage::gettypes($_POST['types']);

      $model->from_id=$_POST['fid'];
      $model->to_id=$_POST['tid'];
      $model->ref=$_POST['reference'];
      //           $model->rego=$_POST['rego'];
      $model->kg=empty($_POST['weight'])?'':$_POST['weight'];
      $model->cbm=empty($_POST['cbm'])?'':$_POST['cbm'];
      $model->mdata['notes']['op']=empty($_POST['notes'])?'':$_POST['notes'];
      $user= User::model()->find('id=:id', [':id'=>Yii::app()->user->id]);
      $model->mdata['log']['create']=$user->id.'+'.$user->fname.' '.$user->lname.' create task at '.date('Y-m-d h:i:s');
      $model->status=10;   //new
      $model->scd_time=$_POST['scd_date'];
      if (!empty($_POST['awb'])) {
        $model->mdata['awb'] = $_POST['awb'];
      }
      if (!empty($_POST['flight'])) {
        $model->mdata['flight'] = $_POST['flight'];
      }
      if (!empty($_POST['cbm'])) {
        $model->mdata['cbm'] = $_POST['cbm'];
      }
      if (!empty($_POST['weight'])) {
        $model->mdata['weight'] = $_POST['weight'];
      }
    
      if ($model->save()) {
        $this->ajaxResult($model);
      } else {
        $this->ajaxResult($model);
      }
    }
  }
  
  public function actionBook()
  {
    if(!empty($_POST)){
      $from = '';
      foreach($_POST['from'] as $k => $v){
        $from .= $k.': <b>'.$v.'</b><br />';
      }
      $to = '';
      foreach($_POST['to'] as $k => $v){
        $to .= $k.': <b>'.$v.'</b><br />';
      }
      $t = '<table width="100%"><tr><td width="50%"><h3>From:</h3><br />'.$from.'</td><td><h3>To:</h3><br />'.$to.'</td></tr></table>';
      $t .= '<h3>Goods</h3><table><thead><tr><td>Type</td><td>Dimension</td><td>Weight</td><td>Qty</td></tr></thead><tbody>';
      foreach($_POST['goods']['type'] as $k => $v){
        $t .= '<tr><td>'.$v.'</td><td>'.$_POST['goods']['dim_l'][$k].' x '.$_POST['goods']['dim_w'][$k].' x '. $_POST['goods']['dim_h'][$k].'</td><td>'.$_POST['goods']['weight'][$k].'</td><td>'.$_POST['goods']['qty'][$k].'</td></tr>';
      }
      $t .= '</tbody></table>';
      $t .= '<h3>Nots</h3>';
      $t .= '<p>'.$_POST['notes'].'</p>';

      include_once('PHPMailer/class.phpmailer.php');
      $mail = new PHPmailer();
      $mail->CharSet = "UTF-8";
      $mail->IsHTML(true);
      $mail->Subject = 'Cartage Booking';
      $mail->Body = $t;
      $mail->AltBody = preg_replace("/[\n\r\t]+/", "\n", strip_tags($t));
      $mail->AddAddress('cartage@toplogistics.com.au');
      $mail->From     = "donotreplay@toplogistics.com.au";
      $mail->FromName = "HVLV";
      $mail->Send();
      $r = ['done' => true, 'msg' => 'Booking Received'];
      echo json_encode($r);
      Yii::app()->end();
    }
    $this->render('book');
  }

  public function actionTakeList()
  {
    $model=new Cartage('search');
    $model->unsetAttributes();
    if (isset($_GET['Cartage'])) {
      $model->attributes=$_GET['Cartage'];
    }
    $this->render('take_list', ['model'=>$model]);
  }
  
  public function actionFinishList()
  {
    $model=new Cartage('search');
    $model->unsetAttributes();
    if (isset($_GET['Cartage'])) {
      $model->attributes=$_GET['Cartage'];
    }
    $this->render('finish_list', ['model'=>$model]);
  }

  
  public function actionAddNotes()
  {
    if (!empty($_POST['id'])&&!empty($_POST['user'])) {
      $r= Cartage::model()->find('id=:id', [':id'=>$_POST['id']]);
      $user= User::model()->find('id=:id', [':id'=>$_POST['user']]);
      $r->comp_time=date('Y-m-d h:i:s');
      $r->mdata['notes']['finish']=empty($_POST['notes'])?'':$_POST['notes'];
      $r->mdata['log']['completed']=$user->id.'+'.$user->fname.' '.$user->lname.' completed at '.date('Y-m-d h:i:s');
      $r->status=70;
      if ($r->save()) {
        $this->ajaxResult($r);
      } else {
        //               var_dump($r->getErrors());
      }
    }
  }
   
  public function actionConfirm()
  {
    if (!empty($_POST['id'])) {
      $r= Cartage::model()->find('id=:id', [':id'=>$_POST['id']]);
      $user= User::model()->find('id=:id', [':id'=>Yii::app()->user->id]);
      $r->mdata['notes']['confirm']=empty($_POST['notes'])?'':$_POST['notes'];
      $r->mdata['log']['confirm']=$user->id.'+'.$user->fname.' '.$user->lname.' confirm at '.date('Y-m-d h:i:s');
      $r->status=80;
      if ($r->save()) {
        $this->ajaxResult($r);
      } else {
        //               var_dump($r->getErrors());
      }
    }
  }
  public function actionEditFinish()
  {
    $model= Cartage::model()->find('id=:id', [':id'=>$_GET['id']]);
    $this->render('edit', ['model'=>$model]);
  }
  public function actionTakeConfirm()
  {
    $model= Cartage::model()->find('id=:id', [':id'=>$_GET['id']]);
    $this->render('take', ['model'=>$model]);
  }
  public function actionCancelConfirm()
  {
    $this->render('cancel');
  }
  public function actionFinishConfirm()
  {
    $model= Cartage::model()->find('id=:id', [':id'=>$_GET['id']]);
    $this->render('confirm', ['model'=>$model]);
  }
  public function actionTake()
  {
    if (!empty($_POST['id'])) {
      $r= Cartage::model()->find('id=:id', [':id'=>$_POST['id']]);
      $user= User::model()->find('id=:id', [':id'=>Yii::app()->user->id]);
      if (!empty($r)) {
        $r->status=20;
        $r->rego= empty($_POST['rego'])?'':$_POST['rego'];
        $r->mdata['notes']['take']=empty($_POST['notes'])?'':$_POST['notes'];
        $r->mdata['log']['accept']=$user->id.'+'.$user->fname.' '.$user->lname.'take at '.date('Y-m-d h:i:s');
        $r->take_time=date('Y-m-d h:i:s');
        if ($r->save()) {
          $this->ajaxResult($r);
        } else {
          var_dump($r->getErrors());
        }
      }
    }
  }
  
  public function actionViewLog()
  {
    echo '<h4>Task '.$_GET['id'].' Logs:</h4>';
    if (!empty($_GET['id'])) {
      $r=Cartage::model()->find('id=:id', [':id'=>$_GET['id']]);
       
      if (!empty($r->mdata)&&is_array($r->mdata)) {
        foreach ($r->mdata as $key=>$value) {
          if (is_array($value)) {
            echo $key.':'.'<br>';
            foreach ($value as $k=>$v) {
              echo $k.'==>'.$v.'<br>';
            }
          } else {
            echo $key.'==>'.$value.'<br>';
          }
        }
      }
    }
  }
  public function actionViewDetail()
  {
    echo '<h4>Task '.$_GET['id'].' Details:</h4>';
    if (!empty($_GET['id'])) {
      $r=Cartage::model()->find('id=:id', [':id'=>$_GET['id']]);
      echo '<b>Start From:</b> '.(empty($r->from_org->name)?'':$r->from_org->name).'&nbsp <b>Contact Detail:</b> '.$r->from_contact.'<br>';
      echo '<b>Delivery To:</b> '.(empty($r->to_org->name)?'':$r->to_org->name).'&nbsp <b>Contact Detail:</b>'.$r->to_contact.'<br><hr>';
      echo '<h4>Goods Information:</h4>';
      echo '<b>Pallet Number:</b>'.$r->plt.'&nbsp &nbsp &nbsp <b>Cubic Meters(M<sup>3</sup>): </b>'.$r->cbm.'&nbsp &nbsp &nbsp <b>Weight(Kg): </b>'.$r->kg.'<br><hr>';
      echo '<b>Assign To:</b>'.(empty($r->org->name)?'':$r->org->name).'<br>';
      if (!empty($r->mdata)&&is_array($r->mdata)) {
        foreach ($r->mdata as $key=>$value) {
          if ($key=='notes') {
            echo '<b>op Notes:</b><br>';
            if (is_array($value)) {
              echo(empty($value['op'])?'':'create: '.$value['op']).'<br>';
              echo(empty($value['confirm'])?'':'confirm: '.$value['confirm']).'<br>';
            }
            echo '<b>Driver Notes:</b><br>';
            if (is_array($value)) {
              echo(empty($value['take'])?'':'take: '.$value['take']).'<br>';
              echo(empty($value['finish'])?'':'finish: '.$value['finish']).'<br>';
            }
          }
        }
      }
    }
  }
   
  public function actionCancel()
  {
    if (!empty($_POST['id'])) {
      $r= Cartage::model()->find('id=:id', [':id'=>$_POST['id']]);
      $user= User::model()->find('id=:id', [':id'=>Yii::app()->user->id]);
      if (!empty($r)) {
        $r->status=100;
        $r->mdata['notes']['cancel']=empty($_POST['notes'])?'':$_POST['notes'];
        $r->mdata['log']['cancelled']=$user->id.'+'.$user->fname.' '.$user->lname.'canceled at '.date('Y-m-d h:i:s');
        if ($r->save()) {
          $this->ajaxResult($r);
        } else {
          //               var_dump($r->getErrors());
        }
      }
    }
  }
  public function actionSuggest_agent()
  {
    $rs = Org::model()->findAll([
      'condition' => 'status = 1 AND (name LIKE :n OR code LIKE :n OR id = :tn)',
      'params' => [':n' => '%'.$_GET['term'].'%', ':tn' => $_GET['term']],
      'order' => 'name',
      'limit' => 20,
    ]);
    $a = [];
         
    foreach ($rs as $r) {
      $a[] = [
        'value' => $r->name,
        'id'=>$r->id,
        'addr'=>$r->address.(!empty($r->suburb)?', ':'').$r->suburb.(!empty($r->state)?', ':'').$r->state.(!empty($r->postcode)?', ':'').$r->postcode,
        'contact'=>$r->phone,
               
      ];
    }
    //                var_dump($a);
    echo json_encode($a);
  }

  public function actionUpload($hash)
  {
    $fr = new FileRepo;
    $fr->store($_FILES['file'], $hash);
    echo 'DONE';
  }

  public function actionUploadGrid($id)
  {
    $fs = FileRepo::model()->findAll('fid = :id AND type = 92 AND status = 20', [':id' => $id]);
    $data = '';
    $i = 0;
    foreach ($fs as $file) {
        $data .= '<tr class="' . ($i++ % 2 == 0 ? 'even' : 'odd') . '"><td>' . '<a href="' . Yii::app()->baseUrl . '/filerepo/' . $file->hash . '/' . $file->name . '" target="_blank">' . $file->name . '</a>' . '</td><td>' . $file->formatSize() . '</td><td>' . $file->date . '</td><td class="button-columns"><a class="delete_btn" target="_blank" tilte="Delete" href="' . Yii::app()->createUrl('cart/cartage/delete', ['id' => $file->id]) . '">Delete</a></td></tr>';
    }
    if (empty($fs)) {
      $data .= '<tr class="' . ($i++ % 2 == 0 ? 'even' : 'odd') . '"><td colspan="4">No files</td></tr>';
    }
    echo json_encode(['data' => $data]);
  }

  public function actionDelete($id)
  {
    $fr = FileRepo::model()->findByPk($id);
    $fr->status = 0;
    $fr->save();
    $this->ajaxResult($fr);
  }
}
