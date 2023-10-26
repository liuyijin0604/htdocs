<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ConsolPortController extends Controller {
    
    public function filters()
	{
		return array();
	}
            
        
    public function actionIndex(){
        
        if(empty($_GET['id'])||empty($_GET['no'])||empty($_GET['poc'])){
           throw new CHttpException(400, 'Bad Request, not valid url');
        }
        $id=$_GET['id'];
        $no=$_GET['no'];
        $poc=$_GET['poc'];
        
        $r= Consol::model()->find('id=:id and no=:no and poc=:poc',array(':id'=>$id,':no'=>$no,':poc'=>$poc));
        if(empty($r)){
             throw new CHttpException(401, 'the consol id and number is not valid');
        }
     
        $this->render('//excoConsol/view_port',array('model'=>$r));
        }
           
        public function actionDownload($id){
            // verify at first!         
            if (empty($_GET['id']) || empty($_GET['no']) || empty($_GET['poc'])) {
                throw new CHttpException(400, 'Bad Request, not valid url');
             }
                  $no=$_GET['no'];
                  $poc=$_GET['poc'];
                  $r= Consol::model()->find('id=:id and no=:no and poc=:poc',array(':id'=>$id,':no'=>$no,':poc'=>$poc));
                   if(empty($r)){
                      throw new CHttpException(401, 'the consol id and number is not valid');
                      }
                  $this->forward('excoConsol/download');             
         }
         
         
      public function actionExport($id){
           // verify at first!
             if (empty($_GET['id']) || empty($_GET['no']) || empty($_GET['poc'])) {
                throw new CHttpException(400, 'Bad Request, not valid url');
             }
                  $no=$_GET['no'];
                  $poc=$_GET['poc'];
                  $r= Consol::model()->find('id=:id and no=:no and poc=:poc',array(':id'=>$id,':no'=>$no,':poc'=>$poc));
             if(empty($r)){
                      throw new CHttpException(401, 'the consol id and number is not valid');
               }
          $this->forward('excoConsol/export');  
      }
      
}