<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
        @import url(https://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css);
        @import url(http://fonts.googleapis.com/css?family=Calibri:400,300,700);

        body {
            background-color: white;
            font-family: 'Calibri', sans-serif !important
        }

        fieldset,
        label {
            margin: 0;
            padding: 0
        }

        body {
            margin: 20px
        }

        h1 {
            font-size: 2.5em;
            margin: 10px;
            text-align: center;
        }

        .rating {
            border: none;
            margin-right: 49px
        }

        .myratings {
            font-size: 85px;
            color: green
        }

        .rating>[id^="star"] {
            display: none
        }

        .rating>label:before {
            margin: 5px;
            font-size: 2.25em;
            font-family: FontAwesome;
            display: inline-block;
            content: "\f005"
        }

        .rating>.half:before {
            content: "\f089";
            position: absolute
        }

        .rating>label {
            color: #ddd;
            float: right
        }

        .rating>[id^="star"]:checked~label,
        .rating:not(:checked)>label:hover,
        .rating:not(:checked)>label:hover~label {
            color: #FFD700
        }

        .rating>[id^="star"]:checked+label:hover,
        .rating>[id^="star"]:checked~label:hover,
        .rating>label:hover~[id^="star"]:checked~label,
        .rating>[id^="star"]:checked~label:hover~label {
            color: #FFED85
        }

        .reset-option {
            display: none
        }

        .reset-button {
            margin: 6px 12px;
            background-color: rgb(255, 255, 255);
            text-transform: uppercase
        }

        .mt-100 {
            margin-top: 100px
        }

        .card {
            position: relative;
            display: flex;
            width: 350px;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 1px solid #d2d2dc;
            border-radius: 11px;
            -webkit-box-shadow: 0px 0px 5px 0px rgb(249, 249, 250);
            -moz-box-shadow: 0px 0px 5px 0px rgba(212, 182, 212, 1);
            box-shadow: 0px 0px 5px 0px rgb(161, 163, 164)
        }

        .card .card-body {
            padding: 1rem 1rem
        }

        .card-body {
            flex: 1 1 auto;
            padding: 1.25rem
        }

        p {
            font-size: 14px
        }

        h4 {
            margin-top: 18px;
            text-align: center;
            font-size: 20px;
        }

        .btn {
            float: right;
        }

        #high {
            display: none;
            margin-right: 34%;
        }

        #low {
            margin-right: 34%;
        }

        .comment-container {
            text-align: center;
            width: 30%;
            margin-left: 35%;
        }

        /* 2340x1080 pixels at 476ppi iphone 13 mini */
        @media only screen and (device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3) {
            .comment-container {
                text-align: center;
                width: 50%;
                margin-left: 25%;
            }

            #low {
                margin-right: 25%;
            }

            #high {
                display: none;
                margin-right: 25%;
            }
        }

        /* 2532x1170 pixels at 460ppi iphone 13 and 13 pro*/
        @media only screen and (device-width: 390px) and (device-height: 844px) and (-webkit-device-pixel-ratio: 3) {
            .comment-container {
                text-align: center;
                width: 50%;
                margin-left: 25%;
            }

            #low {
                margin-right: 25%;
            }

            #high {
                display: none;
                margin-right: 25%;
            }
        }

        /* 2778x1284 pixels at 458ppi iphone 13 pro max*/
        @media only screen and (device-width: 428px) and (device-height: 926px) and (-webkit-device-pixel-ratio: 3) {
            .comment-container {
                text-align: center;
                width: 50%;
                margin-left: 25%;
            }

            #low {
                margin-right: 25%;
            }

            #high {
                display: none;
                margin-right: 25%;
            }
        }

        /* 1792x828px at 326ppi iphone 11*/
        @media only screen and (device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2) {
            .comment-container {
                text-align: center;
                width: 50%;
                margin-left: 25%;
            }

            #low {
                margin-right: 25%;
            }

            #high {
                display: none;
                margin-right: 25%;
            }
        }

        /* 2436x1125px at 458ppi iphone 11 pro*/
        @media only screen and (device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3) {
            .comment-container {
                text-align: center;
                width: 50%;
                margin-left: 25%;
            }

            #low {
                margin-right: 25%;
            }

            #high {
                display: none;
                margin-right: 25%;
            }
        }

        /* 2688x1242px at 458ppi iphone 11 pro max*/
        @media only screen and (device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3) {
            .comment-container {
                text-align: center;
                width: 50%;
                margin-left: 25%;
            }

            #low {
                margin-right: 25%;
            }

            #high {
                display: none;
                margin-right: 25%;
            }
        }

        @media only screen and (min-width: 481px) {
            .comment-container {
                text-align: center;
                width: 50%;
                margin-left: 25%;
            }

            #low {
                margin-right: 25%;
            }

            #high {
                display: none;
                margin-right: 25%;
            }
        }
    </style>
    <script language="JavaScript" type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <script language="JavaScript" type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {

            $("input[type='radio']").click(function() {
                var sim = $("input[type='radio']:checked").val();
                //alert(sim);
                if (sim < 3) {
                    $('.myratings').css('color', 'red');
                    $(".myratings").text(sim);
                    $("#high").css('display', 'none');
                    $("#low").css('display', 'block');
                } else {
                    $('.myratings').css('color', 'green');
                    $(".myratings").text(sim);
                    $("#low").css('display', 'none');
                    $("#high").css('display', 'block');
                }
            });
        });
    </script>
