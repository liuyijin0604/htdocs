<div class="form" style="position:relative">

<?php
    // get zone rate based on org id
    $orgRate = OrgRate::getOrgZoneRate($model->id);
    $zoneRate = new ZoneRate();
?>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'org-zone-rate-form',
	'enableAjaxValidation'=>false,
));

?>

	<?php echo $form->hiddenField($model,'id'); ?>
    <?php echo CHtml::hiddenField('rate_id',$orgRate->id ); ?>

	<div class="row rowcol rowleft" style="margin-bottom: 20px;">
        <?php echo   CHtml::label( 'Org: ' . $model->name,'forrate'); ?>
	</div>

    <div class="row" >
        <div class="col" style="width: 48%; margin-right: 10px;">
            <span> <h2> Weight Range (Unit kg) </h2></span>

            <div id="zone-rate-weight-range-grid" class="grid-view editableGrid">
                <table class="items">
                    <thead>
                    <tr>
                        <th id="zone-rate-weight-range-grid_c0">From</th><th id="zone-rate-weight-range-grid_c1">To</th><th class="button-column" id="zone-rate-weight-range-grid_c2">&nbsp;</th></tr>
                    </thead>
                    <tfoot>
                    <tr><td><input style="width:100%" id="zone-rate-weight-range-grid_weight_lo" name="ZoneRate[weight_lo]" type="text" maxlength="10"></td><td><input style="width:100%" id="zone-rate-weight-range-grid_weight_hi" name="ZoneRate[weight_hi]" type="text" maxlength="10"></td><td><a href="" class="save_btn add_btn" title="Add">Add</a></td></tr></tfoot>
                    <tbody>
                    <tr><td colspan="3" class="empty"><span class="empty">No results found.</span></td></tr>
                    </tbody>
                </table>

            </div>
