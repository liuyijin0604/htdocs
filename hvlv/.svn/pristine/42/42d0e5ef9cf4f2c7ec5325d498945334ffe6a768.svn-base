<style>
    .uploading {
        background: url("https://www.pcaexpress.com.au/client/css/images/ajaxLoader.gif") no-repeat 0 0 !important;
        background-size: 20px 20px !important;
        background-color: white !important;
    }
    .error-msg, .refund-records {
        display: block;
    }
    /* Absolute Center Spinner */
    .loading {
    position: fixed;
    z-index: 999;
    height: 2em;
    width: 2em;
    overflow: visible;
    margin: auto;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    }

    /* Transparent Overlay */
    .loading:before {
    content: '';
    display: block;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.3);
    }

    /* :not(:required) hides these rules from IE9 and below */
    .loading:not(:required) {
    /* hide "loading..." text */
    font: 0/0 a;
    color: transparent;
    text-shadow: none;
    background-color: transparent;
    border: 0;
    }

    .loading:not(:required):after {
    content: '';
    display: block;
    font-size: 10px;
    width: 1em;
    height: 1em;
    margin-top: -0.5em;
    -webkit-animation: spinner 1500ms infinite linear;
    -moz-animation: spinner 1500ms infinite linear;
    -ms-animation: spinner 1500ms infinite linear;
    -o-animation: spinner 1500ms infinite linear;
    animation: spinner 1500ms infinite linear;
    border-radius: 0.5em;
    -webkit-box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.5) -1.5em 0 0 0, rgba(0, 0, 0, 0.5) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
    box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) -1.5em 0 0 0, rgba(0, 0, 0, 0.75) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
    }

    /* Animation */

    @-webkit-keyframes spinner {
    0% {
        -webkit-transform: rotate(0deg);
        -moz-transform: rotate(0deg);
        -ms-transform: rotate(0deg);
        -o-transform: rotate(0deg);
        transform: rotate(0deg);
    }
    100% {
        -webkit-transform: rotate(360deg);
        -moz-transform: rotate(360deg);
        -ms-transform: rotate(360deg);
        -o-transform: rotate(360deg);
        transform: rotate(360deg);
    }
    }
    @-moz-keyframes spinner {
    0% {
        -webkit-transform: rotate(0deg);
        -moz-transform: rotate(0deg);
        -ms-transform: rotate(0deg);
        -o-transform: rotate(0deg);
        transform: rotate(0deg);
    }
    100% {
        -webkit-transform: rotate(360deg);
        -moz-transform: rotate(360deg);
        -ms-transform: rotate(360deg);
        -o-transform: rotate(360deg);
        transform: rotate(360deg);
    }
    }
    @-o-keyframes spinner {
    0% {
        -webkit-transform: rotate(0deg);
        -moz-transform: rotate(0deg);
        -ms-transform: rotate(0deg);
        -o-transform: rotate(0deg);
        transform: rotate(0deg);
    }
    100% {
        -webkit-transform: rotate(360deg);
        -moz-transform: rotate(360deg);
        -ms-transform: rotate(360deg);
        -o-transform: rotate(360deg);
        transform: rotate(360deg);
    }
    }
    @keyframes spinner {
    0% {
        -webkit-transform: rotate(0deg);
        -moz-transform: rotate(0deg);
        -ms-transform: rotate(0deg);
        -o-transform: rotate(0deg);
        transform: rotate(0deg);
    }
    100% {
        -webkit-transform: rotate(360deg);
        -moz-transform: rotate(360deg);
        -ms-transform: rotate(360deg);
        -o-transform: rotate(360deg);
        transform: rotate(360deg);
    }
    }

    .loading-container {
        position: absolute;
        height: 100vh;
        width: 100vw;
        z-index: 1000;
        display: none;
    }
</style>

<h1><?= $this->t('Import Inbound Transactions'); ?></h1>

<div class="form">
<div class="loading-container" id="loading-container">
    <div class="loading">Loading&#8230;</div>
