<?php

if (!empty($model)) {
    $questionId = $model['id'];
    $questionNum = $model['question_num'];
    $question = $model['question'];
    $questionType = $model['question_type'];
}

$salesService = new SalesfunnelRequirementsService();

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
        .red-star {
            color: red;
        }
    </style>
    <title>Customer Requirements</title>
</head>

<body>
    <div class="container-lg">
        <br />
        <main id="site-content" role="main">
<?php 
    $url = $this->createUrl('salesfunnelRequirements/saveCustomerDetail');
    if(!empty($model->id))
    {
        $url = $url."?id=".$model->id;
    }

    $form=$this->beginWidget('CActiveForm', array(
    'id'=>'customer_detail_form',
    'enableAjaxValidation'=>false)
    );
?>
                <div class="container" id="items">
                    <header class="entry-header has-text-align-center header-footer-group">
                        <div class="entry-header-inner section-inner medium">
                            <h1 class="entry-title">Your Details</h1>
                        </div><!-- .entry-header-inner -->
                    </header>                    

                    <div class="item" id="item">
                        <!-- <h2>Please complete the contact before the requirement form.</h2> -->
                        <div class="row">
                            <div class="col-md-12">
                                <label for="company_name" class="form-label pe-label text-input-row">Company Name</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" required
                                <?php
                                    if (!empty($submissionModel)){
                                        echo 'value="'.$submissionModel->company_name.'"';
                                    }
                                ?>
                                >
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label pe-label text-input-row">First name<span class="red-star">*</span></label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required
                                <?php
                                    if (!empty($submissionModel)){
                                        echo 'value="'.$submissionModel->first_name.'"';
                                    }
                                ?>
                                >
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label pe-label text-input-row">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required
                                <?php
                                    if (!empty($submissionModel)){
                                        echo 'value="'.$submissionModel->last_name.'"';
                                    }
                                ?>
                                >
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="email" class="form-label pe-label text-input-row">Email address<span class="red-star">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="xxxxxx@xx.xx" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" required
                                <?php
                                    if (!empty($submissionModel)){
                                        echo 'value="'.$submissionModel->email.'"';
                                    }
                                ?>
                                >
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="contact_number" class="form-label pe-label text-input-row">Contact mobile number<span class="red-star">*</span></label>
                                <input type="number" class="form-control" id="contact_number" name="contact_number" placeholder="04xxxxxxxx" required
                                <?php
                                    if (!empty($submissionModel)){
                                        echo 'value="'.$submissionModel->contact_number.'"';
                                    }
                                ?>
                                >
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px">
                            <label class="form-label pe-label text-input-row">Types of services you require<span class="red-star">*</span></label>
                            <div class="col-md-12">
                                                                                  
                                <input type="checkbox" class="form-check-input" id="category_import" name="category[]" value="31"
                                <?php
                                    if (!empty($submissionModel)){
                                        $categoryArray = explode(',', $submissionModel->category);
                                        if ( in_array(31, $categoryArray)){
                                            print(' checked ');
                                        }
                                    }
                                ?>
                                >
                                <label for="category_import" class="form-label pe-label text-input-row">Import Service</label>                          
                                <input type="checkbox" class="form-check-input" id="category_SameDay" name="category[]" value="32"
                                <?php
                                    if (!empty($submissionModel)){
                                        $categoryArray = explode(',', $submissionModel->category);
                                        if ( in_array(32, $categoryArray)){
                                            print(' checked ');
                                        }
                                    }
                                ?>
                                >
                                <label for="category_SameDay" class="form-label pe-label text-input-row">Same City Delivery</label>                      
                                <input type="checkbox" class="form-check-input" id="category_3PL" name="category[]" value="33"
                                <?php
                                    if (!empty($submissionModel)){
                                        $categoryArray = explode(',', $submissionModel->category);
                                        if ( in_array(33, $categoryArray)){
                                            print(' checked ');
                                        }
                                    }
                                ?>
                                >
                                <label for="category_3PL" class="form-label pe-label text-input-row">Service Type For 3PL</label>                       
                            </div>
                        </div>
                        <div class="row">
                            <label class="form-label pe-label text-input-row">Are you a Forwarder/Shipping Agent?<span class="red-star">*</span></label>
                            <div class="col-md-12">                                
                                <input type="radio" class="form-check-input" id="fsa_yes" name="forwarder_shippingagent" value="1"
                                <?php
                                    if (!empty($submissionModel) && $submissionModel->forwarder_shippingagent == 1){
                                        print(' checked ');
                                    }
                                ?>
                                >
                                <label for="fsa_yes" class="form-label pe-label text-input-row">Yes</label>                                             
                                <input type="radio" class="form-check-input" id="fsa_no" name="forwarder_shippingagent" value="0"
                                <?php
                                    if (!empty($submissionModel) && $submissionModel->forwarder_shippingagent == 0){
                                        print(' checked ');
                                    }
                                ?>
                                >
                                <label for="fsa_no" class="form-label pe-label text-input-row">No</label>                                
                            </div>
                        </div>
                        <div class="row" id="fsa_no_div" >
                            <label for="fsa_no_requirement" class="form-label pe-label text-input-row">Detailed Requirements</label>
                            <textarea maxlength="1800" name="fsa_no_requirement" id="fsa_no_requirement" cols="86" rows ="20"></textarea>
                            <button type="submit" class="btn btn-primary fsa-no-submit" onclick="return submitForm(2);">Check and Submit</button>
                        </div>

                    </div>
                </div>
                <br><br><br>
                
                <!-- <div class="g-recaptcha" data-sitekey="6LfyO2saAAAAAJ6CdVI_q_Nt8pFZkcVqbO7aGZHJ" style="float:left"></div> -->
                <div id="process_submit_btn" class="col-12">
                    <button type="submit" class="btn btn-primary" onclick="return submitForm(1);">Check and Process</button>
                </div>
