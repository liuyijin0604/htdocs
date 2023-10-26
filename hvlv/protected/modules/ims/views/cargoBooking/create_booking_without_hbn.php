<!DOCTYPE html>


<head>
    <style>
        #page_container {
            width: 100%;
        }

        .sub-header {
            width: 100%;
            background: #e5e5e5;
        }

        .pallet-container,
        .carton-container {
            display: none;
        }

        #pallet_size {
            height: 44px;
            width: 100%;
            border: none;
            margin-top: 1px;
        }

        #pallet_quantity {
            width: 74%;
        }

        #pallet_length_container,
        #pallet_width_container,
        #pallet_more_details_container {
            display: none;
        }

        #items_container {
            margin-left: -62px;
        }

        #address_container {
            margin-left: -24px;
        }

        .length-width-container {
            display: none;
        }

        #other_info_container {
            margin-left: -15px;
        }

        .result-container .error-box {
            display: none;
        }

        #success_msg {
            display: none;
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
</head>

<body>
    <div class="loading-container" id="loading-container">
        <div class="loading">Loading&#8230;</div>
    </div>
    <div class="container" id="page_container">
        <div class="container" id="form-container">
            <form id="request_form">
                <div class="row" id="category_container" style="margin-left:-46px;">
                    <div class="col-lg-4">
                        <label for="item_type">Category</label>
                        <select class="form-control" name="item_type" id="item_type">
                            <option value="">Select One</option>
                            <option value="pallets">Pallets</option>
                            <option value="cartons">Cartons</option>
                        </select>
                    </div>
                </div>
                <input type="number" id="item_count" value="0" style="display: none;" />
                <input type="number" id="file_count" value="1" style="display: none;" />
                <br />
                <div class="container" id="items_container">
                </div>
                <div class="container" id="add_remove_btn_container" style="display: none;">
                    <div class="row" style="margin-left: 24px;">
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-success" id="add_item"> + ADD ANOTHER ITEM</button>
                        </div>
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-danger" id="remove_item"> - REMOVE ITEM</button>
                        </div>
                    </div>
                </div>

                <div class="container" id="info_container" style="display: none; margin-left: -15px;">
                    <div class="container sub-header" style="margin-left: -32px;">
                        <h4>2. Collection & Delivery Dates</h4>
                    </div>
                    <h5 style="font-weight: 700;">Pickup Date</h5>
                    <div class="row">
                        <div class="col-lg-2">
                            <label for="pickup_date_1">First Choice</label><br />
                            <input type="date" id="pickup_date_1" name="pickup_date_1" style="height: 46px; width: 170px;" required />
                        </div>
                        <div class="col-lg-2">
                            <label for="pickup_date_2">Second Choice(optional)</label><br />
                            <input type="date" id="pickup_date_2" name="pickup_date_2" style="height: 46px; width: 170px;" />
                        </div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-group">
                                <label for="pickup_time">Pickup Before<span style="color:red">&nbsp;*Extra surcharge may apply</span></label><br />
                                <select name="pickup_time" id="pickup_time">
                                    <option value="none">None</option>
                                    <option value="10:00">10:00</option>
                                    <option value="11:00">11:00</option>
                                    <option value="12:00">12:00</option>
                                    <option value="13:00">13:00</option>
                                    <option value="14:00">14:00</option>
                                    <option value="15:00">15:00</option>
                                    <option value="16:00">16:00</option>
                                    <option value="17:00">17:00</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <label for="delivery_time">Delivery Before<span style="color:red">&nbsp;*Extra surcharge may apply</span></label><br />
                                <select name="delivery_time" id="delivery_time">
                                    <option value="none">None</option>
                                    <option value="10:00">10:00</option>
                                    <option value="11:00">11:00</option>
                                    <option value="12:00">12:00</option>
                                    <option value="13:00">13:00</option>
                                    <option value="14:00">14:00</option>
                                    <option value="15:00">15:00</option>
                                    <option value="16:00">16:00</option>
                                    <option value="17:00">17:00</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="col-lg-4">
                            <label for="tailgate_pickup">I Require a Tailgate Service At Pickup</label>
                            <select class="form-control" name="tailgate_pickup" id="tailgate_pickup">
                                <option value="">Select One</option>
                                <option value="no">No</option>
                                <option value="yes">Yes</option>
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label for="tailgate_delivery">I Require a Tailgate Service At Delivery</label>
                            <select class="form-control" name="tailgate_delivery" id="tailgate_delivery">
                                <option value="">Select One</option>
                                <option value="no">No</option>
                                <option value="yes">Yes</option>
                            </select>
                        </div>
                    </div> -->
                </div>
                <div class="container" id="address_container" style="display: none; margin-left: -15px;">
                    <div class="container sub-header" style="margin-left: -32px;">
                        <h4>3. Collection & Delivery Locations</h4>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="collection_name">Name</label>
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control" name="collection_name" id="collection_name" placeholder="Name" required />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="collection_tel">Tel</label>
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control" name="collection_tel" id="collection_tel" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="collection_company">Company</label>
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control" name="collection_company" id="collection_company" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="collection_email">Email</label>
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control" name="collection_email" id="collection_email" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="collection_address">Street Address</label>
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control" name="collection_address" id="collection_address" placeholder="Enter full street address" required />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="collection_suburb">Suburb</label>
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control" name="collection_suburb" id="collection_suburb" placeholder="Enter collection suburb" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="collection_city">City</label>
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control" name="collection_city" id="collection_city" placeholder="Enter collection city" required />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="collection_state">State</label>
                            <div class="input-group input-group-lg">
                                <select class="form-control" name="collection_state" id="collection_state" style="width: 500px; height: 44px;">
                                    <option value="NSW">New South Wales</option>
                                    <option value="VIC">Victoira</option>
                                    <option value="QLD">Queensland</option>
                                    <option value="WA">Western Australia</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="collection_postcode">Postcode</label>
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control" name="collection_postcode" id="collection_postcode" placeholder="Enter collection postcode" required />
                            </div>
                        </div>
                        <div class="col-lg-6">

                        </div>
                    </div>
                    <br />
                    <h5 style="font-weight: 700;">Delivery Address:</h5>
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="delivery_name">Name</label>
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control" name="delivery_name" id="delivery_name" placeholder="Name" required />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="delivery_tel">Tel</label>
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control" name="delivery_tel" id="delivery_tel" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="delivery_company">Company</label>
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control" name="delivery_company" id="delivery_company" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="delivery_email">Email</label>
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control" name="delivery_email" id="delivery_email" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="delivery_address">Street Address</label>
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control" name="delivery_address" id="delivery_address" placeholder="Enter full street address" required />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="delivery_suburb">Suburb</label>
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control" name="delivery_suburb" id="delivery_suburb" placeholder="Enter delivery suburb" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="delivery_city">City</label>
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control" name="delivery_city" id="delivery_city" placeholder="Enter delivery city" required />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="delivery_state">State</label>
                            <div class="input-group input-group-lg">
                                <select class="form-control" name="delivery_state" id="delivery_state" style="width: 500px; height: 44px;">
                                    <option value="NSW">New South Wales</option>
                                    <option value="VIC">Victoira</option>
                                    <option value="QLD">Queensland</option>
                                    <option value="WA">Western Australia</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="delivery_postcode">Postcode</label>
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control" name="delivery_postcode" id="delivery_postcode" placeholder="Enter delivery postcode" required />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="unloading_location_type">Unloading Location Type</label>
                            <div class="input-group input-group-lg">
                                <select class="form-control" name="unloading_location_type" id="unloading_location_type" style="width: 500px; height: 44px;">
                                    <option value="b2c">Residential</option>
                                    <option value="b2c">Commercial without unloading facilities</option>
                                    <option value="b2b">Commercial with unloading facilities</option>
                                    <option value="fba">Amazon</option>
                                    <option value="interstate">Interstate</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <br />
                <div class="container" id="other_info_container" style="display: none;">
                    <div class="container sub-header" style="margin-left: -32px;">
                        <h4>4. Documents & Other Information</h4>
                    </div>
                    <h5 style="font-weight: 700;">Upload Files</h5>
                    <div class="container" id="file_container">
                        <div class="row" id="file_input_1">
                            <div class="col-lg-4">
                                <input type="file" class="form-control file_uploader" id="file_1" name="file_1" />
                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-lg-2" style="text-align: left;">
                            <button type="button" class="btn btn-primary add-file" id="add_file_btn" style="margin-left: 14px;">Add File</button>
                        </div>
                        <div class="col-lg-2" style="text-align: center;">
                            <button type="button" class="btn btn-danger remove-file" id="remove_file_btn" style="margin-right: -10px;">Remove File</button>
                        </div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-lg-4">
                            <label for="other_ref">Customer Reference</label>
                            <input type="text" id="other_ref" name="other_ref" />
                        </div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="input-group">
                                <label for="other">Other Needs<span style="color:red">&nbsp;*Extra surcharge may apply</span></label><br />
                                <textarea id="other" name="other" rows="4" cols="100"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6" style="text-align: left;">
                        <button type="button" class="btn btn-warning" id="prev_1" style="display: none;">Prev</button>
                        <button type="button" class="btn btn-warning" id="prev_2" style="display: none;">Prev</button>
                        <button type="button" class="btn btn-warning" id="prev_3" style="display: none;">Prev</button>
                    </div>
                    <div class="col-lg-6" style="text-align: right;">
                        <button type="button" class="btn btn-success" id="next_1" style="margin-right: 64px; display: none;">Next</button>
                        <button type="button" class="btn btn-success" id="next_2" style="margin-right: 64px; display:none;">Next</button>
                        <button type="button" class="btn btn-success" id="next_3" style="margin-right: 64px; display:none;">Next</button>
                        <button type="submit" class="btn btn-success" id="submit_btn" style="margin-right: 64px; display:none;">Submit</button>
                    </div>
                </div>
                <br />
            </form>
        </div>
    </div>
    <br />
    <div class="container result-container" style="margin-left: -1px;">
        <h3 id="success_msg">You have successfully booked cargo delivery.</h3>
        <p style="color: red;">This price is calculated based on the shipment information you provide, if the information is incomplete or inaccurate, extra surcharge fees may be incurred.</p>
        <table id="price">
            <tr>
                <th>Charge Items</th>
                <th>Price</th>
            </tr>
            <tr>
                <td>Base Price</td>
                <td id="base_price"></td>
            </tr>
            <tr>
                <td>Oversize Fee</td>
                <td id="oversize"></td>
            </tr>
            <tr>
                <td>Timed Delivery Fee</td>
                <td id="time_fee"></td>
            </tr>
            <tr>
                <td>Unloading Fee</td>
                <td id="unloading"></td>
            </tr>
            <tr>
                <td>GST</td>
                <td id="gst"></td>
            </tr>
            <tr>
                <td>Total</td>
                <td id="total"></td>
            </tr>
        </table>
    </div>

    <div class="container error-box">
        <h5 id="error_message">Cargo delivery booking unsuccessful, please contact TLA customer service team.</h5>
    </div>

    <script type="text/javascript">
        $(function() {
            var itemCount = 0;
            var fileCount = 1;
            $('#next_1').on('click', function() {
                $('#next_1').hide();
                $('#next_2').show();
                $('#prev_1').show();
                $('#items_container').hide();
                $('#info_container').show();
                $('#category_container').hide();
                $('#add_remove_btn_container').hide();
            });

            $('#next_2').on('click', function() {
                $('#next_2').hide();
                $('#prev_1').hide();
                $('#prev_2').show();
                $('#next_3').show();
                $('#info_container').hide();
                $('#address_container').show();
            });

            $('#next_3').on('click', function() {
                $('#next_3').hide();
                $('#prev_2').hide();
                $('#prev_3').show();
                $('#address_container').hide();
                $('#other_info_container').show();
                $('#submit_btn').show();
            });

            $('#prev_1').on('click', function() {
                $('#prev_1').hide();
                $('#next_2').hide();
                $('#next_1').show();
                $('#info_container').hide();
                $('#items_container').show();
                $('#category_container').show();
                $('#add_remove_btn_container').show();
            });

            $('#prev_2').on('click', function() {
                $('#prev_2').hide();
                $('#prev_1').show();
                $('#next_2').show();
                $('#next_3').hide();
                $('#address_container').hide();
                $('#info_container').show();
            });

            $('#prev_3').on('click', function() {
                $('#prev_3').hide();
                $('#prev_2').show();
                $('#next_3').show();
                $('#address_container').show();
                $('#other_info_container').hide();
                $('#submit_btn').hide();
            });

            $('#item_type').on('change', function() {
                let strHtml = '';
                if ($('#item_type').val() == 'pallets') {
                    $('#item_type').prop('disabled', true);
                    $('#pallet_1').show();
                    $('#next_1').show();
                    $('#add_remove_btn_container').show();
                    itemCount++;
                    $('#item_count').val(itemCount);
                    strHtml += '<div class="container" id="pallet_' + itemCount + '">';
                    strHtml += '<div class="container sub-header">';
                    strHtml += '<h4>Item ' + itemCount + '</h4>';
                    strHtml += '</div><br />';
                    strHtml += '<div class="container form-content-container">';
                    strHtml += '<div class="row">';
                    strHtml += '<div class="col-lg-6">';
                    strHtml += '<label for="pallet_content_' + itemCount + '">Contents On Pallet</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="text" class="form-control" id="pallet_content_' + itemCount + '" name="pallet_content_' + itemCount + '" />';
                    strHtml += '</div></div>';
                    /* strHtml += '<div class="col-lg-4">';
                    strHtml += '<label for="pallet_size_' + itemCount + '">Pallet Size</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<select class="form-control" id="pallet_size_' + itemCount + '" name="pallet_size_' + itemCount + '">';
                    strHtml += '<option value="standard" selected>Standard (116.5cm * 116.5cm)</option>';
                    strHtml += '<option value="Euro1" disabled>Euro (100cm x 120cm)</option>';
                    strHtml += '<option value="Euro2" disabled>Euro (80cm x 120cm)</option>';
                    strHtml += '<option value="non_standard" disabled>Non Standard</option>';
                    strHtml += '</select>';
                    strHtml += '</div></div>'; */
                    strHtml += '<div class="col-lg-6" style="margin-left: -40px;">';
                    strHtml += '<label for="' + itemCount + '">Quantity</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="number" class="form-control pallet-quantity" id="pallet_quantity_' + itemCount + '" name="pallet_quantity_' + itemCount + '" min="0" required />';
                    strHtml += '</div></div></div>';
                    /* strHtml += '<div class="row length-width-container" style="display: none;">';
                    strHtml += '<div class="col-lg-6">';
                    strHtml += '<label for="pallet_length_' + itemCount + '">Length per Pallet</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="number" id="pallet_length_' + itemCount + '" name="pallet_length_' + itemCount + '" min="0" value="116.5" aria-describedby="pallet_length_des" /><span class="input-group-addon" id="pallet_length_des">cm</span>';
                    strHtml += '</div></div></div>';
                    strHtml += '<div class="row length-width-container" style="display: none;">';
                    strHtml += '<div class="col-lg-6">';
                    strHtml += '<label for="pallet_width_' + itemCount + '">Width per Pallet</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="number" id="pallet_width_' + itemCount + '" name="pallet_width_' + itemCount + '" min="0" value="116.5" aria-describedby="pallet_width_des" /><span class="input-group-addon" id="pallet_width_des">cm</span>';
                    strHtml += '</div></div></div>'; */
                    strHtml += '<div class="row">';
                    strHtml += '<div class="col-lg-6">';
                    strHtml += '<label for="pallet_height_' + itemCount + '">Height per Pallet</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="number" class="form-control pallet-height" id="pallet_height_' + itemCount + '" name="pallet_height_' + itemCount + '" min="0" aria-describedby="pallet_height_des" required /><span class="input-group-addon" id="pallet_height_des">cm</span>';
                    strHtml += '</div></div>';
                    strHtml += '<div class="col-lg-6" style="margin-left: -40px;">';
                    strHtml += '<label for="pallet_weight_' + itemCount + '">Weight per Pallet</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="number" class="form-control" id="pallet_weight_' + itemCount + '" name="pallet_weight_' + itemCount + '" min="0" aria-describedby="pallet_weight_des" required /><span class="input-group-addon" id="pallet_weight_des">kg</span>';
                    /* strHtml += '<div class="col-lg-6">';
                    strHtml += '<label for="pallet_cbm_' + itemCount + '">Total Cubic Meters (m<sup>3</sup>)</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="number" class="form-control" id="pallet_cbm_' + itemCount + '" name="pallet_cbm_' + itemCount + '" aria-describedby="pallet_cbm_des" disabled /><span class="input-group-addon" id="pallet_cbm_des">m<sup>3</sup></span>'; */
                    strHtml += '</div></div></div>';
                    strHtml += '</div><br /></div>';
                    $('#items_container').append(strHtml);
                    /* $('#pallet_length_' + itemCount).val(116.5);
                    $('#pallet_width_' + itemCount).val(116.5); */
                } else if ($('#item_type').val() == 'cartons') {
                    $('#item_type').prop('disabled', true);
                    $('#carton_1').show();
                    $('#next_1').show();
                    $('#add_remove_btn_container').show();
                    itemCount++;
                    $('#item_count').val(itemCount);
                    strHtml += '<div class="container" id="carton_' + itemCount + '">';
                    strHtml += '<div class="container sub-header">';
                    strHtml += '<h4>Item ' + itemCount + '</h4>';
                    strHtml += '</div><br />';
                    strHtml += '<div class="container form-content-container">';
                    strHtml += '<div class="row">';
                    strHtml += '<div class="col-lg-6">';
                    strHtml += '<label for="carton_content_' + itemCount + '">Contents In Cartons</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="text" class="form-control" id="carton_content_' + itemCount + '" name="carton_content_' + itemCount + '" />';
                    strHtml += '</div></div>';
                    strHtml += '<div class="col-lg-6">';
                    strHtml += '<label for="carton_quantity_' + itemCount + '">Quantity</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="number" class="form-control" id="carton_quantity_' + itemCount + '" name="carton_quantity_' + itemCount + '" min="0" required />';
                    strHtml += '</div></div></div>';
                    strHtml += '<div class="row">';
                    strHtml += '<div class="col-lg-6">';
                    strHtml += '<label for="carton_length_' + itemCount + '">Length per Carton</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="number" class="form-control" id="carton_length_' + itemCount + '" name="carton_length_' + itemCount + '" min="0" aria-describedby="carton_length_des" required /><span class="input-group-addon" id="carton_length_des">cm</span>';
                    strHtml += '</div></div>';
                    strHtml += '<div class="col-lg-6">';
                    strHtml += '<label for="carton_width_' + itemCount + '">Width per Carton</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="number" class="form-control" id="carton_width_' + itemCount + '" name="carton_width_' + itemCount + '" min="0" aria-describedby="carton_width_des" required /><span class="input-group-addon" id="carton_width_des">cm</span>';
                    strHtml += '</div></div></div>';
                    strHtml += '<div class="row">';
                    strHtml += '<div class="col-lg-6">';
                    strHtml += '<label for="carton_height_' + itemCount + '">Height per Carton</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="number" class="form-control" id="carton_height_' + itemCount + '" name="carton_height_' + itemCount + '" min="0" aria-describedby="carton_height_des" required /><span class="input-group-addon" id="carton_height_des">cm</span>';
                    strHtml += '</div></div>';
                    strHtml += '<div class="col-lg-6">';
                    strHtml += '<label for="carton_weight_' + itemCount + '">Weight per Carton</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="number" class="form-control" id="carton_weight_' + itemCount + '" name="carton_weight_' + itemCount + '" min="0" required />';
                    /* strHtml += '<div class="col-lg-6">';
                    strHtml += '<label for="carton_cbm_' + itemCount + '">Total Cubic Meters(m<sup>3</sup>)</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="number" class="form-control" id="carton_cbm_' + itemCount + '" name="carton_cbm_' + itemCount + '" min="0" aria-describedby="carton_cbm_des" disabled /><span class="input-group-addon" id="carton_cbm_des">m<sup>3</sup></span>'; */
                    strHtml += '</div></div></div>';

                    strHtml += '</div><br /></div>';
                    $('#items_container').append(strHtml);
                }
            });

            $('#add_item').on('click', function() {
                let strHtml = '';
                itemCount++;
                $('#item_count').val(itemCount);
                if ($('#item_type').val() == 'pallets') {
                    strHtml += '<div class="container" id="pallet_' + itemCount + '">';
                    strHtml += '<div class="container sub-header">';
                    strHtml += '<h4>Item ' + itemCount + '</h4>';
                    strHtml += '</div><br />';
                    strHtml += '<div class="container form-content-container">';
                    strHtml += '<div class="row">';
                    strHtml += '<div class="col-lg-6">';
                    strHtml += '<label for="pallet_content_' + itemCount + '">Contents On Pallet</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="text" class="form-control" id="pallet_content_' + itemCount + '" name="pallet_content_' + itemCount + '" />';
                    strHtml += '</div></div>';
                    /* strHtml += '<div class="col-lg-4">';
                    strHtml += '<label for="pallet_size_' + itemCount + '">Pallet Size</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<select class="form-control" id="pallet_size_' + itemCount + '" name="pallet_size_' + itemCount + '">';
                    strHtml += '<option value="standard" selected>Standard (116.5cm * 116.5cm)</option>';
                    strHtml += '<option value="Euro1" disabled>Euro (100cm x 120cm)</option>';
                    strHtml += '<option value="Euro2" disabled>Euro (80cm x 120cm)</option>';
                    strHtml += '<option value="non_standard" disabled>Non Standard</option>';
                    strHtml += '</select>';
                    strHtml += '</div></div>'; */
                    strHtml += '<div class="col-lg-6" style="margin-left: -40px;">';
                    strHtml += '<label for="' + itemCount + '">Quantity</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="number" class="form-control pallet-quantity" id="pallet_quantity_' + itemCount + '" name="pallet_quantity_' + itemCount + '" min="0" required />';
                    strHtml += '</div></div></div>';
                    /* strHtml += '<div class="row length-width-container" style="display: none;">';
                    strHtml += '<div class="col-lg-6">';
                    strHtml += '<label for="pallet_length_' + itemCount + '">Length per Pallet</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="number" id="pallet_length_' + itemCount + '" name="pallet_length_' + itemCount + '" min="0" value="116.5" aria-describedby="pallet_length_des" /><span class="input-group-addon" id="pallet_length_des">cm</span>';
                    strHtml += '</div></div></div>';
                    strHtml += '<div class="row length-width-container" style="display: none;">';
                    strHtml += '<div class="col-lg-6">';
                    strHtml += '<label for="pallet_width_' + itemCount + '">Width per Pallet</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="number" id="pallet_width_' + itemCount + '" name="pallet_width_' + itemCount + '" min="0" value="116.5" aria-describedby="pallet_width_des" /><span class="input-group-addon" id="pallet_width_des">cm</span>';
                    strHtml += '</div></div></div>'; */
                    strHtml += '<div class="row">';
                    strHtml += '<div class="col-lg-6">';
                    strHtml += '<label for="pallet_height_' + itemCount + '">Height per Pallet</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="number" class="form-control pallet-height" id="pallet_height_' + itemCount + '" name="pallet_height_' + itemCount + '" min="0" aria-describedby="pallet_height_des" required /><span class="input-group-addon" id="pallet_height_des">cm</span>';
                    strHtml += '</div></div>';
                    strHtml += '<div class="col-lg-6" style="margin-left: -40px;">';
                    strHtml += '<label for="pallet_weight_' + itemCount + '">Weight per Pallet</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="number" class="form-control" id="pallet_weight_' + itemCount + '" name="pallet_weight_' + itemCount + '" min="0" aria-describedby="pallet_weight_des" required /><span class="input-group-addon" id="pallet_weight_des">kg</span>';
                    /* strHtml += '<div class="col-lg-6">';
                    strHtml += '<label for="pallet_cbm_' + itemCount + '">Total Cubic Meters (m<sup>3</sup>)</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="number" class="form-control" id="pallet_cbm_' + itemCount + '" name="pallet_cbm_' + itemCount + '" aria-describedby="pallet_cbm_des" disabled /><span class="input-group-addon" id="pallet_cbm_des">m<sup>3</sup></span>'; */
                    strHtml += '</div></div></div>';
                    strHtml += '</div><br /></div>';
                    $('#items_container').append(strHtml);
                    $('#pallet_length_' + itemCount).val(116.5);
                    $('#pallet_width_' + itemCount).val(116.5);
                } else if ($('#item_type').val() == 'cartons') {
                    strHtml += '<div class="container" id="carton_' + itemCount + '">';
                    strHtml += '<div class="container sub-header">';
                    strHtml += '<h4>Item ' + itemCount + '</h4>';
                    strHtml += '</div><br />';
                    strHtml += '<div class="container form-content-container">';
                    strHtml += '<div class="row">';
                    strHtml += '<div class="col-lg-6">';
                    strHtml += '<label for="carton_content_' + itemCount + '">Contents In Cartons</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="text" class="form-control" id="carton_content_' + itemCount + '" name="carton_content_' + itemCount + '" />';
                    strHtml += '</div></div>';
                    strHtml += '<div class="col-lg-6">';
                    strHtml += '<label for="carton_quantity_' + itemCount + '">Quantity</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="number" class="form-control" id="carton_quantity_' + itemCount + '" name="carton_quantity_' + itemCount + '" min="0" required />';
                    strHtml += '</div></div></div>';
                    strHtml += '<div class="row">';
                    strHtml += '<div class="col-lg-6">';
                    strHtml += '<label for="carton_length_' + itemCount + '">Length per Carton</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="number" class="form-control" id="carton_length_' + itemCount + '" name="carton_length_' + itemCount + '" min="0" aria-describedby="carton_length_des" required /><span class="input-group-addon" id="carton_length_des">cm</span>';
                    strHtml += '</div></div>';
                    strHtml += '<div class="col-lg-6">';
                    strHtml += '<label for="carton_width_' + itemCount + '">Width per Carton</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="number" class="form-control" id="carton_width_' + itemCount + '" name="carton_width_' + itemCount + '" min="0" aria-describedby="carton_width_des" required /><span class="input-group-addon" id="carton_width_des">cm</span>';
                    strHtml += '</div></div></div>';
                    strHtml += '<div class="row">';
                    strHtml += '<div class="col-lg-6">';
                    strHtml += '<label for="carton_height_' + itemCount + '">Height per Carton</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="number" class="form-control" id="carton_height_' + itemCount + '" name="carton_height_' + itemCount + '" min="0" aria-describedby="carton_height_des" required /><span class="input-group-addon" id="carton_height_des">cm</span>';
                    strHtml += '</div></div>';
                    strHtml += '<div class="col-lg-6">';
                    strHtml += '<label for="carton_weight_' + itemCount + '">Weight per Carton</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="number" class="form-control" id="carton_weight_' + itemCount + '" name="carton_weight_' + itemCount + '" min="0" required />';
                    /* strHtml += '<div class="col-lg-6">';
                    strHtml += '<label for="carton_cbm_' + itemCount + '">Total Cubic Meters(m<sup>3</sup>)</label>';
                    strHtml += '<div class="input-group input-group-lg">';
                    strHtml += '<input type="number" class="form-control" id="carton_cbm_' + itemCount + '" name="carton_cbm_' + itemCount + '" min="0" aria-describedby="carton_cbm_des" disabled /><span class="input-group-addon" id="carton_cbm_des">m<sup>3</sup></span>'; */
                    strHtml += '</div></div></div>';

                    strHtml += '</div><br /></div>';
                    $('#items_container').append(strHtml);
                }
            });

            $('#remove_item').on('click', function() {
                if (itemCount > 1) {
                    if ($('#item_type').val() == 'pallets') {
                        $('#pallet_' + itemCount).remove();
                        itemCount--;
                        $('#item_count').val(itemCount);
                    } else if ($('#item_type').val() == 'cartons') {
                        $('#carton_' + itemCount).remove();
                        itemCount--;
                        $('#item_count').val(itemCount);
                    }
                }
            });

            /* $('.pallet-quantity').on('input', function() {
                for (let i = 1; i <= itemCount; i++) {
                    let cbm = 0.0;
                    let quantity = $('#pallet_quantity_' + i).val();
                    let height = $('#pallet_height_' + i).val();
                    let length = 116.5;
                    let width = 116.5;
                    cbm = (height / 100) * (length / 100) * (width / 100) * quantity;
                    $('#pallet_cbm_' + i).val(cbm);
                }
            });

            $('.pallet-height').on('input', function() {
                for (let i = 1; i <= itemCount; i++) {
                    let cbm = 0.0;
                    let quantity = $('#pallet_quantity_' + i).val();
                    let height = $('#pallet_height_' + i).val();
                    let length = 116.5;
                    let width = 116.5;
                    cbm = (height / 100) * (length / 100) * (width / 100) * quantity;
                    $('#pallet_cbm_' + i).val(cbm);
                }
            }); */

            $('.add-file').on('click', function() {
                fileCount++;
                let strHtml = '<div class="row" id="file_input_' + fileCount + '"><div class="col-lg-4"><input type="file" class="form-control file_uploader" id="file_' + fileCount + '" name="file_' + fileCount + '" /></div></div>';
                $('#file_container').append(strHtml);
                $('#file_count').val(fileCount);
            });

            $('.remove-file').on('click', function() {
                if (fileCount > 1) {
                    $('#file_input_' + fileCount).remove();
                    fileCount--;
                    $('#file_count').val(fileCount);
                }
            });

            $('#submit_btn').on('click', function() {
                $('#items_container').show();
                $('#address_container').show();
                $('#info_container').show();
                $('button#prev_3').hide();
            })

            $('form#request_form').on('submit', function(event) {
                $('#submit_btn').prop('disabled', true);
                $('#loading-container').show();
                event.preventDefault();
                event.stopImmediatePropagation();
                var formData = new FormData();
                formData.append('item_type', $('#item_type').val());
                let items = [];
                if ($('#item_type').val() == 'pallets') {
                    for (let i = 1; i <= itemCount; i++) {
                        let temp = {
                            content: $('#pallet_content_' + i).val(),
                            quantity: $('#pallet_quantity_' + i).val(),
                            length: 116.5,
                            width: 116.5,
                            height: $('#pallet_height_' + i).val(),
                            weight: $('#pallet_weight_' + i).val()
                        };
                        items.push(temp);
                    }
                } else if ($('#item_type').val() == 'cartons') {
                    for (let i = 1; i <= itemCount; i++) {
                        let temp = {
                            content: $('#carton_content_' + i).val(),
                            quantity: $('#carton_quantity_' + i).val(),
                            length: $('#carton_length_' + i).val(),
                            width: $('#carton_width_' + i).val(),
                            height: $('#carton_height_' + i).val(),
                            weight: $('#carton_weight_' + i).val()
                        };
                        items.push(temp);
                    }
                }

                formData.append('items', JSON.stringify(items));
                formData.append('pickup_date_1', $('#pickup_date_1').val());
                formData.append('pickup_date_2', $('#pickup_date_2').val());
                formData.append('pickup_time', $('#pickup_time').val());
                formData.append('delivery_time', $('#delivery_time').val());
                formData.append('collection_name', $('#collection_name').val());
                formData.append('collection_tel', $('#collection_tel').val());
                formData.append('collection_company', $('#collection_company').val());
                formData.append('collection_email', $('#collection_email').val());
                formData.append('collection_address', $('#collection_address').val());
                formData.append('collection_suburb', $('#collection_suburb').val());
                formData.append('collection_city', $('#collection_city').val());
                formData.append('collection_state', $('#collection_state').val());
                formData.append('collection_postcode', $('#collection_postcode').val());
                formData.append('delivery_name', $('#delivery_name').val());
                formData.append('delivery_tel', $('#delivery_tel').val());
                formData.append('delivery_company', $('#delivery_company').val());
                formData.append('delivery_email', $('#delivery_email').val());
                formData.append('delivery_address', $('#delivery_address').val());
                formData.append('delivery_suburb', $('#delivery_suburb').val());
                formData.append('delivery_city', $('#delivery_city').val());
                formData.append('delivery_state', $('#delivery_state').val());
                formData.append('delivery_postcode', $('#delivery_postcode').val());
                formData.append('unloading_location_type', $('#unloading_location_type').val());
                formData.append('other_ref', $('#other_ref').val());
                formData.append('other', $('#other').val());

                $.ajax({
                    url: "<?= $this->createUrl('cargoBooking/calculatePrice'); ?>",
                    type: 'POST',
                    data: formData,
                    enctype: 'multipart/form-data',
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        $("#loading-container").hide();
                        $('#page_container').hide();
                        
                        var response = jQuery.parseJSON(res);
                        if (Number(response.base_price) != 0) {
                            $('td#base_price').html("$ " + (Math.round(response.base_price * 100) / 100).toFixed(2));
                            $('td#oversize').html("$ " + (Math.round(response.oversize_price * 100) / 100).toFixed(2));
                            $('td#unloading').html("$ " + (Math.round(response.unloading_fee * 100) / 100).toFixed(2));
                            $('td#gst').html("$ " + (Math.round(response.gst * 100) / 100).toFixed(2));
                            $('td#total').html("$ " + (Math.round((response.total + response.gst) * 100) / 100).toFixed(2));
                        } else {
                            $('td#base_price').html("TBC");
                            $('td#oversize').html("TBC");
                            $('td#time_fee').html("TBC");
                            $('td#unloading').html("TBC");
                            $('td#gst').html("TBC");
                            $('td#total').html("TBC");
                        }
                        $('.result-container').show();
                        if (response.success) {
                            $('h3#success_msg').show();
                        } else {
                            $('h3#success_msg').hide();
                            $('.error-box').show();
                        }
                    }
                })
            });
        });
    </script>
</body>