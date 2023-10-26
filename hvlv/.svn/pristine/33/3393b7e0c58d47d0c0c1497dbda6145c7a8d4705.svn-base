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
                <div class="item" id="item">
                    <p style="color: red;">Notice: For multiple items with the same dimensions and weight, please enter data for a single item and quantity.
                        For multiple items with different dimensions and weight, please click 'Add Item' button.
                    </p>
                    <h6>Item 1</h6>
                    <label class="pe-label">Category:</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="category_1" id="category_1_0" value="carton" checked>
                        <label class="form-check-label pe-label" for="category_1_0">Carton</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="category_1" id="category_1_1" value="pallet">
                        <label class="form-check-label pe-label" for="category_1_1">Pallet</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="category_1" id="category_1_2" value="crate">
                        <label class="form-check-label pe-label" for="category_1_2">Crate</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="category_1" id="category_1_3" value="others">
                        <label class="form-check-label pe-label" for="category_1_3">Others</label>
                    </div>
                    <div class="row text-input-row">
                        <div class="col-md-2">
                            <label for="length_1" class="form-label pe-label">Length</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="length_1" name="length_1" min="1" required>
                                <div class="input-group-text">cm</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="width_1" class="form-label pe-label">Width</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="width_1" name="width_1" min="1" required>
                                <div class="input-group-text">cm</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="height_1" class="form-label pe-label">Height</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="height_1" name="height_1" min="1" required>
                                <div class="input-group-text">cm</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="weight_1" class="form-label pe-label">Weight</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="weight_1" name="weight_1" min="0" required>
                                <div class="input-group-text">kg</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="quantity_1" class="form-label pe-label">x Quantity</label>
                            <div class="input-group">
                                <!-- <div class="input-group-text">*</div> -->
                                <input type="number" class="form-control" id="quantity_1" name="quantity_1" placeholder="0" min="1" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <button type="button" class="btn btn-success" onclick="addItem()">Add Item</button>
                <button type="button" class="btn btn-danger" onclick="removeItem()">Remove Item</button>
            </div>
            <div class="container">
                <h6>Depot From</h6>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="depot" id="depot_0" value="Sydney" checked>
                    <label class="form-check-label pe-label" for="depot_0">Sydney</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="depot" id="depot_1" value="Melbourne">
                    <label class="form-check-label pe-label" for="depot_1">Melbourne</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="depot" id="depot_2" value="Brisbane">
                    <label class="form-check-label pe-label" for="depot_2">Brisbane</label>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label for="address" class="form-label pe-label text-input-row">Delivery Address</label>
                        <input type="text" class="form-control" id="address" name="address" placeholder="1234 Main St" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="suburb" class="form-label pe-label text-input-row">Delivery Suburb</label>
                        <input type="text" class="form-control" id="suburb" name="suburb" placeholder="Bondi" required>
                    </div>
                    <div class="col-md-6">
                        <label for="postcode" class="form-label pe-label text-input-row">Delivery Postcode</label>
                        <input type="text" class="form-control" id="postcode" name="postcode" placeholder="2026" required>
                    </div>
                </div>
                <h6 class="text-input-row">Does receiver have forklift?</h6>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="forklift" id="forklift_0" value="No" checked>
                    <label class="form-check-label pe-label" for="forklift_0">No</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="forklift" id="forklift_1" value="Yes">
                    <label class="form-check-label pe-label" for="forklift_1">Yes</label>
                </div>
                <h6 class="text-input-row">Who pays for surcharges?</h6>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="paid_by" id="paid_by_0" value="Shipper" checked>
                    <label class="form-check-label pe-label" for="paid_by_0">Shipper</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="paid_by" id="paid_by_1" value="Receiver">
                    <label class="form-check-label pe-label" for="paid_by_1">Receiver</label>
                </div>
            </div>

            <div class="container" style="display: block;">
                <div class="row">
                    <div class="col-md-4">
                        <label for="name" class="form-label pe-label text-input-row">Enquiry From</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col-md-4">
                        <label for="tel" class="form-label pe-label text-input-row">Contact Tel</label>
                        <input type="text" class="form-control" id="tel" name="tel" required>
                    </div>
                    <div class="col-md-4">
                        <label for="email" class="form-label pe-label text-input-row">Contact Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>
                <div class="col-12">
                    <label for="note" class="form-label pe-label text-input-row">Notes</label>
                    <input type="text" class="form-control" id="note" name="note">
                </div>
            </div>
            <div class="container" style="display: none">
                <div class="row">
                    <label for="itemCount" class="form-label pe-label text-input-row">Number of Items</label>
                    <input type="number" class="form-control" id="itemCount" name="itemCount">
                </div>
            </div>
            <!-- <div class="g-recaptcha" data-sitekey="6LfyO2saAAAAAJ6CdVI_q_Nt8pFZkcVqbO7aGZHJ" style="float:left"></div> -->
            <div class="col-12">
                <button type="submit" class="btn btn-primary" onclick="submitForm()">Show me the price</button>
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

        function addItem() {
            numCountItem++;
            var strHtmlItem = '<div class="item" id="item_' + numCountItem + '"><h6>Item ' + numCountItem + '</h6><label class="pe-label">Category:</label><div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="category_' + numCountItem + '" id="category_' + numCountItem + '_0" value="carton" checked><label class="form-check-label pe-label" for="category_' + numCountItem + '_0">Carton</label></div><div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="category_' + numCountItem + '" id="Category_' + numCountItem + '_1" value="pallet"><label class="form-check-label pe-label" for="category_' + numCountItem + '_1">Pallet</label></div><div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="category_' + numCountItem + '" id="category_' + numCountItem + '_2" value="crate"><label class="form-check-label pe-label" for="category_' + numCountItem + '_2">Crate</label></div><div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="category_' + numCountItem + '" id="category_' + numCountItem + '_3" value="others"><label class="form-check-label pe-label" for="category_' + numCountItem + '_3">Others</label></div><div class="row text-input-row"><div class="col-md-2"><label for="length_' + numCountItem + '" class="form-label pe-label">Length</label><div class="input-group"><input type="number" class="form-control" id="length_' + numCountItem + '" name="length_' + numCountItem + '" min="1" required><div class="input-group-text">cm</div></div></div><div class="col-md-2"><label for="width_' + numCountItem + '" class="form-label pe-label">Width</label><div class="input-group"><input type="number" class="form-control" id="width_' + numCountItem + '" name="width_' + numCountItem + '" min="1" required><div class="input-group-text">cm</div></div></div><div class="col-md-2"><label for="height_' + numCountItem + '" class="form-label pe-label">Height</label><div class="input-group"><input type="number" class="form-control" id="height_' + numCountItem + '" name="height_' + numCountItem + '" min="1" required><div class="input-group-text">cm</div></div></div><div class="col-md-2"><label for="weight_' + numCountItem + '" class="form-label pe-label">Weight</label><div class="input-group"><input type="text" class="form-control" id="weight_' + numCountItem + '" name="weight_' + numCountItem + '" min="0" required><div class="input-group-text">kg</div></div></div><div class="col-md-2"><label for="quantity_' + numCountItem + '" class="form-label pe-label">x Quantity</label><div class="input-group"><input type="number" class="form-control" id="quantity_' + numCountItem + '" name="quantity_' + numCountItem + '" placeholder="0" min="1" required></div></div></div></div>';
            $('#items').append(strHtmlItem);
        }

        function removeItem() {
            $('#item_' + numCountItem).remove();
            if (numCountItem > 1) {
                numCountItem--;
            }
        }

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