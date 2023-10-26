<style>
@media (min-width: 1200px) {
    #pacerate .container{
        max-width: 900px;
    }
}
</style>
<div id="pacerate">
<div class="container">
<h1><?=$this->t('Flex Rate By Zone');?></h1>

<?php echo $this->renderPartial('_form_pca_rate', array('model'=>$model,'chgcodeid' => $chgcodeid)); ?>
</div>
</div>