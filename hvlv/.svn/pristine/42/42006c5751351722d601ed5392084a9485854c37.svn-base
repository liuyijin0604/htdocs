<head>

    <!-- <script type="text/javascript" src="https://cdn.canvasjs.com/jquery.canvasjs.min.js"></script> -->
    <style>
        #consol_managment .type_row {
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

        .pallet-request-report-table {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        .pallet-request-report-table td,
        .pallet-request-report-table th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .pallet-request-report-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .pallet-request-report-table tr:hover {
            background-color: #ddd;
        }

        .pallet-request-report-table th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #04AA6D;
            color: white;
        }
    </style>
</head>

<body>
    <div class="pane" id="consol_managment">
        <?php
        if ($type == 1) {
            $strTitle = "Pallet Delivery Request";
            $strUrl = "cargoProcessPlan/list";
            $strId = "_cargo_process_plan";
        }
        ?>
        <h1>
            <?php echo $strTitle ?>
        </h1>
        <div class="list-group flex-container">
            <div class="flex-child" id="consol_manage_dp_view" style="max-width:200px;">
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
                        'id' => 'consol_main_menu' . $_GET['tabid'],
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
            <!-- <div class="flex-child" style="padding-left: 100px;">
                <div id="chartContainer" style="height: 370px; width: 50%;"></div>
            </div> -->
            <!-- <div class="flex-child" style="padding-left: 100px;">
                <div class="container" id="pallet_request_summary" style="display:block;">
                </div>
            </div> -->
        </div>
        <br />
        <div id='consol_manage_dp_view_type<?php echo $strId ?>'>

        </div>
    </div>
    <script>
        $(function() {
            var tab = $('#<?= $_GET['tabid'] ?>');
            var panel = tab.data('panel');
            $('.summary', panel).html('');
            $("#consol_manage_dp_view", panel).on('click', "table tbody td", function() {
                var delivery_type = parseInt($(this).parent().children(':nth-child(1)').html());
                var data = {};
                data['depot'] = delivery_type;
                //console.log(data);
                $.ajax({
                    type: 'GET',
                    url: '<?php echo Yii::app()->createAbsoluteUrl($strUrl, array('tabid' => $_GET['tabid'])); ?>',
                    data: data,
                    dataType: 'html',
                    success: function(resp) {
                        $('#consol_manage_dp_view_type<?php echo $strId ?>').html(resp);
                    },
                });

                /* $.ajax({
                    type: 'GET',
                    url: '<?= $this->createUrl("cargoProcessPlan/cargoProcessPlanManagement") ?>',
                    data: data,
                    dataType: 'html',
                    success: function(response) {
                        let res = jQuery.parseJSON(response);
                        $('#pallet_request_summary').empty();
                        let strHtml = '';
                        strHtml += '<table class="pallet-request-report-table" id="pallet_request_report_table_' + delivery_type +'">';
                        strHtml += '<tr><th>Today New</th><th>Today Complete</th><th>This Week Complete %</th><th>MTD Complete %</th></tr>';
                        strHtml += '<tr>';
                        strHtml += '<td>' + res.todayNew + '</td>';
                        strHtml += '<td>' + res.todayComplete + '</td>';
                        strHtml += '<td>' + (Math.round((res.thisWeekComplete / 0) * 100) * 100 / 100).toFixed(2) + '% (' + res.thisWeekNew + '/' + res.thisWeekComplete + ')</td>';
                        //strHtml += '<td>' + (Math.round((1 / 3) * 100 * 100) / 100).toFixed(2) + '% (' + 1 + '/' + 3 + ')</td>';
                        strHtml += '<td>' + res.mtdNew + '</td>';
                        strHtml += '</tr>';
                        strHtml += '</table>';
                        $('#pallet_request_summary').append(strHtml);
                    }
                }) */
            });

            /* var options = {
                title: {
                    text: "Deconsolidation Record"
                },
                data: [{
                    type: "pie",
                    startAngle: 45,
                    showInLegend: "true",
                    legendText: "{label}",
                    indexLabel: "{label} ({y})",
                    yValueFormatString: "#,##0.#" % "",
                    dataPoints: [{
                            label: "New",
                            y: 36
                        },
                        {
                            label: "Warehouse Processing",
                            y: 31
                        },
                        {
                            label: "Complete",
                            y: 7
                        },
                    ]
                }]
            };
            $("#chartContainer").CanvasJSChart(options); */
        });
    </script>

</body>