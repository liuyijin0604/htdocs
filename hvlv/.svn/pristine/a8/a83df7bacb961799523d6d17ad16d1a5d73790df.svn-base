<h3>Reply Record:</h3>
<div id='accordion_1<?=$_GET['tabid']?>'>
<?php foreach($model->reply_mails as $index=> $reply):?>
    <h3 >Reply-<?=$index+1?>-By-<?=$reply->getReplyOp()?></h3>
    <div>
         <p><b>Reply Subject:</b> <?=$reply->reply_subject?></p>
        <p><b>Reply Time:</b> <?=$reply->reply_time?></p>
        <p><b>To Address:</b> <?=$reply->reply_email?></p>
        <p><b>CC:</b> <?=$reply->mdata['cc']?></p>
       <?php
            $attachements =$reply->attachments;
            $attachIndex = 1;
            if(!empty($attachements)): ?>
    <h2>Attachments</h2>
    <div><ul id="attachements-reply-div">
            <?php
            foreach ( $attachements as $attachement ) {
                $line = '<li>' .  $attachIndex++ . '. <a target="_blank" href="' . $attachement->getUrl() . '" >' . $attachement->name . '</a></li>';
                echo $line;
            }
            ?>
        </ul>
    </div>
<?php endif;?>
    <h2>Email Body</h2>
      <?php
      $reply->loadReplyBody();
      echo $reply->reply_body?>
    </div>
 <?php endforeach;?>
</div>

<script>
  $( function() {
      var tab=$('#<?=$_GET['tabid']?>');
      var panel=tab.data('panel');
       $( "#accordion_1<?=$_GET['tabid']?>",panel ).accordion({
            collapsible: true,
           heightStyle: "content"
          });
  } );
</script>
 

