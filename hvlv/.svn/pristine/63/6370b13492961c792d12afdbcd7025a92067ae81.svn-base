<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
     <link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.min.css" type="text/css"/>
     <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.min.js"></script>
     <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-ui.min.js"></script>
     <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.min.js"></script>
     <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/signature_pad.min.js"></script>
    <link rel="icon" href="<?php echo Yii::app()->request->baseUrl; ?>/favicon.ico">

    <title>PCA Express</title>
<style >
.header,.marketing,.footer {padding-right: 1rem;padding-left: 1rem;}.header {padding-bottom: 1rem;border-bottom: .05rem solid #e5e5e5;}
/* Make the masthead heading the same height as the navigation */
.header h3 {margin-top: 0; margin-bottom: 0; line-height: 3rem;}
.footer {padding-top: 1.5rem;color: #777;border-top: .05rem solid #e5e5e5; }

/* Customize container */
@media (min-width: 48em) {
  .container {
    max-width: 80rem;
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
      <?php 
    
?>
    <div class="container">
        <div class="jumbotron">
              <h3 >理赔信息表填写</h3>
              <hr>
              <div class="row">
                  <div class="col-md-12"> 
                      <h3><b>票号:</b>&nbsp;&nbsp;<?=$model->no?></h3>
                      <h3><b>包裹号码:</b>&nbsp;&nbsp;<?=$model->getshipmentNo();?></h3>
                   </div>
              </div>
        </div>
          <div class="row">
     </div>
          <?php 
          $tabs=[];
               $form_tab=$this->renderPartial('tab_claim_form',array('model'=>$model),true);
               $tabs[]=  array( 'label' => 'Claim Form','content' => $form_tab,'active' => true);
               $this->widget('application.extensions.booster.TbTabs',array(
                'id'=>'viw-task-'.$model->id,
                'type'=>'tabs',
                'tabs'=> $tabs,
                 ));
                ?>
          <footer class="footer">
              <p style="margin: auto 320px">&copy; PCAExpress 2017</p>
          </footer>

      </div> 
  </body>
</html>