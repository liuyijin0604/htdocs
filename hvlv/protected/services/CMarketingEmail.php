<?php
class CMarketingEmail{
    
    // public function funcNewMarketingEmail($listPost,$listFile){
    //     $objMarketingEmail = new MarketingEmail;
    //     $objMarketingEmail = $this->funcSetMarketingEmailFromPost($objMarketingEmail,$listPost);
    //     //Picture
    //     if (isset($listFile['picture1'])) {
    //         $numId = FileRepo::storeFile($listFile['picture1']['tmp_name'], $listFile['picture1']['name'], FileRepo::type_marketing_picture, 0);
    //         $objFileRepo = FileRepo::model()->findByPk($numId);
    //         $objMarketingEmail->mdata['Picture1'] = Yii::app()->createAbsoluteUrl('/') . '/filerepo/' . $objFileRepo->hash . '/' . $objFileRepo->name;
    //     }
    //     if ($listPost['no_picture1'] == 'true') {
    //         $objMarketingEmail->mdata['Picture1'] =  '';
    //     }

    //     if (isset($listFile['picture2'])) {
    //         $numId = FileRepo::storeFile($listFile['picture2']['tmp_name'], $listFile['picture2']['name'], FileRepo::type_marketing_picture, 0);
    //         $objFileRepo = FileRepo::model()->findByPk($numId);
    //         $objMarketingEmail->mdata['Picture2'] = Yii::app()->createAbsoluteUrl('/') . '/filerepo/' . $objFileRepo->hash . '/' . $objFileRepo->name;
    //     }
    //     if ($listPost['no_picture2'] == 'true') {
    //         $objMarketingEmail->mdata['Picture2'] =  '';
    //     }

    //     // max limitation
    //     $objMarketingEmail->mdata['address_count'] = 0;

    //     $objMarketingEmail->status = MarketingEmail::status_new;
    //     $objMarketingEmail->mdata['History'] =  [];
    //     $objMarketingEmail->mdata['History'][] = date('Y-m-d H:i:s').' - '.MarketingEmail::listStatus[MarketingEmail::status_new];

    //     $objMarketingEmail->save();
    // }

    // public function funcUpdateMarketingEmail($numIdMarketingEmail,$listPost,$listFile){
    //     $objMarketingEmail = MarketingEmail::model()->findByPk($numIdMarketingEmail);
    //     $objMarketingEmail = $this->funcSetMarketingEmailFromPost($objMarketingEmail, $listPost);
    //     //Picture
    //     if (isset($listFile['picture1'])) {
    //         $numId = FileRepo::storeFile($listFile['picture1']['tmp_name'], $listFile['picture1']['name'], FileRepo::type_marketing_picture, 0);
    //         $objFileRepo = FileRepo::model()->findByPk($numId);
    //         $objMarketingEmail->mdata['Picture1'] = Yii::app()->createAbsoluteUrl('/') . '/filerepo/' . $objFileRepo->hash . '/' . $objFileRepo->name;
    //     }
    //     if ($listPost['no_picture1'] == 'true') {
    //         $objMarketingEmail->mdata['Picture1'] =  '';
    //     }

    //     if (isset($listFile['picture2'])) {
    //         $numId = FileRepo::storeFile($listFile['picture2']['tmp_name'], $listFile['picture2']['name'], FileRepo::type_marketing_picture, 0);
    //         $objFileRepo = FileRepo::model()->findByPk($numId);
    //         $objMarketingEmail->mdata['Picture2'] = Yii::app()->createAbsoluteUrl('/') . '/filerepo/' . $objFileRepo->hash . '/' . $objFileRepo->name;
    //     }
    //     if ($listPost['no_picture2'] == 'true') {
    //         $objMarketingEmail->mdata['Picture2'] =  '';
    //     }

    //     $objMarketingEmail->mdata['History'][] = date('Y-m-d H:i:s').' - Update Email';

    //     $objMarketingEmail->save();
    // }

