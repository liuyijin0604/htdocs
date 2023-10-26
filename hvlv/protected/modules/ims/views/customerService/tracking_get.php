<html>

<head>
<script src="https://www.google.com/recaptcha/api.js?render=<?=RecaptchaAPI::RECAPTCHA_V3_SITE_KEY?>"></script>
  <style>
    .background {
      background-color: rgb(243, 247, 251);
    }

    .btnGo {
      width: 100px;
    }

    .inputTracking {
      width: 400px;
    }

    .divTracking {
      margin-left: auto;
      margin-right: auto;
      width: 510px;
    }
  </style>
</head>

<body class="background">
  <div class="divTracking">
    <h1 style="font-size: 40;">TRACK YOUR  SHIPMENT</h1>
    <span><form method="post"><input name="tracking_number" class="inputTracking" type="text" placeholder="Enter tracking No. eg TCNxxx"> <input class="btnGo" value="go" type="submit"></form></span>
  </div>

</body>

</html>