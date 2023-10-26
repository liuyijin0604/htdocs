<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="language" content="en"/>
    <meta name="viewport" content="width=320,initial-scale=1, maximum-scale=1"/>
    <title>PCA Express Xero Api tester</title>
    <style type="text/css">
        <!--
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Verdana, Geneva, sans-serif;
            font-size: 12px;
            background-color: #000;
            color: #f1f1f1;
            padding: 20px 0 50px 0;
            margin: 0;
        }

        a {
            color: #BE3426;
        }

        nav a {
            display: block;
            width: 100%;
            color: #ccc;
            background: #666;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin: 0;
            line-height: 40px;
            text-decoration: none;
            border-bottom: 1px solid #ccc;
        }

        nav a.hi {
            color: #333;
            background: #ccc;
        }

        #olay {
            position: absolute;
            top: 0;
            width: 100%;
            height: 100%;
            z-index: 99;
            background: #000;
            text-align: center;
            padding-top: 100px;
            font-size: 20px;
            font-weight: bold;
        }

        #main, #log {
            padding: 10px;
            margin: 0;
            clear: both;
        }

        #log {
            height: 350px;
            overflow: auto;
        }

        #log p {
            margin: 0;
            padding-bottom: 5px;
            lin-height: 15px;
        }

        #log img {
            background: #ccc;
        }

        h2 {
            margin: 0;
            padding-bottom: 10px;
            font-size: 16px;
        }

        .btn {
            background-color: #666;
            color: #ccc;
            font-weight: bold;
            font-size: 16px;
            text-align: center;
            height: 35px;
            padding: 5px 20px;
            border: #ccc 2px solid;
            border-radius: 15px;
            margin: 5px 0 10px 0;
        }

        label {
            line-height: 28px;
        }

        .field {
            background-color: #000;
            color: #f1f1f1;
            font-weight: bold;
            font-size: 16px;
            border: #666 2px solid;
            padding: 4px 8px;
            line-height: 20px;
            margin-bottom: 10px;
        }

        .clear, .row {
            clear: both;
        }

        .row .col, .left {
            float: left;
        }

        .row .col input {
            max-width: 100%;
        }

        .row .col .field, .c12 {
            width: 100%;
        }

        .c9 {
            width: 75%;
        }

        .c8 {
            width: 66.66%;
        }

        .c6 {
            width: 50%;
        }

        .c4 {
            width: 33.33%;
        }

        .c3 {
            width: 25%;
        }

        .align-center {
            text-align: center;
        }

        button.sound-ctrl {
            position: fixed;
            width: 32px;
            height: 32px;
            border: 2px solid #ccc;
            border-radius: 8px;
            bottom: 5px;
            right: 60px;
            background: #000 url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAABABAMAAACJoGidAAAAA3NCSVQICAjb4U/gAAAALVBMVEX////MzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMxkerMOAAAAD3RSTlMAETNEVWZ3iJmqu8zd7v9lNdiyAAAACXBIWXMAAAsSAAALEgHS3X78AAAAHHRFWHRTb2Z0d2FyZQBBZG9iZSBGaXJld29ya3MgQ1M26LyyjAAAABZ0RVh0Q3JlYXRpb24gVGltZQAwMi8yMi8xNTD6qGwAAAC5SURBVDiNY2CgJ2BD4zPtQxOoe4fK136HJMCyatWqd0ABMQYGFbAA+zswYJjXwHRPAVlA94XuCxQVTPfeNaAIMMS9YcCvAsMMuC0Y7sBwKVa/YPoWIzyGNwD5VlsBwQeHx7wDCAFgiGkKyD6H80FhWlfADokpWKjLPmR8ZwASgMUL+zOGeQXIAkwvGeoWIAswvGKwI1EAwwy4LUjuuOeA7NK8Ara3KH4RQ/YLxLd9B9HDw4CkIBx0AAA1Tpx/gqjy9gAAAABJRU5ErkJggg==') center -30px no-repeat;
        }

        button.sound-ctrl.off {
            background-position: center 0;
        }

        ul.ac_list {
            list-style: none;
            border-top: 1px solid #666;
            padding: 0;
        }

        ul.ac_list li {
            padding: 10px;
            border-bottom: 1px solid #666;
            line-height: 20px;
            font-size: 20px;
        }

        -->
    </style>


    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/components/core-min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/components/md5-min.js"></script>



</head>

<body>


<div class="test-addcontact">
    <form id="xero-addcontact-form" name="LoginForm" method="post" action="<?php echo Yii::app()->request->baseUrl; ?>/api/xero/addContact">
        <div class="row align-left">
            <input type="hidden" name="api_id" value="pca_xero">
            <input type="hidden" name="data" id="addcontact_post_data" value="">
            <input type="hidden" name="method" value="addContact">
            <input type="hidden" name="sign" id="addcontact_post_sign" value="">
            <div class="row rowcol"><input type="submit" id="addcontact_form_submit" class="btn" value="Add Contact"/></div>
        </div>
    </form>
</div>



<div class="test-addinvoice">
    <form id="xero-addinvoice-form" name="LoginForm" method="post" action="<?php echo Yii::app()->request->baseUrl; ?>/api/xero/addInvoice">
        <div class="row align-left">
            <input type="hidden" name="api_id" value="pca_xero">
            <input type="hidden" name="data" id="addinvoice_post_data" value="">
            <input type="hidden" name="method" value="addInvoice">
            <input type="hidden" name="sign" id="addinvoice_post_sign" value="">
            <div class="row rowcol"><input type="submit" id="addinvoice_form_submit" class="btn" value="Add Invoice"/></div>
        </div>
    </form>
</div>



<script type="text/javascript">


    $(document).ready(function(){

        var key = 'fhwjewiu234028fowqoujoq';
        var api_id = 'api_idpca_xero';

        // test add contact logic
        $('#addcontact_form_submit').click(function(e){
            e.preventDefault();

            // create sign string
            var mydata = {
                'name':'Y-SOCKS',
                'id':'388'
               };
            mydata = JSON.stringify(mydata);
            $('#addcontact_post_data').val(mydata);

            var sign = key + api_id+'data' + mydata + 'methodaddContact' + key;

            var testsign = CryptoJS.MD5(sign);
            sign = testsign.toString(CryptoJS.enc.Hex).toUpperCase();
            $('#addcontact_post_sign').val(sign);

            $('#xero-addcontact-form').submit();
        });


        // test add invoice logic
        $('#addinvoice_form_submit').click(function(e){
            e.preventDefault();

            // create sign string
            var mydata = {
                'receiver':'Y-SOCKS',
                'id':'INV388',
                'currency':'USD',
                'receiverId':'388',
                'date':'2016-09-16',
                'dueDate':'2016-09-20',
                'taxType':1,
                'items':[{'name':'name1','qty':2,'amount':10},{'name':'name2','qty':2,'amount':5}]
            };
            mydata = JSON.stringify(mydata);
            $('#addinvoice_post_data').val(mydata);

            var sign = key + api_id+'data' + mydata + 'methodaddInvoice' + key;

            var testsign = CryptoJS.MD5(sign);
            sign = testsign.toString(CryptoJS.enc.Hex).toUpperCase();
            $('#addinvoice_post_sign').val(sign);

            $('#xero-addinvoice-form').submit();
        });

    });

</script>

</body>
</html>