    // private function funcSetMarketingEmailFromPost($objMarketingEmail , $listPost){
    //     $objMarketingEmail->subject =  $listPost['subject'];
    //     $objMarketingEmail->mdata['Heading1'] =  $listPost['Heading1'];
    //     $objMarketingEmail->mdata['Content1'] =  $listPost['Content1'];
    //     $objMarketingEmail->mdata['Heading2'] =  $listPost['Heading2'];
    //     $objMarketingEmail->mdata['Content2'] =  $listPost['Content2'];
    //     $objMarketingEmail->mdata['Heading3'] =  $listPost['Heading3'];
    //     $objMarketingEmail->mdata['Content3'] =  $listPost['Content3'];
    //     $objMarketingEmail->mdata['Heading4'] =  $listPost['Heading4'];
    //     $objMarketingEmail->mdata['Content4'] =  $listPost['Content4'];
    //     $objMarketingEmail->mdata['Heading5'] =  $listPost['Heading5'];
    //     $objMarketingEmail->mdata['Content5'] =  $listPost['Content5'];

    //     return $objMarketingEmail;
    // }

        
    public function funcNewMarketingEmail($strSubject,$strHtml){
        $objMarketingEmail = new MarketingEmail;

        $objMarketingEmail->subject = $strSubject;
        $objMarketingEmail->html = $strHtml;

        // max limitation
        $objMarketingEmail->mdata['address_count'] = 0;

        $objMarketingEmail->status = MarketingEmail::status_new;
        $objMarketingEmail->mdata['History'] =  [];
        $objMarketingEmail->mdata['History'][] = date('Y-m-d H:i:s').' - '.MarketingEmail::listStatus[MarketingEmail::status_new];

        $objMarketingEmail->save();

        //online view
        $strUrl = ' http://'.$_SERVER['HTTP_HOST'].'/ims/customerService/marketingEmailOnlineView?id='.$objMarketingEmail->id;
        if($_SERVER['HTTP_HOST'] == 'ims.toplogistics.com.au'){
            $strUrl = ' https://'.$_SERVER['HTTP_HOST'].'/customerService/marketingEmailOnlineView?id='.$objMarketingEmail->id;
        }
        $strA='<a href="'.$strUrl.'">View Online</a><br/>';
        $strTemplete = str_replace('--OnlineView--',$strA,MarketingEmail::templete_tla);
        $objMarketingEmail->html = $strTemplete;
        $objMarketingEmail->save();

        return $objMarketingEmail;
    }

    public function funcUpdateMarketingEmail($numIdMarketingEmail,$strSubject,$strHtml){
        $objMarketingEmail = MarketingEmail::model()->findByPk($numIdMarketingEmail);

        $objMarketingEmail->subject = $strSubject;
        $objMarketingEmail->html = $strHtml;

        $objMarketingEmail->mdata['History'][] = date('Y-m-d H:i:s').' - Update Email';

        $objMarketingEmail->save();
        return $objMarketingEmail;
    }



    public function funcSchedule($numIdMarketingEmail,$strTime,$listGroup){
        $objMarketingEmail = MarketingEmail::model()->findByPk($numIdMarketingEmail);
        $objMarketingEmail->date = $strTime;
        $objMarketingEmail->status = MarketingEmail::status_pending;
        $objMarketingEmail->mdata['History'][] = date('Y-m-d H:i:s').' - '.MarketingEmail::listStatus[MarketingEmail::status_pending];
        $objMarketingEmail->mdata['Group'] = $listGroup;
        $objMarketingEmail->save();
    }

    public function funcCancel($numIdMarketingEmail){
        $objMarketingEmail = MarketingEmail::model()->findByPk($numIdMarketingEmail);
        $objMarketingEmail->status = MarketingEmail::status_cancel;
        $objMarketingEmail->mdata['History'][] = date('Y-m-d H:i:s').' - '.MarketingEmail::listStatus[MarketingEmail::status_cancel];

        $objMarketingEmail->save();
    }

