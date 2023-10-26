<?php
$hideDisplayPrice = 'none';
$totalWeight = 0;
$totalDimension = 0;
$totalQuantity = 0;
$baseTLD = 0;
$baseToll = 0;
$oversizeTLD = 0;
$oversizeToll = 0;
$tailgateTLD = 0;
if (!empty($model)) {
    $hideDisplayPrice = 'block';
    $totalWeight = $model['totalWeight'];
    $totalDimension = $model['totalDimension'];
    $totalQuantity = $model['totalQuantity'];
    $baseTLD = $model['result']['base_TLD'];
    $baseToll = $model['result']['base_Toll'];
    $oversizeTLD = $model['result']['oversize_TLD'];
    $oversizeToll = $model['result']['oversize_Toll'];
    $tailgateTLD = $model['result']['tailgate_TLD'];
}
?>
<!doctype html>
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
        .pe-label {
            font-weight: 600;
        }

        .text-input-row {
            margin-top: 8px;
        }

        #displayPrice {
            display: none;
        }
    </style>
    <title>TLA Postage Calculator</title>
</head>

<body>
    <div class="container-lg">
        <br />
        <form class="row g-3 needs-validation" id="price-enquiry-form" method="POST">
            <div class="container" id="items">
                <h2 >Customer Requirements Form</h2>
                <div class="item" id="item">                    
                    <h3>Question 1</h3>
                    <h5>Arrival information</h5>
                    <label class="pe-label" >Do you need to provide trunk services:</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="category_1" id="category_1_0" value="yes" checked>
                        <label class="form-check-label pe-label" for="category_1_0">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="category_1" id="category_1_1" value="no">
                        <label class="form-check-label pe-label" for="category_1_1">No</label>
                    </div>                   
                </div>
            </div>
            
            <div class="col-12">                
                <button type="button" class="btn btn-danger" onclick="previousQustion()">Previous</button>
                <button type="button" class="btn btn-success" onclick="nextQustion()">Next</button>
            </div>
            <br><br><br>
            
            <!-- <div class="g-recaptcha" data-sitekey="6LfyO2saAAAAAJ6CdVI_q_Nt8pFZkcVqbO7aGZHJ" style="float:left"></div> -->
            <div class="col-12">
                <button type="submit" class="btn btn-primary" onclick="submitForm()">Save</button>
            </div>
        </form>
    </div>

    <div class="container-md" style="display: <?php echo $hideDisplayPrice; ?>;">
        <h1 style="text-align: center;">TLA Price Enquiry<?php if (!empty($model['peNumber'])) echo '(' . $model['peNumber'] . ')'; ?></h1>
        <h5>Total Weight: <?php echo number_format($totalWeight, 3, '.', ''); ?> Kg</h5>
        <h5>Total Dimension: <?php echo number_format($totalDimension, 3, '.', ''); ?> cbm</h5>
        <h5>Total Quantity: <?php echo $totalQuantity; ?> pcs</h5>
        <br />
        <div class="row">
            <div class="col"></div>
            <div class="col">
                <h6>TLD Cargo Delivery</h6>
            </div>
            <div class="col">
                <h6>Express Delivery(Toll/Allied/TNT)</h6>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <h6>Delivery Fee</h6>
            </div>
            <div class="col"></div>
            <div class="col"></div>
        </div>
        <div class="row">
            <div class="col">-Base delivery fee</div>
            <div class="col"><?php if ($baseTLD == 0) {
                                    echo 'Out of service area';
                                } else {
                                    echo '$' . number_format($baseTLD, 2, '.', '');
                                } ?></div>
            <div class="col"><?php echo '$' . number_format($baseToll, 2, '.', ''); ?></div>
        </div>
        <div class="row">
            <div class="col">-Oversize fee</div>
            <div class="col"><?php if ($baseTLD == 0) {
                                    echo 'Out of service area';
                                } else {
                                    echo '$' . number_format($oversizeTLD, 2, '.', '');
                                } ?></div>
            <div class="col"><?php echo '$' . number_format($oversizeToll, 2, '.', ''); ?></div>
        </div>
        <div class="row">
            <div class="col">
                <h6>Subtotal</h6>
            </div>
            <div class="col"><?php if ($baseTLD == 0) {
                                    echo 'Out of service area';
                                } else {
                                    echo '$' . number_format($baseTLD + $oversizeTLD, 2, '.', '');
                                } ?></div>
            <div class="col"><?php echo '$' . number_format($baseToll + $oversizeToll, 2, '.', ''); ?></div>
        </div>
        <div class="row">
            <div class="col">
                <h6>TLD Optional Fee</h6>
            </div>
            <div class="col"></div>
            <div class="col"></div>
        </div>
    <div class="row">
            <div class="col">-Unloading Fee</div>
            <div class="col"><?php if ($baseTLD == 0) {
                                    echo 'Out of service area';
                                } else {
                                    echo '$' . number_format($tailgateTLD, 2, '.', '');
                                } ?></div>
            <div class="col">$0.00</div>
        </div>
        <div class="row">
            <div class="col">
                <h6>Total</h6>
            </div>
            <div class="col"><?php if ($baseTLD == 0) {
                                    echo 'Out of service area';
                                } else {
                                    echo '$' . number_format($baseTLD + $oversizeTLD + $tailgateTLD, 2, '.', '');
                                } ?></div>
            <div class="col"><?php echo '$' . number_format($baseToll + $oversizeToll, 2, '.', '') ?></div>
        </div>
        <div class="row">
            <p>Price doesn't include GST.</p>
        </div>
        <div class="col-12">
            <button class="btn btn-primary" type="button" id="sendEmail" onclick="sendEmail()">Email Me</button>
        </div>
    </div>



    <script type="text/javascript">
        var numCountItem = 1;
        var strHtmlItem = "";

        function nextQustion() {
            numCountItem++;
            $('#item').remove();
            this.showItem();            
            
            $('#items').append(strHtmlItem);
        }

        function previousQustion() {
            numCountItem--;
            $('#item').remove();
            this.showItem();            
            
            $('#items').append(strHtmlItem);
        }

        function showItem(){
            switch(numCountItem)
            {
                case 1:
                  strHtmlItem = '<div class="item" id="item"><h3>Question 1</h3><h5>Arrival information</h5><label class="pe-label" >Do you need to provide trunk services:</label><div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="category_1" id="category_1_0" value="yes" checked><label class="form-check-label pe-label" for="category_1_0">Yes</label></div><div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="category_1" id="category_1_1" value="no"><label class="form-check-label pe-label" for="category_1_1">No</label></div></div>'
                  break;
                case 2:
                    strHtmlItem = '<div class="item" id="item"><h3>Question 2</h3><h5>Type of services</h5><label class="pe-label">Import</label><div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="category_2" id="category_2_0" value="import" checked><label class="form-check-label pe-label" for="category_2_0">Carton</label></div><div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="category_2" id="Category_2_1" value="3pl"><label class="form-check-label pe-label" for="category_2_1">3PL</label></div><div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="category_2" id="category_2_2" value="delivery"><label class="form-check-label pe-label" for="category_2_2">Delivery</label></div><div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="category_2" id="category_2_3" value="others"><label class="form-check-label pe-label" for="category_2_3">Others</label></div></div></div>';
                    break;


            }
        }

        // function removeItem() {
        //     $('#item').remove();
        //     if (numCountItem > 1) {
        //         numCountItem--;
        //     }
        // }

        function submitForm() {
            //alert("Submit");
            document.getElementById('itemCount').value = numCountItem;

        }

        (function() {
            'use strict'
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation')
            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()

        function sendEmail() {
            peNumber = '<?php if(!empty($model['peNumber'])) { echo $model['peNumber']; } else { echo 0; } ?>';
            totalCBM = '<?php echo $totalDimension; ?>';
            $.ajax({
                type: "POST",
                url: "https://imshk.toplogistics.com.au/priceEnquiry/sendPriceEnquiryEmail?peNumber=" + peNumber + "&totalCBM=" + totalCBM,
                success: function() {
                    alert('Email successfully sent.');
                }
            });
        }
    </script>
</body>

</html>