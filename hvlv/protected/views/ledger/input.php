<?php

?>
<h1><?php echo $this->t('AP Input Invoice'); ?></h1>

<div class="form">
    <div id="ledger-input-search-data">
        <?php $this->renderPartial('_ledger_input_search',array('model' => $model)); ?>
    </div>
</div>


<script type="text/javascript">

    function ledgerInputSearch(){
        var data = $('#ledger-input-search-form').serialize();

        $.ajax({
            type : 'POST',
            url : '<?php echo Yii::app()->createAbsoluteUrl("ledger/ajaxSearchInput") ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                $('#ledger-input-search-data').html(resp);
            }
        });
    }

    function ledgerInputCommit(){
        var data = $('#ledger-input-search-form').serialize();

        $.ajax({
            type : 'POST',
            url : '<?php echo Yii::app()->createAbsoluteUrl("ledger/ajaxInputCommit") ;?>',
            data: data,
            dataType: 'json',
            success:function(resp){
                alert(resp.msg);
            }
        });
    }

</script>


