<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'import-new-statements-form',
        'enableAjaxValidation'=>false,
        'action' => $this->createUrl('bank/AjaxImportStatements'),
    ));
    ?>
    <div class="row">
        <label for="bank-statements">Bank Statements- <small>.xlsx File</small>(<a href="../template/bank_statement_template.xlsx" target="_blank">Tempalte file</a>)</label><br>
        <input type="file" name="banksfile" id="banks_file" />
    </div>

    <br>

    <p><input id="banks_statements_import_btn" type="submit" value="Submit" /></p>
    <?php $this->endWidget(); ?>
</div>
<div id="banks_statements_import_result" style="margin: 10px 0; border: 1px solid;padding:20px; font-weight: bold; font-size: 20px;">
</div>

<script type="text/javascript">
    $(function(){
        var tab = $('#jqmw_<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');


        $('form#import-new-statements-form', panel).data('custom_success', function(r){
            $('#banks_statements_import_result', panel).empty().prepend($('<p>'+r.msg+'</p>').fadeIn());
            $('#banks_statements_import_btn', panel).attr('disabled', false);

            $('.popCancel', panel).on('click', function() {
                $('#bank-statement-grid').yiiGridView('update');

                r.balance = r.balance ? r.balance : 0;
                r.reconciled = r.reconciled ? r.reconciled : 0;
                r.unreconciled = r.unreconciled ? r.unreconciled : 0;
                $('#statementBalance').text(r.balance.toFixed(2));
                $('#reconciledAmount').text(r.reconciled.toFixed(2));
                $('#unreconciledAmount').text(r.unreconciled.toFixed(2));
            });
            return true;
        });
    });
</script>