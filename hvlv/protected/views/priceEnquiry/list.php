<head>
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

        .hidden-block {
            display: none;
        }
    </style>
</head>

<body>
    <h1><?= $this->t('Price Enquiry'); ?></h1>

    <div class="container" id="price_enquiry_summary" style="display:block;">
        <a href="#" id="expand_price_enquiry_summary_description"> + Description</a>
        <div class="container hidden-block" id="price_enquiry_description_container">
            <p>Only status in 'active', 'cannot do', or 'used' are counted as completed.</p>
            <p>Cancelled records will not be used in KPI calculation.</p>
        </div>
        <table class="price-enquiry-report-table">
            <tr>
                <th>Today New</th>
                <th>Today Complete</th>
                <th>MTD Complete %</th>
            </tr>
            <tr>
                <td><?= $todayNew ?></td>
                <td><?= $todayComplete ?></td>
                <td><?= $completePercentage . "% (" . $mtdComplete . "/" . $mtdNew . ")"?></td>
            </tr>
        </table>
    </div>
    <?php
    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'price_enquiry_grid',
        'cssFile' => false,
        'dataProvider' => $model->search(),
        'filter' => $model,
        'columns' => array(
            'date',
            array('header' => 'Quotation No.', 'name' => 'code'),
            'address',
            'suburb',
            'postcode',
            ['header' => 'Total Weight', 'name' => 'weight'],
            ['header' => 'Total Quantity', 'name' => 'quantity'],
            ['header' => 'Org', 'name' => 'org_id'],
            ['header' => 'Enquiry From', 'name' => 'name'],
            ['header' => 'Tel', 'name' => 'tel'],
            ['header' => 'Email', 'name' => 'email'],
            ['header' => 'Customer Notes', 'name' => 'note'],
            'paid_by',
            ['header' => 'Memo', 'name' => 'memo'],
            'price',
            ['name' => 'depot', 'value' => 'PriceEnquiry::listDepot[$data->depot]', 'filter' => CHtml::dropDownList('PriceEnquiry[depot]', $model->depot, $this->t(['' => 'All'] + PriceEnquiry::listDepot)),],
            ['name' => 'status', 'value' => 'PriceEnquiry::listStatus[$data->status]', 'filter' => CHtml::dropDownList('PriceEnquiry[status]', $model->status, $this->t(['' => 'All'] + PriceEnquiry::listStatus)),],
            [
                'class' => 'oButtonColumn',
                'template' => '{update}',
                'buttons' => [
                    'update' => [
                        'imageUrl' => false,
                        'visible' => 'true',
                        'url' => 'Yii::app()->createUrl("priceEnquiry/edit", ["id" => $data->id])',
                        'options' => array('class' => 'jqm_link grid_edit_btn', 'label' => $this->t('Update'), 'title' => '$data->id'),
                    ],
                ],
            ],
        ),
    ));
    ?>
    <script type="text/javascript">
        $(function() {
            $('a#expand_price_enquiry_summary_description').on('click', function(e) {
                e.preventDefault();
                if ($('#price_enquiry_description_container').hasClass('hidden-block')) {
                    $('#price_enquiry_description_container').removeClass('hidden-block');
                } else {
                    $('#price_enquiry_description_container').addClass('hidden-block');
                }
            })
        })
    </script>
</body>