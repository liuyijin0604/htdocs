<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
ob_start();
class ApiLiveChatWebhookAction extends CAction
{
    public function run()
    {
       $this->liveChat();
    }

    public function liveChat()
    {
        $data = file_get_contents('php://input');        
        $data_decode = json_decode($data);

        if ($data_decode->event_type === 'chat_ended') {
            // code to send email
            $subject= "Test Email";
            $htmlStr= print_r($data, true);
            $chatService = new LiveChatService();
            $chatService->saveChat($data_decode);
            // $chatRecordsModels = SalesfunnelRequirementsRecords::model()->with('question','answer')->findAll(["condition"=>'submit_id =  '.($submit_id),"order"=>"t.question_id ASC"]);
            // $emailService = new EmailService();
            // $emailService->sendNormalEmail("weijt133@gmail.com",$subject,$htmlStr,"ray.tang@toplogistics.com.au");
            //mail('weijt133@gmail.com', 'Chat transcript', print_r($data, true));
        }
    }

    Public function writeData($txt)
    {
        $myfile = fopen("C:/Users/weijt/Desktop/Sales Funnel/newfile.txt", "w") or die("Unable to open file!");
        // $txt = "John Doe\n";
        fwrite($myfile, $txt);
        fclose($myfile);
    }
}