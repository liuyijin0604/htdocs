<div style="right: 20px;position: absolute;">
<a class="jqm_link" href="<?=$this->createUrl('import/getCourierCost');?>" title="Courier cost export"><div class="icon" style="background-position:-16px 0"></div> Courier cost export</a> &nbsp; 
</div>
<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'import-cost-manager-form',
        'enableAjaxValidation'=>false,
        'action' => $this->createUrl('import/AjaxQueryCost'),
    ));
    ?>
    <div class="row">
        <?php echo CHtml::label('Postcode:','for-import-cost-query'); ?>
        <?php echo CHtml::textField('postcode',''); ?>
    </div>

    <div class="row">
        <?php echo CHtml::label('Weight(kg):','for-import-cost-query'); ?>
        <?php echo CHtml::textField('weight',''); ?>
    </div>

    <div class="row">
    <input id="imort_cost_query_btn" type="submit" value="Query" />
    </div>
    <?php $this->endWidget(); ?>
</div>

<div class="grid-view row" style="width: 80%;margin-top: 50px;">
    <label> <h2> All Available Couriers </h2></label>
    <table class="items">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
        </tr>
        </thead>
        <tbody id="body-all-couriers">
        </tbody>
    </table>

</div>



<br><br><br>
<h1> Check Cost Batch </h1>
<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'courier-check-batch-form',
        'enableAjaxValidation'=>false,
        'action' => $this->createUrl('import/AjaxCourierBatchCheck'),
    ));
    ?>
    <div class="row">
        <label for="postw-batch">Postcode & Weight List - <small>.xlsx File</small>(<a href="/ims/postcode_w_check.xlsx" target="_blank">Get template file</a>)</label> <br>
        <input type="file" name="postw_batch" id="postw_batch" />
    </div>
    <br>

    <input id="batch-check-op-type" type="hidden" name="op" value="show">
    <p><input id="postw_batch_btn" type="submit" value="Submit" /> &nbsp;&nbsp; <input id="postw_batch_export_btn" type="submit" value="Export as Excel" /></p>

    <?php $this->endWidget(); ?>
</div>
<div id="postw_batch_result" style="margin: 10px 0; border: 1px solid;padding:20px;">
</div>



<script type="text/javascript">

    function addCourier(key, data){

        var existingNum = $('#body-selected-parcles tr').length;
        var trClassType = 'odd';
        if (existingNum % 2 == 0) trClassType = 'even';
        var parcelElements = '<tr class="' + trClassType + '">';
        parcelElements += '<td colspan="3" style="font-weight: bold;">' +  key + '</td>';
        parcelElements += '</tr>';
        var obj = $(parcelElements);
        $('#body-all-couriers').append(obj.fadeIn());

        for ( var j in data ) {
            var courier = data[j]
            var existingNum = $('#body-selected-parcles tr').length;
            var trClassType = 'odd';
            if (existingNum % 2 == 0) trClassType = 'even';
            var parcelElements = '<tr class="' + trClassType + '">';
            parcelElements += '<td><a class="tab_link" href="org/update/' + courier.id + '" title="ORG-' + courier.id + '">' + courier.id + '</a></td>';
            parcelElements += '<td>' + courier.name + '</td>';
            parcelElements += '<td>' + courier.price + '</td>';
            parcelElements += '</tr>';
            var obj = $(parcelElements);
            $('#body-all-couriers').append(obj.fadeIn());
        }
    }

    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
        $('form#import-cost-manager-form', panel).data('custom_success', function(r){
            $('#imort_cost_query_btn', panel).attr('disabled', false);
            $('#body-all-couriers',panel).empty();
            if ( r.success == 1 ) {
                //console.log('return data');
                for (var i in r.data) {
                    //console.log(r.data[i]);
                    addCourier(i,r.data[i]);
                }
            }
            return true;
        });

        $('#postw_batch_export_btn',panel).click(function(e){
            e.preventDefault();
            $('#batch-check-op-type',panel).val('export');
            $('form#courier-check-batch-form', panel).submit();
        });

        $('#postw_batch_btn',panel).click(function(e){
            e.preventDefault();
            $('#batch-check-op-type',panel).val('show');
            $('form#courier-check-batch-form', panel).submit();

        });

        $('form#courier-check-batch-form', panel).data('custom_success', function(r){
            $('#postw_batch_result', panel).empty().prepend($('<p>'+r.msg+'</p>').fadeIn());
            $('#postw_batch_btn', panel).attr('disabled', false);
            $('#postw_batch_export_btn', panel).attr('disabled', false);
            return true;
        });

    });
</script>
