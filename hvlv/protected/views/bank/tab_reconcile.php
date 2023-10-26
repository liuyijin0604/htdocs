<div id="AllDone">
    <div class="all-done-container">
        <div class="all-done">
            <div style="background-position:0px -752px;float:left;" class="icon"></div>
            <span class="title">Great job!</span><span>You've reconciled all the transactions for this account</span>
        </div>
        <div class="statement-balance">
            <span>Statement Balance</span>
            <span class="statement-date"><?php echo $model->created; ?></span>
            <span class="statement-amount" data-automationid="statementBalance"><?php echo number_format($model->balance,2); ?>(AUD)</span>
        </div>
    </div>
</div>

<script type="text/javascript">

</script>
