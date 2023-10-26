<?php

class LiveChatController extends Controller
{
	 /**
	 * Declares class-based actions.
	 */
    protected $nonAjax = array('addLiveChat','writeData','createEmailDaily');
    protected $skipAcl = array('addLiveChat','writeData','createEmailDaily');
    protected $skipLogin = array('addLiveChat','writeData','createEmailDaily');
    
    public function beforeAction($action){
        $this->layout="sales";
        return parent::beforeAction($action);
    }

    public function actions(){
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha'=>array(
                'class'=>'CCaptchaAction',
                'backColor'=>0xEBF0FA,
                'maxLength' => 4,
                'minLength' => 4,
                'livechatWebhook' => 'application.controllers.ApiLiveChatWebhookAction',
            ),
        );
    }

    public function actionAddLiveChat(){
        $this->renderPartial('live_chat_page');
    }

    public function actionCreateEmailDaily(){      
        $liveChatService = new LiveChatService();
        $liveChatService->createChatEmailDaily(Date("Y-m-d"));
    }

    public function actionWriteData(){
        $myfile = fopen("C:/Users/weijt/Desktop/Sales Funnel/newfile.txt", "w") or die("Unable to open file!");
        $txt = "John Doe\n";
        fwrite($myfile, $txt);
        fclose($myfile);
    }

}
