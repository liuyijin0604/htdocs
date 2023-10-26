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

</style>

<div class="row grid-view">

    <div class="form">
        <?php $form = $this->beginWidget('CActiveForm', array(
			'id' => 'report_consol_form',
			// 'enableAjaxValidation' => false,
		));
		?>

        <div class="row rowcol">
            <?php echo CHtml::label('ETA From', 'ETA From'); ?>
            <?php echo CHtml::textField('from', date("Y-m-d",strtotime("-3 day")), array('size' => 12, 'id' => 'from', 'class' => 'date_input')); ?>
        </div>
        <div class="row rowcol">
            <?php echo CHtml::label('ETA To', 'ETA To'); ?>
            <?php echo CHtml::textField('to', date("Y-m-d"), array('size' => 12, 'id' => 'to', 'class' => 'date_input')); ?>
        </div>
        <br />
        <!-- <h5>Status</h5>
        <?php
		foreach (ImcoConsol::$states as $code => $status) {
			$strHtml = '<div class="row rowcol"><input checked="checked" type="CheckBox" name="' . $status . '" value="' . $code . '" />' . $status . '</div>';
			if ($code == ImcoConsol::Status_Cancelled) {
				$strHtml = '<div class="row rowcol"><input type="CheckBox" name="' . $status . '" value="' . $code . '" />' . $status . '</div>';
			}
			echo $strHtml;
		}
		?> -->
        <br />
        <h5>Air/Sea</h5>
        <?php
		foreach (ImcoConsol::$services as $code => $service) {
			$strHtml = '<div class="row rowcol"><input checked="checked" type="CheckBox" name="' . $service . '" value="' . $code . '" />' . $service . '</div>';
			echo $strHtml;
		}
		?>
        <br />
        <div class="row rowcol">
            <?php echo CHtml::label('Customer', 'Customer'); ?>
            <!-- <?php echo CHtml::textField('customer', ''); ?> -->
            <?php
			$this->widget('zii.widgets.jui.CJuiAutoComplete', [
				'name' => 'customer',
				'sourceUrl' => ['org/ACRSuggest'],
				'value' =>'',
				'options' => [
					'showAnim' => 'fold',
					'minLength' => 2,
					'delay' => 200,
					'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).parent().parent().find("#mdata_owner_id").val(ui.item["value"]); return false; }',
					'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val(""); return false; }',
				],
				'htmlOptions' => [
					'size' => '50',
				],
			]);
			?>
        </div>
        <div class="row rowcol">
            <?php echo CHtml::label('By State', 'By State'); ?>
            <?php
				$dicPort2State = [
                    ''=>'ALL',
					'AUSYD'=>'NSW',
					'AUMEL'=>'VIC',
					'AUBNE'=>'QLD',
					'AUPER'=>'WA',
					'AUADL'=>'SA',
					// 'AUFRE'=>'AUFRE',
					// 'AUOOL'=>'AUOOL',
					// 'EGCAI'=>'EGCAI',
					// 'KRPUS'=>'KRPUS',
					// 'NZAKL'=>'NZAKL',
					// 'THBKK'=>'THBKK',
					// 'USEWR'=>'USEWR',
					// 'USSFO'=>'USSFO',
				];
				echo CHtml::dropDownList('state','',$dicPort2State);
			?>
        </div>
        <div class="row rowcol">
            <?php echo CHtml::label('By Urgent', 'By Urgent'); ?>
            <!-- <?php echo CHtml::dropDownList('by_urgent','des',['des'=>'Descend','asc'=>'Ascend']); ?> -->
            <?php echo CHtml::dropDownList('by_urgent','des',['des'=>'Descend','asc'=>'Ascend']); ?>
        </div>

        <br />
        <div class="row rowcol">
            <?php echo CHtml::label('Consol No.', 'Consol No.'); ?>
            <?php echo CHtml::textField('consol_no', ''); ?>
        </div>

        <div class="row buttons">
            <input id="btn_search" type="button" value="Search" onclick="funcSearch()" />
        </div>

        <?php $this->endWidget(); ?>
    </div>
    <br />

    <!-- <div id='div_loading' class="grid-view grid-view-loading" style="display: none;"></div> -->
    <h1>Consol Report</h1>
    <table class="items">
        <thead>
            <tr>
                <th>Customer</th>
                <th>No.</th>
                <!-- <th>Status</th> -->
                <th>State</th>
                <th>Air/Sea</th>
                <th>awb/Ocean Bill</th>
                <th>Airline/Vessel</th>
                <th>ETD</th>
                <th>ETA</th>
                <th>Discharge</th>
                <th>Pickup</th>
                <th>Unpack</th>
            </tr>
        </thead>
        <tbody id="table_records">
            <?php
			// include('report_consol_table_content.php');
            echo $strTbody;
			?>

        </tbody>
    </table>
    <div class="load" id='div_loading' style="display: none;">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
    </div>
</div>


<script type="text/javascript">
function funcSearch() {
    $('#table_records').html('');
    $('#btn_search').attr('disabled', true);
    $('#div_loading').attr('style', '');
    listData = $('#report_consol_form').serializeArray();
    $.ajax({
            url: "/imcoConsol/reportSearch",
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
</script>