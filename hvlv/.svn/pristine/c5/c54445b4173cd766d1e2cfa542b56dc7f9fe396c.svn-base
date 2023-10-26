<div>
  <h1>Sending History</h1>
  <?php
  $listMarketingEmail=MarketingEmail::model()->findAll();
  foreach($listMarketingEmail as $objMarketingEmail){
      $listSendHistory = [];
      if(isset($objMarketingEmail->mdata['History'] )){
        foreach($objMarketingEmail->mdata['History'] as $strHistory){
            if(strpos($strHistory,MarketingEmail::listStatus[MarketingEmail::status_sent]) !== false){ 
                $listSendHistory[] =  $strHistory;
            }
          }
          if(!empty($listSendHistory)){
              echo '<h3>'.$objMarketingEmail->subject.'</h3>';
              foreach($listSendHistory as $strHistory){
                echo '<h5>'.$strHistory.'</h5>';
              }
              echo '<br/>';
          }
      }
  }
  ?>
</div>