</head>

<body>

    <h1>TLA Value your feedback</h1>
    <br>
    <br>
    <h4>Thank you for using our delivery service.</h4>
    <h4>Please leave us a feedback based on your experience.</h4>
    <form id="review_form" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <div class="container d-flex justify-content-center mt-100">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-center"> <span class="myratings"></span>
                            <h4 class="mt-1">Please Rate us</h4>
                            <fieldset class="rating">
                                <input type="radio" id="star5" name="rating" value="5" />
                                <label class="full" for="star5" title="Awesome - 5 stars"></label>
                                <input type="radio" id="star4" name="rating" value="4" />
                                <label class="full" for="star4" title="Pretty good - 4 stars"></label>
                                <input type="radio" id="star3" name="rating" value="3" />
                                <label class="full" for="star3" title="Meh - 3 stars"></label>
                                <input type="radio" id="star2" name="rating" value="2" />
                                <label class="full" for="star2" title="Kinda bad - 2 stars"></label>
                                <input type="radio" id="star1" name="rating" value="1" />
                                <label class="full" for="star1" title="Sucks big time - 1 star"></label>
                                <input type="radio" class="reset-option" name="rating" value="reset" />
                            </fieldset>
                            <div style="width: 100%; display: table;">
                                <div style="display: table-row">
                                    <div style="display: table-cell;"> Dissatisfied </div>
                                    <div style="display: table-cell;"> Satisfied </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <br>
        <div class="form-group comment-container">
            <label for="comment">Comment:</label>
            <textarea class="form-control" id="comment" rows="5" name="comment"></textarea>
        </div>
        <br>
        <br>
        <br>
        <input type="submit" class="btn btn-success" id="low" data-toggle="modal" data-target="#lowRating">
        <button type="button" class="btn btn-success" id="high" data-toggle="modal" data-target="#highRating">Submit</button>
    </form>
    <!-- Modal -->
    <div class="modal fade" id="lowRating" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Thank you</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Thank you for giving us feedback.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="highRating" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Thank you</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Great, Thank you! Could you spread the words by providing a top review on Google?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No, thanks</button>
                    <a class="btn btn-primary" href="https://g.page/top-logistics-australia/review?rc" role="button">Sure, why not</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
    <script>
        $(document).ready(function(){
            $('#review_form').on('submit', function(e){
                // Stop the form from submitting itself to the server
                e.preventDefault();
                var comment = $('#comment').val();
                var rating = $('input[type="radio"]:checked').val();
                $.ajax({
                    type: "POST",
                    url: "<?= $this->createUrl('customerService/submitLowRatingReview'); ?>",
                    data: {rating: rating, comment: comment},
                    success: function(data){
                       //alert(data);
                       //window.location.href = 'https://www.toplogistics.com.au/';
                    }
                });
            });
        });
    </script>
    
    
</body>

</html>