<?php $this->endWidget(); ?>
    </div>

    <script type="text/javascript">
        var numCountItem = 1;
        var strHtmlItem = "";
        var questionsArray = [];
        var answersArray = [];
        $("#process_submit_btn").hide();
        $(".fsa-no-submit").hide();

        function checkEmpty() {         
            if(!$('#company_name').val()){
                alert("Company name can not be blank");
                return false;
            }
            if(!$('#first_name').val()){
                alert("First name can not be blank");
                return false;
            }            
            if(!$('#last_name').val()){
                alert("Last name can not be blank");
                return false;
            }
            if(!$('#email').val()){
                alert("Email can not be blank");
                return false;
            }
            if(!$('#contact_number').val()){
                alert("Contack number can not be blank");
                return false;
            }
            if($('input[name="category[]"]:checked').length<1){
                alert("Type of services can not be blank");
                return false;
            }
            if(!$('input[name="forwarder_shippingagent"]:checked').val()){
                alert("Forwarder/Shipping Angent can not be blank");
                return false;
            }
            return true;
        }


        function submitForm(type) {

            if(!checkEmpty()){
                return false;
            }

            let email = $('#email').val();
            let valid = email.match(
                /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
              );

              if (!valid) {
                alert("email is not valid");
                return false;
              }
              
            // alert("");

            var form = new FormData(document.getElementById("customer_detail_form"));
            if(type==1){
                $.ajax({
                        url: '<?=$url?>',
                        type: "post",
                        data: form,
                        processData: false,
                        contentType: false,
                        success: function(r) {

                            var response = JSON.parse(r);
                            if(!response.done)
                            {
                                console.log(response);
                                alert(response.msg);
                            }else
                            {
                                var submit_id = response['submit_id'];
                                var services_types = response['services_types'];  
                                // console.log(submit_id); 
                                // debugger;                       
                                window.location.replace("<?=$this->createUrl('salesfunnelRequirements/requirement')?>"+"?submit_id="+submit_id+"&&services_types="+services_types);
                            }
                         },
                        error: function(e) {
                            console.log(e);
                        }
                    });
            }
            else{
                if (!$("#fsa_no_requirement").val()) {
                    alert("Business requirement can not be blank");
                    return false;
                }
                else{
                    $.ajax({
                        url: '<?=$this->createUrl('salesfunnelRequirements/nonAgentCustomerSave')?>',
                        type: "post",
                        data: form,
                        processData: false,
                        contentType: false,
                        success: function(r) {

                            var response = JSON.parse(r);
                            if(!response.done)
                            {
                                console.log(response);
                                alert(response.msg);
                            }else
                            {
                                let submit_id = response['submit_id'];                      
                                window.location.replace("<?=$this->createUrl('salesfunnelRequirements/customerSubmit')?>"+"?id="+submit_id);
                                return false;
                            }
                         },
                        error: function(e) {
                            console.log(e);
                        }
                    });
                }               
            }             
             return false;

        }

        $('#fsa_no').click(function(){  
            $(".fsa-no-submit").show();
            $("#process_submit_btn").hide();               
        }); 

        $('#fsa_yes').click(function(){
            $(".fsa-no-submit").hide();  
            $("#process_submit_btn").show();  
        });
    </script>

</body>

</html>