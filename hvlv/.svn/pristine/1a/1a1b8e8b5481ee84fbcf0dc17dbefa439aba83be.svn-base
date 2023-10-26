<?php 
class CMarketingAddress{
    const dicYesNo2Numner = [
        'yes'=>1,
        'no'=>0,
    ];

    private $listError = [];

    public function getListError(){
        return $this->listError;
    }

    public function funcNewMarketingAddress($listPost){
        $objMarketingAddress = new MarketingAddress;
        $objMarketingAddress = $this->funcSetMarketingAddress($objMarketingAddress,$listPost);
        $objMarketingAddress->save();
    }

    public function funcUpdateMarketingAddress($numId,$listPost){
        $objMarketingAddress = MarketingAddress::model()->findByPk($numId);
        $objMarketingAddress = $this->funcSetMarketingAddress($objMarketingAddress,$listPost);
        $objMarketingAddress->save();
    }

    private function funcSetMarketingAddress($objMarketingAddress, $listPost){
        $objMarketingAddress->email =  $listPost['email'];
        $objMarketingAddress->company =  $listPost['company'];
        $objMarketingAddress->country =  $listPost['country'];
        $objMarketingAddress->contact =  $listPost['contact'];
        $objMarketingAddress->tel =  $listPost['tel'];
        $objMarketingAddress->imp =  $listPost['imp'];
        $objMarketingAddress->tpl =  $listPost['tpl'];
        $objMarketingAddress->tld =  $listPost['tld'];
        $objMarketingAddress->status =  $listPost['status'];
        $objMarketingAddress->group =  $listPost['group'];

        return $objMarketingAddress;
    }

    public function funcImportExcel($strExcelName){
        $xls = new oExcel;
        $xls->load($strExcelName);
        $listRow = $xls->getAll();
        // Check
        $this->funcCheckListRow($listRow);
        $this->funcCheckAlreadyExist($listRow);

        if(empty($this->listError)){
            // Import
            foreach($listRow as $i => $objRow){
                if($i ==1){
                    continue;
                }
                $this->funcNewAddressFromExcel($objRow);
            }
        }
    }

    function funcCheckListRow($listRow){
        foreach($listRow as $i => $objRow){
            if($i ==1){
                continue;
            }
            $this->funcCheckAddress($i,$objRow[1],$objRow[2],$objRow[3],$objRow[4],$objRow[5],$objRow[10]);
        }
    }

    function funcCheckAlreadyExist($listRow){
        $strEmail = '';
        foreach($listRow as $i => $objRow){
            if($i ==1){
                continue;
            }
            $strEmail .= '"'.$objRow[4].'"';
            if($i != sizeof($listRow)){
                $strEmail .= ',';
            }
        }
        $listMarketingAddress = MarketingAddress::model()->findAll('email in ('.$strEmail.')');
        foreach($listMarketingAddress as $objMarketingAddress){
            $this->listError[]='email already exise: '.$objMarketingAddress->email;
        }
    }

    public function funcCheckMarketingAddress($listPost){
        $this->funcCheckAddress(1,$listPost['company'],$listPost['country'],$listPost['contact'],$listPost['email'],$listPost['tel'],$listPost['group']);
        if(empty($listPost['id'])){
            $listMarketingAddress = MarketingAddress::model()->findAll('email = "'.$listPost['email'].'"');
            foreach($listMarketingAddress as $objMarketingAddress){
                $this->listError[]='email already exise: '.$objMarketingAddress->email;
            }
        }
    }

    function funcCheckAddress($numLine,$company,$country,$contact,$email,$tel,$group){
        if(empty($company)){
            $this->listError[]='line:'.$numLine.' empty company.';
        }
        if(empty($country)){
            $this->listError[]='line:'.$numLine.' empty country.';
        }
        if(empty($contact)){
            $this->listError[]='line:'.$numLine.' empty contact.';
        }
        if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email)) {
            $this->listError[]='line:'.$numLine.' invalid email.';
        }
        // if (!preg_match("/^(04)\d{8}$/",$tel)) {
        //     $this->listError[]='line:'.$numLine.' tel 04xxxxxxxx.';
        // }
        if(empty($group)){
            $this->listError[]='line:'.$numLine.' empty group.';
        }
    }

    function funcNewAddressFromExcel($objRow){
        $objMarketingAddress = new MarketingAddress;

        $objMarketingAddress->company =  $objRow[1];
        $objMarketingAddress->country =  $objRow[2];
        $objMarketingAddress->contact =  $objRow[3];
        $objMarketingAddress->email =  $objRow[4];
        $objMarketingAddress->tel =  $objRow[5];
        
        $objMarketingAddress->imp = self::dicYesNo2Numner[$objRow[6]];
        $objMarketingAddress->tpl = self::dicYesNo2Numner[$objRow[7]];
        $objMarketingAddress->tld = self::dicYesNo2Numner[$objRow[8]];
        $objMarketingAddress->status = self::dicYesNo2Numner[$objRow[9]];
        $objMarketingAddress->group =  $objRow[10];

        $objMarketingAddress->save();
    }

}
?>