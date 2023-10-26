<?php
class MarketingToolController extends Controller{

    public function actionMarketingTool(){
        $this->render('marketing_tool',[
			'model'=>null,
		]); 
    }

    public function actionEditAddress(){

        if(!empty($_POST)){
            $objCMarketingAddress = new CMarketingAddress;
            $objCMarketingAddress->funcCheckMarketingAddress($_POST);
            if(!empty($objCMarketingAddress->getListError())){
                $objResponce = ['isSuccess'=>false];
                $strMessage = '';
                foreach($objCMarketingAddress->getListError() as $strError){
                    $strMessage.= $strError.'
';
                }
                $objResponce['strMessage'] = $strMessage;
                echo json_encode( $objResponce);
                return;
            }

            if(!empty($_POST['id'])){
                (new CMarketingAddress)->funcUpdateMarketingAddress($_POST['id'],$_POST);
            }
            else{
                (new CMarketingAddress)->funcNewMarketingAddress($_POST);
            }

            echo json_encode(['isSuccess'=>true]);
            return;
        }
        else{
            // get
            if(isset($_GET['id'])){
                $numId = $_GET['id'];
                $objMarketingAddress = MarketingAddress::model()->findByPk($numId);
            }
            else{
                $objMarketingAddress = new MarketingAddress;
                $objMarketingAddress->status = 1;
            }
    
            $this->render('address_edit',[
                'model'=>$objMarketingAddress,
            ]);
        }
    }

    public function actionImportExcelAddress(){
        if(!empty($_FILES)){
            if (!empty($_FILES['excel'])) {
                $objCMarketingAddress = new CMarketingAddress;
                $objCMarketingAddress->funcImportExcel($_FILES['excel']['tmp_name']);
                if(empty($objCMarketingAddress->getListError())){
                    echo json_encode(['isSuccess'=>true]);
                    return;
                }
                else{
                    // todo
                    $objResponce = ['isSuccess'=>false];
                    $strMessage = '';
                    foreach($objCMarketingAddress->getListError() as $strError){
                        $strMessage.= $strError.'
';
                    }
                    $objResponce['strMessage'] = $strMessage;
                    echo json_encode( $objResponce);
                    return;
                }
            }

        }
        else{
            $this->render('import_excel_address',[
                'model'=>null,
            ]);
        }
    }


    public function actionEditEmail(){

        if(!empty($_POST)){
            if(!empty($_POST['id'])){
                $objMarketingEmail = (new CMarketingEmail)->funcUpdateMarketingEmail($_POST['id'],$_POST["subject"],$_POST["MarketingEmail"]["html"]);
            }
            else{
                $objMarketingEmail = (new CMarketingEmail)->funcNewMarketingEmail($_POST["subject"],$_POST["MarketingEmail"]["html"]);
            }
            // echo json_encode(['isSuccess'=>true]);
            $this->ajaxResult($objMarketingEmail, array('id'), 'Save Successfully');
            return;
        }
        else{
            // get
            if(!empty($_GET['id'])){
                $numId = $_GET['id'];
                $objMarketingEmail = MarketingEmail::model()->findByPk($numId);
            }
            else{
                $objMarketingEmail = new MarketingEmail;
                // $objMarketingEmail->status = 1;
            }
    
            $this->render('email_edit',[
                'model'=>$objMarketingEmail,
            ]);
        }
    }

    public function actionSendingHistory(){
        $this->render('email_send_history');
    }

    // public function actionEditEmailPicture(){
    //     if(!empty($_FILES)){
    //         if (!empty($_FILES['inputPicture1'])){
    //             $_FILES['inputPicture1']['tmp_name'];
    //         }
    //     }

    // }

    public function actionScheduleEmail(){
        if(!empty($_POST)){
            (new CMarketingEmail)->funcSchedule($_POST['id'],$_POST['schedule_time'],$_POST['group']);
            echo json_encode(['isSuccess'=>true]);
        }
        else{
            // get
            $numId = $_GET['id'];
            $objMarketingEmail = MarketingEmail::model()->findByPk($numId);
    
            $this->render('email_schedule',[
                'model'=>$objMarketingEmail,
            ]);
        }
    }

    public function actionCancelSchedule(){
        (new CMarketingEmail)->funcCancel($_POST['id']);
        echo json_encode(['isSuccess'=>true]);
    }


    public function actionSendTestEmail(){
        $numId = $_POST['id'];
        $strAddress = $_POST['test_address'];
        (new CMarketingEmail)->funcSendTestEmail($numId,$strAddress);

        echo json_encode(['isSuccess'=>true]);
    }

    public function actionSelectGroup()
    {
        $id = $_GET['id'];
        $marketingEmail = new MarketingEmail();
        $model = $marketingEmail->findByPk($id);
        if(isset($_POST['group'])) {
            $model->mdata['Group'] = $_POST['group'];
            $model->save();
        }
        $this->render('group_selecting', array('model'=>$model));
    } 

    public function actionSendEmail(){
        $numId = $_POST['id'];
        $email = MarketingEmail::model()->findByPk($numId);
        if (empty($email->mdata['Group'])) {
            echo json_encode(['isSuccess'=>false]);
        }
        else {
            (new CMarketingEmail)->funcSendEmail($numId);
            echo json_encode(['isSuccess'=>true]);
        }
    }

    public function actionPreviewEmail(){
        $strPicture1 = $_POST['last_picture1'];
        if(isset($_FILES['picture1'])){
            $numId = FileRepo::storeFile($_FILES['picture1']['tmp_name'],$_FILES['picture1']['name'], FileRepo::type_marketing_picture,0);
            $objFileRepo = FileRepo::model()->findByPk($numId);
            $strPicture1 = Yii::app()->createAbsoluteUrl('/').'/filerepo/'.$objFileRepo->hash.'/'.$objFileRepo->name;
        }
        if($_POST['no_picture1'] == 'true'){
            $strPicture1 ='';
        }

        $strPicture2 = $_POST['last_picture2'];
        if(isset($_FILES['picture2'])){
            $numId = FileRepo::storeFile($_FILES['picture2']['tmp_name'],$_FILES['picture2']['name'], FileRepo::type_marketing_picture,0);
            $objFileRepo = FileRepo::model()->findByPk($numId);
            $strPicture2 = Yii::app()->createAbsoluteUrl('/').'/filerepo/'.$objFileRepo->hash.'/'.$objFileRepo->name;
        }
        if($_POST['no_picture2'] == 'true'){
            $strPicture2='';
        }

        $strHtml = (new CMarketingEmail)-> funcEmailHtml($_POST['Heading1'],$_POST['Content1'],$_POST['Heading2'],$_POST['Content2'],$_POST['Heading3'],$_POST['Content3'],$_POST['Heading4'],$_POST['Content4'],$_POST['Heading5'],$_POST['Content5'],$strPicture1,$strPicture2);
        echo json_encode(['isSuccess'=>true,'strHtml'=>$strHtml]);
        
    }

    public function actionEmailMaxLimitation(){
        if(!empty($_POST)){
            $numEmailMax = $_POST['max_limitation'];
            $objSetting = SystemSetting::model()->find(' `key` = :key ',[':key'=>'marketing_email_max']); 
            $objSetting->mdata['email_max'] = $numEmailMax;
            $objSetting->save();
            echo json_encode(['isSuccess'=>true]);
        }
        else{
            $objSetting = SystemSetting::model()->find(' `key` = :key ',[':key'=>'marketing_email_max']); 
            $this->render('marketing_setting',[
                'model'=>$objSetting,
            ]);
        }
    }
}