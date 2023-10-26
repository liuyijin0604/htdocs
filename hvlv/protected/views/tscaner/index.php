<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="language" content="en"/>
    <meta name="viewport" content="width=320,initial-scale=1, maximum-scale=1"/>
    <title>PCA Express Scaner Terminator</title>
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


<div class="test-login">
<div class="row rowcol"><input type="text" class="field" name="LoginForm[user]" id="username"
                               placeholder="user name"/></div>
<div class="row rowcol"><input type="password" class="field" name="LoginForm[pwd]" id="password"
                               placeholder="password"/></div>
<form id="tscanner-loginform" name="LoginForm" method="post" action="<?php echo Yii::app()->request->baseUrl; ?>/api/scanner/login">
    <div class="row align-left" >
        <input type="hidden" name="api_id" value="pca_scanner">
        <input type="hidden" name="data" id="post_data" value="">
        <input type="hidden" name="method" value="login">
        <input type="hidden" name="sign" id="post_sign" value="">
        <div class="row rowcol"><input type="submit" id="form_submit" class="btn" value="Login"/></div>
    </div>
</form>
</div>

<div class="test-versioninfo">
    <form id="tscanner-versionform" name="LoginForm" method="post" action="<?php echo Yii::app()->request->baseUrl; ?>/api/scanner/version">
        <div class="row align-left" >
            <input type="hidden" name="api_id" value="pca_scanner">
            <input type="hidden" name="data" id="version_post_data" value="">
            <input type="hidden" name="method" value="version">
            <input type="hidden" name="sign" id="version_post_sign" value="">
            <div class="row rowcol"><input type="submit" id="version_form_submit" class="btn" value="Check version"/></div>
        </div>
    </form>
</div>


<div class="test-agentinfo">
    <form id="tscanner-agentform" name="LoginForm" method="post" action="<?php echo Yii::app()->request->baseUrl; ?>/api/scanner/agentinfo">
        <div class="row align-left" >
            <input type="hidden" name="api_id" value="pca_scanner">
            <input type="hidden" name="data" id="agent_post_data" value="">
            <input type="hidden" name="method" value="agentinfo">
            <input type="hidden" name="sign" id="agent_post_sign" value="">
            <div class="row rowcol"><input type="submit" id="agent_form_submit" class="btn" value="Get AgentInfo"/></div>
        </div>
    </form>
</div>


<div class="test-task">
    <form id="tscanner-taskform" name="LoginForm" method="post" action="<?php echo Yii::app()->request->baseUrl; ?>/api/scanner/task">
        <div class="row align-left">
            <input type="hidden" name="api_id" value="pca_scanner">
            <input type="hidden" name="data" id="task_post_data" value="">
            <input type="hidden" name="method" value="task">
            <input type="hidden" name="sign" id="task_post_sign" value="">
            <div class="row rowcol"><input type="submit" id="task_form_submit" class="btn" value="Get Task"/></div>
        </div>
    </form>
</div>


<div class="test-pickupstart">
    <form id="tscanner-pickupstartform" name="LoginForm" method="post" action="<?php echo Yii::app()->request->baseUrl; ?>/api/scanner/pickupstart">
        <div class="row align-left">
            <input type="hidden" name="api_id" value="pca_scanner">
            <input type="hidden" name="data" id="pickupstart_post_data" value="">
            <input type="hidden" name="method" value="pickupstart">
            <input type="hidden" name="sign" id="pickupstart_post_sign" value="">
            <div class="row rowcol"><input type="submit" id="pickupstart_form_submit" class="btn" value="Ready to Pickup parcles"/></div>
        </div>
    </form>
</div>



<div class="test-pickup">
    <form id="tscanner-pickupform" name="LoginForm" method="post" action="<?php echo Yii::app()->request->baseUrl; ?>/api/scanner/pickup">
        <div class="row align-left">
            <input type="hidden" name="api_id" value="pca_scanner">
            <input type="hidden" name="data" id="pickup_post_data" value="">
            <input type="hidden" name="method" value="pickup">
            <input type="hidden" name="sign" id="pickup_post_sign" value="">
            <div class="row rowcol"><input type="submit" id="pickup_form_submit" class="btn" value="Pickup parcles"/></div>
        </div>
    </form>
</div>


<div class="test-pickupcancel">
    <form id="tscanner-pickupcancelform" name="LoginForm" method="post" action="<?php echo Yii::app()->request->baseUrl; ?>/api/scanner/pickupcancel">
        <div class="row align-left">
            <input type="hidden" name="api_id" value="pca_scanner">
            <input type="hidden" name="data" id="pickupcancel_post_data" value="">
            <input type="hidden" name="method" value="pickupcancel">
            <input type="hidden" name="sign" id="pickupcancel_post_sign" value="">
            <div class="row rowcol"><input type="submit" id="pickupcancel_form_submit" class="btn" value="Cancel One Parcel"/></div>
        </div>
    </form>
</div>


<div class="test-pickupdone">
    <form id="tscanner-pickupdoneform" name="LoginForm" method="post" action="<?php echo Yii::app()->request->baseUrl; ?>/api/scanner/pickupdone">
        <div class="row align-left">
            <input type="hidden" name="api_id" value="pca_scanner">
            <input type="hidden" name="data" id="pickupdone_post_data" value="">
            <input type="hidden" name="method" value="pickupdone">
            <input type="hidden" name="sign" id="pickupdone_post_sign" value="">
            <div class="row rowcol"><input type="submit" id="pickupdone_form_submit" class="btn" value="Pickup Done"/></div>
        </div>
    </form>
</div>


<div class="test-onboard">
    <form id="tscanner-onboardform" name="LoginForm" method="post" action="<?php echo Yii::app()->request->baseUrl; ?>/api/scanner/onboard">
        <div class="row align-left">
            <input type="hidden" name="api_id" value="pca_scanner">
            <input type="hidden" name="data" id="onboard_post_data" value="">
            <input type="hidden" name="method" value="onboard">
            <input type="hidden" name="sign" id="onboard_post_sign" value="">
            <div class="row rowcol"><input type="submit" id="onboard_form_submit" class="btn" value="Load on board"/></div>
        </div>
    </form>
</div>


<div class="test-onboardcancel">
    <form id="tscanner-onboardcancelform" name="LoginForm" method="post" action="<?php echo Yii::app()->request->baseUrl; ?>/api/scanner/onboardcancel">
        <div class="row align-left">
            <input type="hidden" name="api_id" value="pca_scanner">
            <input type="hidden" name="data" id="onboardcancel_post_data" value="">
            <input type="hidden" name="method" value="onboardcancel">
            <input type="hidden" name="sign" id="onboardcancel_post_sign" value="">
            <div class="row rowcol"><input type="submit" id="onboardcancel_form_submit" class="btn" value="Cancel one loaded parcel"/></div>
        </div>
    </form>
</div>


<div class="test-delivery">
    <form id="tscanner-deliveryform" name="LoginForm" method="post" action="<?php echo Yii::app()->request->baseUrl; ?>/api/scanner/delivery">
        <div class="row align-left">
            <input type="hidden" name="api_id" value="pca_scanner">
            <input type="hidden" name="data" id="delivery_post_data" value="">
            <input type="hidden" name="method" value="delivery">
            <input type="hidden" name="sign" id="delivery_post_sign" value="">
            <div class="row rowcol"><input type="submit" id="delivery_form_submit" class="btn" value="Delivery Parcels"/></div>
        </div>
    </form>
</div>

<div class="test-missedycard">
    <form id="tscanner-missedycardform" name="LoginForm" method="post" action="<?php echo Yii::app()->request->baseUrl; ?>/api/scanner/missedycard">
        <div class="row align-left">
            <input type="hidden" name="api_id" value="pca_scanner">
            <input type="hidden" name="data" id="missedycard_post_data" value="">
            <input type="hidden" name="method" value="missedycard">
            <input type="hidden" name="sign" id="missedycard_post_sign" value="">
            <div class="row rowcol"><input type="submit" id="missedycard_form_submit" class="btn" value="Missed your card"/></div>
        </div>
    </form>
</div>


<div class="test-umissedycard">
    <form id="tscanner-umissedycardform" name="LoginForm" method="post" action="<?php echo Yii::app()->request->baseUrl; ?>/api/scanner/umissedycard">
        <div class="row align-left">
            <input type="hidden" name="api_id" value="pca_scanner">
            <input type="hidden" name="data" id="umissedycard_post_data" value="">
            <input type="hidden" name="method" value="umissedycard">
            <input type="hidden" name="sign" id="umissedycard_post_sign" value="">
            <div class="row rowcol"><input type="submit" id="umissedycard_form_submit" class="btn" value="undo Missed your card"/></div>
        </div>
    </form>
</div>

<div class="test-consumegoods">
    <form id="tscanner-consumegoodsform" name="LoginForm" method="post" action="<?php echo Yii::app()->request->baseUrl; ?>/api/scanner/consumegoods">
        <div class="row align-left" >
            <input type="hidden" name="api_id" value="pca_scanner">
            <input type="hidden" name="data" id="consumegoods_post_data" value="">
            <input type="hidden" name="method" value="consumegoods">
            <input type="hidden" name="sign" id="consumegoods_post_sign" value="">
            <div class="row rowcol"><input type="submit" id="consumegoods_form_submit" class="btn" value="ConsumeGoods"/></div>
        </div>
    </form>
</div>

<div class="test-logout">
<form id="tscanner-logoutform" name="LoginForm" method="post" action="<?php echo Yii::app()->request->baseUrl; ?>/api/scanner/logout">
    <div class="row align-left" >
        <input type="hidden" name="api_id" value="pca_scanner">
        <input type="hidden" name="data" id="logout_post_data" value="">
        <input type="hidden" name="method" value="logout">
        <input type="hidden" name="sign" id="logout_post_sign" value="">
        <div class="row rowcol"><input type="submit" id="logout_form_submit" class="btn" value="Logout"/></div>
    </div>
</form>
</div>




<div class="test-logout">
  <form id="api-testform" name="LoginForm" method="post" action="<?php echo Yii::app()->request->baseUrl; ?>/api/shipment">

   <!-- <form id="api-testform" name="LoginForm" method="post" action="http://api.pcaexpress.com.au/shipment"> -->

        <div class="row align-left" >
            <input type="hidden" name="api_id" value="sume">
            <input type="hidden" name="data" id="api_post_data" value="">
            <input type="hidden" name="method" value="label">
            <input type="hidden" name="sign" id="api_post_sign" value="">
            <div class="row rowcol"><input type="submit" id="apitest_form_submit" class="btn" value="Api Test Only"/></div>
        </div>
    </form>
</div>



