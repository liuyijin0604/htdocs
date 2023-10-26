<h1>Scan for Checking</h1>
<div class="form">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'scan-form',
        'enableAjaxValidation'=>false,
    ));
    ?>
    <p>Barcode: <input id="scan" type="text" size="30" name="barcode" /></p>
    <input type="hidden" name="gpfound" value="0">
    <?php $this->endWidget(); ?>
    <div id="result" style="display:none;margin: 20px; border: 1px solid;padding:30px 40px; font-weight: bold; font-size: 42px;">
    </div>
    <audio id="sound" src=""></audio>
</div>

<div id="gp-selected-parcels" class="grid-view" style="width:80%; margin-top: 40px;">
    <label> <h2> Checking for GatePass : <span style="color:#000000" id="gatepass-id"></span> </h2> </label>
    <label> <h2> Company : <span style="color:#000000;" id="gatepass-company"></span> </h2>  </label>
    <table class="items">
        <thead>
        <tr>
            <th>Connote</th>
            <th>Status</th>
            <th>Package</th>
            <th>Weight</th>
            <th>cbm</th>
            <th>Tips</th>
        </tr>
        </thead>
        <tbody id="gp-body-selected-parcles">
        </tbody>
    </table>
</div>

<script type="text/javascript">

    function addSelectedParcel(parcel){
        var existingNum = $('#gp-body-selected-parcles tr').length;
        var trClassType = 'odd';
        if ( existingNum % 2 == 0 ) trClassType = 'even';
        var parcelElements = '<tr class="' + trClassType + '" data-id="' + parcel.id + '">';
        parcelElements +=  '<td>' + parcel.hbn + '</td>';
        parcelElements +=  '<td>' + parcel.status + '</td>';
        parcelElements +=  '<td>' + parcel.pkg + '</td>';
        parcelElements +=  '<td>' + parcel.weight + '</td>';
        parcelElements +=  '<td>' + parcel.cbm + '</td>';
        parcelElements +=  '<td data-pkg="0"> ' + parcel.pkg  + ' items left </td>';
        var obj = $(parcelElements);
        $('#gp-body-selected-parcles').append(obj.fadeIn());

       // var selectedAmount = parseInt($('#selected-item-amount').html()) + 1;
       // $('#selected-item-amount').html(selectedAmount);

    }

    function showCheckTips(sid){
        $('#gp-body-selected-parcles tr').each(function(e){
            if ( $(this).data('id') == sid ) {
                var pkgitem = $(this).find('td:nth-child(6)');
                var curnum = parseInt(pkgitem.data('pkg'));
                curnum++;
                //console.log('cur num is :' + curnum);
                var realnum = parseInt($(this).find('td:nth-child(3)').html());
                //console.log('real num is :' + realnum);
                if ( curnum >= realnum ) {
                    pkgitem.html('OK DONE');
                    if ( !pkgitem.hasClass('gp-check-done') ) {
                        pkgitem.addClass('gp-check-done');
                    }
                } else {
                    var leftnum = realnum - curnum;
                    pkgitem.data('pkg',curnum);
                    pkgitem.html( leftnum + ' items left');
                }
            }
        });
    }

    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
        $('input#scan', panel).focus();
        $('form#scan-form', panel).data('custom_success', function(r){
            $('#sound', panel).attr('src', 'site/voice/'+r.sound+'.mp3');
            $('#sound', panel)[0].play();
            $('#result', panel).text(r.msg).css('color', r.color).fadeIn(100, function(){
              //  alert(r.msg);
            });
            if (r.gpfound == 1 ) {
                $('input[name="gpfound"]').val(1);

                $('#gatepass-id').html(r.gpid);
                $('#gatepass-company').html(r.company);
                // show all gp data
                for ( var i in r.data ) {
                    addSelectedParcel(r.data[i]);
                }
            }

            if ( r.sid > 0 ) {
                showCheckTips(r.sid);
            }

            return true;
        }).on('submit', function(){
            $('input#scan', panel).focus();
        });
        $('input#scan', panel).on('focus', function(){
            $(this).select();
        });

        $('input#scan', panel).focus();

    });
</script>