    private function funcSend($strBcc,$objMarketingEmail){
        $objEmailService = new EmailService;
        // $strHtml = $this->funcEmailHtml(
        //     $objMarketingEmail->mdata['Heading1'],
        //     $objMarketingEmail->mdata['Content1'],
        //     $objMarketingEmail->mdata['Heading2'],
        //     $objMarketingEmail->mdata['Content2'],
        //     $objMarketingEmail->mdata['Heading3'],
        //     $objMarketingEmail->mdata['Content3'],
        //     $objMarketingEmail->mdata['Heading4'],
        //     $objMarketingEmail->mdata['Content4'],
        //     $objMarketingEmail->mdata['Heading5'],
        //     $objMarketingEmail->mdata['Content5'],
        //     $objMarketingEmail->mdata['Picture1'],
        //     $objMarketingEmail->mdata['Picture2'],
        // );
        $strHtml = $objMarketingEmail->html;

        return $objEmailService->funcSendMarketingEmail($strBcc,$objMarketingEmail->subject,$strHtml);
    }

    public function funcEmailHtml($strHeading1,$strContent1,$strHeading2,$strContent2,$strHeading3,$strContent3,$strHeading4,$strContent4,$strHeading5,$strContent5,$strPicture1,$strPicture2){
        $strHtml = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>TLA</title>
        </head>
        <body>
        <div style="background-color:white">
            <img src="http://branch.toplogistics.com.au:8189/images/marketing_email_header.jpg"alt="tla" />
        ';

        $strHtml .= $this->funcEmailHtmlHeadingContent($strHeading1,$strContent1);
        $strHtml .= $this->funcEmailHtmlHeadingContent($strHeading2,$strContent2);
        $strHtml .= $this->funcEmailHtmlHeadingContent($strHeading3,$strContent3);
        $strHtml .= $this->funcEmailHtmlHeadingContent($strHeading4,$strContent4);
        $strHtml .= $this->funcEmailHtmlHeadingContent($strHeading5,$strContent5);
        
        // Picture1 Picture2
        if(!empty($strPicture1)&&!empty($strPicture2)){
            $strHtml .='<img src="'.$strPicture1.'"  alt="tla" width="260" height="230" style="margin-left:30px;border-radius: 15px"/>';
            $strHtml .='<img src="'.$strPicture2.'"  alt="tla" width="260" height="230" style="margin-left:20px;border-radius: 15px"/>';
        }
        else if(!empty($strPicture1)&&empty($strPicture2)){
            $strHtml .='<img src="'.$strPicture1.'"  alt="tla" width="540" height="230" style="margin-left:30px;border-radius: 15px"/>';
        }
        else if(empty($strPicture1)&&!empty($strPicture2)){
            $strHtml .='<img src="'.$strPicture2.'"  alt="tla" width="540" height="230" style="margin-left:30px;border-radius: 15px"/>';
        }


        $strHtml .='
        <div style="height:50px;margin-top:30px">
            <div style="border-top: 1px solid rgb(41, 50 ,106);color: rgb(209, 81 ,76);font-size: 15px;font-weight: 900;font-family: Arial, Helvetica, sans-serif;float:left;vertical-align: top;width: 120px;height: 30px;padding-left: 30px;padding-top: 10px;">
                CONTACT US
            </div>
        
            <div style="background-color: rgb(41, 50 ,106);color: white;float:left;vertical-align: top;width: 440px;padding-left: 10px;padding-top: 13px;padding-bottom: 15px;">
                sales@toplogistics.com.au | toplogistics.com.au | 02 90668206
            </div>
        
        </div>

        </div>
        </body>
        </html>';

        return $strHtml;
    }

