<style>
.load{
            width: 80px;
            height: 40px;
            margin: 0 auto;
            margin-top:100px;
        }
        .load span{
            display: inline-block;
            width: 8px;
            height: 100%;
            border-radius: 4px;
            background: lightgreen;
            -webkit-animation: load 1s ease infinite;
        }
        @-webkit-keyframes load{
            0%,100%{
                height: 40px;
                background: lightgreen;
            }
            50%{
                height: 70px;
                margin: -15px 0;
                background: lightblue;
            }
        }
        .load span:nth-child(2){
            -webkit-animation-delay:0.2s;
        }
        .load span:nth-child(3){
            -webkit-animation-delay:0.4s;
        }
        .load span:nth-child(4){
            -webkit-animation-delay:0.6s;
        }
        .load span:nth-child(5){
            -webkit-animation-delay:0.8s;
        }

.container {
    padding-right: 15px;
    padding-left: 15px;
    margin-right: auto;
    margin-left: auto
}


@media (min-width: 1200px) {
    .container {
        width: 1600px;
    }
}
.grid-container {
    overflow: auto;
    max-height: 800px; /* Set a maximum height for the container to enable scrolling */
}

.grid-container .table thead {
    position: sticky;
    top: 0;
    background-color: #f2f2f2; /* Adjust the background color of the fixed header */
    z-index: 1; /* Ensure the header stays above the content */
}

</style>

<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
    'homeLink' => CHtml::link('Home', array('site/index/org_id/' . Yii::app()->session['org_id'])),
    'links' => array(
        'Reports',
    ),
));
?>
<br>

<div class="form">
    <?php
$form=$this->beginWidget('CActiveForm', array(
    'id'=>'report_consol_form',
)); ?>
    <div class="row">
        <div class="col col-md-2 col-sm-4">
            <div class="form-group">
                <?php echo CHtml::label('ETA From', 'ETA From'); ?>
                <?php echo CHtml::textField('from', date("Y-m-d",strtotime("-3 day")), array('size' => 12, 'id' => 'from', 'class' => 'datetime_input form-control')); ?>
            </div>
        </div>
        <div class="col col-md-2 col-sm-4">
            <div class="form-group">
                <?php echo CHtml::label('ETA To', 'ETA To'); ?>
                <?php echo CHtml::textField('to', date("Y-m-d"), array('size' => 12, 'id' => 'to', 'class' => 'datetime_input form-control')); ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col col-md-2 col-sm-4">
            <div class="form-group">
                <?php echo CHtml::label('Air/Sea', 'Air/Sea'); ?>
                <?php echo CHtml::dropDownList('air_sea','',[''=>'ALL',10=>'Air',20=>'Sea'],['class' => 'form-control']); ?>
            </div>
        </div>
        <div class="col col-md-2 col-sm-4">
            <div class="form-group">
                <?php echo CHtml::label('By State', 'By State'); ?>
                <?php
                $dicPort2State = [
                    ''=>'ALL',
                    'AUSYD'=>'NSW',
                    'AUMEL'=>'VIC',
                    'AUBNE'=>'QLD',
                    'AUPER'=>'WA',
                    'AUADL'=>'SA',
                ];
                echo CHtml::dropDownList('state','',$dicPort2State,['class' => 'form-control']);
            ?>
            </div>
        </div>
        <div class="col col-md-2 col-sm-4">
            <div class="form-group">
                <?php echo CHtml::label('By Accumulative Delay', 'By Accumulative Delay'); ?>
                <?php echo CHtml::dropDownList('by_urgent','des',['des'=>'More to Less','asc'=>'Less to More'],['class' => 'form-control']); ?>
            </div>
        </div>

    </div>

    <div class="row">
        
     <div class="col col-md-2 col-sm-4">
        <div class="form-group">
            <label for="awb-or-container-id">AWB OR Container No:</label>
            <input type="text" id="awb-or-container-id" name="awb_or_container_id" class="form-control">
        </div>
     </div>

    </div>



    <?php $this->endWidget(); ?>

</div><!-- form -->
<input class="btn btn-primary" id="btn_search" type="submit" value="Search" onclick="funcSearch()">
<input class="btn btn-primary" id="btn_export" type="submit" value="export" onclick="funcExport()">

<br />
<br />


<h1>Consol Report</h1>
<div class="grid-container">
<table class="table table-striped table-bordered">
    <thead>
        <tr>
 
            <th>State</th>
            <th>Air/Sea</th>
            <th>awb/Ocean Bill</th>
            <th>ETD</th>
            <th>ETA</th>
            <th>Checkin</th>
            <th>Arrived at Airport</th>
            <th>Collected</th>
            <th>Arrived at TLA</th>            
            <th>Unloaded at TLA</th>
            
            <th>Unpack</th>
            <th>Despatch</th>
        </tr>
    </thead>
    <tbody id="table_records">
        <?php

            echo $strTbody;
            ?>

    </tbody>
</table>
</div>



<div class="load" id='div_loading' style="display: none;">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
</div>


<script type="text/javascript">
function funcSearch() {
    $('#table_records').html('');
    $('#btn_search').attr('disabled', true);
    $('#div_loading').attr('style', '');
    listData = $('#report_consol_form').serializeArray();
    $.ajax({
            url: "<?=$this->createUrl("/reports/consolReportSearch");?>",
            data: listData,
            async: true,
            success:(data)=>{
                $('#table_records').html(data);
                
            },
            complete: function() {
            $('#btn_search').attr('disabled', false);
            $('#div_loading').attr('style', 'display:none');
            }
        });
    
}

function funcExport()
{
    var $form = $('#report_consol_form');

    // Serialize the form data
    var formData = $form.serialize();

    window.open("<?=$this->createUrl("/reports/consolReportExport");?>"+ '?' + formData, "_blank");
}
</script>