<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
     <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.min.js"></script>
     <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-ui.min.js"></script>
     <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/cart.min.js"></script>
   

    <link rel="icon" href="<?php echo Yii::app()->request->baseUrl; ?>/favicon.ico">

    <title>PCA Express</title>

    <!-- Bootstrap core CSS -->
  <link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap4.min.css" type="text/css"/>
   <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">-->
<style >
.header,.marketing,.footer {padding-right: 1rem;padding-left: 1rem;}.header {padding-bottom: 1rem;border-bottom: .05rem solid #e5e5e5;}
/* Make the masthead heading the same height as the navigation */
.header h3 {margin-top: 0; margin-bottom: 0; line-height: 3rem;}
.footer {padding-top: 1.5rem;color: #777;border-top: .05rem solid #e5e5e5; }

/* Customize container */
@media (min-width: 48em) {
  .container {
    max-width: 45rem;
  }
}
.jumbotron{
    margin-top: 2em;
    padding-top: 2em;
}

/* Responsive: Portrait tablets and up */
@media screen and (min-width: 48em) {
  /* Remove the padding we set earlier */
  .header,
  .marketing,
  .footer {
    padding-right: 0;
    padding-left: 0;
  }
  /* Space out the masthead */
  .header {
    margin-bottom: 2rem;
  }
}
</style> 
  </head>
  
  <body>
      <nav class="navbar  navbar-inverse bg-inverse ">
      <a class="navbar-brand" href="https://www.pcaexpress.com.au/">PCA Express</a>
      </nav>
<?php $poc_to=$_GET['poc']?>
      <div class="container">
        <div class="jumbotron">
              <h4 >Consol Number:<?= $model->no ?></h4>
              <hr>
              <div class="row">
                  <div class="col-md-6"> 
                      <h5><b>POL:</b>&nbsp&nbsp<?= $model->pol ?></h5>
                      <h5><b>Flight Number:</b> <?= $model->flight ?></h5>
                      <h5><b>ETD:</b> <?= $model->etd ?></h5>
                      <h5><b>AWB Weight:</b> <?= $model->mdata['awb_check_wt'] ?></h5> 
                   </div>
                   <div class="col-md-6">
                       <h5><b>POD:</b> <?= $model->pod ?></h5>
                       <h5><b>AWB Number:</b> <?= $model->awb ?></h5>
                       <h5><b>ETA:</b> <?= $model->eta ?></h5>
                       <h5><b>Channel:</b><?=ExChannel::getName($model->poc);?></h5>
                  </div>
              </div>
        </div>
          <div class="row">
            
              <div class="col-lg-7">
                  <ul>
                     
                      <?php
                      foreach (ExChannel::getPocs() as $i => $cp) {
                          if(!preg_match("/".substr($i,0,4)."/", $poc_to)) continue;
                          if($i=='CNXM3')                              continue;
                          if (in_array($i, ['CNPEK', 'CNCAN', 'CNTAO', 'CNTA2', 'STO', 'CNJNA', 'CNJM2', 'CNCA2', 'CNXM2', 'CNGYA', 'A2U']))
                              continue;
                          echo '<li><a href="', $this->createUrl('consolPort/export', array('id' => $model->id, 'type' => $i,'no'=>$model->no,'poc'=>$model->poc)), '" target="_blank">', ($i == $model->poc ? '<b>' . $cp . ' Manifest</b>' : $cp . ' Manifest'), '</a></li>';
                      }
                      ?>
                  </ul>
              </div>
              <div class="col-lg-5" style="display:none;">
                  <ul>
                      <li><a href="<?= $this->createUrl('consolPort/download', array('id' => $model->id, 'type' => 'id', 'joint' => 1,'no'=>$model->no,'poc'=>$model->poc)); ?>" target="_blank">ID Joint</a></li>
       <?php if($poc_to=='CNXIA'):?> 
             <li><a href="<?= $this->createUrl('consolPort/download', array('id' => $model->id, 'type' => 'id_valid','no'=>$model->no,'poc'=>$model->poc)); ?>" target="_blank">ID Validation</a></li> 
             <li><a href="<?= $this->createUrl('consolPort/download', array('id' => $model->id, 'type' => '3in1','no'=>$model->no,'poc'=>$model->poc)); ?>" target="_blank">Xi'an 3 IN 1</a></li>
       <?php endif;?>
       <?php if($poc_to=='CNXM2'):?> 
              <li><a href="<?= $this->createUrl('consolPort/download', array('id' => $model->id, 'type' => 'label','no'=>$model->no,'poc'=>$model->poc)); ?>" target="_blank">Courier Labels (JPG)</a></li>
       <?php endif;?>
        
      <?php if($poc_to=='CNCS2'||$poc_to=='CNCSX'):?> 
		    <li><a href="<?=$this->createUrl('excoConsol/download', array('id'=>$model->id, 'type'=> 'label', 'ft' => 'pdf','no'=>$model->no,'poc'=>$model->poc));?>" target="_blank">Courier Labels (PDF)</a></li>
                     <li><a href="<?= $this->createUrl('consolPort/download', array('id' => $model->id, 'type' => 'rcpt','no'=>$model->no,'poc'=>$model->poc)); ?>" target="_blank">Shopping Receipts</a></li>
       <?php endif;?>                
                 </ul>
              </div>
          </div>
          <?php
          $fr = new FileRepo('search');
          $fr->unsetAttributes();
          if (empty($_GET['FileRepo'])) {
              $fr->status = 20;
              $fr->type = 80;
          } else {
              $fr->attributes = $_GET['FileRepo'];
              if (empty($fr->type))
                  $fr->type = 80;
          }
          $fr->fid = $model->id;
          $mf = Acl::hasAccess('B:org/manageFile');

          $this->widget('zii.widgets.grid.CGridView', array(
              'id' => '_excofile-grid',
              'summaryText' => '',
              'dataProvider' => $fr->search(),
              'filter' => $fr,
              'columns' => array(
                  array('name' => 'name', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->baseUrl."/filerepo/".$data->hash."/".$data->name."\" target=\"_blank\">".$data->name."</a>"'),
                  array(
                      'name' => 'size',
                      'value' => '$data->formatSize()',
                      'filter' => false,
                  ),
                  'date',
              ),
          ));
          ?>
     
          <footer class="footer">
              <p style="margin: auto 220px">&copy; PCAExpress 2017</p>
          </footer>

      </div> 
     
  </body>
</html>