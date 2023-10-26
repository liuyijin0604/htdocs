<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=Lato:300,400|Montserrat:700' rel='stylesheet' type='text/css'>
    <style>
        @import url(//cdnjs.cloudflare.com/ajax/libs/normalize/3.0.1/normalize.min.css);
        @import url(//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css);
    </style>
    <!-- <link rel="stylesheet" href="https://2-22-4-dot-lead-pages.appspot.com/static/lp918/min/default_thank_you.css"> -->

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style type="text/css">
        @media (min-width: 1200px) {
            .col-lg-3 {
                width: 33.3%;
            }
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

        #label_has_forklift, #label_no_forklift {
            display: none;
        }
    </style>
</head>

<body>
    <div class="loading-container" id="loading-container">
        <div class="loading">Loading&#8230;</div>
    </div>
    <div class="container-sm" style="margin-top: 5%;">
        <h5>This is Top Logistics Australia, a logistics and delivery company in Australia. You have a shipemnt that will be delivered to following address. We would like to confirm in advance if your address has a forklift available.</h5>
        <form class="row g-3" id="confirm_forklift_form">
            <div class="col-md-12">
                <label for="ref" class="form-label">Tracking Number</label>
                <input type="text" class="form-control" id="ref" value="<?= $data->ref; ?>" disabled>
            </div>
            <div class="col-md-12">
                <label for="address" class="form-label">Street Address</label>
                <input type="text" class="form-control" id="address" value="<?= $data->address; ?>" disabled>
            </div>
            <div class="col-md-4">
                <label for="suburb" class="form-label">Suburb</label>
                <input type="text" class="form-control" id="suburb" value="<?= $data->suburb; ?>" disabled>
            </div>
            <div class="col-md-4">
                <label for="city" class="form-label">City</label>
                <input type="text" class="form-control" id="city" value="<?= $data->city; ?>" disabled>
            </div>
            <div class="col-md-2">
                <label for="state" class="form-label">State</label>
                <input type="text" class="form-control" id="state" value="<?= $data->state; ?>" disabled>
            </div>
            <div class="col-md-2">
                <label for="postcode" class="form-label">Postcode</label>
                <input type="text" class="form-control" id="postcode" value ="<?= $data->postcode; ?>" disabled>
            </div>
            <div class="col-md-4">
                <label for="packages" class="form-label">Packages</label>
                <input type="text" class="form-control" id="packages" value="<?= $data->pkg; ?>" disabled>
            </div>
            <div class="col-md-4">
                <label for="weight" class="form-label">Weight(kg)</label>
                <input type="text" class="form-control" id="weight" value="<?= $data->weight; ?>" disabled>
            </div>
            <div class="col-md-4">
                <label for="volumn" class="form-label">Volumn(M<sup>3</sup>)</label>
                <input type="text" class="form-control" id="volumn" value="<?= $data->cbm; ?>" disabled>
            </div>
            <div class="col-md-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="has_forklift" name="has_forklift">
                    <label class="form-check-label" id="label_has_forklift" for="has_forklift">Forklift is available at delivery address.</label>
                    <label class="form-check-label" id="label_no_forklift" for="has_forklift">Forklift is <span style="color:orangered;">not</span> available at delivery address.</label>
                </div>
            </div>
            <div class="col-md-12">
                <p style="color: red;">*Your response will serve as the basis for our delivery arrangement. If it is found to be inconsistent with the facts, any additional costs incurred as a result will be your responsibility.</p>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Confirm</button>
            </div>
        </form>
    </div>

    <script type="text/javascript">
        $(function() {
            var has_forklift = <?= $data->has_forklift; ?>;
            if (has_forklift == 1) {
                $('#has_forklift').prop('checked', true);
                $('#label_has_forklift').show();
            } else {
                $('#label_no_forklift').show();
            }

            $('#has_forklift').on('change', function(){
                if ($('#has_forklift').is(":checked")) {
                    $('#label_has_forklift').show();
                    $('#label_no_forklift').hide();
                } else {
                    $('#label_has_forklift').hide();
                    $('#label_no_forklift').show();
                }
            });

            $('#confirm_forklift_form').on('submit', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                $('#loading-container').show();
                var formData = new FormData();
                formData.append('ref', '<?= $data->ref; ?>');
                if ($('#has_forklift').is(":checked")) {
                    formData.append('has_forklift', 1);
                } else {
                    formData.append('has_forklift', 0);
                }
                $.ajax({
                    url: "<?= $this->createUrl('customerService/submitConfirmForklift'); ?>",
                    method: 'POST',
                    async: false,
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        $('#loading-container').hide();
                        let r = JSON.parse(res);
                        if (r.status == 'success') {
                            $(location).prop('href', '<?= $this->createUrl("customerService/forkliftConfirmResult"); ?>');
                        } else if (r.status == 'fail') {
                            alert(r.message);
                        }
                    }
                });
            })
        });
    </script>
</body>

</html>