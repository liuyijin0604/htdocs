<style type="text/css">
    .cargoNew {
        color: #DAA569;
    }

    .cargoClose {
        color: #2E8B57;
    }

    .cargoLeft {
        color: #FF4500;
    }

    .hidden-block {
        display: none;
    }
</style>

<?php
$model = new Dash();
$filtersForm = new FiltersForm;
if (isset($_GET['FiltersForm'])) {
    $filtersForm->filters = $_GET['FiltersForm'];
}

$monthProvide = $model->getTldReport(811);
$report = ReportCache::model()->findByAttributes(array('type' => ReportCache::TLD_REPORT, 'status' => ReportCache::Active));

if (empty($report)) {
    $periodToday = date("m-d");
} else {
    $periodToday = date("m-d", strtotime($report->modify_time));
}

$filteredDataMonth = $filtersForm->filter($monthProvide);
$monthProvide = new CArrayDataProvider($filteredDataMonth);
$monthProvide->pagination = ['pageSize' => 100,];

?>
<a href="#" id="per_tld_kpi_report_description"> + Description</a>
<div class="container hidden-block" id="per_tld_kpi_description_container">
    <p>This report data will be updated at 18:00 every day.</p>
</div>
<div class="row">
    <?php
    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'dashboard-wid-cargo-booking-grid_per',
        'cssFile' => false,
        'dataProvider' => $monthProvide,
        'summaryText' => '',
        'enablePagination' => true,
        'pager' => array(
            'prevPageLabel' => 'Prev.',
            'maxButtonCount' => 5,
        ),
        'columns' => array(
            array('header' => $periodToday, 'type' => 'raw', 'value' => '$data["col"]'),
            array('header' => 'Today New', 'type' => 'raw', 'value' => '$data["todayNew"]', 'htmlOptions' => array('class' => 'cargoNew')),
            array('header' => 'Today Complete', 'type' => 'raw', 'value' => '$data["todayComplete"]', 'htmlOptions' => array('class' => 'cargoClose')),
            //array('header' => 'Today Left', 'type' => 'raw', 'value' => '$data["todayLeft"]', 'htmlOptions' => array('class' => 'cargoLeft')),
            //array('header' => 'Today %', 'type' => 'raw', 'value' => '($data["todayNew"] + $data["todayLeft"] == 0) ? "N/A" : number_format(($data["todayComplete"] / ($data["todayNew"] + $data["todayLeft"]))*100, 2, ".", "")'),
            array('header' => 'MTD New', 'type' => 'raw', 'value' => '$data["newMTD"]', 'htmlOptions' => array('class' => 'cargoNew')),
            array('header' => 'MTD Complete', 'type' => 'raw', 'value' => '$data["completeMTD"]', 'htmlOptions' => array('class' => 'cargoClose')),
            //array('header' => 'MTD Left', 'type' => 'raw', 'value' => '$data["leftMTD"]', 'htmlOptions' => array('class' => 'cargoLeft')),
            array('header' => 'MTD %', 'type' => 'raw', 'value' => '($data["newMTD"] + $data["leftMTD"] == 0) ? "N/A" : number_format(($data["completeMTD"] / ($data["newMTD"] + $data["leftMTD"]))*100, 2, ".", "")'),
        ),
    ));
    ?>
</div>

<script type="text/javascript">
    $(function() {
        $('a#per_tld_kpi_report_description').on('click', function(e) {
            e.preventDefault();
            if ($('#per_tld_kpi_description_container').hasClass('hidden-block')) {
                $('#per_tld_kpi_description_container').removeClass('hidden-block');
            } else {
                $('#per_tld_kpi_description_container').addClass('hidden-block');
            }
        })
    })
</script>