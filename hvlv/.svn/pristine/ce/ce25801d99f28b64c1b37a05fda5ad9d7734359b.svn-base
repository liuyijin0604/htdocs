<?php if ( in_array($model->type ,[30,50,60,65] ) ) :?>
    <div>

        <span><input type="radio" name="cg_sell_type" value="0" <?php if ($model->getCgSellModel() == 0 ): ?> Checked <?php endif;?> > Free <input type="radio" name="cg_sell_type" value="1"  <?php if ($model->getCgSellModel() == 1 ): ?> Checked <?php endif;?> > Not Free</span> <br/>

        <br/>
        <?php echo CHtml::button($this->t('Refresh Consumables'),array('onclick' => 'refresh_org_cg();','id'=>'refresh-orgcg-btn')); ?>
        <?php
        $orgCgModel = new OrgCgPrice();
        $orgCgModel->unsetAttributes();
        $orgCgModel->org_id = $model->id;
        $this->widget('application.extensions.editablegrid.CEditableGridView', array(
            'id'=>'org-cg-inventory-grid-'.$_GET["tabid"],
            'cssFile' => false,
            'formUrl' => $this->createUrl('cgoods/updateOrgCgPriceLine'),
            'dataProvider'=>$orgCgModel->search(),
            'columns'=>array(
                array('header' => 'Consumables','name' => 'cg_id','type' => 'raw','value' => 'empty($data->cg) ? "" : $data->cg->name'),
                array('name' => 'price','class' => 'CEditableColumn'),
                array(
                    'class'=>'CEditableButtonColumn',
                    'template'=>'{edit} {cancel} {save}',
                ),
            ),
        )); ?>

        <?php echo CHtml::button($this->t('Save'),array('onclick' => 'setting_save();','id'=>'setting-btn-save')); ?>
    </div>
<?php else : ?>
    <div>Only for customer</div>
<?php endif; ?>

<script type="application/javascript">
    var tab = $('#<?=$_GET["tabid"];?>');
    var panel = tab.data('panel');

    function setting_save(){
        var sellType = $('input[name="cg_sell_type"]:checked',panel).val();
        var data = {'oid' : <?php echo json_encode($model->id); ?>,'stype' : sellType};
        $.ajax({
            type : 'POST',
            url : '<?php echo Yii::app()->createAbsoluteUrl("cgoods/saveCgSetting") ;?>',
            data: data,
            dataType: 'json',
            success:function(resp){
                if ( resp.success == 1 ) {
                    alert('saved succesfully!');
                } else {
                    alert(resp.err);
                }
            }
        });
    }

    function refresh_org_cg(){
        var data = {'oid' : <?php echo json_encode($model->id); ?>};
        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createAbsoluteUrl("cgoods/refreshOrgCgoods") ;?>',
            dataType: 'json',
            data : data,
            success:function(resp){
                if ( resp.success == 1 ) {
                    alert('refreshed succesfully!')
                    $('#org-cg-inventory-grid-<?=$_GET["tabid"];?>',panel).yiiGridView('update');
                } else {
                    alert(resp.err);
                }

            }
        });
    }

    $(document).ready(function(e){

        var cgSellType = <?php echo $model->getCgSellModel(); ?>;
        if ( cgSellType == 0 ) {
            $('#org-cg-inventory-grid-<?=$_GET["tabid"];?>',panel).hide();
            $('#refresh-orgcg-btn',panel).hide();
        }


        $('input[name="cg_sell_type"]',panel).click(function(e){
         if ( $(this).val() == 0 ) {
             $('#org-cg-inventory-grid-<?=$_GET["tabid"];?>',panel).hide();
             $('#refresh-orgcg-btn',panel).hide();
         } else {
             $('#org-cg-inventory-grid-<?=$_GET["tabid"];?>',panel).show();
             $('#refresh-orgcg-btn',panel).show();
         }
        });
    });

</script>


