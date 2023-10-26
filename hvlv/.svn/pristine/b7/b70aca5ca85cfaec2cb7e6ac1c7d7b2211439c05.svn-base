<?php
?>
<!Doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!-- jQuery CSS -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <!-- reCaptcha  -->
    <!-- <script src="https://www.google.com/recaptcha/api.js"></script> -->
    <!-- Custom CSS -->
    <style>
        body {
            background: #ece7dc;
        }
        .btn-danger
        {
            background-color: rgb(226, 116, 112);
        }

        .slots-4 {
            height: 220px;
            margin-bottom: 108px;
        }

        .slots-2 {
            height: 110px;
            margin-bottom: -2px;
        }

        .slots-1 {
            height: 55px;
            margin-bottom: -54px;
        }

        .timeslot-container {
            text-align: center;
        }

        .timeslot {
            margin-bottom: 0px;
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
            background-color: rgba(0, 0, 0, 0.3);
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
    <title>TLA Container Pickup Booking</title>
</head>

<body>
    <div class="loading-container" id="loading-container">
        <div class="loading">Loading&#8230;</div>
    </div>
   <?php $this->render("terms")?>
    <br />
    <!-- Form -->
    <div class="container-md">
        <form class="row g-3 needs-validation" id="bookingForm" method="POST" action="<?php $this->createUrl('customerService/containerPickupBooking'); ?>" enctype="multipart/form-data">
            <div class="card">
                <div class="card-header">
                    <h3 style="color:green"><i class="bi bi-calendar2-check"></i>Pickup List Summary</h3>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Container#/AWB No.</th>
                                <th scope="col">House BL</th>
                                <th scope="col">Number of Pallets</th>
                                <th scope="col">Number of Packages</th>
                                <th scope="col">Weight(kg)</th>
                                <th scope="col">Volume(M<sup>3</sup>)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($data as $key => $d)
                            {
                            ?>
                            <tr>
                                <td><?php echo $d['container_no']; ?></td>
                                <td><?php echo $d['house_bl']; ?></td>
                                <td><?php echo $d['pallets']; ?></td>
                                <td><?php echo $d['packages']; ?></td>
                                <td><?php echo number_format((float)$d['weight'], 2, '.', ''); ?></td>
                                <td><?php echo number_format((float)$d['volume'], 3, '.', ''); ?></td>
                            </tr>
                            <?php 
                            } 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
             <?php
             foreach ($data as $index => $d)
             {
                $container_no = $d['container_no'];
                $house_bl = $d['house_bl'];
                $pallets = $d['pallets'];
                $packages = $d['packages'];
                $weight = $d['weight'];
                $volume = $d['volume'];
                $depot = $d['depot'];
                $palletType = $d['pallet_type'];
             ?>
            <div id="connoteDiv_<?=$index?>" style= "border-bottom:1px dashed rgb(53,54,58);padding-bottom:0.5em;" class="row g-3 needs-validation">

                <div class="col-md-6" style="display: none;">
                    <label for="container_no" class="form-label">Container Number</label>
                    <input type="text" class="form-control" id="container_no" name="container_no[]" value="<?php echo $container_no; ?>" autocomplete="off" required>
                </div>
                <div class="col-md-6" style="display: none;">
                    <label for="depot" class="form-label">Depot</label>
                    <input type="text" class="form-control" id="depot" name="depot" value="<?php echo $depot; ?>" autocomplete="off" required>
                </div>
                


                <div class="col-md-12">
                    <label for="house_bl" class="form-label"><b>House BL:</b></label>
                    <input type="hidden" name="house_bl[]" value="<?php echo $house_bl; ?>" >
                    <input type="text" class="form-control" id="house_bl" value="<?php echo $house_bl; ?>" autocomplete="off" disabled>
                </div>
                <div class="col-md-6">
                    <label for="booking_date_<?=$index?>" class="form-label">Pickup Date</label>
                    <input type="text" class="form-control" id="booking_date_<?=$index?>" name="booking_date[]" autocomplete="off" required>
                </div>
                <div class="col-md-6">
                    <label for="booking_time" class="form-label">Pickup Time</label>
                    <input type="text" class="form-control readonly" id="booking_time_<?=$index?>" name="booking_time[]" autocomplete="off" required>
                </div>
                <br />
                <div class="container-sm timeslot-container" id="timeslot-container_<?=$index?>" style="display: none;">
                    <div class="timeslot">
                            <?php  $thisIndex=1;
                            foreach ($staticTimeSlot as $sku1 => $sv){ ?>
                            <div class="btn-group-vertical">
                                 <?php  
                                 foreach ($sv as $sku2 => $svv){ 
                                    if(empty($svv))
                                    {
                                        echo "<div class=\"btn-timeslot btn-sm\">&nbsp;</div>";
                                    }else
                                    {
                                        echo "<button type=\"button\" class=\"btn btn-success btn-timeslot btn-sm time_{$sku1}\" id=\"time_{$index}_{$thisIndex}\" onclick=\"setBookingTime('{$svv}', '#time_{$index}_{$thisIndex}','{$index}')\">{$svv}</button>";
                                        $thisIndex++;
                                    }
                                    
                                } ?>
                            </div>
                            <?php } ?>
                       
                        <div class="btn-group-vertical" role="group" style="margin-left: 120px;">
                            <button type="button" class="btn btn-success" id="btn-example-available">Available Slot</button>
                            <button type="button" class="btn btn-info" id="btn-example-selected">Selected Slot</button>
                            <button type="button" class="btn btn-danger" id="btn-example-occupied" disabled>Occupied Slot</button>
                        </div>
                    </div>
                </div>
                 <b>
                    <p>Pallet requirement: </p>
                </b>
                <div class="form-check">
                    <input class="form-check-input pallet" type="radio" name="pallet_requirement_<?=$index?>[]" id="pallet_requirement_chep" value="chep" <?=(!empty($palletType)&&$palletType!="Plain")?(($palletType=="Chep")?"checked":"disabled"):"checked"?> >
                    <label class="form-check-label" for="pallet_requirement_chep">CHEP(swap)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input pallet" type="radio" name="pallet_requirement_<?=$index?>[]" id="pallet_requirement_origin" value="origin" <?=(!empty($palletType)&&$palletType!="Plain")?(($palletType=="Origin")?"checked":"disabled"):""?> >
                    <label class="form-check-label" for="pallet_requirement_origin">Origin</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input pallet" type="radio" name="pallet_requirement_<?=$index?>[]" id="pallet_requirement_plain" value="plain" disabled>
                    <label class="form-check-label" for="pallet_requirement_plain">Plain($20/pallet)</label>
                </div>
                <b>
                    <p>Do you require shrink wrap? </p>
                </b>
                <div class="form-check">
                    <input class="form-check-input wrap" type="radio" name="shrink_wrap_<?=$index?>[]" id="shrink_wrap_true" value="wrap" checked>
                    <label class="form-check-label" for="shrink_wrap_true">
                        Yes($5/pallet)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input wrap" type="radio" name="shrink_wrap_<?=$index?>[]" id="shrink_wrap_false" value="non_wrap">
                    <label class="form-check-label" for="shrink_wrap_false">
                        No
                    </label>
                </div>
                <div class="mb-3" id="file_upload_container_<?=$index?>">
                    <label for="file_1" class="form-label">Upload Files(Delivery Order(s)) <span style="color:red;">*At least 1 file is required.</span></label>
                    <input class="form-control file_uploader" type="file" id="file_<?=$index?>_1" name="file_<?=$index?>_1" required>
                </div>
                <div class="mb-3">
                    <button type="button" class="btn btn-primary" id="add_file_btn" onclick="addFile(<?=$index?>)">Add File</button>
                    <button type="button" class="btn btn-danger" id="remove_file_btn" onclick="removeFile(<?=$index?>)">Remove File</button>
                </div>
                <div class="col-md-12">
                    <label for="note" class="form-control">Note</label>
                    <textarea class="form-control" id="note" name="note[]" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <button type="button" class="btn btn-primary" id="add_driverInfo_btn" onclick="showInDriverDiv(<?=$index?>)">Individial Driver Info?</button>
                </div>

                <div id = "indriverdiv_<?=$index?>" class="row g-3 needs-validation" style="display: none;">
                <div class="col-md-12">
                    <label for="name" class="form-label"><b>Individial Driver Information</b></label>
                </div>

                <div class="col-md-6">
                    <label for="name" class="form-label">Driver Name</label>
                    <input type="text" class="form-control" id="name" name="name[]" >
                </div>
                <div class="col-md-6">
                    <label for="rego" class="form-label">Rego</label>
                    <input type="text" class="form-control" id="rego" name="rego[]" >
                </div>
                <div class="col-md-6">
                    <label for="company_name" class="form-label">Company Name(Bill to)</label>
                    <input type="text" class="form-control" id="company_name" name="company_name[]" >
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">Company Emails(To receive invoice and booking confirmation, split by ;)</label>
                    <input type="text" class="form-control" id="email" name="email[]" >
                </div>
                </div>

            </div>
             <?php 
             } 
             ?>

           
            <div class="col-md-12">
                <label for="name" class="form-label"><b>Default Driver Information</b></label>
            </div>

            <div class="col-md-6">
                <label for="name" class="form-label">Driver Name</label>
                <input type="text" class="form-control" id="name" name="name_default" required>
            </div>
            <div class="col-md-6">
                <label for="rego" class="form-label">Rego</label>
                <input type="text" class="form-control" id="rego" name="rego_default" required>
            </div>
            <div class="col-md-6">
                <label for="company_name" class="form-label">Company Name</label>
                <input type="text" class="form-control" id="company_name" name="company_name_default" required>
            </div>
            <div class="col-md-6">
                <label for="phone" class="form-label">Company Emails(split by ;)</label>
                <input type="text" class="form-control" id="email_default" name="email_default" required>
            </div>

            <div class="col-md-3">
                <label for="note">Account Code <font style="font-size: 12px;">(leave as blank if you don't have one)</font></label>
                <input id="account_code" name="account_code" class="form-control"></input>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary" id="form_submit">Make a Reservation</button>
            </div>
        </form>
    </div>

    <script type="text/javascript">
        var fileCount = 1;

        $(window).on('load', function() {
            $('#terms').modal('show');
        });

        $(".readonly").on('keydown paste focus mousedown', function(e) {
            if (e.keyCode != 9) // ignore tab
                e.preventDefault();
        });

        function getNextMonday(date = new Date()) {
            //debugger;
            const dateCopy = new Date(date.getTime());

            const temp = new Date(
                dateCopy.setDate(
                dateCopy.getDate() + ((7 - dateCopy.getDay() + 1) % 7 || 7),
                ),
            );
            const nextMonday = dateFormat(temp, 'yyyy-MM-dd');
            //console.log(nextMonday);
            return nextMonday;
        }

        function isEmail(strEmail)
        {
            var emails = strEmail.split(';');
            for (var i = emails.length - 1; i >= 0; i--)
            {
               if (emails[i].search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) != -1)
                {
                    
                }
                else
                {
                    return false;
                }
            }
            
            return true;
        }

        // Convert date format
        function dateFormat(inputDate, format) {
            //parse the input date
            const date = new Date(inputDate);

            //extract the parts of the date
            const day = date.getDate();
            const month = date.getMonth() + 1;
            const year = date.getFullYear();

            //replace the month
            format = format.replace("MM", month.toString().padStart(2, "0"));

            //replace the year
            if (format.indexOf("yyyy") > -1) {
                format = format.replace("yyyy", year.toString());
            } else if (format.indexOf("yy") > -1) {
                format = format.replace("yy", year.toString().substr(2, 2));
            }

            //replace the day
            format = format.replace("dd", day.toString().padStart(2, "0"));

            return format;
        }

        $(document).on('change', '.file_uploader', function() {
            var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp', 'pdf'];
            if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                alert("Only formats are allowed : " + fileExtension.join(', '));
                $(this).val('');
            }
        });

        function addFile(index) {
            fileCount++;
            var strHtmlFile = '<input class="form-control file_uploader" type="file" id="file_'+index +'_'+ fileCount + '" name="file_'+index +'_'+ fileCount + '">';
            $('#file_upload_container_'+index).append(strHtmlFile);
        }

        function removeFile(index) {
            if (fileCount > 1) {
                $('#file_'+index+'_'+fileCount).remove();
                fileCount--;
            }
        }

        function showInDriverDiv(index) {
            $("#indriverdiv_"+index).show();
        }

        /**
         * Set booking time value, adjust display
         */
        function setBookingTime(bookingTime, btn_id,index) 
        {
             var divContainer = $("#connoteDiv_"+index);
            $("#booking_time_"+index).val(bookingTime);
            $(".btn-info",divContainer).addClass("btn-success");
            $(".btn-info",divContainer).removeClass("btn-info");
            $(btn_id).removeClass("btn-success");
            $(btn_id).addClass("btn-info");
            $("#btn-example-selected",divContainer).removeClass();
            $("#btn-example-selected",divContainer).addClass("btn btn-info");
            $("#btn-example-occupied",divContainer).removeClass();
            $("#btn-example-occupied",divContainer).addClass("btn btn-danger");
        }


        $(function() {

            var dataArr  = '<?=json_encode($data, JSON_HEX_TAG)?>';
            var dataArr = JSON.parse(dataArr);
            for (let ind = 0; ind < dataArr.length; ind++)
            {
                var data = dataArr[ind];
                var availableDate = data['availableDate'];
                var endDate = data['endDate'];
                availableDate = new Date(availableDate);
                endDate = new Date(endDate);
                endDate.setDate(endDate.getDate() - 1);
                var occupiedSlots = data['occupiedSlots'];
                /* var today = new Date();
                if (availableDate.getTime() < today.getTime()) {
                    availableDate = today;
                } */
                var exclude = <?php if($depot == 'Melbourne') {
                 echo '["' . implode('", "', ["22-09-2022", "23-09-2022","01-11-2022"]) . '"]'; 
                } else {
                    echo '["' . implode('", "', ["22-09-2022"]) . '"]';

                }
                ?>;
                //console.log(exclude);
                $("#booking_date_"+ind).datepicker({
                    minDate: availableDate,
                    maxDate: endDate,
                    changeMonth: true,
                    beforeShowDay: function(date) {
                        var day = jQuery.datepicker.formatDate('dd-mm-yy', date);
                        return [!~$.inArray(day, exclude) && (date.getDay() != 0) && (date.getDay() != 6)];
                    },
                    dateFormat: "dd MM yy"
                });

                 /**
                 * Display timeslots based on selected date.
                 * Set pickup time.
                 */
                $("#booking_date_"+ind).change(function() {
                    var divContainer = $("#connoteDiv_"+ind);
                    $("#booking_time_"+ind).val(''); // Clear booking time on booking date change.
                    $("#timeslot-container_"+ind).show();
                    $(".btn-info",divContainer).addClass("btn-success");
                    $(".btn-info",divContainer).removeClass("btn-info");
                    $(".btn-danger",divContainer).addClass("btn-success");
                    $(".btn-danger",divContainer).attr('disabled', false);
                    $(".btn-danger",divContainer).removeClass("btn-danger");

                    $("#btn-example-selected",divContainer).removeClass("btn-success");
                    $("#btn-example-selected",divContainer).addClass("btn-info");
                    $("#btn-example-occupied",divContainer).removeClass("btn-success");
                    $("#btn-example-occupied",divContainer).addClass("btn-danger");
                    var datePicked = $(this).val();
                    datePicked = dateFormat(datePicked, 'yyyy-MM-dd'); // Convert date format


                    /**
                     * Disable all slot buttons have hours <= current hour + 2 hours
                     * If booking hour > 17:00, disable next working day first 2 hours' slots
                     */
                    var today = new Date();
                    var todayStr = undefined;
                    if(today.getDate()<10)
                    {
                        var todayStr = "0"+today.getDate();
                    }else
                    {
                        var todayStr = today.getDate();
                    }

                    var todayMonthStr = undefined;
                    if((today.getMonth()+1)<10)
                    {
                        var todayMonthStr = "0"+(today.getMonth()+1);
                    }else
                    {
                        var todayMonthStr = (today.getMonth()+1);
                    }

                    var currentDate = today.getFullYear()+'-'+todayMonthStr+'-'+todayStr;
                    var tomr = new Date();
                    tomr.setDate(today.getDate()+1);

                    var tomrStr = undefined;
                    if(tomr.getDate()<10)
                    {
                        var tomrStr = "0"+tomr.getDate();
                    }else
                    {
                        var tomrStr = tomr.getDate();
                    }

                    var tomorrow = tomr.getFullYear()+'-'+todayMonthStr+'-'+tomrStr;
                    var nextMonday = getNextMonday(today);
                    var currentHour = today.getHours();
                    console.log(currentHour);
                    if (datePicked == currentDate) {
                        for (let i = 7; i <= 17; i++) {
                            if (i <= currentHour+2) {
                                $(".time_"+i).removeClass("btn-success");
                                $(".time_"+i).addClass("btn-danger");
                                $(".time_"+i).attr('disabled', true);
                            }
                        }
                    } else if (datePicked == tomorrow || ((today.getDay() === 5 || today.getDay() === 6) && datePicked == nextMonday)) {
                        //debugger;
                        if (currentHour >= 17) {
                            //console.log(depot);
                            //debugger;
                            switch (depot) {
                                case "Sydney":
                                    for (let i = 7; i < 9; i++) {
                                        $(".time_"+i).removeClass("btn-success");
                                        $(".time_"+i).addClass("btn-danger");
                                        $(".time_"+i).attr('disabled', true);
                                    }
                                    break;
                                case "Melbourne":
                                case "Brisbane":
                                    //debugger;
                                    for (let i = 7; i < 11; i++) {
                                        $(".time_"+i).removeClass("btn-success");
                                        $(".time_"+i).addClass("btn-danger");
                                        $(".time_"+i).attr('disabled', true);
                                    }
                                    break; 
                            }
                        }
                    } else {
                        // Initialize all buttons
                        for (let i = 7; i <= 17; i++) {
                            $(".time_"+i).removeClass("btn-danger");
                            $(".time_"+i).addClass("btn-success");
                            $(".time_"+i).attr('disabled', false);
                        }
                    }

                    // Read occupied slots array on selected date
                    //console.log(occupiedSlots);
                    var slots = occupiedSlots[datePicked];
                    // console.log(slots);
                    var staticTimeSlot = '<?=json_encode($staticTimeSlot)?>';
                    staticTimeSlot = JSON.parse(staticTimeSlot);
                    //console.log(slots);
                    // Set occupied slots to red and disable them
                    if (slots !== undefined && slots.length > 0) {
                        for (let i = 0; i < slots.length; i++)
                        {
                            var timeButtonId = "#time_"+ind;
                            var getId = 0;
                            var thisIndex = 1;
                            var keyMaps = Object.keys(staticTimeSlot);
                            // console.log(keyMaps);
                            // console.log(staticTimeSlot);
                            for(var j = 0; j<keyMaps.length;j++)
                            {
                                // console.log(staticTimeSlot[keyMaps[j]].length);
                                for(var ji = 0; ji<staticTimeSlot[keyMaps[j]].length;ji++)
                                {
                                    if(staticTimeSlot[keyMaps[j]][ji]!="")
                                    {
                                        var sarr = staticTimeSlot[keyMaps[j]][ji].split(":");
                                        if(sarr[0]<10)
                                        {
                                            sarr[0] = "0"+sarr[0];
                                        }
                                        var checkTime = sarr[0]+":"+sarr[1]+":00";
                                        // console.log(checkTime);
                                        // console.log(slots[i]);
                                        if(checkTime==slots[i])
                                        {
                                            timeButtonId = timeButtonId+"_"+thisIndex;
                                            getId = 1;
                                            break;
                                        }
                                        thisIndex++;
                                    }
                                }
                                if(getId==1)
                                {
                                    break;
                                }
                            }
                            // console.log(timeButtonId);
                            $(timeButtonId).removeClass("btn-success");
                            $(timeButtonId).addClass("btn-danger");
                            $(timeButtonId).attr('disabled', true);

                        }
                    }

                });
 
            }

            $("form#bookingForm").submit(function(event) {
                if(!isEmail($('#email_default').val()))
                {
                    $('#email_default').focus();
                    alert("Company Email is invalid");
                    return false;
                }

                $('#form_submit').prop('disabled', true);
                $('#loading-container').show();
                event.preventDefault();
                var bookingDateArr = $("input[name='booking_date[]']");
                for (var i = bookingDateArr.length - 1; i >= 0; i--) {
                    console.log($(bookingDateArr[i]).val());
                    $(bookingDateArr[i]).val(dateFormat($(bookingDateArr[i]).val(), 'yyyy-MM-dd'));
                }
                var formData = new FormData(this);
                $.ajax({
                    url: "https://ims.toplogistics.com.au/customerService/containerPickupBooking",
                    type: 'POST',
                    data: formData,
                    enctype: 'multipart/form-data',
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        //console.log(formData);
                        console.log(res);
                        //alert('Success');
                        var jsonData = JSON.parse(res);
                        if (jsonData.status==1) {
                           window.location.href = "https://ims.toplogistics.com.au/customerService/bookingCheckoutMultiple?submitNo="+jsonData.msg;
                        }else if(jsonData.status==2)
                        {
                            window.location.href = "https://ims.toplogistics.com.au/customerService/paymentSuccess";
                        }
                        else {
                            alert(jsonData.msg);
                            $('#form_submit').prop('disabled', false);
                            $("#loading-container").hide();
                        }

                    },
                });
            });


        });
        

      
    </script>
</body>
</html>