<head>
    <style>
        #price_enquiry_management .type_row {
            color: rgb(119, 119, 218);
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }

        .flex-container {
            display: flex;
        }

        .flex-child {
            flex: 1;
        }

        .price-enquiry-report-table {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        .price-enquiry-report-table td,
        .price-enquiry-report-table th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .price-enquiry-report-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .price-enquiry-report-table tr:hover {
            background-color: #ddd;
        }

        .price-enquiry-report-table th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #04AA6D;
            color: white;
        }
    </style>
</head>

<body>
    <div class="pane" id="price_enquiry_management">
        <?php
        if ($type == 1) {
            $strTitle = "Price Enquiry";
            $strUrl = "priceEnquiry/list";
            $strId = "_price_enquiry";
        }
        ?>
        <h1>
            <?php echo $strTitle ?>
        </h1>
        <div class="list-group flex-container">
            <div class="flex-child" id="price_enquiry_manage_dp_view" style="max-width:200px;">
                <?php
                $provide = [];
                $provide[] = ['id' => 106, 'Depot' => 'Sydney'];
                $provide[] = ['id' => 218, 'Depot' => 'Melbourne'];
                $provide[] = ['id' => 530, 'Depot' => 'Brisbane'];
                $provide[] = ['id' => 811, 'Depot' => 'Perth'];
                $dataprovider = new CArrayDataProvider($provide);
                $dataprovider->pagination = false;
                $this->widget(
                    'zii.widgets.grid.CGridView',
                    array(
                        'id' => 'price_enquiry_main_menu' . $_GET['tabid'],
                        'cssFile' => false,
                        'dataProvider' => $dataprovider,
                        'columns' => array(
                            array(
                                'name' => 'id',
                                'headerHtmlOptions' => array('style' => 'display:none'),
                                'filterHtmlOptions' => array('style' => 'display:none'),
                                'htmlOptions' => array('style' => 'display:none')
                            ),
                            array(
                                'name' => 'Depot',
                                'cssClassExpression' => '"type_row"',
                                'headerHtmlOptions' => array(
                                    'style' => 'text-align: center;
                                font-size: 20px;
                                font-weight: bold;'
                                )
                            )
                        ),
                    )
                ); ?>
            </div>
        </div>
        <br />
        <div id='price_enquiry_manage_dp_view_type<?php echo $strId ?>'>

        </div>
    </div>
    <script>
        $(function() {
            var tab = $('#<?= $_GET['tabid'] ?>');
            var panel = tab.data('panel');
            $('.summary', panel).html('');
            $("#price_enquiry_manage_dp_view", panel).on('click', "table tbody td", function() {
                var delivery_type = parseInt($(this).parent().children(':nth-child(1)').html());
                var data = {};
                data['depot'] = delivery_type;
                $.ajax({
                    type: 'GET',
                    url: '<?php echo Yii::app()->createAbsoluteUrl($strUrl, array('tabid' => $_GET['tabid'])); ?>',
                    data: data,
                    dataType: 'html',
                    success: function(resp) {
                        $('#price_enquiry_manage_dp_view_type<?php echo $strId ?>').html(resp);
                    },
                });
        });
    });
    </script>

</body>