<?php
/*
            $this->widget('application.extensions.editablegrid.CEditableGridView', array(
            'id'=>'zone-rate-weight-range-grid',
            'cssFile' => false,
            'dataProvider' => $zoneRate->getZoneRateWeightRange(-1),
            'formUrl' => $this->createUrl('invoice/linesGrid', array('id'=>empty($model->id)? 0 : $model->id)),
            'summaryText' => '',
            'afterSave' => "function(r){
            if(r.done == true){
            myApp.notice(r.msg, 5000);
            }else{
            myApp.alert(r.msg, false);
            }
            return r.done;
            }",
            'columns'=>array(
            array('header' => 'From','name' => 'weight_lo', 'class' => 'CEditableColumn'),
            array('header' => 'To','name' => 'weight_hi', 'class' => 'CEditableColumn'),
            array('class'=>'CEditableButtonColumn', 'template' => '{save}')
            ),
            ));
*/
 ?>

        </div>
        <div class="col" style="width: 50%;">
            <span> <h2> Zone Rate <span id="zone-rate-weight-span" style="width:20px;height:20px;font-size: 12px;">(0kg - 0kg)</span> </h2></span>

            <div id="org-zone-rate-view">
                <div class="grid-view">
                    <table class="items">
                        <thead>
                        <tr>
                            <th id="gp-imco-consol-grid_c0">Code</th>
                            <th id="gp-imco-consol-grid_c1">Name</th>
                            <th id="gp-imco-consol-grid_c2">Price / Piece</th>
                            <th id="gp-imco-consol-grid_c3">Price / kg</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                  </div>
            </div>
        </div>
    </div>


	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
    $(document).ready(function(e){

    var win = $('#jqmw_<?=$_GET["tabid"];?>');
    // current zone rate data
    var curZoneRateData = <?php echo json_encode($zoneRate->getZoneRateWeightRangeByArray($orgRate->id)); ?>;

    // set default zone rate data
    var zoneRateData = <?php echo json_encode(ZoneMap::getOrgZoneMap($cid,$orgRate->zone_id) ); ?>;
    var allZoneRateData = [];
    var allRowIndex = 0;
    var selectedRowIndex = 0;
    initZoneRateData(zoneRateData);
    appendCurZoneRateData(curZoneRateData);

    function appendCurZoneRateData(rateData){

        $('#zone-rate-weight-range-grid .items tbody td.empty', win).parent().remove();
        var wtpl = $('#zone-rate-weight-range-grid .items tfoot', win);
        var row = wtpl.find('tr').clone();
        for ( var i in rateData ) {
            allRowIndex++;
            var r = wtpl.find('tr').clone();
            $('.add_btn',r).replaceWith('<a href="javascript:;" data-index="'+allRowIndex+'"class="delete_btn" title="Remove">Remove</a>');
            $('input', r).each(function(){
                var n = $(this).attr('name');
                $(this).attr('name', n+'[]');
            });
            $('#zone-rate-weight-range-grid .items tbody', win).append(r);
            r.find('#zone-rate-weight-range-grid_weight_lo').val(rateData[i]['weight_lo']);
            r.find('#zone-rate-weight-range-grid_weight_hi').val(rateData[i]['weight_hi']);
            addOneZoneRateByData(allRowIndex,rateData[i]['data']);
        }
    }

     function addOneZoneRateByData(tag,rateData){
         var bFound = false;
         for ( var i in allZoneRateData ) {
             var oneZone = allZoneRateData[i];
             if ( oneZone['tag'] == tag ) {
                 bFound = true;
             }
         }
         if ( !bFound ) {
             var zoneData = {};
             zoneData['tag'] = tag;
             zoneData['lo'] = 0;
             zoneData['hi'] = 0;
             zoneData['data'] = rateData;
             allZoneRateData.push(zoneData);
         }
     }

     function addOneZoneRate(tag){
        // check existing or not
        // if not existing just create a new one
        var bFound = false;
        for ( var i in allZoneRateData ) {
            var oneZone = allZoneRateData[i];
            if ( oneZone['tag'] == tag ) {
                bFound = true;
            }
        }
        if ( !bFound ) {
            var zoneData = {};
            zoneData['tag'] = tag;
            zoneData['lo'] = 0;
            zoneData['hi'] = 0;
            zoneData['data'] = JSON.parse(JSON.stringify(zoneRateData));
            allZoneRateData.push(zoneData);
        }
    }

    function updateLoHi(dataIndex, lo, hi){
        // get related zone rate data
        for ( var i in allZoneRateData ) {
            var oneZone = allZoneRateData[i];
            if ( oneZone['tag'] == dataIndex ) {
                oneZone['lo'] = lo;
                oneZone['hi'] = hi;
                break;
            }
        }
    }

    function refreshZoneRate(tag){
        // get related zone rate data
        var bFound = false;
        var oneZoneRate = [];
        for ( var i in allZoneRateData ) {
            var oneZone = allZoneRateData[i];
            if ( oneZone['tag'] == tag ) {
                bFound = true;
                oneZoneRate = oneZone['data'];
                break;
            }
        }
        if ( bFound ) {
            selectedRowIndex = tag;
            var zoneBody = $('#org-zone-rate-view tbody', win);
            zoneBody.empty();
            fillZoneRateData(oneZoneRate);
        }
    }

    function fillZoneRateData(rateData){
        var zoneBody = $('#org-zone-rate-view tbody', win);
        var index = 0;
        for (var i in rateData ) {
            var detail = rateData[i];
            var rowData = '<tr class="odd">';
            if ( index++ % 2 == 0  ) {
                rowData = '<tr class="even">';
            }
            rowData += '<td class="show-details"><input type="hidden" name="id" value="">';
            rowData += '<a href="javascript:;" class="tab_link" title="ZoneCode">' + detail['code']+ '</a></td>';
            rowData += '<td>' + detail['name']+ '</td>';
            rowData += '<td><input name="ppc[]" data-code="'+detail['code']+'" type="text" value="' + detail['ppc']+ '"></td>';
            rowData += '<td><input name="pkg[]" data-code="'+detail['code']+'"type="text" value="' + detail['pkg']+ '"></td></tr>';

            zoneBody.append(rowData);
        }
    }

    function initZoneRateData(rateData){
        fillZoneRateData(rateData);
    }

    function validWeightFrom($fromWeight){
        var lo =  parseFloat($fromWeight.val());
        var hi =  parseFloat($fromWeight.parent().parent().find('#zone-rate-weight-range-grid_weight_hi').val());
        if ( !isNaN(lo) && !isNaN(hi) ) {
            if ( lo > hi ) {
                alert( "low weight must be less than high weight");
                return false;
            }
        }
        return true;
    }

    function validWeightTo($toWeight){
        var hi =  parseFloat($toWeight.val());
        var lo =  parseFloat($toWeight.parent().parent().find('#zone-rate-weight-range-grid_weight_lo').val());
        if ( !isNaN(lo) && !isNaN(hi) ) {
            if ( lo > hi ) {
                alert( "low weight must be less than high weight");
                return false;
            }
        }
        return true;
    }

    function refreshSelectedWeightRangeByDest(destRange){
        if ( typeof destRange != 'undefined' ) {
            var lo = destRange.find('#zone-rate-weight-range-grid_weight_lo').val();
            var hi = destRange.find('#zone-rate-weight-range-grid_weight_hi').val();
            var wrangeStr = '(' + lo + 'kg - ' + hi + 'kg)';
            $('#zone-rate-weight-span', win).html(wrangeStr);
        }
    }


	$('form#org-zone-rate-form', win).on('success', function(e, r){
	//	win.data('opener').trigger('reload_contact_grid');
		win.jqmHide();
	});


    $('#zone-rate-weight-range-grid .add_btn', win).on('click', function(e){

        e.preventDefault();


        allRowIndex++;
        $('#zone-rate-weight-range-grid .items tbody td.empty', win).parent().remove();
        var r = $(this).parents('tr').clone();
        $('.add_btn',r).replaceWith('<a href="javascript:;" data-index="'+allRowIndex+'"class="delete_btn" title="Remove">Remove</a>');
        $('input', r).each(function(){
            var n = $(this).attr('name');
            $(this).attr('name', n+'[]');
        });
        $('#zone-rate-weight-range-grid .items tbody').append(r);
        $(this).parents('tr').find('input').val('');

        addOneZoneRate(allRowIndex);

        return false;
    });

    $('#zone-rate-weight-range-grid', win).on('click','tr',function(e){
        // exclude the dummy row data
        if ( $(this).parent().is("tfoot") ) return;

        // get related zone data related tag
        var tag = $(this).find('.delete_btn').data('index');
        refreshZoneRate(tag);
        refreshSelectedWeightRangeByDest($(this));

    });

    $('#zone-rate-weight-range-grid', win).on('click','.delete_btn',function(e){
        e.preventDefault();
        if ( confirm( 'Are you sure remove the zone rate data?') ) {
            $(this).parents('tr').remove();
            return false;
        }
        return true;
    });

    $('#zone-rate-weight-range-grid', win).on('change textInput input','input[name="ZoneRate[weight_lo][]"]',function(e){
        refreshSelectedWeightRangeByDest($(this).parent().parent());
        if ( !validWeightFrom($(this)) )  $(this).focus();
    }).on('change textInput input','input[name="ZoneRate[weight_hi][]"]',function(e){
        refreshSelectedWeightRangeByDest($(this).parent().parent());
        if ( !validWeightTo($(this)) ) $(this).focus();
    });

    $('#org-zone-rate-view', win).on('change','input[type=text]',function(e){
        var bFound = false;
        var oneZoneRate = [];
        var rowIndex = 0;
        for ( var i in allZoneRateData ) {
            var oneZone = allZoneRateData[i];
            if ( oneZone['tag'] == selectedRowIndex ) {
                bFound = true;
                oneZoneRate = oneZone['data'];
                rowIndex = i;
                //console.log('related zone data found');
                break;
            }
        }
        if ( bFound ) {
            // get code related data item
            var code = $(this).data('code');
            for ( var j in oneZoneRate ) {
                var oneData = oneZoneRate[j];
                if ( oneData['code'] == code ) {
                    oneData[$(this).attr('name').replace(/[\[\]]+/,'')] = $(this).val();
                    break;
                }
            }
        }
    });

    $('#org-zone-rate-view', win).on('paste','tbody input[type=text]',function(e){
        if (window.clipboardData && window.clipboardData.getData){
            pastedText = window.clipboardData.getData('Text');
        } else if (e.originalEvent.clipboardData && e.originalEvent.clipboardData.getData) {
            pastedText = e.originalEvent.clipboardData.getData('text/plain');
        }

        var ppos = -1;
        var me = $(this);
        var cells = pastedText.trim().split(/[\t\n\r]+/);
        $('#org-zone-rate-view tbody input[type=text]', win).each(function(i){
            if($(this).is(me)) ppos = 0;
            if(ppos >= cells.length) return false;
            if(ppos > -1) $(this).val(cells[ppos++]).trigger('change');
        });
        
        return false;
    });

    $('input[type="submit"]',win).click(function(e){
        //alert('callme');
        e.preventDefault();

        // update all lo and hi weight range
        $('#zone-rate-weight-range-grid', win).find('tr').each(function(e){
            var dataIndex = $(this).find('.delete_btn').data('index');
            if ( !isNaN(dataIndex) ) {
                var lo = $(this).find('#zone-rate-weight-range-grid_weight_lo').val();
                var hi = $(this).find('#zone-rate-weight-range-grid_weight_hi').val();
                updateLoHi(dataIndex,lo,hi);
            }
        });

        // ajax post to save them
        var orgId = $('#Org_id', win).val();
        var rateId = $('#rate_id', win).val();

        $.ajax({
            type : 'POST',
            url : '<?php echo Yii::app()->createAbsoluteUrl("org/saveZonePrice") ;?>',
            dataType: 'json',
            data:{ 'oid' : orgId,'rid' : rateId,'data' : allZoneRateData},
            success:function(resp){
                if ( resp.success == 1 ) {
                    myApp.notice('flex rate by zone saved successfully!', 5000);
                } else {
                    myApp.alert(resp.msg);
                }
            }
        });

    })
});
</script>