    private function funcEmailHtmlHeadingContent($strHeading,$strContent){
        $strHtml='';
        if(!empty($strHeading)&&!empty($strContent) ){
            $strContent =  str_replace('
','<br/>',$strContent);
            $strHtml.='
            <div style="border:2px solid rgb(209, 81 ,76);width: 540px;margin-left: 30px;margin-bottom: 20px;">
                <div style="background-color: rgb(209, 81 ,76);text-align:center;font-size: 20px;color: white;padding: 3px;">
                '.$strHeading.'
                </div>
                <div style="font-size: 18px;padding: 10px;word-wrap:break-word">
                '.$strContent.'
                </div>
            </div>';
        }
        return  $strHtml;
    }



    public function funcSendTestEmail($numIdMarketingEmail,$strBcc){
        $objMarketingEmail = MarketingEmail::model()->findByPk($numIdMarketingEmail);
        $this->funcSend($strBcc,$objMarketingEmail);
        $objMarketingEmail->mdata['History'][] = date('Y-m-d H:i:s').' - '.MarketingEmail::listStatus[MarketingEmail::status_sent] .'(test)';
        $objMarketingEmail->save();
    }

    public function funcSendEmail($numIdMarketingEmail){
        $objMarketingEmail = MarketingEmail::model()->findByPk($numIdMarketingEmail);
        $listMarketingAddress = MarketingAddress::model()->findAll('status='.MarketingAddress::status_active);
        $filteredEmails = [];
		$emailSubGroups = [];
        $maxBcc = 40;       // The max number of bcc

        // Filter Email address by selected group(s)
        foreach ($listMarketingAddress as $objMarketingAddress) {
			foreach ($objMarketingEmail->mdata['Group'] as $group) {
				if ($objMarketingAddress->group == $group) {
					array_push($filteredEmails, $objMarketingAddress);
				}
			}
		}
        /**
         * Because the max number of Bcc is limited,
         * we need to divide $filteredEmails into sub groups.
         */
		$totalEmails = sizeof($filteredEmails);
		if ($totalEmails % $maxBcc == 0) {
			$numOfGroups = $totalEmails / $maxBcc;
		} else {
			$numOfGroups = floor($totalEmails / $maxBcc + 1);
		}
		for ($i = 0; $i < $numOfGroups; $i++) {
			array_push($emailSubGroups, array_slice($filteredEmails, $i * $maxBcc, $maxBcc));
		}
        /**
         * Send Email separately by sub group.
         */
		foreach ($emailSubGroups as $emailSubGroup) {
			$strAddress = '';
			foreach ($emailSubGroup as $emailAddress) {
				$strAddress .= $emailAddress->email . ';';
			}
			$this->funcSend($strAddress,$objMarketingEmail);
            $objMarketingEmail->mdata['History'][] = date('Y-m-d H:i:s').' - '.MarketingEmail::listStatus[MarketingEmail::status_sent] .'(mannual)';
            $objMarketingEmail->save();
		}        
    }

    // public function funcCheckPending(){
    //     $listMarketingEmail = MarketingEmail::model()->findAll('status='.MarketingEmail::status_pending);
    //     if(!empty($listMarketingEmail)){
    //         $listMarketingAddress = MarketingAddress::model()->findAll('statuse='.MarketingAddress::status_active);
    //         $strAddress = '';
    //         foreach($listMarketingAddress as $objMarketingAddress){
    //             $strAddress.=$objMarketingAddress->email.';';
    //         }
    //         foreach($listMarketingEmail as $objMarketingEmail){
    //             $this->funcSend($strAddress,$objMarketingEmail);
    //             $objMarketingEmail->status = MarketingEmail::status_sent;
    //             $objMarketingEmail->mdata['History'][] = date('Y-m-d H:i:s').' - '.MarketingEmail::listStatus[MarketingEmail::status_sent] .'(schedule)';
    //             $objMarketingEmail->save();
    //         }
    //     }
    // }

    public function funcCheckPending(){
        $strTimeNow =  date('Y-m-d H:i:s');

        $listMarketingEmail = MarketingEmail::model()->findAll('status='.MarketingEmail::status_pending.' and date <"'.$strTimeNow.'"');

        foreach($listMarketingEmail as $objMarketingEmail){
            // marketing email max
            $objSetting = SystemSetting::model()->find(' `key` = :key ',[':key'=>'marketing_email_max']); 

            $strCurrentDate = $objSetting->mdata['current_date'];
            $strDateToday =  date('Y-m-d');
            if($strCurrentDate != $strDateToday){
                $objSetting->mdata['current_count'] = 0;
                $objSetting->mdata['current_date'] = $strDateToday;
                $objSetting->save();
            }

            $numEmailMax = $objSetting->mdata['email_max'];
            if($numEmailMax ==  $objSetting->mdata['current_count']){
                break;
            }

            $listMarketingAddress = MarketingAddress::model()->findAll('status='.MarketingAddress::status_active);
            // group
            $listMarketingAddressGroupFilted = [];
            $listGroup = [];
            if(!empty($objMarketingEmail->mdata['Group'])){
                $listGroup = $objMarketingEmail->mdata['Group'];
            }
            foreach($listMarketingAddress as $i => $objMarketingAddress){
                $strGroup = $objMarketingAddress->group;
                if(in_array($strGroup, $listGroup)){
                    $listMarketingAddressGroupFilted[] =$objMarketingAddress;
                }
            }

            // amazon count limit 50
            $numLimit = 40;



            // $numAllAddressCount = sizeof($listMarketingAddress);
            $numAllAddressCount = sizeof($listMarketingAddressGroupFilted);

            // $strAddress = '';
            $listStrAddress = [];
            $numI = 0;
            $listStrAddress[$numI] = '';
            $munCount = 1;
            // $listGroup = [];
            // if(!empty($objMarketingEmail->mdata['Group'])){
            //     $listGroup = $objMarketingEmail->mdata['Group'];
            // }
            // foreach($listMarketingAddress as $i => $objMarketingAddress){
            foreach($listMarketingAddressGroupFilted as $i => $objMarketingAddress){
                if($i < $objMarketingEmail->mdata['address_count']){
                    continue;
                }

                if($objSetting->mdata['current_count'] < $numEmailMax){
                    // $strGroup = $objMarketingAddress->group;
                    // if(in_array($strGroup, $listGroup)){
                        // $strAddress.=$objMarketingAddress->email.';';
                    // }
                    if($munCount <=$numLimit){
                        $munCount++;
                    }
                    else{
                        $munCount = 0;
                        $numI++;
                        $listStrAddress[$numI] = '';
                    }

                    $listStrAddress[$numI] .= $objMarketingAddress->email.';';
                    $objMarketingEmail->mdata['address_count']++;
                    $objSetting->mdata['current_count'] ++;

                    // $objMarketingEmail->mdata['address_count']++;
                    // $objSetting->mdata['current_count'] ++;
                }
                else {
                    break;
                }
                
            }

            $strPercent = '('.$objMarketingEmail->mdata['address_count'].'/'.$numAllAddressCount.')';
            if($objMarketingEmail->mdata['address_count'] == $numAllAddressCount ){ // all send
                $objMarketingEmail->mdata['address_count'] =0;
                $objMarketingEmail->status = MarketingEmail::status_sent;
            }
            else{ //partial send
                
            }

            // $this->funcSend($strAddress,$objMarketingEmail);
            foreach($listStrAddress as $strAddress){
                $this->funcSend($strAddress,$objMarketingEmail);
            }

            $objMarketingEmail->mdata['History'][] = date('Y-m-d H:i:s').' - '.MarketingEmail::listStatus[MarketingEmail::status_sent] .'(schedule)'.$strPercent;
            // $objMarketingEmail->mdata['History'][] = date('Y-m-d H:i:s').' - '.MarketingEmail::listStatus[MarketingEmail::status_sent] .'(schedule)';
            $objMarketingEmail->save();
            $objSetting->save();
        }
    }

}
?>