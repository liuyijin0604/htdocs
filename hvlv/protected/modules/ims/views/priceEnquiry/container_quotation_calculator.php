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
    $id = @$model['id'];
    $hideDisplay = 'block';
    $distance = @$model['distance'];
    $quotationNum = @$model['quotationNum'];
    $cartageFee = @$model['cartageFee'];
    $fuel = @$model['fuel'];
    $timeslot = @$model['timeslot'];
    $infrastructure = @$model['infrastructure'];
    $emptyDeHire = @$model['emptyDeHire'];
    $toll = @$model['toll'];
    $totalFee = @$model['totalFee'];
    $addressLine = @$model['addressLine'];
    $state = @$model['state'];
    $postcode = @$model['postcode'];
    $suburb = @$model['suburb'];
    $standardTrailer = @$model['standardTrailer'];
    if (!empty($model['invalid'])) {
        $invalid = $model['invalid'];
        $hideDisplay = 'none';
    }
}
else{
    $hideDisplay = 'none';
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
        <form class="row g-3 needs-validation" id="check-container-quotation-form" method="POST" action="<?= $this->createUrl('tools/getContainerQuotation').'?ims=1&cach='.rand(1,1000000);?>">
            <div class="container">
                <h6>Depot From</h6>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="state" id="NSW" value="NSW" 
                    <?= (!empty($state)&&$state=="NSW")?"checked disabled":"";?>>
                    <label class="form-check-label pe-label" for="NSW">Sydney</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="state" id="VIC" value="VIC"
                    <?= (!empty($state)&&$state=="VIC")?"checked disabled":"";?>>
                    <label class="form-check-label pe-label" for="VIC">Melbourne</label>
                </div>
                <!-- <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="state" id="QLD" value="QLD">
                    <label class="form-check-label pe-label" for="QLD">Brisbane</label>
                </div>
 -->                <div class="row">
                    <div class="col-md-12">
                        <label for="address" class="form-label pe-label text-input-row">Delivery Address</label>
                        <input type="text" class="form-control" id="address_line" name="address_line" placeholder="1234 Main St" <?= (!empty($addressLine))?'value="'.$addressLine.'" disabled':'';?> required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="suburb" class="form-label pe-label text-input-row">Delivery Suburb</label>
                        <input type="text" class="form-control" id="suburb" name="suburb" placeholder="Bondi" <?= (!empty($suburb))?'value="'.$suburb.'" disabled':'';?> required>
                    </div>
                    <div class="col-md-6">
                        <label for="postcode" class="form-label pe-label text-input-row">Delivery Postcode</label>
                        <input type="text" class="form-control" id="postcode" name="postcode" placeholder="2026" <?= (!empty($postcode))?'value="'.$postcode.'" disabled':'';?> required>
                    </div>
                </div>               
                <h6 class="text-input-row">Standard Trailer Request</h6>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="standard_trailer" id="20ft_standard" value="20ft_standard" <?= (!empty($standardTrailer)&&$standardTrailer=="20ft_standard")?"checked disabled":"";?>>
                    <label class="form-check-label pe-label" for="20ft_standard">20ft Standard</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="standard_trailer" id="40ft_standard" value="40ft_standard" <?= (!empty($standardTrailer)&&$standardTrailer=="40ft_standard")?"checked disabled":"";?> >
                    <label class="form-check-label pe-label" for="40ft_standard">40ft Standard</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="standard_trailer" id="20ft_sideloader" value="20ft_sideloader" <?= (!empty($standardTrailer)&&$standardTrailer=="20ft_sideloader")?"checked disabled":"";?> >
                    <label class="form-check-label pe-label" for="20ft_sideloader">20ft Sideloader</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="standard_trailer" id="40ft_sideloader" value="40ft_sideloader" <?= (!empty($standardTrailer)&&$standardTrailer=="40ft_sideloader")?"checked disabled":"";?> >
                    <label class="form-check-label pe-label" for="40ft_sideloader">40ft Sideloader</label>
                </div>
                <h6 class="text-input-row">Is this the residential address?</h6>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="check_residential" id="yes_residential" value="yes_residential">
                    <label class="form-check-label pe-label" for="yes_residential">Yes</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="check_residential" id="no_residential" value="no_residential" <?=$hideDisplay=='block'?"checked disabled":""; ?> >
                    <label class="form-check-label pe-label" for="no_residential">No</label>
                </div>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary" id="check_btn">Show me the price</button>
            </div>
        </form>
    </div>

    <div class="load" id='div_loading' style="display: none;">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
    </div>
    <div class="load" id='div_loading' style="display: none;">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
    </div>
    <div id="invalid" style="width:60%; margin:0 auto; padding-top:80px; font-size:2em;color:#666; display: <?=empty($invalid)?'none;':'block;'; ?>">
        <p>This Address is invalid.</p>
    </div>
    <div id="result" style="width:60%; margin:0 auto; padding-top:80px; font-size:2em;color:#666; display: <?php echo $hideDisplay; ?>">
        <div class="container-md">
            <h1 style="text-align: center;">Container Quotation Enquiry</h1>
            <table style="border-collapse: collapse; width: 100%;">
                <tr>
                    <th style="border: 1px solid black; padding: 8px;">Delivery Distance</th>
                    <td style="border: 1px solid black; padding: 8px;"><?php echo @$distance; ?> kilometers</td>
                </tr>
                <tr>
                    <th style="border: 1px solid black; padding: 8px;">Reference Number</th>
                    <td style="border: 1px solid black; padding: 8px;"><?php echo @$quotationNum; ?></td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 8px;">Cartage Fee</td>
                    <td style="border: 1px solid black; padding: 8px;">$<?php echo @$cartageFee; ?></td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 8px;">Fuel Surcharge</td>
                    <td style="border: 1px solid black; padding: 8px;">$<?php echo @$fuel; ?></td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 8px;">Timeslot Fee</td>
                    <td style="border: 1px solid black; padding: 8px;">$<?php echo @$timeslot; ?></td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 8px;">Infrastructure Fee</td>
                    <td style="border: 1px solid black; padding: 8px;">$<?php echo @$infrastructure; ?></td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 8px;">Empty De-hire Fee</td>
                    <td style="border: 1px solid black; padding: 8px;">$<?php echo @$emptyDeHire; ?></td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 8px;">Toll Surcharge</td>
                    <td style="border: 1px solid black; padding: 8px;">$<?php echo @$toll; ?></td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 8px;">Total Fee</td>
                    <td style="border: 1px solid black; padding: 8px;">$<?php echo @$totalFee; ?></td>
                </tr>
                <!-- <tr>
                    <td colspan="2" style="border: 1px solid black; padding: 8px;">For any further information, please reach out to our sales team. Or you can submit your enquiry via <a target="_blank" href="https://toplogistics.com.au/contact-us/">Contact us</a></td>
                </tr> -->
                <tr>
                    <td colspan="2" style="border: 1px solid black; padding: 8px;">
                        <h6 class="text-input-row">Are you happy with our price, do you want to use it ?</h6>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="check_residential" id="yes_happy" value="yes_happy">
                            <label for="yes_happy"><h6 class="text-input-row">Yes</h6></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="check_residential" id="no_happy" value="no_happy" >
                            <label for="no_happy"><h6 class="text-input-row">No</h6></label>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

    </div>

    <div id="residential_notice" style="width:60%; margin:0 auto; padding-top:80px; font-size:2em;color:#666;"><p>Sorry we can not deliver full container to residential address, please contact your account manager. </p><p>(To proceed and check the commercial address, please select "No" in the last question.)</p></div>

    <div class="container-lg" id="customer_detail">
        <br />
        <form class="row g-3 needs-validation" id="customer-contact-detail-form" method="GET" action="<?= $this->createUrl('tools/submitContainerQuotationEmail',['id'=>@$model['id']]);?>">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <label for="name" class="form-label pe-label text-input-row">Name</label>
                        <input type="text" class="form-control" id="name" name="name" <?= (!empty($name))?'value="'.$name.'" disabled':'';?> required>
                    </div>
                    <div class="col-md-6">
                        <label for="contact_number" class="form-label pe-label text-input-row">Contact Number</label>
                        <input type="number" class="form-control" id="contact_number" name="contact_number" placeholder="04xxxxxxxx" <?= (!empty($contactNumber))?'value="'.$contactNumber.'" disabled':'';?> required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="email" class="form-label pe-label text-input-row">Email</label>
                        <input type="email" class="form-control" id="email" name="email" <?= (!empty($email))?'value="'.$email.'" disabled':'';?> required>
                    </div>
                    <div class="col-md-6">
                        <label for="enquiry_date" class="form-label pe-label text-input-row">when do you need this service ?</label>
                        <input type="date" class="form-control" id="enquiry_date" name="enquiry_date" <?= (!empty($enquiryDate))?'value="'.$enquiryDate.'" disabled':'';?> required>
                    </div>
                </div>                
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary" id="submit_btn">Submit</button>
            </div>
        </form>
    </div>
    <div id="no_happy_notice" style="width:60%; margin:0 auto; padding-top:80px; font-size:2em;color:#666;"><p>Sorry, if you require further information, please get in touch with our account manager. </p><p>(Just click 'Yes' in the last question and let's get started to upload your detail.)</p></div>

    <div id="break" style="height:180px;"></div>



    <script type="text/javascript">
        $(function(){
            $("#check_btn").hide(); 
            $("#residential_notice").hide(); 
            $("#customer_detail").hide(); 
            $("#no_happy_notice").hide();

            $('#yes_residential').click(function(){  
                $("#residential_notice").show();
                $("#check_btn").hide(); 
                $("#invalid").hide(); 
                $("#customer_detail").hide(); 
                $("#no_happy_notice").hide();            
            }); 

            $('#no_residential').click(function(){
                $("#residential_notice").hide();  
                $("#check_btn").show();  
                $("#invalid").hide();
                $("#customer_detail").hide(); 
                $("#no_happy_notice").hide();
            });

            $('#yes_happy').click(function(){  
                $("#customer_detail").show(); 
                $("#no_happy_notice").hide();             
            }); 

            $('#no_happy').click(function(){
                $("#customer_detail").hide(); 
                $("#no_happy_notice").show();
            });
        });

    </script>
</body>

</html>