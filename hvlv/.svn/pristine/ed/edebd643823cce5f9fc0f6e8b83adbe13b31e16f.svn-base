<?php

?>
<h1><?php echo $this->t('AP Approve Invoice'); ?></h1>

<div class="form">
    <div id="ledger-approve-search-data">
        <?php $this->renderPartial('_ledger_approve_search',array('model' => $model)); ?>
    </div>
</div>

<script type="text/javascript">

    function ledgerApproveSearch(){
        var data = $('#ledger-approve-search-form').serialize();

        $.ajax({
            type : 'POST',
            url : '<?php echo Yii::app()->createAbsoluteUrl("ledger/ajaxSearchApprove") ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                $('#ledger-approve-search-data').html(resp);
            }
        });
    }

    function ledgerApproveCommit(){
        var data = $('#ledger-approve-search-form').serialize();

        $.ajax({
            type : 'POST',
            url : '<?php echo Yii::app()->createAbsoluteUrl("ledger/ajaxApproveCommit") ;?>',
            data: data,
            dataType: 'json',
            success:function(resp){
                alert(resp.msg);
            }
        });
    }

</script>


