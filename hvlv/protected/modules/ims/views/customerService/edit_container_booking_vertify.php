<?php
$displayErrorMsg = 'none';
if (!empty($status) && $status == 'failed') {
    $displayErrorMsg = 'block';
} else {
    $container_no = '';
    $house_bl = '';
}
?>
<!Doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- reCaptcha  -->
    <!-- <script src="https://www.google.com/recaptcha/api.js"></script> -->
    <!-- Custom CSS -->
    <style>
        body {
            background: #ece7dc;
        }
    </style>
    <title>TLA Container Pickup Booking</title>
</head>

<body>
    <br />
    <div class="container-sm">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="<?= $this->createUrl('customerService/editContainerPickupBooking'); ?>">
                    <div class="mb-3">
                        <label for="booking_number" class="form-label">Booking Number</label>
                        <input type="text" class="form-control" id="booking_number" name="booking_number" value="<?php echo @$booking_number; ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Send The Link For Adjustment To Booking Email</button>
                </form>
            </div>
        </div>
        <div class="card" style="display: <?php echo $displayErrorMsg; ?>;">
            <p>&nbsp;&nbsp;&nbsp;Msg: <?php if (!empty($msg)) echo $msg; ?></p>
        </div>
    </div>
</body>

</html>