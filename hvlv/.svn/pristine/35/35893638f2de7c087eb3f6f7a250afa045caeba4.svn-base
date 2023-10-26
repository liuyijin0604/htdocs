<?php
$displayResult = 'none';
$resultCount = 0;

if ($status == 'success') {
    $displayResult = 'block';
    if (!empty($model)) {
        $resultCount = count($model);
    }
}
?>
<!DOCTYPE html>

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
</head>

<body>
    <br/>
    <div class="container-sm">
        <div class="card">
            <h3 class="card-header">Container Availability</h3>
            <div class="card-body">
                <form method="POST" action="<?= $this->createUrl('customerService/containerAvailability'); ?>">
                    <div class="mb-3">
                        <label for="container_no" class="form-label">Container Number</label>
                        <input type="text" class="form-control" id="container_no" name="container_no">
                    </div>
                    <button type="submit" class="btn btn-primary">Find</button>
                </form>
            </div>
        </div>
        <br/>
        <div class="card" style="display: <?php echo $displayResult; ?>;">
            <h5 class="card-header"><?php echo $resultCount . " records found."; ?></h5>
            <div class="card-body">
                <table class="table">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Container Number</th>
                            <th scope="col">Vessel</th>
                            <th scope="col">Voyage</th>
                            <th scope="col">Estimate Arrival</th>
                            <th scope="col">Unpacking Date</th>
                            <th scope="col">Available Date</th>
                            <th scope="col">Storage Date</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        <tr>
                            <?php
                                if (!empty($model)) {
                                    foreach($model as $m) {
                                        echo "<tr><td>".$m->container_no."</td><td>".$m->airline."</td><td>".$m->flight."</td><td>".$m->eta."</td><td>".$m->mdata['ContainerUnloadDate']."</td><td>".$m->mdata['available_date']."</td><td>".$m->mdata['input_storage_date']."</td></tr>";
                                    }
                                }
                            ?>
                        </tr>
                </table>
            </div>
        </div>
    </div>
    <script type="text/javascript">
    </script>
</body>