</div>
    <form id="inbound-transaction-form" method="post" enctype="multipart/form-data">
        <div class="row" style="display: none;">
            <input type="text" name="whatever" id="whatever" value="whatever" />
        </div>
        <div class="row">
            <label for="invoice_date" required="required">Date From </label>
            <?php echo CHtml::textField('invoice_date', '', array('class' => 'date_input', 'required' => true, 'autocomplete' => 'off')); ?>
        </div>
        <div class="row">
            <input type="file" id="transaction_file" name="transaction_file" required />
        </div>
        <div class="row buttons">
            <button id="submit_btn">Submit</button>
        </div>
    </form>
</div>
<br />
<div class="error-msg" id="err_msg" style="height: 100px;">

</div>

<script type="text/javascript">
    $(function() {
        $('#inbound-transaction-form').on('submit', function(e) {
            $('#loading-container').show();
            e.preventDefault();
            var formData = new FormData(this);
            //formData.append("transaction_file", transaction_file.files[0]);
            $.ajax({
                url: '<?= Yii::app()->createUrl("invoice/importInboundTransactions") ?>',
                type: 'POST',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(res) {
                    $('#loading-container').hide();
                    res = JSON.parse(res);
                    if (res.done == true) {
                        console.log('Yo!');
                        console.log(res);
                        //$('#err_message').text(res.processedData.errMsg);
                        /* for (let i = 0; i < res.processedData.errMsg.length; i++) {
                            $('#err_msg').append('<div class="row"><p>'+res.processedData.errMsg[i]+'</p></div>')
                        } */
                        //$('.error-msg').show();
                        //$('.refund-records').show();
                        $('#err_msg').append('<h3>Total amount in this file(include gst)</h3><p>'+(Math.round(res.processedData.totalInFile*100)/100).toFixed(2)+'</p>');
                        $('#err_msg').append('<h3>Amount already imported in this file(include gst)</h3><p>'+(Math.round(res.statisticsData.alreadyImported*100)/100).toFixed(2)+'</p>');
                        $('#err_msg').append('<h3>Total amount imported this time(include gst)</h3><p>'+(Math.round(res.statisticsData.totalImported*100)/100).toFixed(2)+'</p>');
                        $('#err_msg').append('<h3>Error Message:</h3>');
                        for (let key in res.processedData.errMsg) {
                            $('#err_msg').append('<div class="row"><p>'+res.processedData.errMsg[key]+'</p></div>');
                        }
                        /* for (let i = 0; i < res.processedData.refund.length; i++) {
                            $('#err_msg').append('<div class="row"><p>'+res.processedData.refund[i]+'</p></div>')
                        } */
                        myApp.notice(res.msg, 5000);
                    } else {
                        console.log('Hey!');
                        console.log(res);
                        //$('#err_message').text(res.processedData.errMsg);
                        for (let i = 0; i < res.processedData.errMsg.length; i++) {
                            $('#err_msg').append('<div class="row"><p>'+res.processedData.errMsg[i]+'</p></div>')
                        }
                        myApp.alert(res.msg, false);
                    }
                },
                error: function(res) {
                    $('#loading-container').hide();
                    console.log('Wo Cao');
                    myApp.alert('System error', false);
                }
            });
            return false;
        });

        /* $(win).off('click', '#btn-save').on('click', '#btn-save', function() {
            uploading_on($('#btn-save'));
            var formData = new FormData();
            formData.append('transaction_file', $('#transaction_file', win)[0].files[0]);
            $.ajax({
                url: '<?= Yii::app()->createUrl("pickupBooking/importTransactions") ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(r) {
                    uploading_off($('#btn-save'));
                    r = JSON.parse(r);
                    if (r.done) {
                        myApp.notice(r.msg, 5000);
                    } else {
                        myApp.alert(r.msg, false);
                    }
                },
                error: function(r) {
                    r = JSON.parse(r);
                    console.log(r.done);
                    console.log(r.msg);
                    uploading_off($('#btn-save'));
                    myApp.alert('System error', false);
                }
            });
        }) */
    });
</script>