<script type="text/javascript">


    $(document).ready(function(){

        var key = 'f026aa92ecabc99f54e778cf2625783ad48e44652463ea4938a567e1720f3088';
        var courier_id = '337';
        var agent_id = '1';
        var api_id = 'api_idpca_scanner';

        // test login logic
        $('#form_submit').click(function(e){
            e.preventDefault();

            // create sign string

            var username = $('#username').val();
            var password = $('#password').val();
            var mydata = {'username':username,'password':password};
            mydata = JSON.stringify(mydata);
            $('#post_data').val(mydata);

            var sign = key + api_id+'data' + mydata + 'methodlogin' + key;

            var testsign = CryptoJS.MD5(sign);
            sign = testsign.toString(CryptoJS.enc.Hex).toUpperCase();
            $('#post_sign').val(sign);


            $('#tscanner-loginform').submit();
        });

        $('#apitest_form_submit').click(function(e){
            e.preventDefault();

            // create sign string
           var apikey = 'testonly';
            var apikey = '2376yhgju37487653456234wedfcvghu';
            var mydata = {"no" : "STARTRACK1"};
            mydata = JSON.stringify(mydata);
            $('#api_post_data').val(mydata);
          //  var sign = apikey +'api_idsumedata' + mydata + 'methodcreate' + apikey;
         //   var sign = apikey +'api_idchukou1data' + mydata + 'methodcreate' + apikey;
            var sign = apikey +'api_idsumedata' + mydata + 'methodlabel' + apikey;
            var testsign = CryptoJS.MD5(sign);
            sign = testsign.toString(CryptoJS.enc.Hex).toUpperCase();
            $('#api_post_sign').val(sign);
            $('#api-testform').submit();
        });

        // test get agent information
        $('#agent_form_submit').click(function(e){
            e.preventDefault();

            // create sign string
            var mydata = {'userid':courier_id};
            mydata = JSON.stringify(mydata);
            $('#agent_post_data').val(mydata);

            var sign = key + api_id+'data' + mydata + 'methodagentinfo' + key;

            var testsign = CryptoJS.MD5(sign);
            sign = testsign.toString(CryptoJS.enc.Hex).toUpperCase();
            $('#agent_post_sign').val(sign);


            $('#tscanner-agentform').submit();
        });


        // test check version logic
        $('#version_form_submit').click(function(e){
            e.preventDefault();

            // create sign string
            var mydata = {'verid':1};
            mydata = JSON.stringify(mydata);
            $('#version_post_data').val(mydata);

            var sign = key + api_id+'data' + mydata + 'methodversion' + key;

            var testsign = CryptoJS.MD5(sign);
            sign = testsign.toString(CryptoJS.enc.Hex).toUpperCase();
            $('#version_post_sign').val(sign);


            $('#tscanner-versionform').submit();
        });


        // test task logic
        $('#task_form_submit').click(function(e){
            e.preventDefault();

            // create sign string
            var mydata = {'userid':courier_id};
            mydata = JSON.stringify(mydata);
            $('#task_post_data').val(mydata);

            var sign = key + api_id+'data' + mydata + 'methodtask' + key;

            var testsign = CryptoJS.MD5(sign);
            sign = testsign.toString(CryptoJS.enc.Hex).toUpperCase();
            $('#task_post_sign').val(sign);


            $('#tscanner-taskform').submit();
        });


        // test pickup start logic
        $('#pickupstart_form_submit').click(function(e){
            e.preventDefault();

            // create sign string
            var mydata = {'agentid':agent_id,'userid':courier_id};
            mydata = JSON.stringify(mydata);
            $('#pickupstart_post_data').val(mydata);

            var sign = key + api_id+'data' + mydata + 'methodpickupstart' + key;

            var testsign = CryptoJS.MD5(sign);
            sign = testsign.toString(CryptoJS.enc.Hex).toUpperCase();
            $('#pickupstart_post_sign').val(sign);


            $('#tscanner-pickupstartform').submit();
        });


        // test pickup logic
        $('#pickup_form_submit').click(function(e){
            e.preventDefault();

            // create sign string
            var mydata = {
                'mani_id':'9572',
                'agentid':agent_id,
                'userid':courier_id,
                'items':[
                    {'bc':'PE234234234AU','time':'2016-03-29 12:12:12'},
                    {'bc':'PE6677234234AU','time':'2016-03-29 12:12:22'}
                ]
            };
            mydata = JSON.stringify(mydata);
            $('#pickup_post_data').val(mydata);

            var sign = key + api_id+'data' + mydata + 'methodpickup' + key;

            var testsign = CryptoJS.MD5(sign);
            sign = testsign.toString(CryptoJS.enc.Hex).toUpperCase();
            $('#pickup_post_sign').val(sign);


            $('#tscanner-pickupform').submit();
        });


        // test cancel one parcel logic
        $('#pickupcancel_form_submit').click(function(e){
            e.preventDefault();

            // create sign string
            var mydata = {
                'mani_id':'9575',
                'agentid':agent_id,
                'userid':courier_id,
                'tid':'66689',
                'bc': 'PE6677234234AU'
            };
            mydata = JSON.stringify(mydata);
            $('#pickupcancel_post_data').val(mydata);

            var sign = key + api_id+'data' + mydata + 'methodpickupcancel' + key;

            var testsign = CryptoJS.MD5(sign);
            sign = testsign.toString(CryptoJS.enc.Hex).toUpperCase();
            $('#pickupcancel_post_sign').val(sign);

            $('#tscanner-pickupcancelform').submit();
        });


        // test pickup done logic
        $('#pickupdone_form_submit').click(function(e){
            e.preventDefault();

            // create sign string
            var mydata = {
                'mani_id':'9022',
                'agentid':'112',
                'userid':'337',
                'goods' : [{'id':1.0,'qty':10.0}],
                'others' : 'pens 4',
               // 'signature' : "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAASwAAAB4CAYAAABIFc8gAAAgAElEQVR4Xu2dB9AES1HHQTFiQkEk38OAKKighSCGe2CpYELBArXAA4wg+kCrMHsCBVqmpyKoGA5FDEiJIqho6T0zmJ45UOgZAAtRzICg+P89tqVfM7s7e7f33be3PVVdd7s7oadn57/dPb2zN7xBppRASiAlMBEJ3HAifCabKYGUQErgBglYeROkBFICk5FAAtZkhioZTQmkBBKw8h5ICaQEJiOBBKzJDFUymhJICSRg5T2QEkgJTEYCCViTGaqzYvSD1JsvFn2A6K1Ed3a9u1b/31X0MtFrmvPvEs4tdPx60c1ELxW9veimov8R/Wdz/Nb6vZHotaJ3bOqhzD+LfkO0Ff2UaHdWkj3zziRgnfkAX7LuLcXP14r4vSwJwHuW6DmiaxxTC/3/l4YuC6+z5yMBa/a3wNEEwIT/QBHa1J1E9xDd6mitjVPx/6qaV4tu7Kp7pf4/QfT0BK9xhHxILQlYh0gvy5Yk8CCd/AqRN/PaJIWJ9muiv2nAANMPgMOEe7mI66SSSYj2c1vRP4i8ScjxzUVmEgI4/y16hybf2+05bDuV+xQRJmumE0kgAetEgj/DZgGVbxWtKvr2r8qzEa1FAM9Fp/upwYeL7iq65YDG/0N5v1e0FeH/ynTBEkjAumCBn2lzV6lfDxTdvad/L9F1zCv8RacAqhJ7C50EwB4iei/RGxr6N/3i/PfmoS+P1vYU0aPPdEwvZbcSsC7lsFx6pjDbPku0FPG/lFjle5po1xCm1GUBqVoBozVuRJ/cUYD+palYK9ED8yVgHSjAmRXHP0U4Qp8m9XXKsz4j2Xyp+vJFotu09Om/dB6/XTrmjzzoCVhHFvCZVI+m8QMiTKeu9CJdfKxoeyb9jt2g/18mYvXzbVv6iLkLYKdz/gg3QQLWEYR6ZlUu1J+fFJVMP5znTEwmKbQ7s763dQeZ0F+Aq5SQC+B2rsB9sm"
                'signature' : '/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEB\nAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/2wBDAQEBAQEBAQEBAQEBAQEBAQEBAQEB\nAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/wAARCAJYBgADASIA\nAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQA\nAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3\nODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWm\np6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEA\nAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSEx\nBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElK\nU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3\nuLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD+/iii\nigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKK\nACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooA\nKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAo\noooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACii\nigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKK\nACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooA\nKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAo\noooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACii\nigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKK\nACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooA\nKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAo\noooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACii\nigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKK\nACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooA\nKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAo\noooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACii\nigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKK\nACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooA\nKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAo\noooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACii\nigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKK\nACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooA\nKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAo\noooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACii\nigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKK\nACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooA\nKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAo\noooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACii\nigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKK\nACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooA\nKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAo\noooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACii\nigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKK\nACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooA\nKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAo\noooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACii\nigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKK\n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\nACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooA\nKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAo\noooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACii\nigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKK\nACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAorK1z\nXdC8MaRf+IPEutaV4f0PSoDdapreuajZ6VpGnWqsqNc3+pX89vaWcCsVUzXEyRhmVS+45b8qv2hf\n+C3P/BOz9nx77TB8X5PjP4osjIj+GvgVpsXjlDIiyYB8aPqWkfD0jzUEMscPi+a9gdiXsiqu1eRm\n/EGRZBQ+sZ3m+X5XSs3F43F0aEqlm1ajTnUVSvK60hSjOb1stHf9E8O/CLxU8XMz/sfww8POL+Os\ndGcYVqfDHD+Z5rRwfNa1TMcZhcNUwmWUEnFzxGPr4fDwUoOpVipxb/WqoLq6tbG1ub29uYLSzs4J\nrq7u7qaOC1tbWCN5Z7m5nldYoIIIo3lmmldY441d3cKrOf50l/4KIf8ABXT9sQPB+xB+wPH8HPAm\noSCGz+Mn7Qc7pI+mStth8R6EniqbwJ4cuAgaNriz0TSPiQibZ4YBdTJKyS2n/BGb9rb9p+Wz1j/g\npH/wUQ+I/wAQrCacXmpfB/4OyTaf4It7gusu/TL7WtO0rwdpkpaONLhNM+DcDYSNbbUdscUo+SXH\nmJzS8eEOE894gi7cmZYuiuHcilFuSdSGYZxCnicRFWT/ANjy/EOSU+RybR/Qi+ibkvAt630i/pB+\nFXhDVotRxXBfD2Yy8Y/FajVtOSwuI4R8Oq+PyfJ69SMUl/rLxdlCpVJRjXUUqkl+gH7Qv/BX3/gn\np+za19p3in4/aF468U2QkDeC/g5G3xK1xriIyLLY3Oo+Hp5PCWh38TRlJLLxL4m0m4RyqvGDur4E\nj/4LN/tjfH8Bv2IP+CXnxf8AHPhzUHJ8P/FD4lya9aeEb6BkYxPeR6FoNl4Ssnb5JMJ8WLhGTeqO\nc+cP0n/Z5/4JZ/sDfsxNZX/w2/Z18Haj4nsxGyeOviJDP8R/GC3kZU/2jp+o+Mn1a28NXrhFVn8J\nWOiQbN6pAolufM/QJVVFVEVURVCqqgKqqowqqo4CqOABwBwKFlHiNm/vZpxTlvDVB/8AMBwvlkMd\niuRtu1XOc8hVSqxWjlhcspRvyuM2oy5k/EL6GXh2vq/AvgPxr43ZpTSa4s8d+N8TwtkSxVPmj7TA\neGvhZi8FUlgarjGpCjnnHWYVPZylCvRVSdqX87aeFv8Ag48/aGj3a147/Zr/AGOdHvdkl5p2kW/h\nzVtaS0lEhNvYT2el/HvU7W4iyj4HizSrxWVIpNTCmeN3/wDDjn43/GZjP+2d/wAFOP2i/i/Z6gmN\nV8GeFptW0vw/bpJGVnsdLn8aeLfGehx2bsXbZB4B062YvIz6eZZJHP8ARFRVLw0yDEf8j3MOJeJn\nppnvEeaVcPe7vbAYKtgcByyvrT+rez3vBtmX/E7ni1kl4+FfCPgn4IQentvCvwZ4Hy/OeVNct+K+\nJsv4q4s9rBxhKGKWfLFqUY2xCiuU/H74Uf8ABCX/AIJo/C1Ypr74N638V9VgZDFrHxX8e+KNabCg\nbll8P+Hrnwp4PulmZVd/tfhqZlIKQtFE0qP+j/wy/Z3/AGffgpFDF8Hvgf8ACj4YCCEwJL4C+Hfh\nHwrduhjaKRri90TR7K7uprhC/wBquLmaW4umklkupZZZJHb2Kivpcr4X4ayRR/sfIMoy6UbWqYTL\nsJRrNp6SnXhSVapL+9Ocpbe87K34nx147eN3ifKt/wARE8W/EPjOlXcufBcRcZcQZnlkYyupU8Pl\nWJzCpl+FovV+ww2Gp0buTUE3Jsooor3T8pCiiigAooooAKKKKACv50viUE/a8/4ODvhP4BQ/2n4E\n/YQ+EK+O/EUEbGWyj+IF1ZweJLC9iuljeFL628S/EH4XQ3VqrsyzeFb+0Zo72C+WP9lP2sf2t/gj\n+xb8H9b+Mfxv8TwaRpVlHNb+HPDdrNay+LvH3iQQySWfhPwVo011byavrF4UVpnDJp2kWBuNb1++\nsdEtL7UU/Kr/AIIh/Cf4neJLr9rb/goB8avBd/4S8Uftl/EoeKPh5Y61ltQtvhYureIfEkV1pIlE\nV7F4Z1m/8R2el6HJfWtq2p6H4T0HW9NgbQ7zTL2b824vr0c64i4S4OoVI1qizfD8S59Rp81RYXKM\nkhWxeDWOjFONGnmOarA0qMarXtXGolGUW5H9rfRyyrMfDLwb+kT9I/N8JVyzCT8Os28FPCfMsW4Y\nKWfeInidjMDw7xJU4VrV5U6mY4zg/wAP6vFWPzSeXqo8vjisPKrWp1+SnP8Ae6iiiv0k/ikKKKKA\nCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAK\nKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAoo\nooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiii\ngAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKA\nCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAK\nKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACikZlRWd2VURSzOxCqqqCSzM\nThVAUkknAAOScFj+Tf7V3/BZ/wDYi/ZfuL3wnpnjSf4+/FZJBY2Xw0+CRs/FLjWZm8iz0/XfGcV1\n/wAIpo0jXhS1vtPtdR1bxXZSF1HhS4uUW3fyc4z3JeH8I8bneZ4PLcMrpVMVWhTdWSv+7oU3J1cR\nVdvdo0IVKstOWEm9fv8Aw48KfEvxfz+HDHhhwPxFxtnTUJ1sHkGWYjGQwNCUnBYzNcZGCwWT4CLX\n73Mc0xOFwFJXdbEwSbf6y1yvjPx34G+G/h688W/EXxp4V8B+F9Px9v8AE3jPxHo3hjw9ZZWRx9s1\nnXL6x0+2ykUjjzrlcqkjchHY/wA98Pxm/wCC9/7b9vJcfB34O/Dj9g74Va6YzpPiz4mgP8Sho87T\nNIbiHxRpHiTxN58lqIza6np3wh8LiQTQT6VqyMXv4uw8Gf8ABBHwv4/8QWXj79vT9rf47/tZeMIp\nDdSaVceItW8OeEoTPL511pH27WNW8XeLpdKB/dQf8I7rXg9UiEawWFrEkcC/ILjTPM3VuEuC81xt\nKVuTNuIZx4aylwk5KGIpU8XTrZtjKVkp2o5dHmi0lNN3P6K/4ln8LvDxyqfSE+kzwBwzj8PJ/WfD\n3wew9bxq8Qo16CTxWU5hjMgxWXeH3DmYQlfDuWYca1/YV41PaYWpycj9s+O3/BeP9gD4Q3r+HfAn\nifxf+0b4xeVrK10L4KeGZNT0l9SZmS0gfxj4iu/D2g6hb3Uiqq3PhK48SzLuG2zkkIjPzOn7XP8A\nwXI/bPRo/wBmD9kPwn+x98O9WTZY/E747OZvF9lDK0sdtqtpB4602wbULG6hZbqJ9I+CniGBQiSQ\n6rNA0Bm/aL4D/scfsqfswWyQ/AT4CfDn4cXawm3k8Q6P4et7rxnd25hEBg1HxzrA1HxdqkJiDKYt\nR1u4TMtw+0yT3Lv9KU/9W+Ns4u+IuNJZZQn8WV8F4NZclZ6J57mDxmZzTV+d4eng5XbcHFtNJeNX\n0YvDZez8Gvo00uOc2oqKocd/SZ4hqcZVJtubqVKPhVwg+G+CcJKMnGVCGcYzieEYqNKv7aP1mVb+\nd/RP+CGfxE+Ouqaf4u/4KL/t3/Gn9oTU4pFvj4A8H6tf6R4I0a94L2Wj6p4sbXI4dHdnuPMg8MeB\n/BUxEzvA1tO88r/qz+z7/wAE+f2Kv2WXtbz4I/s7fD/wxr9n5DQeNNR02bxd4/ilhA/e23jjxlca\n94msPOcCaa307VLWzaYRuLZfKhA+x6K9fJ+BeEsjrfW8Fk2HqZg3GU81zB1czzadRXvUeY5jUxOK\nhKT1kqVWEL2tBJWPzvxD+lV9IPxPyx8O8SeJWcYLhGNOdChwFwfSwHAvh/h8JKTcMJDgzg3C5HkN\nejSjaFOeNwOJxPLd1cRUqSqTkUUUV9afz2FFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFfK/7Yf7Yf\nwX/Yg+C2tfGb4y615Fpb+Zp/hHwjp8lu/iv4g+K3gnlsPCvhWwmlj8+7n8rztQ1CUrpmiaYLnVtX\nuIbKBnY/bD/bD+C/7EHwW1r4zfGXWvItLfzNP8I+EdPkt38V/EHxW8E8th4V8K2E0sfn3c/ledqG\noSldM0TTBc6tq9xDZQM7fit+x5+x78av+CmHxq0j/goz/wAFGdHa2+HFq0d9+y1+y1fJcnwtaeFj\nci+0XxF4i0W+jUzeE5iltqdvb6nbpqnxS1NIPEviWCD4dxeH/D2pfD8S8TYyhjKfC/DFKlj+KcdS\nVVKreWAyHASlKEs6zqUb8lGGn1PB/wAfHV1GlTi4OU5f1L4H+B/Dma8OZj46+OuYZhwr4C8KZh9Q\nlPA8lHi3xa4tpQdeh4Z+GdCu4rE5jilG/EfEbvlXCmVOrjsdXWKUKSP2PP2PfjV/wUw+NWkf8FGf\n+CjOjtbfDi1aO+/Za/ZavkuT4WtPCxuRfaL4i8RaLfRqZvCcxS21O3t9Tt01T4pamkHiXxLBB8O4\nvD/h7Uv6TYYYbaGK3t4o4III44YIIY1ihhhiUJFFFEgCRxxooWONAFRQFUADl6qqKqIqoiqFVVAV\nVVRhVVRwFUcADgDgUtepwzwzg+GcHWp06tXH5lj6v1vOc6xdpY/Nse78+IxE7vkpQXuYTCQfsMLQ\n/dUk5OdR/CeN/jhxJ428RYDF4zAYDhTgvhXALh/w08M+HvaUuEvD7hOjUnLDZPk+HlyvFY7Evlxf\nEHEOLg824gzV1cfj6igsJhKJRRRX0h+KhRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAF\nFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUU\nUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRR\nQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFA\nBRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAF\nFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUU\nUUAFFeQfGn9oD4H/ALOPhCXx38dvin4N+F/hhPtC2+o+LtbtNOl1W5toTPNp3h7S2kbVfE2rCEeZ\nHo3h+y1DVplwYLGQnB/D3x3/AMFqfi1+0b4p1X4R/wDBKr9lDxv8fvEcMklhdfGXx9oOoaJ8NdBZ\n5Eih1gaM2o6T9j02eKVZLLVPiV4o8DG3vjbw3fhnUIX8iT5jPeMOHeHZww+Y49TzCtZYXJ8DSq4/\nOcXOXN7OOGyzCRq4qXtHG0as6cKCco89aKvI/cfCn6OHjH4zYfF5vwbwnUocIZW5PPvEfinG4HhL\nw14eoU5uOJr5zxxxDXwGRYd4VJ1KuCo4yvmsoprDYCtVtTf9AnibxT4X8E6Bqnirxn4k0Lwl4a0W\n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\nHh2COERW8AiSLR9PskSMQ2ttCI1AXyoLePGyKMDtKK+mwmU5Tl9vqGV5fgrbfVMFhsPa21vY0oW/\nrVvU/DuIfELxA4uc3xZx1xfxM6jvUfEPE+eZw6j2vN5lj8U5Pzld+b3CiiivQPkAooooAKKKKACi\niigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKK\nKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAoooo\nAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigA\nooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACi\niigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACikZlRWd2VURSzOxCqqqCSzMThVAUk\nknAAOScFj/Pz+1X/AMFXPif8bPihffsW/wDBKDwufjF8Z703Wn+Mvj9b2tne/DL4X2EVwbHU9W8O\n6jqQl0HU20maQLdePPECv4GtLk2Wn+GbPxhrGo2cNt4HEHEuU8M4WniMxq1J1sTU+r5dluDpSxOa\nZri3dQwmW4Km/aYmtJ8qb92jS54yxFWnTvM/XPB3wQ4/8cc/xuT8GYLBYfLcjwf9rcZcacRY6lkn\nAnAfD0HU+scQ8Z8TYuP1LJstowo1p04ydXMcfKlVw2U4DGYyLoP7h/b7/wCCnfwF/YP0OLQ9WaX4\nm/HjxHbwr4B+BPhG6jm8T6ndX7vBpWo+KZoVu28JeHru58uO3uri0udZ1hzJB4Z0PVJYrtovzg+C\nX/BOT9p7/goj8RfD/wC1l/wVk8Qanp/hGxkfU/hN+xvoU17oGi+H9IvJYrm2h8ZWVtcG48L2V1Ck\nX9p6LHdz/ErxD5dhH458V6bFpreGJfs79gb/AIJL/Dj9lbXH+Pfxy8TXH7R/7XXiOaTWfE/xf8YT\nX2t6f4Z16+BbUf8AhX0evpJqb33zG0n8ea8zeK9ShWRrC38O6deXnh+v13r5LD8N5xxfXp5nx3CO\nGyynUhWy3gbD1lVwVNwk50cTxLiadoZxjYtKUcBG+V4ZqKccRUnUmf0FmnjT4cfRzyvMOCPopYmv\n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\nA06WuCzXP+Ec38XuLcJU15a8c78Vc/4my/28Goyp1aGQ4eUJc6X7ubgfzzWX/Bv7oXxHubfVP2wf\n27/2ov2jdUimF0xbWo9EtPObLPEZPHupfGHUfKJyjy2l7Y3EkRHlm2ZVr7T+Fv8AwRf/AOCZ/wAJ\n2gudP/Zm8P8AjTU4VUSan8Ute8WfENbooVIafw/4m1q98JqxK5b7L4egVwzI6snFfqNRXfgPDrgX\nLqntqHDOW18Ro3isxpTzbF80dp/Ws1nja6noveVTmt1tzKXyvFP0y/pU8X4L+y8y8ceN8syjllBZ\nFwZj6Hh/w/7KTd6H9g8BYbhvKnh3e/sJYN0b2lyc65jhPAfws+F3wr00aN8L/hv4E+HOkBBGNK8B\n+DvDnhLTRGrblQWPh/TdOtggb5gvl7Q3IBPzV3dFFfYUqVKhTjSoUqdGlBJQp0oRp04pXsowglGK\nXRJWV3vrf+ccdj8dmeLr4/Msbi8wxuJm6mJxmOxNbFYvEVHe9SviK9SpVqzfWVScpO7vJu7ZRRRW\nhyBRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUU\nAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQA\nUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABR\nRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFF\nFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUU\nAFFFFABRRRQAUUUUAFFFfjV/wVT/AOChfif4AWfhr9kr9lezuvG37aP7QMdtoHgbRtCt11C9+Gui\neILi60uHxvewbvJXxHelLr/hCrO9/wCJfp72d5428UBfDmlQWGpeNn2e5fw3lWJzbMqko0MOoQp0\naUXUxWMxVWTp4bBYOgmpV8Xi6vLToUo7ympTcaUZ1D9I8JfCrjDxq49yXw94JwlGtmmaSrV8XmGO\nrLCZHw9keBpyxOd8UcSZnOMqOU8P5BgadTMM1zCtdU6EVRoQrY2thcNU+f8A/gph+198Wf2kvjHB\n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\nKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooo\noAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiig\nAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKAC\niiigAooooAKKK+af2tv2rfhN+xf8C/Fnx2+L+qG30XQo/sWg6BZyQ/8ACQeOPF93BdvofgvwxbSu\ni3Gr6zJaSMZZCLPS9Mh1LXtWmg0fT7+7XmxmMwmXYTE4/HYilhcHhKNTEYnE1pqFKjRpxlKdScns\noxg31beiTk9fb4a4bz/jHiHJeFOFcox2fcQ8QZlg8oyTJstoTxGPzLM8dXjhsJg8LRgm51a1WSir\n2hBXnUnGnGUzwH/gpL/wUD8FfsBfBBvErW9r4q+M3jw3nh74H/DQrcXEviTxQEWOTWtZtbK4gvk8\nI+GXubO41qS2liu9RuptO8OaZcRalqcN1H80f8Eqv+CffjT4TXXij9t39sC4uvGX7Z/7QEdxr2tX\nPiKISX3wk8N+IFjnbwva27xJDpvi7UrBLOz8S/ZILeHwro0Fr8M/DkVpolrr8upeA/8ABN79lH4s\nftjfHKT/AIKw/t36Ys3iTxALe5/ZV+DeoWt6nh/4e+Dbaaafwt42t9H1IM8Wn2Fvc3Fx8NYLsSyX\n15fah8XNQ+0eINS8Ma2n9FNfnmQYLF8Y5rQ41zuhUw+V4NylwTkeIi4ypUqilB8TZnRen9pY+nzP\nLaE7rLsDNTi5Yuq68f7E8WOJMg+jfwBnX0YvC/OMFm/HfEUaFH6Tvilk1aFWhmOPwlZ1oeBvBOZU\n1zrgnhTF0l/rnmeHnGfGfFNOrhq6p8PYCll9Qooor9MP4gCiiigAooooAKKKKACiiigAooooAKKK\nRmVFZ3ZVRFLM7EKqqoJLMxOFUBSSScAA5JwWJ/X5+fl+eujub7a/00uvl+eraba0V+Xf7UH/AAWL\n/YG/ZYXUtK1z4vWnxT8c6f50R+HnwU+w+PNbS9hllglsdW1y01O18GeG7u1mi2X2n6/4mstYt13s\nmlzSIIm+DrX9u3/gs/8AtjK1x+x7+w34e/Z9+G2qZXTPil8frln1r7C8QW11/SP+ExuvBum6paXL\n5m2aF8PPGtrE7Q2y39zFb3l3L8TmPiDwxgcXPLcLicTn2awtz5Vw3gsRnWNp6yT+sfUo1MPg2nH3\nljcRQaTUmlFSkf09wZ9EDxx4qyChxnnuT5J4TcBYhr2HiB42cS5T4Y8L4tOMZQllEuJq2FzjiONS\nLvTlw1lGaqcrUot15U6b/o0ZlRWd2VURSzOxCqqqCSzMThVAUkknAAOScFj8KfHX/gpr+wT+zgL2\n3+J/7TPw7j1uxLRT+EfBupTfETxjFdK8ka2d54b8BQ+ItR0id5Iiu7W4tPtosiS5uYYQ0tfl+n/B\nHb9tf9ppftf/AAUL/wCCk/xE8WaVfEf2z8JvgkLyw8EzJI6NN9ln1Wy8M+DLOQxp5RRPg3NHvZJP\nPaOHyZPuf4F/8EZv+CcfwE+xXmlfs+aN8SPEFn5RPif403t38Sbu4lhKNFcP4d1xf+EGtLhJEMyz\n6X4SsZRLI2G2JCq8X9seImb3/snhTLuHsO7OGN4szNYjFSg+ZcyyXI5V3Cokk/Z4jNKTvZSstZfU\nR8N/obeHl34h+P8Axn4xZvRap1+Gfo/cDSyfIYYmDk5wn4m+KcMrjicJPl5Fi8n4FzCPK6dSjKpz\nT5finWP+C72v/GPUr7wt+wD+wl8fP2kNXinNj/wlWuaTeaF4T0yYGQjUbzT/AAhZeN7l9NkRI9g8\nQ654OlVZjNcywNCLaTJk+Fn/AAcI/tdf8lD+MPwh/YU8C6iA914d+HkllP46jtbiN1gk07UfC9x8\nQPFFvewRNuntp/iv4ZdJpS89qt5bwQQ/0P6PoujeHNKsdD8PaTpuhaNpsC2um6Po9haaZpWn2qFi\nltY6fZRQWtpAhLFYYIkjUs2FySzaVL/UnOszTfFHHWe46Erc2X5CqPDOWOLbc6VT6h7XM8RTe16u\nZ3lG6kmkkEfpOeGfAr9n4FfRX8K+FsRTv7HjDxYqZl45ccRrU7xoZjhI8WLA8DZRi1ZVuTA8DuFG\nq4OlVdSnOvV/BD4ef8G+/wCzZPrsfjb9rH44fHj9rjxxL5X9rah4x8X6r4T0DV9hR5PtMenatrfx\nAXzXEgH/ABc5/LhkZVJuFF2f1n+Cv7I/7Ln7ONtFB8DfgH8L/hvPHGIn1rw54P0iLxVdxgMANT8X\n3NtceJtWKqzKr6nq106ozIrhSyn6HZlRWd2VURSzOxCqqqCSzMThVAUkknAAOScFj+Ef7W//AAWH\nmn8fzfsof8E1/Akn7VX7TOrS3ukzeItAsn1r4V+AJoJGtdQ1H+0YL20sfFt1oZdJr/V5NRsfhn4e\nYw3viLxVfR2+q+H6dbAeHfh1ho49ZVl+BxFaqqWEdLCyzHiHNMXJ2hhsFUq/Wc0x+JqTtaCqzUXP\n2lacIc9QWVcWfTG+mXnOI4RfHvGHFWS5ZgvrvENLHZ9R4P8AB/gbh6kqnt894mwmA/sbgThDI8JR\npVZSr1MBh5VadL6nl+HxOKVDCy/Sv9rf9tv9nD9iLwDJ46+Pfju10WW6gum8K+B9K8nU/iF46vLY\nbWsPCXhhLmG4vAJTFDd6xeyWXhvSXnhfXdbsIXjlb8MLLwh/wUK/4Lg3tprfxGn1n9i//gnzcXdp\ne6V4O0qW4b4h/GnRY7lbmG8ke8tbSTxbFexr51j4i1uwsfhfpMj6bqHhzwl4x1ew1HUX+q/2Rv8A\ngjxt+If/AA1f/wAFIfHf/DWH7TWt/ZNSXQfEcn9tfCr4f3UZaSys49MurS3sPGN3oUbfZtK09tK0\n/wCHPhoyS23hrwjcy2WjeJ0/dVVVFVEVURVCqqgKqqowqqo4CqOABwBwK5Fk3EfHL9txV7fh7hqb\nUqHCOExDjmeaUm7wlxRmOHnejSnFOUsky+ooJVIwx2Lq1KcqZ9A/EnwZ+ivGeXeAcss8YPG6hH2W\nY/SH4gyZVeB+BsfFTWIo+BPBmc4eUcyx+Gq2pUvE7i/CSxE3QeK4W4fwmExccY/nr9mj9lP4A/sg\nfDq2+GP7P/w80nwRoI+zT61fQI154n8XatbwmH+3vGfiW7D6p4j1aQM/lzX07WunwSf2dotpYaRF\nb6en0NRRX6NhMJhMBhqOCwOGoYTCYenGlh8NhqUKNCjTjflhSpU4xhCK7RSV2203e/8AGXEHEOf8\nWZ3mfEvFGdZpxDn+c4qpjs3zvOsfisyzbMsbVb9pisdj8ZWrYnFVp6J1K1WcuVQjflhEKKKK6Dxw\nooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACi\niigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKK\nKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAoooo\n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\n42ld4/GxVnHKsvlPEc3P9Yq0FTqo/sDgz6NuQ8IcMZV4r/Sq4izLwv4EzTDxzLg/w9yzDYet43+L\nWFU5qnPhPhrHuEOE+GMTyWqcf8X08PlHI6LybA5rPE0Jngdx4n/4KF/8FvtSfS/A9v4i/Ys/4J53\nVxdWureKL7evxD+NuixzzW89tALeW0ufFtvqUTNaXGjaTd2vwl0qQ6pb+IfEXjXX9JsNLb9z/wBk\nf9if9nT9iH4er8P/AICeCINGN5FaHxX401Vo9T8feOtQtVcLqXi3xI9vDNeFZJJpbPSbGKx8OaSZ\np4tD0axgkeM/UtjY2Ol2NnpmmWdrp+nafa29jp+n2NvDaWNjY2kKW9pZ2dpAkcFra2sEaQ29vCiw\nwwqkUSKijNqvQ4f4NwuVYuedZrjK3EPE1eHJXz3MIwU6NN83PhMowcU6GT4C7fLh8KvaT5p/Wa9a\nTu/kfFr6SWe8e8PUfDHgPh7LfB/wPyvExxGU+FPCFfEyw2aYujNujxD4i8RYiUc38RuLJxhQdTOM\n+nLCYadGislyrLoQkplFFFfZH82hRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAB\nRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFF\nFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUU\nUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQ\nAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAB\nRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFF\nFFABRRRQAUUUUAFFFFABRRRQAUUVk69r+geFdG1HxF4o1zSfDmgaRbveatruvalZaTo2mWaEK91q\nOp6hcW9nY26EgPPczxxKSAz5OSpSjCMpykoxinKUpNKMYpNuUm3ZJKLbbdkk7vRyelGjVxFWlh8P\nSqV69apCjRo0YTqVa1WpNU6dKlTgpTqVKk2oQhFOcptRinJ3etRX4s/tFf8ABdn9i34R6kfBHwWl\n8T/tafFK7uF03RfCXwUsZbvwzeazMG+x2UvxBubebTtSivGCRRz+AdN8bXKzyxRNp5cT7PmYTf8A\nBd//AIKCsrW6eGf+CbnwO1YyFZXfUIPjXd6NMXCbTsufiLa67ablMciR/CG0uot8yyO0cKv8Ji/E\nTIViKmX5DTx3F2Z03yTwXDWHWOo0JtyjH69mrqUsqwMFKNqjr41VKet6TkuV/wBW8OfQ38V6mTYP\njDxZxnC30eOBcXB1sNxP42ZvLhXMM1w8F7Sf+q/AUMLjePuKcRUoJ1cHDKuGqmDxSceXMYUnKvH9\nqP2hv2yP2Wv2UNIfVf2gfjd4H+HcptGvrLw7qOqi/wDHGsWq+YPO0DwHoy6j4t1yIvGYmuNM0a4t\nopWjSeeNnXP42+IP+C0f7QX7T+r6r4C/4Jc/sT+P/jFeW90dNn+MvxT02bSfh7oVw4VUmu9L07V7\nDRdOW5R3udMuPGfxH8O3CpFDJdeGLpXurNPfv2d/+CEn7GXwm1c+O/jc/iz9rf4o3k6ahq/in403\n0tz4XutYxF9pv08BWtzPaarHdsjmS18fav40IErD7SzJG4/ZPw/4d8PeEtF0/wAN+FNC0fw14f0m\nD7LpWg+H9LsdH0XTLUO8gttP0vToLaysoN7u/k20KR72dtpZmY8qwfiLxDf+0MxwHBGXz3wWTKnn\nPEU6bclKnWzjF0lluBm1qp4HBYmcHyqOJclzL3FxJ9DTwd93hLg/iv6UXGGGt7PibxLeN8NvBzD4\num3Oli8t8OuH8wq8a8U4VSShUw3FPFOSYfExs6+TqmqlGf8AO/af8EmP26f20rqy8Tf8FO/23vEh\n8NTTRXw/Z5+BUtpZ+G7AeYs1vDdXg0rT/AOm6tp6mW0mu7XwN4wvp42jdPGkrxmST9cP2aP+Cfn7\nHH7Idtat8CvgX4R8O+IYIFhl8f6vbS+KviRdkiT7RI/jjxLJqeu2Ud48jyXGnaRd2GjDMcNvpsNt\nBbQJ9kUV6+TcC8M5JiP7QpYGWYZtLldTPM6r1s2zmpON/fWOx0qs8O5X1hhFQpbWpqx+deI/0qfG\n/wATMl/1Qx3FNHhHw9pc8MH4W+GmVZf4feG2Ew83ph5cLcLUcvwubqH2MTxDLNcws3z42V2FFFFf\nXn87hRRRQAUVwfxL+KPw2+DHgrWviL8WfHPhn4e+CdAgM+r+J/FmsWWjaRaja5it1uLyaL7Vf3jI\nYNO0u0E2paldtFZadaXF5LDA34EfEf8A4K0ftM/tn+NNc+An/BIn4Ia14reyuF07xR+1T8Q9Cj03\nwT4StppZAmr6JpXiSNNE0NJoInvNKu/iKJ/EOr20eoWej/Ci61GC3uh8zn/F2R8N+xo46vUxGY4r\nTL8ky6jPHZ1mE7ySjhMvot1XFuNnXrezwsG7VcRFo/b/AAh+jz4oeNazPMeFsrwOUcG8P8s+L/E/\njPMsPwt4ZcHYa8FOvxHxfmajgaNWMZwqQyrAfXc+xUJJ4HKsQ1r+x/7Uv7aH7NP7GPgw+Mv2gvib\no/hIXUFxJ4d8KQMdV8feMZ4El/0Xwn4PsXfVtUDTRraz6q8Nv4d0u4ntjr2tadbyC5r8MdU+N3/B\nSL/gs1LL4O/Zt8La7+xR+xJqsk1j4n+N/iw3K+PviZoP2qa1vbTQ5tPlsLrWoL63MkFx4V8A3sfh\nZbu31PQPGvxbuLS4TSj9Ufss/wDBE34beF/GH/C//wBu3x9qX7aP7RerT22q6pdePLrVNY+FWhal\nGqOlrZ6F4gMt94/j052ntbK48Zxw+GBposYtN+HGiz2scp/cS3t7e0t4LS0ghtra2hit7a2t4kht\n7e3hRY4YIIY1WOGGKNVSKKNQkaBURQor5n+yeL+Mk3xJWlwpw9Ut/wAY3lWKVTPMxotu9PPM8otR\nwVGrFNVsvyj35Uqro18wcotP9vh4gfR2+jVJQ8F8sw/j94wYJrl8auP8hnhfCzg7M4c9sX4W+F2Z\nRlW4mzHAVWp4Di/xEX1WjjsNhszyrhBUqkZnxT+xV/wT6/Zq/YN8Ef8ACNfBfwmLnxTqdrFF4z+K\n/ieOy1H4j+M5UcSGPUdais7ZNM0SGVVaw8L6HBY6DaFVupLO51iS+1af7coor7/Lcty/J8FQy7K8\nHh8DgsNBU6GFw1KNKlTinJtqMVrKTcpVKkm6lScpTqTlNuUv5I4y414v8ROJs24y464kzfirifPM\nTLFZrnud46vjswxlZ+7BTrVpydOhQgo0cJhKKhhcJhoUsLhKFLDU6VKJRRRXafMBRRRQAUUUUAFF\nFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUU\nUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQ\nAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAB\nRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFF\nFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUU\nUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRTJJI4Y5JZZEiiiRpJZZ\nGVI440Us8kjsQqIiqWZmIVVBLHALH8nP2rP+C0f7Dn7L8974W0/xxN8e/ihDMbC3+HPwQax8VtFq\n7SG2isNe8Zx3qeEdGmS822moabb6pqniqxm8yM+F5rlBbt5OcZ7kvD+FeNzvM8HluGV1Gpi68Kbq\nSV/3dCm5e1xFV292jQhOrLTlhJvX9A8OPCnxM8X8+hwz4X8DcR8b5y1CVXCcP5XicbDBUZScVi81\nxkYfUsnwMWv3mYZpicLgaWrq4mKTk/1mr5M/aZ/bo/ZM/Y+0tr34/wDxp8K+D9Ue3Fzp/gqC4l17\n4iaxE4YQy6X4F0FNQ8RzWkzhY/7WnsINEgd4ze6pbxkyV+M6eLf+C4X/AAUk/d+EfD2lf8E3v2eN\naUH/AISDWP7Wt/jPq+hzgA/Y7i7tbb4gTX7rIL3TL/RND+Fei3tkRGvia6UM831x+zL/AMEOv2MP\ngbqg8d/Fmw1n9q/4t3dz/aeteOPjkyaz4fudcmYy6hqNp8OpJb3Q7r7dck3Zfxxc+M9YgnZnh1wO\n0jt8cuKuJ+IU48HcNToYOVuTiTi2OIyzASg9VWwOSwX9sZhCcGpUp1o4Ci5aSquLu/6P/wCIB+Bf\ng/ev9JPxtw2bcRYezq+C/wBHqtlHHHFtKvFuFTL+KvEvEVH4dcHYnDV4ulj8Ll2I4szOlTvKngXX\nhKm/lu9/4K3ftwftlald+FP+CYX7EPia+8OzTyWB/aC+OtrFY+FbCMtLb3F5BbpreleBNJ1KwOL6\n0tNQ8eeKdRvIYnj/AOEIubgSWJu+H/8AgjD+0d+1DrNp45/4Kjftu+Pfi1L9oXUofg18JdTl0j4f\n6Pdt50pS2vtQ0PTvDulI3nC3vrLwb8NdEl2xulp4nlQ29yn9EFjY2Ol2Vrp2mWdrp9hZQRWtlY2N\nvDa2VpawoI4ba1tYEjht4IUUJFDEixxoAqKFHNqqh4fRzOSrca59mfFk+aM/7OnJ5Vw3TlFtx5Mj\ny+pCGI5NEpZjiMZKSvzXk5N41fpe1uBKNbLPox+E3BHgBhZUnh3xjhaL4+8acZSmpRrvE+KXF+Gx\nGKyf6zaFWVHgvKOG6dCreNGXso0lH5j/AGdv2Mf2Vv2TdJGl/s+/BDwT8PZmgFteeI7PTW1Txzqs\nO1VMWtePdel1Txdq8BI3paX+tTWcEjzNa20Rln3fTlFFfd4PA4LLcNTweX4TDYHC0ly0sNg6FLD4\nemlfSFGjCFOK9Eul72P5S4j4m4l4xzjGcQ8XcQ53xRnuYT9pj874hzbH5zm+NqXl7+LzLMsRicXi\nJau0qtabSbSaW5RRRXUeGFFFFABRRX5g/ts/8FaP2Uv2Knu/B+pa3P8AF743mSOy0z4H/DO4ttU8\nSx6rPIsNra+MdWjNzpvghZZXiBs9QF14pmiljm0nwrqSZrzM3zrKchwNTMc5zDDZdg6Wkq+JqKCl\nN35aVKF3Ur1p8rVOhRjOtUfu06cpb/ceHnhn4g+LXE+D4N8NeEc74y4jxvvUsryTBzxM6OHjKMau\nOzDEPlwmVZbh7xli81zKvhstwdNqri8XSp3m/wBNry8s9Ps7rUNQurexsbG3nvL29vJ4razs7O2i\nea5urq5mdIbe3t4Y3lnnmdYookeSRwis9fh9+09/wWt+H+h+Nv8Ahnr9gr4e6l+2h+0Vqs9xpenR\neCLbU9U+E+gajFLJBNdX2u6G32zx1a6YRFeajJ4TuLXwfDphuLrUviTpbW9wi/Mdl+y7/wAFP/8A\ngrXc23iT9s/xpqn7F/7J97cW9/pX7O3gmG4sPiJ4u0dyJbb/AISLSNQMl1DcSQsUl1P4redJp2qQ\nrfaL8IILSbzh+5P7L/7HH7Nn7G3gv/hCf2e/hjovgy3uY4V1/wARlG1Pxx4tniZnW68WeMNQ87Wt\naKytJNaWE10ujaWZpbfQ9MsLMi2Hw6zTjHjFOOQ4arwhkM7L+384winxBjqTlO8smyOs+TAUqkV+\n7x2bt1fZ1KVWhl7lHmP6j/1F+jf9HFOv4s51gfpF+LWF1h4R+HefVcP4QcKZhBytR8SPFLL7Yni/\nG4Oo19c4U8POXAxxWHr5dmnGKpTcZfjZ8NP+CSP7SH7Y/jPRfj5/wV1+Oet+NrmBl1Dw3+yz8Ptb\nGleBvCEU3ln+x9b1bw5JBo2jKIA1nq+n/DdDrGqPHZ6jqfxY1K5W6t5P32+G3wx+HHwb8F6L8Ovh\nR4H8M/D7wT4fg+z6P4W8JaNY6Lo1mrEtNMLSxhhSa9vJS1zqOo3Hm6hqN7JNfajdXF7LNO3c0V9N\nw/wlknDXt6uAoVK+YYuzzDOswrTxudZjP7U8ZmFdyqzUmoyVCn7PC02v3VCF3f8AEfF36Qvih41f\n2bl3FWbYPK+DuH7w4R8MuDstw3C/hnwdhl7ZU6HDnB+V+zy/D1YQqypzzXGrGZ9iqdlj82xLXMFF\nFFfSn4kFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFA\nBRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAF\nFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUU\nUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRR\nQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFA\nBRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAF\nFfKn7S37b37KX7IOkPqX7QPxp8JeCb97R7zTPBwvG1n4ha7EqSGNtE8CaIt/4kvbeeVFtv7UOnR6\nJazzQDUtUtYn86vxx1D/AIKs/t2ftv6hfeEf+CXH7HuvWnhF7qfTJf2mPjja2Nr4esGhmkS6udNs\nrq+h8BaXf2At2kWyv/EvjbWLuCYQHwPBqKRRv8lnXG/DmR4j+z62LqY/N5aUciyehVzTOq0ukfqO\nEVSeHUtLVcZKhQ11q6SP6D8MfoveM3ipk9Ti7LOHsJwn4d4VxeZeK/iRm2B4E8MsupOUoyqy4r4i\nqYTDZrOm4vnwHD0M2zbX3Mvm1K39AnxB+JPw7+EnhTUvHPxS8c+Ffh94Q0lC+o+J/GWv6X4e0O1P\nlTyrFJqOq3Vrbm4mS3mNvapI11csjR28MkmFP4g/GL/gu38P9c8WS/B7/gn78B/iV+2X8WLiSW1t\nL/QvDniPSfh3Zss7276lHHBpl34y8RWFjJG0t/dPonhvwz9gKalB42ayE0yc/wDD7/giB4q+Nnim\nw+LP/BUL9qz4hftQeNVP2kfDbwt4i17QPhdobyyRyXOj2Osyw6ZrCaFMPOJ0/wAB6B8NYYZ5RNEH\nYSl/27+DnwH+Cv7PPhOLwP8AA74XeC/hh4ZjEHm6X4O0Cw0gahNbq6RXutXkEIv9f1LDyeZqutXV\n7qczSSPPdyO7s3iqfiLxJf2VPCcBZXPariFh864qrU25NOOHjJ5RlTqQdmqtTMMRRnyt01JWP01Y\nT6G3gmn/AGhjOIPpZcd4ayeByieb+GXgJl2Mg5RnCtnFelDxE4/hha8OeEsBg+EsnzHDtQji61Ca\nrH4KRf8ABPv/AIKof8FBXTVf+Cif7Uf/AAz/APCXUHR5/wBmf4Cyaf8AaLjTZGb7TpGvHRb698Jj\nfFLM9hqni/xD8V9StHkktrjT7eBYoK/WD9lb/gnB+xp+xpbWU/wU+DehweMLaHy5/il4uUeLvife\nStC8FxPH4q1iKSXw+l7ExS703wjb6FocwJJ0kMXZvuOivWybgTh3J8V/aU6OIznOvd5s+z/EzzXN\nuaLbTo18SnTwKWijTy+jhqcY3ioNH594i/Ss8Y/ELIanBGHzTKfDjw0fOqPhR4S5NhuAfD6NOcXG\ncMxyrJpxxvFE6mk6uL4vzLO8fVrXq1MVKp7wUUUV9ifzgFFFFABRRRQAUUV5/wDFD4r/AAx+CXgn\nV/iN8XvHnhj4deCdDRDqfifxbrFno+kwSSl1trSOe8mjN3qN88bQ6dpdms+pahc7bWwtZ7l0jOdW\nrSoUqlevVp0aNGEqlWtVnGnSpU4JynUqVJyUYQjFXlKUlGKu27Jt9eX5fmGbY7B5XlWBxeZZjj8R\nRweAy/L8LXxmOxuLxFSNLD4XB4TDwq18TiK9Rxp0aFGE6tSpKMIRlN+96BXyv+1P+2p+zP8AsYeE\nP+Eu/aB+Jmk+FWureafw94RtW/tXx94ueIyR+R4W8IWUjapqSGZPs82qSx2+gafM8Z1jWLKEmevx\nv+If/BV/9qb9t3xdrPwM/wCCRfwR1rWbS2uG0rxT+1d8SdDTTPCXheC4SRBqmhaV4hhOjeHXWNot\nS0258dx6j4n1K1Fxa2Pwna+SK4Pvf7K//BFT4Y+DPGI+P/7bnjvVP2zP2jdTu4tY1LV/H9xqWrfD\nPQ9USZp4U03w7rrXF14w/s9mMNnc+LwdCjgWA6T4I0Z4Iq/PJ8Z5jxDUqYPw/wAup5lThN0q/FeZ\nqth+F8LJSlGf1OUOXFZ/Xg4tezwCjg1OVKU8wUHM/sfB/Rp4L8HsFheJPpfcZ4vgnF1aFLHZV4A8\nDyy7N/HbP6NSCq4b/WWlXlVyPwiyrFwnTqfXeLp1+Ip0Y4qlhOE54uEZHyvc/tB/8FSf+CuU02hf\nsp+E9R/Yh/ZB1fzLW/8Ajx4wlurX4jeNtCcmG5bw7qWntFqt497C8rWunfDVbTR4LmO40jxD8XfI\nmCN+m37Ev/BKT9lD9iJLbxR4b8PTfFD41So02sfHP4lw2useMG1G4aSS+n8I2MkcmmeAre4lnuE8\nzREbxJdWMos/EXijWmQ3DfpTb29vaW8FpaQQ21tbQxW9tbW8SQ29vbwoscMEEMarHDDFGqpFFGoS\nNAqIoUVNXflHA2CwuNp53n+NxHFPEUNaea5rCn7DANu8oZJlUP8AYsopXSalRhLFt39pi5pu/wAh\n4hfSm4mzzhfGeF3hJw3lHgP4O4m1PF8B8BV8W824tpwVSFPFeKPHuKnHiXxEx0qclGrSzOvh+Hop\nUlhOHsO6NObKKKK+4P5cCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAoo\nooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiii\ngAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKA\nCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAK\nKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAoo\nooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiii\ngAooooAKKK5Hx18QPAXwu8L6n42+JXjXwt4B8I6PH5uq+KfGXiDSvDnh/T48NtN5q2sXlnZQM+wi\nJJJxJK3yRKznmKlSnRpzq1akKVKnGU6lSpKMKcIRTcpznJqMYxSvKUnZK93o5PowmDxeYYrDYHAY\n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\nKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooo\noAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiig\nAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKAC\niiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKK\nKKACiivhL9rz/gpJ+x/+xJp9ynxn+J9lP42W1Fzp3wk8FC38T/FDVN8bS2wfw7bXsEPhu1vUQtaa\nv4xv9B0O4w8dvqctwBC3DmOZ5bk+Dq4/NcdhcvwdFfvMTjK9OhRjvaPPUnFSnNxahTjepOXuwjKW\nj+q4L4F408R+IsDwlwBwrn3GHEeYy5cHknDuV4zNcxrRUoRqVnhsHRrTpYahzxnicXW5MLhqTdbE\n16dJSqH3bXzF+0f+2b+y3+yPoTa3+0F8Z/B/gGR7d7nTfDVzftqfjvXY1DgN4f8AAmipqHinWIjI\niwy3tnpT6baSTQf2hfW0Ugmr8Rbf9oX/AILE/wDBUJ2X9l3wHb/sHfsy6qjCD4yeO5LmL4ieLtFm\n3+Xe+G9auNGk8QXH26FUudLvPhj4b0vTbC+WbS7/AOLE0RWVvq39m/8A4IXfsj/CjXB8SP2gNQ8V\nfthfGG9uE1LWvF/xqu57zwnc6ypCtfxeAnvdRi1nzYVjimj+Iet+NQ7AzwG2YRInw0eLuIeIk48E\ncOylg5WUOJ+J1iMryeUW5WrZflsY/wBr5tTlFe5P2WCw8pK31lR96X9US+jv4PeDj9r9KLxlo0eJ\nMMlLEeBvgW8p468R6FaLlGeW8Xca1cR/xDvgDG0ZqP1rCvH8TZvRoSU45NOsp0l836z/AMFa/wBt\nT9tLVdS8Ef8ABK/9jzxHqWgJdT6ZeftHfGrT7Sy8LaWyPcQzXGn2c+r2fgbRr+BUi1DT4PEPizxF\nrF5amaCb4di8UxDc8B/8ERfiJ8e/FOnfFf8A4Kk/taePP2j/ABZBOby3+FvgnXdW0T4aaIZpInvN\nLttbnsdKu7TSL9Iwl1pXw98K/D1beULJbapcZEi/0G6Ro+keHtK0/QtA0rTtE0bSbSDT9K0fSLG1\n03StNsLWNYrWx0/T7OKG1srS2iVY4La3iSCGMKkaBRitGrp+H9PM6kcTxvnWO4urRlGpDL6y/s/h\nnDzjKUoOjkOEqexxDhflU8zrY2c1aUkppt8uK+l5iuBsJicj+i94acL/AEestrUamEr8Y5bKXGHj\nhm2GqQnTrrMfFjiHDvMMmhiVy1ng+Bsu4Zw+HqSqUqcqlBJPyD4L/s/fA39nPwpH4J+BXwq8FfC/\nw4vlNcWHhDQLHTJtUuIY1hS/1/U44jqniPVDGqpJq2u3t9qkyqgnvJNoI9foor77D4fD4SjTw2Eo\nUcNh6MVClQw9KFGjSgr2hTpU1GEIrpGKSV3vrf8AkjNs4zfP8zxuc59muY51m+Y1pYnMM1zbHYrM\nczx2Jn8eIxuOxlavicVWn9qrXqzqS+1NvUKKKK2POCiiigAooooAKKKKACivnX9o79rT9nD9kfwh\n/wAJn+0J8V/DPw906eO4bR9N1C5kvPFfiaW2B8218KeENMjvPEXiSeNvLS4OladcQWXmxTajPbWx\necfhxqP7f/8AwUh/4KY6le+C/wDgml8FL79n74Jm4uNN1v8Aat+M0dlaX1xamdrWV/D9w1lr2h6L\ndwqzw32j+BrP4i+NrN3s9Ti1bw8Ekevk8940yTIsRDLpTxGaZ3WV8Nw/k1B4/OK63U5YalJRwdDl\n994rH1MPhlBTftm4pP8AoLwo+jP4n+K+UYrjGjQyfgTwwyyqqeeeMHiVmlPhLw3yySnVpyw9DO8d\nSlW4jzT2lL2NPIeFcHnGeTxNSjSlgIKaqn7H/tZft6fsq/sTeHl1j4+/E/TdE1i8tZLnw/8ADzRF\nGv8AxL8UKnmKh0XwhYzfbY7KWSMwf2/rT6Z4Xt7lo4L/AF22kdN34uXHxz/4Kpf8FdJ59G/Zm8Na\np+wl+x3qrxpcfG7xU+oWnxO8f+HJpJAtz4c1SyNnq+qJqVsv2mLTPhkdL8O2032vw74m+MV9ZXEa\nTfZP7Jv/AARP/Zt+CHiNvjB+0Lq+rftf/tA6pcLrGuePPjFF/bPhS28RO0U02qaP4L1ifWV1PUYp\nYk8jX/G+p+I9Vjljiv8ASV0eceRX7MxxxwxpDCiRRRIkccUaKkccaLtRERcKiIvyoijaq8DivB/s\nbjHi5N8TY18LZJUs3w1kOLc83xlK870s64ipqPsac0mq2DyeMIzpVFTnj5yVz9Xh4k/Rw+jw3T8E\nOGo+PHifhVaHjX4tcOwwvh7w9joyusd4Z+DmNniP7RxWGkoVcv4k8Sa+Jr4fG0YYzC8J4eMuQ/Nz\n9iT/AIJVfsmfsN21rr3g3ws/xD+MDRZ1b43/ABGhs9Y8aNdzQhb4eE7Ywf2Z4C064ke5xFoEC65c\n2dx9h8R+JNbEMVxX6TUUV9xlOT5VkOBpZbk2Aw2XYKl8GHwtKNODlZKVSo0uetWna9SvVlOtUleV\nSpKTcn/LXH/iNx74q8T47jPxH4tzvjLiXMWlic4z3H1sZiVRjKpKlg8LGcvY5fl+G9pKODyzAUqG\nX4Ok1RweGpUUoIooor0j4sKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKAC\niiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKK\nKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooo\noAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiig\nAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKAC\niiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKK\nKKACiivgD9sH/gpv+x1+xFZ3ln8XPiXb6v8AEGG386y+Dvw/Fr4p+Jt47xmW2W+0eG/trDwjb3cY\n8211Lxtqmg6ddxhxp91dXCiBvPzPNcsybB1cwzbH4XLsHRX7zE4yvToUk3zcsFKpJc9SfI1ClC9S\ncvdhGUnZ/W8D8A8ceJnEeC4R8PeE8+4y4kzCVsLkvDuV4zNMfOClGNTEVKOEpVXh8HQupYrHYh08\nHhaTdbFYilRUqj+/6+AP2wf+Cm/7HX7EVneWfxc+Jdvq/wAQYbfzrL4O/D8Wvin4m3jvGZbZb7R4\nb+2sPCNvdxjzbXUvG2qaDp13GHGn3V1cKIG/KEfFj/gsV/wVSzbfBPwt/wAO9v2Vta+X/hZfiG41\nW3+LHi/w/OWH2zQtW+x6b4t1D7bblL3TJfAek+DPDzM0+l3nxO1KAGRvv/8AY9/4I3/sdfsl3dp4\n21Dw7cfHv40C4/tS9+Lnxhgtddu7fXZJTc3OqeE/CcyXGg+Gbhrwtd22rTJq/jK3lkk8zxhcBi1f\nCrijibii9PgnJvqeXTsv9bOJ6GIwuDnTbkvbZPkd6eY5nzQtKhXxX1LB88WpSqQbv/VS8CfA/wAD\nL4v6T3iV/rLxjhbS/wCJfvAzNcoz3iPD4uDl/wAJ/iP4pWx3BvBHs61P2GZ5TkH+tHEkMPNTowwe\nJTa+DoPjN/wWK/4KjwNb/AjwXb/8E+P2Y9a3AfFbxXcaqPiz4s8Pz7wlz4a1GSwsPE1x9ttzHfab\ne+BtC8KaQspnsH+Kd7CGZ/t/9kL/AIIw/sf/ALLuoW/j7xfpV7+0n8bnujq2o/Fb4z29vrUMGvyS\nm4udW8LeBrl9Q0PRbqS7C39vq+ty+JvGdjfb7i08YLvdD+uFFehlvAOWUsXSzbiHF4vi3O6XvU8x\nzt06mGwc27y/snJ6cVluWQ5kpQdKhPEwldvFSk3J/JcY/S044xnD2Y+Hvg9kOQfR78L8elRxnB3h\ndHF4POuIsNBVIU/+IgeI+Nr1uNeOsRKjN0cVDMs1o5LiKbUI5FSpqMEUUUV90fyqFFFFABRRRQAU\nUUUAFFFFABRXDfEf4nfDf4O+DtW+IPxX8deFvh54L0OMSar4o8Ya5p2g6LaFtwgga91G5t4pb27d\nPJsNPgaS/v7po7Sxt57qSOFvwZ+KH/BZH4wftMeNNY+An/BJf9nvxJ8bvFkcyWGp/tA+M9Bu9L+G\nPhKCeV4v7fttH1d9LgsrF41dtL174nap4Ztf7SiS2j8F+IElhtpfm8/4tyHhtUqeY4tzx2J93A5P\ngaVTG5zmFRuSjTwWW4ZTxFXnlHl9rKMMNCTXtq8I3kv2rwk+j34reNbzHGcGZBTw/C2RWqcVeIvF\nGPwnC/htwhhYum62K4l40zmphsowHsaU1X+oUq+IznEUeb6hlmKqJQf7g/Gz4+/BT9m7wNffEf46\n/Evwt8NPCFgJA2reJdRW3lv7hEaT+ztB0mBZ9X8S6xIilrfRPD9hqGsXOCLaxlavwf8AFv8AwVU/\nbJ/bu8S658I/+CTH7PWsr4btrg6J4i/av+LWlWmneHvDLzqyy6jpFhqzXHhTw9cW0JXVNLg8SzeK\nPGGsaaZPsvwrg1GIRV3fwR/4Ioap8T/HNr+0J/wVI+OXiL9qv4vzTQXkXw9sNb1Kx+EPh2NHjuIt\nHneGw0S+1jTrW4QSL4c8Maf4N8DIxuLC70DW9PmeVv3g8I+DvCHw+8N6T4N8B+FvD/gzwpoVqllo\nnhjwroum6D4f0mzQkpa6bo+lW1pY2MAJLeVbwIhYliCxLH5lUOOuLrvF1qnAeRT2wuDq0cRxhjqN\n219YxyVXBZAqkWpezwaxWPpS56c8TBu5+3LNfoq/R3vDIMvwn0sPFbCpJ5/xJgs0yT6OPC+Y05Pm\neT8LVJYHijxcnhasa1L63xFVyDhLHUZ4fGUMlxkFaX4nfs3/APBEH4a6d4wb46ft8fErX/22Pjxq\nb2t7ev47v9dvPhhot1C8sqWEej6vez6l48sbJpZLaztvFZt/CAsDFb23w8sDEhr9x9N03TdG0+y0\nnR9PstK0vTbWCx07TNNtILLT7Cyto1htrOysraOK3tLW3iRYoLeCNIYo1WONFVRm7RX1uRcNZFwz\nh54fJsvpYX20lPFYludbHY2reUnWx2OryqYrGVXKUpc9etNx5nGHLFtH8+eK3jd4qeNub4bNvEri\n/MM/WXUvquRZLGOHyzhXhjL4xjTp5bwpwrlVHB5Dw3gIUoU6f1bKcvw0aqp06mKlWxHPVkUUUV7p\n+VBRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUU\nAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQA\nUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABR\nRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFF\nFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUU\nAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUV+b37Yf/BVv9jD\n9ipr7QPiB8QT44+J9qTCvwc+FaWHivx5BdksiweIwNTstB8ElW8qSS28WazpmrzWkn2nSNJ1Er5T\nedmmb5VkeDqZhnGYYTLsHS0liMXXhRpuWvLThzyTq1Z8rVOjTUqtR+7CEpb/AGfAXhzx94qcR4Xh\nHw34Pz/jTiLGe9Ryjh3K8VmWLjRUowqYvErD05wwWBoOUZYrMMZOjgcLTftcViadJOZ+kNfnb+2L\n/wAFS/2Nv2JLfUNL+JvxGh8T/Em1hZrb4O/Dj7H4n+IT3BR2hi1u2jv7bSfBUMoCSLP4x1XSJJ7Z\nnl0u2v5UEDfmBc+J/wDgs1/wVH2weB9FT/gnJ+yxr24HxNq9xqsXxo8U+HZiV8+1dYtH8c6g95bT\npdWK6LZfDbwnqdhLNby+L9XhCvJ+g/7HP/BID9jb9j24sPF9n4Sm+MvxjhmXULn4xfF2Oz8Ra7a6\nyZDcTaj4Q0CS3Ph/wbMLp5pbTU7CzuPGCQytbaj4v1EAyn4f/WXiridulwbkzy3LpWX+tfFGHxGG\nozpvm/fZPw/elmGYc0Gp0MRjpYHCOSSlGrFyR/UUPBLwG8Do/XfpKeJS414yw3vR8AvAjOMpzrM8\nNi6UnfL/ABI8XFHH8IcIeyr0p4XNMo4Wp8U8RU6c+anPBYiDmfnaPGn/AAWS/wCCqmYvh9o3/Dun\n9lPW8D/hL9Wk1iH4v+L/AA9cGQfaNMufI0bxtrP2q2lS6s38O2fw58GanYzS2j+NNYRSzfob+yF/\nwR6/Yw/ZJktfFf8Awhp+OHxf89NSvvi98Zrew8U6zFrnn/bJdT8KeHbm2k8O+EJ1vWluLPVbKyuf\nGMaSGHUPF+oEecf1Por0Mr4CyrDYuGb55icVxXnsdY5rnrp1oYWV22spyuEFl+U0ub3oLC0Pbxu1\nLEzd5P5Hjn6WfHmccO4vw78LMmyHwB8KcTanX4C8K4YvLMTn9GCnTpVfEHjrEYitxh4g46VFxp4q\nefZrLK6rjGVDJsOkoBRRRX3J/LAUUUUAFFFFABRRRQAUUUUAFFVb6+stMsrzUtSvLXT9P0+1uL6/\n1C+uIbWysbK1hknury8up5I4LW1toYpJri4mkSGGFJJJZFRHc/iH+0x/wW9+D/hXxYvwQ/Yk8A6/\n+2x8fNUmudO0zS/hrbatf/DjS7+M+WZ5/EejWGo33jhLUst5JD4HtLnw/LaRXUeoeOtGmQSHw884\nkyLhrDRxOdZjRwcaklDD0Xz1cZjKrkoqjgcFQjUxWMrOTivZ4elUkuaLklFSkfqXhV4KeKfjbnNf\nJPDLg7MuI6uApfWc6zOPsMBw3w5gFGpOeZ8U8T5nWwmRcN5dCnSqTeMznMMLRnyyp0Z1K9qb/arx\nL4n8M+C9A1bxX4y8RaH4U8M6FaSX+ueJPEur6foegaPYRECW+1bWNUurWw060jOPMuby5igQkBpM\n8n8I/jp/wW2h8ceN7j9n3/gmR8FfEX7XfxquXntl8XjRNatvhD4ejhkktrjW8JcaVq/ibS7C6EUN\n3rWo3ng/wLFDPBqdv421CzKwyeZeFv8Agl/+3D/wUA13Rvij/wAFXv2gdV8P+B4Z4tc8Nfsn/B+/\nstN03Q5ZcPDba/c2EV94W8PXVvA0tldT2X/CaeN73T5/s9x8QtKv4ZIz+7nwI/Zz+Bf7MPga1+HP\nwD+GPhb4a+FrdYDcWfh7TwmoazdQI8cep+J9eumuNc8V6x5btG2s+I9R1HVGi2wm78lEjr5FYnjr\ni7TA0KnAmRTtfHZhRo4ni7G0m3rhMtcqmDyKNSPND2uOnicdTbp1YYSEkz+hlkf0Vvo8c0+Ks0wn\n0rfFXCtcnC3COY5lkv0d+GMwg6nKs/40pwwfEnirVw1SNKs8BwrSyXhbFx+s4DE8QYunHmf4g/DT\n/gj58e/2rfGGk/HT/grZ+0N4l+KWrx3Eup6R+zV4E16bTPh14SS4eUjR77W9Dls9O023EMiQ6hpX\nw0sNKuJZ4Ibi5+I+tiS6L/vL8LfhL8L/AIIeCtL+HPwf8A+Fvhz4K0cN/Z/hnwjo1lo2lRzSLGLi\n9mhs4Yje6lemJJNQ1W9afU9QnBub+8nuC0jehUV9Lw/wjkXDXtquX4WVXMMVrjs5zCtUx2dZhP3e\naeLzHEOVeam4xk6FN08LCSXsqEFc/FfFv6Q/it40rAZdxdntDA8I5I+Xhbw14Sy7C8K+GfCWHi6y\no0OHeDMnjQyvCzo0606X9p4qni88xFJqOPzXEzTqMooor6U/EQooooAKKKKACiiigAooooAKKKKA\nCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAK\nKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAoo\nooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiii\ngAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKA\n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\nUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRR\nQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFA\nBRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAF\nFFFABRRRQAUUUUAFFFeX/GD41/CP9n3wJqvxM+NfxC8M/DfwToygXviHxRqUVjbPcOsjQadptvl7\n3WtYvfKdNP0TR7a81jUJh5Gn2NxOQhyrV6GGo1cRia1LD0KMJVK1etUhSo0qcE3OpVq1JRhThFK8\npzkoxV25WTb7ssyvM87zHBZPkuXY7N81zLE0cFl2V5Zg8Tj8xx+Mr1I0sPhMFgsLTrYnF4mvUcad\nHD0KdStUqSjCEZTfveoV8Dftp/8ABSn9lD9hLRZ/+FveOE1b4hT2K3nh/wCDXgo2ut/ErWhOrmzu\nLrSxeQWnhPRrnY0ieIPFt7pWmXMMVzHo8upanGmmSflX4x/4KPftw/8ABSPxRrfwZ/4JVfCvWPh3\n8L7W9k0fxp+2J8TrJNHt9PtwzxXX/COG7tdV03wtJcwSRT2dpZW3if4r3Fhc22qWXhrwncWt5fR/\nan7FX/BHT9nr9l/WU+L3xZ1G/wD2ov2ldQvTrutfGD4rQvrFrpfiOaVbibUfBvhvWbnWBaanHOkc\nqeL/ABHf6540e8E99YazpVvcHSI/zqfFuc8UTnhOAMFTqYNSdPEcZ5vRrQyKjaUoVP7Gwd6WJz/E\nQs+SpTdLK41YpVMXVhKx/ZWF+j14beBWGocQfS74mxuF4jdGjjcp+jV4d5jl2J8VMxU4Rr4V+JXE\nPLjsl8IsnxMJUZYjBYyOYcd18FWqvB5BgcVRdd/C1vbf8FjP+CsMIu7jUE/4J2/sjeIctbxWy6xH\n8XvG3hi4kdVcxJNo3jjxAl5ay5LXNx8Mvh9r+j3CzW8GvRojyfp7+xv/AMEov2NP2KF07XvAvgEe\nPPipahJZ/jN8UBZeJ/HEV/w0lz4Xiext9D8CgSGZIZfCul2GtSWUv2PWdc1UqZ2/SWivTyjgPKcD\njIZxm1fFcT8QRs1nWezjiKmGnfmaynAqKwOT0VPmlShgqEKsIycZ4io05P4fxC+ld4gcUcOYzw58\nPcsyHwN8Iq7lTl4Z+FdCvk2EznDxVSlTqcfcUzr1eKvEbMamHdOGNxPFGaYnAV60I18LlGDVqSKK\nKK+3P5fCiiigAooooAKKKKACiiigAoor4X/bA/4KPfsi/sPadKvxt+JVu3jWSxW/0j4S+DoovEnx\nQ1mGVJGtJV8PQXlvD4fsb4RP9i1vxfqGhaDcsksNtqktyvlHhzHM8tyfB1cfmuOwuX4Ogv3uKxle\nnQox3tHnqSinObVoU4t1JytGEZSdn9TwXwPxn4j8R4DhHgHhbPeMOJMym44LI+Hcrxma5lXUXFVK\n31bB0as6eGoKSnisXVUMLhaV62Kr06MZVD7or88v2y/+Cof7Hv7Dtvd6X8VPH/8AwkPxJS0FzYfB\n34fR23iP4hXHmpI1q2r2wvrTSfB1pcBVkiu/F2q6V9qtjJLpUF/LH5D/AJY/8Li/4K7f8FXQI/2e\n/Dsn7AH7Jesjyh8VvEt1fw/FbxxokkkivqPhzVbe0sPFF5Hd25iuNO/4QCy8L+HluBd6Tf8AxY1O\nPcK/Qv8AYz/4JAfsh/sfT2vjSTw9N8cvjcbs6tf/ABp+Llraa5rVrrkkgnn1Dwb4dnW50XwbKbsz\n3UOrQrqHjbddXMOoeNL+AxIvwi4n4l4pvT4Jyn6lls7L/W7iXD18Pg5025L22S5G5UcfmnND36GI\nxbweBcotSlVg1zf1avAvwS8CU8Z9J7xCfE/GmE95fR48E83yvNuIsNjIOX/Cf4m+KcYZhwlwKqVW\nk6GaZNw9HibiiNGrF0o4HEwqSj+d39of8FfP+CtQC6TDJ/wTv/Y61+XI1KU6tH8Y/HXhh3kXzEIf\nQvGfiKO/t5N8cVp/wrf4c61o9z5cmo+JUhEsv6k/sZf8Esv2Pv2IrSx1X4eeBE8Z/FGJN998afiT\nHp/iP4gvdyKftD+HpmsLfS/BFo5aSJLfwrp9heTWbJDrmqatcrJev+jFFenk/AuV4DGRznN8RiuJ\n+IUlbOs7cK08M78zjlOAjFYHJ6PPeVOGCoxrRUnGpiajcpP4fxD+lTxzxRw1ifDXw8yfIvA7wgqy\n5ZeGfhjTxWW0M8pwjUpUsR4h8WV69XinxIzOdB04YvE8TZlWy6rUpwrYTJ8G1GESiiivtj+YQooo\noAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiig\nAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKAC\niiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKK\nKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooo\noAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiig\nAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACobi4t7S3nu7qeG2t\nraGW4ubm4lSG3t7eFHkmnnmkdY4YYo43kllkYIiK7u4VWY/AX7a//BTH9lP9hTR54Pij4xHiP4mT\n2ol8P/BTwNJZaz8R9UluIDLp8+q6eLyK28G6JeAxyR674ouLGC6tjO2g2+r38P8AZ0n5KW3wg/4K\nff8ABY2eHWf2gdY1b9hj9iPVpor3TvhFoC3cXxM+JfhqSTz7ddZs76LTtV8QQ6jDFE8OvePrTR/B\nUfmab4n8J/C7Wog88nxOc8bYPA42eR5Lg8RxNxIkv+EfK5Q5MFdyiq2d5jNvCZNh78l5YmbxMuZe\nywtTni1/Tvhn9F/iXinhih4peJ/EeU+B3gs5tR8SeOqGL+scTOCcqmX+F/BmGS4h8Ss4lTjOVOlk\ndCOTUnTr/wBo59g3Tkn9QftU/wDBafwboXjc/s5fsBfD+7/bI/aP1Wa50uyPg+21LWPhR4Y1GB3S\n4ubzVtDkS58fJpq7LnUP+EXvrLwjZ2ZludV+IVjLa3VifK/g/wD8Eg/jh+1J480r9oz/AIK8fGbV\nPi34pgxc+GP2b/COuNY/DvwTZ3BjuX0LWtV8Mtp+mW0KSiOHUfD3w2S1sby6soNQ1f4heKBdXcdf\nr7+yr+xd+zZ+xb4I/wCEI/Z++HGmeF1u4bVfEvi27VNU8f8AjS5tkAjvPF3i65iGpaptl825tdKj\ne28O6VNc3Y0HRdPgmlgb6lrzqHBmPz6tTzDxBx9LNXCcK2F4WwHtaXCuAnCUpU3iaNRqtn+JptKX\n1jMv9mjKVSNLAxgos+zzP6S/CXhJl2O4Q+iBwnjuAI4rC1suzzx44teAzDx84sw9aFSnio5NmeFU\n8t8I8kxcZcn9j8FP+2qtGnhauZcU18S8RTOZ8G+CvBvw58L6N4J+H/hXw/4L8I+HrKLTtB8L+FdG\n07QvD+j2EIIitNN0nTLa1srKBevlwQopYl2DPknpqKK/RYQhShCnShGnTpxjCFOEVCEIRSjGEIRS\njGMUkoxSslZK6V3/ABticTicbicRjMZiK+LxeLrVcTisVia1SvicTia1SVStiMRXqznVrVq1SUql\nWrUnKpUqSlOc5TcpMoooqjAKKKKACiiigAooooAKKK+V/wBqH9tf9l/9jXw0viL9oT4saB4Nnu7S\nS70HwjHI+r/EDxSiPNAp8NeC9LFzrmpW73MDWkurm0h0CwuGUavq9nEHnrlxuOwWW4WtjcwxeGwW\nEw8eavisXXp4fD0o3avUq1ZwhFNqyu7tuKTvJJ+9wxwtxPxtnuX8McHcPZzxTxDmlZYfLci4fyzG\n5tm+OrPm/d4TL8BQxGJrySjKc+Sm1CClUm4wTkfVFfHH7WP7fX7KP7E+h/2j8fPilpei69dWLX+h\nfDjQwPEHxN8Sw7pooZNI8H2ExvILG5nt5baLX9ck0rwulyjwXeuwSKwr8cbv9t//AIKd/wDBT67v\nPDv/AATu+Ek37MP7PNxcTaVqX7UHxb+yWvibU4FmkhvJPDt79n1uw0yYQSC3n0z4daR4z8S6XqCw\nXTePdCLkJ9f/ALJv/BFH9mj4G62fix+0DqGqftg/tA6lfLres/Ej4zJc6zoEGuMImkvdK8FazqOv\nWup30c0SSxeIfHF94m15LqOK+0u60lx9mr4D/W/POJXKjwHk6qYNuz4u4gpYjB5Eo3knUyvA2p5j\nnkrJqFSnDDYBVIx5sXOnK7/reH0dvCzwTisx+lj4jSwfEdCMalP6PXhDjcn4l8VZ1tXDCcecUueK\n4M8LaWtKpisJi8RnfFiwlSoqXDtHFwPjt/2pf+Cr3/BVKSTTv2Lfh/N+xV+y7qLtaT/tCfEK4ks/\niF4r0t3bzr3wrrENncX8fn25Q2CfCnSpxp2rw3Gn6l8Y4YpTHF9y/sff8EZP2UP2Y9Rj+IvxCtbv\n9p/48XV8da1T4t/GW1TWYLfXpXWa41Lwv4J1C51bSNMu2u0GoQa9r9x4l8aW2oNJc2niuBG+zj9c\no444Y0hhRIookSOOKNFSOONF2oiIuFREX5URRtVeBxT67st4DwEMXSzfiXG4ni7PKb5qWNzeNP6h\ngZttv+x8kpr+z8sjdRlGcadXGKV5PFyldy+X4z+llxZX4cx/hz4J8M5J9Hnwux0Fh8dwx4d1sX/r\nZxVhoe0jCXiP4n4yS4u42ryhOVOvhquLy/h2pRkqEeHoUqdKKKKKK+7P5SCiiigAooooAKKKKACi\niigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKK\nKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAoooo\nAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigA\nooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACi\niigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKK\nKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKgurq1sbW5vb25gtLOzgmuru7upo4L\nW1tYI3lnubmeV1igggijeWaaV1jjjV3dwqs5/C79p/8A4LT+G4fHbfs3f8E8PhvqH7Y37RGpSTaf\nBqHhay1HU/hJ4XuVcQz302raTNDceN4NJaS3n1W+0e90zwFp1pK11qfxDiktNR09fCz7iXJOGsND\nE5xjYYf20/ZYTCwjOvj8fiG1GOHy/A0Y1MTjK0pcq5KNOXJzRlVcYXm/1Xwm8E/E7xvzvE5J4c8M\n4jN1lmH+vcQ57iq2GyrhPhPKYqcq2dcW8U5nVw2S8OZZRp0qtSWJzPGUXWVOdHB08RilGjL9gPjR\n8dvg3+zn4C1P4m/HL4jeGfht4L0oMJ9b8S34txdXIillj0zRdOhWfVPEWtXSQyGx0DQbLUNbv2R4\n7DT55ARX4CeKv+Chn7d//BTTxJrXwg/4Jc/DHVfhH8Hba9fRvGv7YPxMhOi3FvbSFoLkeGpzBqVn\n4Vn8qUzR6d4dtvFnxUe2k03V7a08ImG9kruvgv8A8Effi7+0l4+079pL/grj8ZdW+NnjlHFz4d/Z\n68N649p8MPBNoZ1uIdF1W+0H7Dp32FDzfeEvh7a6TodxfIb7WvF3ir7XqKSfvz4U8JeFPAXhvRvB\nvgbw1oXg/wAKeHrKPTtB8MeGNI0/Q/D+jafEXMVlpWj6ZbWtjp9qjO7LBawRxh2d9pdmJ+QWH4y4\n0u8bPFcD8N1LOOBw9Sn/AK4ZpRbk0sbioOpR4do1ItOWGwjrZmk6tKriaLkj+iVm30a/oypw4boZ\nD9KXxqwtlLijOMHjP+JceA8ypuUZT4a4fxMMFmfjJmeDqRqKjnHEEMt4Hk54PH4DJs0VKcp/ll+x\nP/wR6/Zv/ZT1SH4rfESa9/aV/aSvr3+3tb+M/wAVIDqw07xRPO93e6r4I8N6pPqsWkahJdsLn/hK\ntcvdd8cvefaLq38SWVvdTaYv620UV9tkuRZPw7go5dkuX4fAYWL5pQoxfPWqP4q+Jrzcq2Krz+3i\nMRUqVpfamz+YvEvxV8RvGLifEcX+JnF+b8W57XgqNPE5lXX1XLsFCUnRyzJMrw8aOW5DlOH5msLl\nGT4TCZbh037DCwcptlFFFesfnwUUUUAFFFFABRRRQAUUV4d8e/2lvgF+y74Nl8e/H74q+FPhp4eV\nZxZya/qGNW1y4t0Ly2Hhfw5ZJda/4q1JUAkOmeHtM1C/ERMrW4iSSSsMTisLgsPVxWMxNDC4ahBz\nr4nE1qdChRgrpzq1qsowpxVtZTkktLtt3fqZJked8TZtgMh4byfNM/zvM8RDCZZk2S5fi8zzXMMV\nUclTw2By/A0a+Kxdepyvko0KU6krO0Xue4185/tHftbfs3/skeEj4x/aE+LPhj4e2EsM0mk6Xf3T\n3ni3xK8IcPb+FfB+lx3niPxHMrqEmbS9NuLe0LpLqE9vbiSdfxK8S/8ABUP9uD9vzX9X+GX/AASe\n/Z/1TQfBlpdyaN4l/au+MmladY6PocjEh7rQ7TVJdR8J6JcQxGO9hstTi8aeM77TJlmh+HmnXqBx\n7f8As4f8EQfhnp3i4/HT9vj4k+IP22Pj1qUlte30njvUdcvPhdo1xCzyRafFour3c+p+OrKxaWa3\ntYPFjw+EDp7QW1r8OdOMCufz+XGmZ8QSnhuAMo/tSnzOEuKM3VfAcL0WnOM5YWXKsdnk4SXK4ZfS\njhubkbx6g5yP6+w/0ZeB/CGhSzr6XXiN/qNjfZUsThvAjw6llfFXjxmcKkVUoUuIKMq0+FvCrD4m\njOniIYri/HV87VFVacOFKmJioPwfV/8Agop/wUR/4KQanqHgj/gl98DL34P/AAfF7c6Lrn7WXxnt\ndOtHj2SPb3beHzcxa74b0ae3jkC3ml+HLD4h+PLZJrHUoYPDtxGz19Kfsu/8ERfgV8PPE8nxo/bA\n8W63+2r+0Bq11Bq2r+J/ixLqmq+BbPVVCsTD4U1vU9Xm8aSQZa1F/wDEC91ewnhS3ubDwvo06CMf\ntLpGj6R4e0rT9C0DStO0TRtJtINP0rR9IsbXTdK02wtY1itbHT9Ps4obWytLaJVjgtreJIIYwqRo\nFGK0a6MFwFhq+KpZrxfmFfi/NaUlUoLMKcKWRZdUV7PKuH6blgqEoq1sTilisc5JVHilNHjcS/Sz\nzrKcizHgL6OnB+VfR24BzCi8Fmc+EMZicf4rcY4JScnHj3xdxcKPEuaUq0nOc8lyJ5FwxTp1quCW\nTVcMryr2dnZ6dZ2un6fa21jY2NtBZ2VlZwRW1nZ2dtEkNta2ttCiQ29tbwxpFBBEixRRKkcaKigG\nxRRX3ySSSSSSSSSVkkrpJK+iXReb13b/AJIlKU5SnOUpSlJylKTblKTbblKTbbk222222222222U\nUUUyQooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKK\nKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooo\noAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiig\nAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKAC\niiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKK\nKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKK8e+OP7QHwV/\nZp+H+p/E/wCOvxG8OfDjwbpisH1XX7wpcahdiOSRNK8P6PbJcav4m1u4SN2tdD0Cxv8AV7lQ7W9l\nIqSMMcRiMPhKFXFYuvRw2GoQlUr4jEVYUaFGnFNyqVatSUYU4RSvKc5KKVrvW79HKMnzfiHNMBke\nQZVmOd5zmmKo4HLMoyjA4rMc0zHG15+zoYTAYDB0q2KxeJrT9ylQoUqlWcmoxhKTR7DX54ftt/8A\nBT39lP8AYT0uax+JHi3/AISz4p3FoLjQPgn4EltNX8f6gZgVsrnXIhcrY+CdGuWw8eq+J7ize+gS\n7Ph2w1m8tpbE/lv4l/b6/b+/4Kg+IdZ+Ff8AwTG+HGrfA34F299PoXjH9r34lwPo2oyRo7Jdp4Wv\n4oNWtfDFw9swI0nwnbeKPibDHc6Rq0134MDzEfe37En/AASA/Zn/AGQ9Rt/ib4qF3+0N+0Xc3b61\nq/xv+KFr/aF3ZeI7p2n1DVPBHhu+u9WtPDV3cXbS3P8AwkN/ea145aW4uxJ4uNrNJaH87lxXnnFU\np4bgHB044DmdOtxpnNCtDKIJScJvIsvbpYnPq0LNQrt0MrjVSU8RWg9f7IoeAHhd4CUKOefS24kx\ndfixUqWLyz6M3htmmX4jxExLlD22Gj4qcYU1jsl8JstxEXh5YjKoxzXj2tgMTUnh8ny7FUXVPz0t\nPgN/wU5/4LDXNvr/AO1Hruq/sQ/sX6jPDf6X8DvCxvIPiT8QdEEv2rTm12x1KC3vtWW4idH/ALf+\nI9rp/h6G9h07xF4U+EN1DIt4v7pfsu/sd/s4/saeBV8Bfs+fDbSfB9ncJAdf8Quran428YXkIJGo\neLvFt8JNW1qUSNLLa2Uk6aNpXnS22g6Xp9iVtB9NUV7eQcGZXkmJnmuIq4nPOIa8OTFcQ5vKOIzC\nUXzc1DBxUVQyvB3lJQweAp0qSpuMKrquKkfl/ix9JbjnxMyPD+H+TYDJfC3weyvEKvkfg74d0K+U\ncIUqtOSVLNOJa069XNeO+JZxp0p4viTi3GZhjp4pVcRgoYGFWph2UUUV9efzsFFFFABRRRQAUUUU\nAFFFfmd+1V/wV1/YT/ZHOpaP4x+LNt8QfH+mu0E3ww+D62XjfxdBdrG7ta6zdQanZeFvC1xFiLzr\nPxP4j0zUtk8UtvYXCCQjzc1znKMjwssbnOZYPLcLG6dfG4ilQhKST9yn7SadWo7e7SpqdSTsoxlJ\n6/bcA+GviH4q5/R4X8NeCuJON8+rcsllfDWT47NcTRpSmofWsZ9Uo1YYDBwetbHY2dHB0Y81SviI\nQjKR+mNeGfHX9pr9nv8AZj8Mt4t+P3xf8FfDDRzDNNZjxLrMMWs6yLfHnQ+G/DNr9q8ReKbuP+Kx\n8PaVqF9jJFuQDX4WD9p//gtF/wAFEs2f7KvwK0n9hr4I6n+7X4yfF83B8cappzsS15ol3r/huW/u\nLTUrR0NhdeBfhpdJZ36zQL8SImC3MfufwJ/4IPfs+aJ4mHxW/bF+JnxG/bT+MF9NBfavq3xK1zXL\nPwVJfxl5gZtFbW9V8S+KPJuJplkPi7xfqWi6jD5Zl8LWge5gf4pcY57n94cE8M4jE4eVlHiPiVV8\nlyNRbajWwuDnT/tjNabS/wCXOFw9Jv8A5ikk2f0yvo3+FPhPfE/Sf8ccoybN8PaVfwa8EZZV4neK\ncqkLqtlufcQ4bHQ8OOAsZF8rf9pZ/nOPhFybyObVjyDxr/wVr/aq/bR8U6v8IP8Agkf+zrrnie0s\nrg6V4k/ag+Kmh2+neDvC7zZKX+kaVrlxH4a0SVLfZqWnHx9dajr+q2Znt7b4Ty3Uau3ovwD/AOCI\nuh+IvGifHz/gpR8Y/En7ZHxuvPs0z6BqmueII/hN4eWGSS4h0dFuWstc8XaZpty7vpmkmLwt4Fgt\nJp9Lm+H11aHzK/cfwd4K8GfDvw3pfg34f+E/DngnwposAtdH8MeE9D0zw/4f0u3BJEOn6PpNraWN\nnGT8zLBAgZiWbc2WPTVrhuA45hiKeY8b5pV4sxtOaq0MvrUlheF8vqa2+pZFCpUpV5017ixWZ1MX\nXqK03yTRxZx9LGrwflGP4M+i5wNgfo/8MY7DTy/NOL8ux8s+8duL8G3FVf8AWXxXxGHwuY5RhcXO\nnHEvIeBsLw9lOEnOrhebGYducsPwz4Y8MeCvD+k+E/BnhzQ/CfhjQrSPT9D8NeGdI0/Q/D+j2ERJ\nisdJ0bS7a1sNOtIySY7a0t4oEJO1ASWO5RRX6BCEacYwhGMIQjGEIQioxjGK5YxjFaRjFaRitEtF\npqfyFXr18VXrYnE1quIxGIq1K+IxFepOrXr16s5VKtatVqSlOrVqzlKdSpOUpznKUpylJykyiiiq\nMgooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKA\nCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAK\nKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAoo\nooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiii\ngAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKA\nCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACivKPjV8c/hB\n+zn8O9c+K3xu8faB8PPA2gQtJfa5r10Y/Pn2SvBpej6dAk2p+INcv/JePTNA0Szvta1KfFvp9hPM\ndlfzzeJf2n/29v8Agsj4i134U/sQaVr/AOyx+xrFcz6J48/aZ8X2t7pvjDxxYpM8OqaTolxpV6Zw\n9zGssH/CDeBtT+2zQE2vxI8f6Jo2rvocfynEPF+XZBUo4CnRxGb59jYv+zeHssjGrmWK1lFVqqcl\nTwGBg4t18wxkqeGpwVRxlUnB03+/eDn0duMfFzB5rxdicwyjw78J+F6tOPGnjFxxUr5fwVkKdpSy\n3AVIUp4zizirEU+WOV8H8N0cZneNxNbCU6tLC4Wuscvs79t3/gsD4L+C/jJf2aP2Q/B0n7Vf7W+u\nX83h3TfBPhGHUtb8HeCtd2XKsni290Bjc+ItY0ySFp9R8G+Hby3m0+1ttRk8W+J/DItl87wb4Bf8\nEfPid+0N440z9p//AIK3fFXW/jh8SpmTUdB/Z/0/XTD8MvAdtNIt1HoGtzaCbXTZbW3lS3kvPBfw\n+TSfBr38Ep1vWvF9peXok/TD9iD/AIJ2fs1fsEeDTo3wh8Mf2l441bTraz8cfF/xPHb3vj/xfJGy\nTTQNerCsPhzw6bpEltPCmgJa6VGIbOfUzqesxS61L9114WF4PzHiKvSzXxCrUMb7Oca2X8I4Oc5c\nN5XJSUqcscpJPiHMKaXv4jGR+o0pzrQwmEdNqq/1bOvpF8HeDWU5hwB9D7Ls04ZWLw1XLOLPpEcR\n4bDUPGnjunOFSljKHC06M60PB/g7FSfNhsn4cxH+tONwtPAV+IeIXjI4nBGH4Z8MeGvBXh7R/CXg\n7w/o3hbwxoFjBpeheHPDul2Wj6Fo+m2y7Law0vStPgt7KwtIF4it7aGOJB0TNblFFfo0IRpxjCEY\nwhCMYQhCKjGMYrljGMVpGMVpGK0S0Wmp/GFevXxVeticTWq4jEYirUr4jEV6k6tevXqzlUq1q1Wp\nKU6tWrOUp1Kk5SnOcpSnKUnKTKKKKoyCiiigAooooAKK+Qf2m/29v2Q/2PbGeX4+fGzwr4W1xLeO\n5tPAVhPL4k+JOpJOhazez8CeHo9R8QxWl6QqQ6zqNlZ6BGXV7zVreASTj8f7v/gqh/wUA/bkvLzw\nx/wTC/Y51nQfBdxcTaa/7Svx3tbCDRtPQTSxy3+lW11qEPw+07VNMWNJ59Ml1z4jalNDL5A8Hi7a\n2D/I5zxzw3kmJ/s6pi6mY5w7qlkOS4ermudVJJpcrwODVSWGT3VTGyw9G1/3t0z+iPDT6LPjT4n5\nK+McFw9hODfDmi4Sx3iv4m5tgeAfDTB0Z3XtocT8RTwtHOXG3vYPhujm+Zu65MDJu5/Qb49+Inw+\n+FXhfUPG3xO8ceFPh94R0pVOpeKfGviLSPDXh+y3h/LW51bWb2ysopJfLcQxNP5szApEjvX4l/Gj\n/gvB8H5vFMnwk/YY+DPxK/bQ+Ld409ppaeD/AA/4h0jwHHdrKbZrpbkaLqPi7xHa2En+lXL6Z4Ys\n/D93Zqrw+NLa3lbUIuP8A/8ABDzxT8bPFFh8Vv8Agp7+1l8R/wBp7xlETPH8OvC/iLW9B+Gei+YV\n87SLTWbqG01hNFlBklOn+BND+HEUV26yo8y/aTN+2nwX/Z8+Bn7OXhZPBnwJ+FHgj4XeHtsH2mz8\nIeH7DTLjVZreMxRXviDVI4jqviTUgnyvquvXt/qcq4E14+M14yq+I3Ed/Y0cFwHls7WrYpUM84oq\n07zalDCQl/ZGWSnCyar1cfWpScXKlzR5X+jrAfQz8F7/ANqZlxN9LHjfDfFl2QyzXwt8CcDi4ucK\nlLEZ/iqP/EReOaeGq0/aQqZVgOEstx9CUYQx06c4Yhfg0P2Mv+CwH/BRDOofttftGWP7IfwV1j55\nv2fPgoscniDUtDvSTd6L4ig0DX5rWS3v7LykT/hYHj3xtc6XcyTfbPA9rPHc6fJ+mP7Kn/BKf9hv\n9j5dO1T4cfB/TvFHjywRP+LqfFJoPHHjw3SSrIt/pc+o2UWg+EbtdiRibwToOgO0AZLhpnluJH/R\nWivTyrgHh3LcUszxNLE5/nK5b55xFiJZrmMXFtxeGdeP1bL1F6Qjl+Hw8Yx91Jxsl8Tx79LXxj40\nyCtwNkePyXwm8NKjkl4X+DeTUOAeDa1OUJQqRzqOVVXnfF9SvFxnia/GGc51WrV19YnP2zcgooor\n7Q/mYKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACi\niigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKK\nKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAoooo\nAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigA\nooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACi\niigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKK\n4/x98QfAnwq8G+IPiF8SvF2geB/BXhexfUvEPinxPqlppOiaVZowjEl3fXk0USvNKUtrS3Vmuby8\nlt7Ozhmu5oIXipUp0ac6tWpClSpQlUqVakowp06cE5TnOcmowhGKvKUnyxV25WTb6cHg8ZmOLwuX\n5fhcTjsdjcRRwmCwWDoVcTi8Xi8RVhRw+FwuGowqVsRiMRVnClRoUoTq1asoU6cZTkr9hX5Qft5f\n8FaPgj+x7fj4R+AtNuv2hf2o9cnttG8MfAz4fyzand6Xr+puINIh8fX2kQalcaPdXcstu9h4N02z\nvvHOtGeyjg0ex0y+g8Qp8F/E7/goT+2D/wAFNfG/iP8AZv8A+CVfhbWPAfwlsbpdF+J37Z3i+11X\nw2mn2kzMtzF4VvGtbibwVFfWbi40tLa1vvi/rFpKt/pGh+EUs7/UG/SX9gz/AIJbfs8fsL2UvivT\n4rn4sfH3X4ZJfGnx78d20V54ou7++EkusW/g60uHvE8FaNqN1LPPdra3d54l1gTBPFfijWUt9PSH\n82qcTZ1xfVq4DgNQw+WQnKjjOOMbh3UwEHGcoVaXDeCqcv8AbWKi1JfXajjlNGUX7+Ik4I/tXL/A\n/wAMvo7YHCcV/Synis644r4ahmPDH0WuGM2jguLMTCtTWIwOP8a+JsJ7d+GORYii4VFwzhFW8Qcx\npVYJ4bKKdLE1j89vgt/wTA/aV/bn+Imj/tTf8FdvGmoajBayf2h8Nf2QvDOpSaV4R8I6RdvDcw2X\niuPRb57bw3BLHHCmpeG9CvLrxnraw6bJ8RfHb6laX/hw/wBDXhfwt4Y8D+HNG8IeC/DuieE/C3h3\nT7bStA8NeG9KsdF0DRdLtE8u107SdJ063trLT7K3T5Yba1gjhjXhUFbtFfUcO8KZTwzTrywca2Kz\nDGyVTNM7zCq8VnGaVlb95jcbUXPKKsvZYenyYWirqjQi3Nv8L8YvHzxB8bMXldLiSvluScI8M0ZY\nPgbwz4PwEMg8OeBcsbqf7Fwxw1hqksPQq1VK+NzjGzxefZlO08zzTEuNKMSiiivpD8VCiiigAoor\nwr47ftPfs7/sxeHD4q+P/wAYvA/wv0p4ZZrKPxNrcEOua0IRI0sPhrwvam58R+KbtVikb7D4d0rU\nb4qkrLbsEkNYYnFYXBUKuKxmJoYTDUYudbEYmtToUKUFo5Va1WcadOK6ynJLzvqerkmRZ5xNmuCy\nLhrJs14gzrMa0cPl+T5Jl2MzTNcdiJX5aGCy/AUcRisVWlb3aVClOb1snZt+61Uv9QsNJsbzU9Uv\nrTTdO0+3mu7/AFC/uYLOxsrS3jaSe6vLu4kjgtreCNGkmnmkSKONWeRwqsx/nv8AGn/Bbzx98e/E\nN/8AC7/gl/8Ash/Ef9o7xashsJfiZ400PUtB+Gvh64uC0Wn6pfaRZ3ltcRaPcMyyG/8AHviv4dw2\n3lsJoZo5HePn9P8A+CVP7ev7b19aeKv+CoP7Y+v6Z4PnuIdST9mv4F3lla6Bp7RSqttbajcWthF8\nPtN1GygWWH+0LTw7441a4huDKfGSXjXbP8FPxBo5nOeG4KyXMOLq0ZOnLH4dLL+G6M4ycZKtn+Nh\nGhWcVyy5MupY2clzKK500f1lhPogZhwVhaGdfSd8TOEfo75XVpU8TS4VzmU+L/GrNMNUXNRllvhJ\nwzXrZvlsa/LKnHE8aY7hnC0ajhUq1Hh5OqfVf7TH/Bcb9ib4FajN4K+Ges6z+1L8U5blNO0vwX8D\nIotc0CbVppHjtrS8+Iu6Xw9cJPJG0BTwYPF+sQ3TQQTaKqyNMvySIf8Aguh/wUYBW4l0b/gmz8At\nZwskSJqtt8bNQ0S53MG2FofiRHrNrFIsckbT/CHTb6NpFeKV4nFfsh+zN+wn+yR+x/p0Vt8Afgn4\nS8I6v9me1vPHNzaP4g+I+qRSqFuY9Q8ea++oeJHtLlgZH0m21C30OFncWelwREx19bVC4W4q4gTl\nxhxPPCYSdr8PcIOvlmClC8+aljc7qt5vjoTi1GrChLAUZXdqbjodC8d/APwhvQ+jp4G4biDiGhpR\n8YvpF08q434kpV4tyhj+GPC7BQXh3wriMNXiq+AxObU+LswoxlCM8X9Ype2f47/sx/8ABD79h/4A\nX0PjHx94c1T9pz4oyXEmo6j43+OcsPiDRpNWnl867vLD4d7G8Lv584W6S48WQ+KtetrvzJoPEAMj\nrX7AWdnZ6dZ2un6fa21jY2NtBZ2VlZwRW1nZ2dtEkNta2ttCiQ29tbwxpFBBEixRRKkcaKigGxRX\n1+S8PZHw7hvqmR5Vg8tou3tFhqMYVK0o3tPE13zV8VV1d6uIqVKrvrNn87+JXi/4p+MmdLP/ABR4\n94k42zKmpxwk88zOviMHllGbvLC5LlUZQyzIsFfWOAyjB4TBQfwUE9Qooor2D84CiiigAooooAKK\nKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooo\noAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiig\nAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKAC\niiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKK\nKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooo\noAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAorK13XdC8L6Lq3i\nTxNrWleHvD+hWF3quua/ruo2ek6Lo+lWMElxfanq2qahPb2WnWFlbxST3d7dzxW1vAkks0qxo7n+\nev48f8FU/jz+1/8AEfVv2Tf+CQ/gu68b+Ilb7H47/ar1rTxZeAvAelNcPbXureGTr9i+m2dkhWWO\nDxn4ktbifUzFPa/Dnwbr+qXOh61XznEPFOUcNUaLx1SrXxuLk6WW5PgKTxeb5pXvJexwGBptVKru\noqdaXJhqPPF4ivTi1J/s3g54DeIfjhmOZ0+FMJgMr4a4coQx3G/iNxXj6eQeHXAeUNy5s04t4pxk\nfqeAhyQlPC5dQ+s51mPLVp5VlmLq0qkT9EP27v8Agp3+zb+wXof2HxlqUnxB+MWqQRN4R+BPgq9t\nJ/GuqSXakaffeJJj9oh8D+HLmQxBdY1a3m1C+ieVvC2ga9dW9xZj8uvh9+wp+2t/wVW8ZaD8fP8A\ngpx4i1v4OfADTdTXWvhl+xz4Sl1Lw7qN1YbJxbXfim0e4a88INeQTG2v9d8QNe/FbVLZ9Y0/T4PA\n2jSaHOfuv9hH/gkf8I/2Vtbb45fGfXrr9pb9rHXb0+IPEHxp8fm81qLw54jui0t9ceALPXpb28i1\nJpG8ufx7r01340vyks1hcaDp97e6AP1zr5alw3nnGNSON46awWUKcauD4HwWIc8NLkmp0qvFGPpO\nP9rV1Jc/9m0eXK6MlD2n1mbq3/ecX41eF30b8Hi+GfoqRq8S+IlXD1sv4k+lLxPlEcLndP21Orh8\nbgfAvhTHxrf8Q9yqpSlVoPjPNFW49zCjXr/VZZNRjg2cJ8M/hf8ADr4MeBtA+Gvwo8GaB4C8D+Gb\nQWWh+GPDWnQ6fpdjDuLyOIolD3F3dSl7m/1C7ebUNQvJJr3ULqe8lmuG7uiiv0ilSpUKVOhQp06N\nGlCNOlRpQjTpU6cIqMKdOnBKMIQjFRjCKUYxSSVlr/FeOx+OzTHYzM8zxuLzHMcwxNfGY/MMdia2\nLx2NxmJqzrYnF4zF4ipUr4nE4irOdWvXrVJ1qtWc6lSpKcpTkUUUVocgUV85/tA/tdfsx/sq6Mut\nftBfGvwP8Nkmt5LrT9H1nVlufF2tQRl1kk8PeCtKW/8AFXiBEePZI+j6PdpE7IszoWBP4y+Kf+C1\nnxu/aR1/Uvh1/wAEuv2LviF8c9VinfTn+LvxJ0i70b4daPdbnRbu70ux1bTtO07T7mNopbDUfHnx\nA8HSLK0Ud7oDlmgf5XO+NOGsgqrCY7MY1cynZUcmy6lWzLOcRNr3IUsswMa+KTqack6tOFHW8qqS\nkz968L/oyeN3i9l9XiDhXgvEYHgvCJ1M08SeMMbgOC/DTKcNCbhXxOO434oxGV5FJYWzlXwuCxmK\nzHlTVLBVKloP+h68vLPT7O61DULq3sbGxt57y9vbyeK2s7Oztonmubq6uZnSG3t7eGN5Z55nWKKJ\nHkkcIrPX5JftL/8ABbf9g39ni5u/DHh7x5e/tC/EeOdLC08DfAq2h8WW0mqTyi2tbS88eG6tvBSM\n91m2urTRtZ1vxBZzLJC/h+S5MVu/x7Y/8Eof28/21LiDxD/wU9/bc8Rx+E554L0fs7/AeexsfDts\nsbxPaxalcxaNpnw+07VdNEb2/wBrt/BXjXULhJBdf8Jmbs3LS/rV+zH/AME+P2Of2P7W2PwL+CHh\nXQvEUMQjn+IWt27+K/iTeMVZZ3bxt4iOoazp8N2WZ59M0S403RAxVYdLjijiQeH/AGn4g8Q6ZRk2\nE4Py+e2Z8TcuPzyVOTaVTDcPYDELD4WrG1+XMsxbWnPhne7/AFJcD/RA8HlKfiJ4lZ/9I7jDC8rl\nwR4IxrcJeF9HGU23PC514wcV5VLOM9wNVWi6vBPB0YvR0c9Tcj8ij8W/+C6H/BQSUxfCD4X+HP8A\ngnp8ENWKGLxp8Qzcp8U73SJSbgNbza3oV14yknuLQ28ulah4Z+HXg2wuJmMJ8ZC0e4nj93+Bn/BB\nn9mLwx4iPxN/au8e/En9s74t31xDf614i+Keva1Y+E73UYQhjubjw7Brep+IPEDhgyXEXjXxr4i0\nq9t/Kjk0aJVmWT9y6K3wvh1k9SvTx3E2LzDjLMacvaQrcQ1418Bh6l7yeByOjCllOEg3rFfValWN\n3au22zy86+mR4j4HKsZwr4H5Dwj9G7g7F0XhMTlvg9ltbKuLs3wcWlSXFPinmWKzHxB4gxCguWtN\n8QYTA4hN8+WxUaaXLeC/A3gj4b+G9O8G/Dvwf4Z8DeFNIjMOleGPCGg6V4d8P6dETlksdH0e0s7G\n1Dn5n8mBS7fM5ZvmrqaKK+8p06dKEKVKEKdOnGMIU6cVCEIRVoxhCNoxjFaRitEtEj+TsVisVjsT\niMbjcTXxmLxVapiMVi8VWqV8TicRVnKdWviK9Wc6tatVnKU6lWpOVSc5SlOUpOUmUUUVZgFFFFAB\nRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFF\nFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUU\nUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQ\nAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAB\nRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFF\n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\nTyLJ6ag6aoZBw3go4XIeH8Moe7HC5LluDwyjZKjZBRRRXuH5YFFFFABRRRQAUUUUAFFFFABRRRQA\nUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABR\nRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFF\nFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUU\nAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQA\nUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABR\nRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAGVruu6H4X0PWPE\n3iXWNN0Dw/4f02+1nXdd1m+ttN0fR9H022mu9R1TVNRvJYbWwsLC1t5rm8vLmWO3t7eOSaaRY1Z6\n/mT+KHxk+P8A/wAFxPjlrn7N37LfiDxP8I/2Afhrqy2Xxy+Odtb3+mah8YJQzF9CsreVrd9QsdVg\nO/wp4AvP9GFjJD8QvifaiQeFvCNv8p/8FX/+CjOo/tEftGQ/scePb34t/svfsh+EPEaN8WdVu/hx\n4msPi98Yo9FuJrlb+08F63b6RdweF7y6tEi+Hmja79m0i4u5bX4heNEuJ7XRPC9j9d/B7/gtr+w/\n8Dfhn4W/Z1/YR/Y1/aT8fxeFbOHTvC3gzTPC3hfR08Q6ldCGW51PU9W0PxP8Q/F2r+INbvGubjW9\nZvfCFxqN/qYllRZ7dopB+A8QcfcN8UZxjOHsbxFRybhXK8Q8PnCp1MSs64qxlKT58pwGGwlOpjsP\nkkHLlx2LhCOIzOTWDwP+ze3xD/118H/okeNXgV4ccP8AjBwz4NZj4k+PnHWTwzjw4q4zCZNLwy8A\n+G8ZFxw3iFxTnfEGKwnC+b+J2Kp1HX4W4fxOMrZVwRSUeI+KebOYZflUP3Y/ZW/Y+/Z7/Yv+HFv8\nNfgD4DsfDGnulq/iLxJcrDf+OPHGpWyyhdZ8beKGtob3XL4NNcPa25EGjaRHPNY+HtK03TNliPo+\n/wBQsNKsrrUtUvrTTdPsoXuLy/v7mC0srS3QEvPdXVxJHDbwoBl5ZZFRRncwwWP87o+OH/BwH+1u\nwg+E/wCzx8K/2HPBGo+X9l8XfFYW91470+G5GXXU9P8AGUHifWy8EQXabb4JadcwSSSq87XCxeTb\n0/8A4IZfE/4639p4g/4KC/8ABQb46/HuTzoLyTwH4P1G90fwjplzGfMez0rUPGd14rsINLmnCtLF\noHgLwtNsM8lu9vfzC9T7HA8VYp4Sjl/A/h7nFbA4aEaWFrZjRw3CWR06Cc1CeHWPi8fVpX99qhlc\npSve7knJfzXxJ4CZBTz7NuLfpR/TB8OMu4pzfFVMdn2XcHZjnX0hPFHG5nJyeJoZtV4Tqf6pYLHc\nsfYxlmnHtOnRcY0ZRhShQhP76+Of/BXr/gnV+z/9stPE/wC0l4S8Y6/ab0Hhf4Ri7+KGqy3MePNs\npL/wdHqXhrSLuLkSxeINf0sRyK9u7i6Uw1+edx/wV5/bd/a9mn0P/gmb+wJ4x1vQ7mea1t/jn8dY\n4rHwfAquYbj9xaa7oPgLStVsgrTRQ3nxU16aR9qv4ZuDE9tL+l3wK/4JWf8ABPr9nVrK88Afsz+A\n9T1+xaOWHxd8RbS5+JnieO9jJK6jY3/jufXYdBvewk8OWulRquVjiQFs/oFDDDbQxW9vFHBBBHHD\nBBDGsUMMMShIoookASOONFCxxoAqKAqgAc9KyfxEzm6znifLeHMJLSeC4SwM8Rj50223F57nKqew\nqJaOphcrg7uPJNKM2/Gj4jfQ58NLz8OfA3jTxpz+haWE4m+kJxTQyfhKhjKbklXp+FXhpWwksywd\nRqM44LP+PcbScZTWIw85ShGn/Odb/wDBI79un9sC5ttb/wCCl37e/irUfDly6Xdx8C/gQ0Vj4Ytx\nJOJo7d7mfRNC8B6dqFjBm0luIfhv4lneQqY/EtzHC1xP+on7Nv8AwTK/YZ/ZQFje/CX4A+Em8WWS\nW5X4i+OIJPHnxAN5ApVtRsfEPir+0n8M3N0fnuoPCEGhabI2wLYIkca1940V6eU8BcLZPiPr8Mu/\ntHNXyuedZ3Xr5xm85xvapHG5hOvOhJ9sL7GGrtBa3+G4/wDpZ+PXiFksuEsRxn/qbwClOnh/DTwv\nyrLPDjw9oYWd/wDZKnDXB+HyrDZtRWlp59LM8U2oOeJlKEZBRRRX2J/OAUUUUAFFFFABRRRQAUUU\nUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQ\nAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAB\nRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFF\nFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUU\nUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQ\nAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAB\nRRRQAUUUUAct4m8DeB/Gy2KeM/BvhbxaumSSzaavibw7o+urp804iE8tiuq2V2LSSYQxCV4NjyCO\nIOzbFresNPsNKtIrDS7G006yg8zyLKwtoLS0h8yWSaTyra3SOKPzJZJJZNijdK8kjbnZ3NuioVKl\nGcqsacFUmkp1FCKnNJJJSmlzSSUVZNuySXS76Z43G1MLRwNTF4mpg8NKc8PhJ4itLC0J1JSlUnRw\n8qjpUpVJOUpyhFSlKUnJyk25FFFFWcwUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRR\nRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFF\nABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUA\nFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAU\nUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRR\nRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFF\nABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUA\nFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAU\nUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRR\nRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFF\nABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUA\nFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAU\nUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRR\nRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFF\nABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUA\nFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAU\nUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRR\nRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFF\nABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUA\nFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAU\nUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRR\nRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFF\nABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUA\nFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAU\nUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRR\nRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFF\nABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUA\nFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAU\nUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRR\nRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFF\nABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUA\nFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAU\nUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRR\nRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFF\nABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUA\nFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAU\nUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRR\nRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFF\nABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUA\nFFFFAH//2Q\u003d\u003d\n'
            };
            mydata = JSON.stringify(mydata);
            $('#pickupdone_post_data').val(mydata);

            var sign = key + api_id+'data' + mydata + 'methodpickupdone' + key;

            var testsign = CryptoJS.MD5(sign);
            sign = testsign.toString(CryptoJS.enc.Hex).toUpperCase();
            $('#pickupdone_post_sign').val(sign);

            $('#tscanner-pickupdoneform').submit();
        });


        // test load on board logic
        $('#onboard_form_submit').click(function(e){
            e.preventDefault();

            // create sign string
            var mydata = {
                'userid':courier_id,
                'items':[
                    {'bc':'Aa','time':'2016-05-18 12:12:12'},
                    {'bc':'ac','time':'2016-05-18 12:12:22'}
                ]
            };
            mydata = JSON.stringify(mydata);
            $('#onboard_post_data').val(mydata);

            var sign = key + api_id+'data' + mydata + 'methodonboard' + key;

            var testsign = CryptoJS.MD5(sign);
            sign = testsign.toString(CryptoJS.enc.Hex).toUpperCase();
            $('#onboard_post_sign').val(sign);

            $('#tscanner-onboardform').submit();
        });


        // test delivery logic
        $('#delivery_form_submit').click(function(e){
            e.preventDefault();

            // create sign string
            var mydata = {
                'userid':courier_id,
                'items':[
                    {'bc':'PE6677234234AU',
                        'printname':'michael',
                        'signature':"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAASwAAAB4CAYAAABIFc8gAAAgAElEQVR4Xu2dB9AES1HHQTFiQkEk38OAKKighSCGe2CpYELBArXAA4wg+kCrMHsCBVqmpyKoGA5FDEiJIqho6T0zmJ45UOgZAAtRzICg+P89tqVfM7s7e7f33be3PVVdd7s7oadn57/dPb2zN7xBppRASiAlMBEJ3HAifCabKYGUQErgBglYeROkBFICk5FAAtZkhioZTQmkBBKw8h5ICaQEJiOBBKzJDFUymhJICSRg5T2QEkgJTEYCCViTGaqzYvSD1JsvFn2A6K1Ed3a9u1b/31X0MtFrmvPvEs4tdPx60c1ELxW9veimov8R/Wdz/Nb6vZHotaJ3bOqhzD+LfkO0Ff2UaHdWkj3zziRgnfkAX7LuLcXP14r4vSwJwHuW6DmiaxxTC/3/l4YuC6+z5yMBa/a3wNEEwIT/QBHa1J1E9xDd6mitjVPx/6qaV4tu7Kp7pf4/QfT0BK9xhHxILQlYh0gvy5Yk8CCd/AqRN/PaJIWJ9muiv2nAANMPgMOEe7mI66SSSYj2c1vRP4i8ScjxzUVmEgI4/y16hybf2+05bDuV+xQRJmumE0kgAetEgj/DZgGVbxWtKvr2r8qzEa1FAM9Fp/upwYeL7iq65YDG/0N5v1e0FeH/ynTBEkjAumCBn2lzV6lfDxTdvad/L9F1zCv8RacAqhJ7C50EwB4iei/RGxr6N/3i/PfmoS+P1vYU0aPPdEwvZbcSsC7lsFx6pjDbPku0FPG/lFjle5po1xCm1GUBqVoBozVuRJ/cUYD+palYK9ED8yVgHSjAmRXHP0U4Qp8m9XXKsz4j2Xyp+vJFotu09Om/dB6/XTrmjzzoCVhHFvCZVI+m8QMiTKeu9CJdfKxoeyb9jt2g/18mYvXzbVv6iLkLYKdz/gg3QQLWEYR6ZlUu1J+fFJVMP5znTEwmKbQ7s763dQeZ0F+Aq5SQC+B2rsB9sm",
                        'time':'2016-03-29 12:12:12'},
                    {'bc':'PE6677234234AU',
                        'printname':"frank",
                        'signature':"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAASwAAAB4CAYAAABIFc8gAAAgAElEQVR4Xu2dB9AES1HHQTFiQkEk38OAKKighSCGe2CpYELBArXAA4wg+kCrMHsCBVqmpyKoGA5FDEiJIqho6T0zmJ45UOgZAAtRzICg+P89tqVfM7s7e7f33be3PVVdd7s7oadn57/dPb2zN7xBppRASiAlMBEJ3HAifCabKYGUQErgBglYeROkBFICk5FAAtZkhioZTQmkBBKw8h5ICaQEJiOBBKzJDFUymhJICSRg5T2QEkgJTEYCCViTGaqzYvSD1JsvFn2A6K1Ed3a9u1b/31X0MtFrmvPvEs4tdPx60c1ELxW9veimov8R/Wdz/Nb6vZHotaJ3bOqhzD+LfkO0Ff2UaHdWkj3zziRgnfkAX7LuLcXP14r4vSwJwHuW6DmiaxxTC/3/l4YuC6+z5yMBa/a3wNEEwIT/QBHa1J1E9xDd6mitjVPx/6qaV4tu7Kp7pf4/QfT0BK9xhHxILQlYh0gvy5Yk8CCd/AqRN/PaJIWJ9muiv2nAANMPgMOEe7mI66SSSYj2c1vRP4i8ScjxzUVmEgI4/y16hybf2+05bDuV+xQRJmumE0kgAetEgj/DZgGVbxWtKvr2r8qzEa1FAM9Fp/upwYeL7iq65YDG/0N5v1e0FeH/ynTBEkjAumCBn2lzV6lfDxTdvad/L9F1zCv8RacAqhJ7C50EwB4iei/RGxr6N/3i/PfmoS+P1vYU0aPPdEwvZbcSsC7lsFx6pjDbPku0FPG/lFjle5po1xCm1GUBqVoBozVuRJ/cUYD+palYK9ED8yVgHSjAmRXHP0U4Qp8m9XXKsz4j2Xyp+vJFotu09Om/dB6/XTrmjzzoCVhHFvCZVI+m8QMiTKeu9CJdfKxoeyb9jt2g/18mYvXzbVv6iLkLYKdz/gg3QQLWEYR6ZlUu1J+fFJVMP5znTEwmKbQ7s763dQeZ0F+Aq5SQC+B2rsB9sm",
                        'time':'2016-03-29 12:12:22'}
                ]
            };
            mydata = JSON.stringify(mydata);
            $('#delivery_post_data').val(mydata);

            var sign = key + api_id+'data' + mydata + 'methoddelivery' + key;

            var testsign = CryptoJS.MD5(sign);
            sign = testsign.toString(CryptoJS.enc.Hex).toUpperCase();
            $('#delivery_post_sign').val(sign);

            $('#tscanner-deliveryform').submit();
        });

        // test cancel loaded parcel logic
        $('#onboardcancel_form_submit').click(function(e){
            e.preventDefault();

            // create sign string
            var mydata = {
                'userid':courier_id,
                'bc':'PE6677234234AU',
                'tid' : '1223'
            };
            mydata = JSON.stringify(mydata);
            $('#onboardcancel_post_data').val(mydata);

            var sign = key + api_id+'data' + mydata + 'methodonboardcancel' + key;

            var testsign = CryptoJS.MD5(sign);
            sign = testsign.toString(CryptoJS.enc.Hex).toUpperCase();
            $('#onboardcancel_post_sign').val(sign);

            $('#tscanner-onboardcancelform').submit();
        });

        // test missed your card logic
        $('#missedycard_form_submit').click(function(e){
            e.preventDefault();

            // create sign string
            var mydata = {
                'userid':courier_id,
                'items':[
                    {'bc':'PE234234234AU','time':'2016-03-29 12:12:12'},
                    {'bc':'PE6677234234AU','time':'2016-03-29 12:12:22'}
                ]
            };
            mydata = JSON.stringify(mydata);
            $('#missedycard_post_data').val(mydata);

            var sign = key + api_id+'data' + mydata + 'methodmissedycard' + key;

            var testsign = CryptoJS.MD5(sign);
            sign = testsign.toString(CryptoJS.enc.Hex).toUpperCase();
            $('#missedycard_post_sign').val(sign);


            $('#tscanner-missedycardform').submit();
        });


        // test undo missed your card logic
        $('#umissedycard_form_submit').click(function(e){
            e.preventDefault();

            // create sign string
            var mydata = {
                'userid':courier_id,
                'items':[
                    {'bc':'PE234234234AU','tid':'12344555'},
                    {'bc':'PE6677234234AU','tid':'3345555'}
                ]
            };
            mydata = JSON.stringify(mydata);
            $('#umissedycard_post_data').val(mydata);

            var sign = key + api_id+'data' + mydata + 'methodumissedycard' + key;

            var testsign = CryptoJS.MD5(sign);
            sign = testsign.toString(CryptoJS.enc.Hex).toUpperCase();
            $('#umissedycard_post_sign').val(sign);


            $('#tscanner-umissedycardform').submit();
        });


        // test consume goods logic
        $('#consumegoods_form_submit').click(function(e){
            e.preventDefault();

            // create sign string
            var mydata = {'userid':courier_id};
            mydata = JSON.stringify(mydata);
            $('#consumegoods_post_data').val(mydata);

            var sign = key + api_id+'data' + mydata + 'methodconsumegoods' + key;

            var testsign = CryptoJS.MD5(sign);
            sign = testsign.toString(CryptoJS.enc.Hex).toUpperCase();
            $('#consumegoods_post_sign').val(sign);


            $('#tscanner-consumegoodsform').submit();
        });

        // test logout logic
        $('#logout_form_submit').click(function(e){
            e.preventDefault();

            // create sign string
            var mydata = {'userid':courier_id};
            mydata = JSON.stringify(mydata);
            $('#logout_post_data').val(mydata);

            var sign = key + api_id+'data' + mydata + 'methodlogout' + key;

            var testsign = CryptoJS.MD5(sign);
            sign = testsign.toString(CryptoJS.enc.Hex).toUpperCase();
            $('#logout_post_sign').val(sign);


            $('#tscanner-logoutform').submit();
        });

    });

</script>

</body>
</html>
