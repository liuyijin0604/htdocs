<?php
if (empty(Yii::app()->session['scan_warehouse'])) {
    echo '<a class="dash-item ajax-link" href="'.$this->createUrl('site/index', ['scan_warehouse' => 'sydney']).'"><span class="glyphicon glyphicon-wrench"></span><br/>Home Page(To Choose Warehouse)</a>';
    return;
}
?>

<?php
$printLabel = ['rtsresend' => 'PRINT RESHIPPNIG NEW PDF LABEL','rtsresend_new' => 'Print PDF Label','discard'=>'Print PDF Label'];
$tts = ['rtsresend' => 'Old Barcode','rtsresend_new' => 'For New Shipment','discard'=>'RTS Discard'];
echo '<h1>Scan for '.$tts[$op].'</h1>';
?>
</br>
<div class="form_<?=$op?>">
    <?php $form=$this->beginWidget('CActiveForm', [
        'id'=>'scan-form',
        'enableAjaxValidation'=>false,
    ]);
    ?>
    <div style="float:right;">

    <label id = "auto_print_label_<?=$op?>">Auto Print: <input type="checkbox" id="auto_print" name="auto_print" value="1" /></label> &nbsp; <select name="sound"><option value="">Default Sound</option><option value="1">中文女声</option><option value="2">中文男声</option></select>
    </div>


    <div style="margin-bottom:15px;">
        <input class="barcode required form-control" type="text" name="shipment_<?=$op?>" placeholder="barcode" id="shipment_<?=$op?>" autocomplete="off" />
    </div>

    <?php $this->endWidget(); ?>

<div id="result_<?=$op?>" style="background-color: white;margin-top:10px;">
    <table id="items" class="table table-striped table-bordered" style="font-size: 1.5em;">
        <tbody>
            <tr><td class="status"></td></tr>
            <tr><td class="area"></td></tr>
            <tr><td class="msg"></td></tr>
            <tr><td class="gatepass"></td></tr>
            <tr><td class="console"></td></tr>
            <tr><td class="hold"></td></tr>
        </tbody>
    </table>
</div>
</br>
<?php
echo CHtml::button($printLabel[$op], ['class' => 'print_btn_pdf_'.$op,'style'=> 'display:none;margin-left:20px;font-size:xx-large;']);
?>



<input type="hidden" name="scanned-id" value="" id="scaned_shipment_id_<?=$op?>">
<input type="hidden" value="" id="my_sn_<?=$op?>">
</div>

<br />
<div id="res"></div>
<div class="printHelper_info_<?=$op?>" style="text-align: center;"></div>
<iframe id="pdf_label_<?=$op?>" style="display: none;" name="pdf_label_<?=$op?>" src="" ></iframe>


<script type="text/javascript">
            function checkInTakePhoto()
            {
                $('#unknown_file').click();
                return false;
            };

            function checkInChangeFile()
            {
                $('#check_in_path').val($("#unknown_file").val());
            }

            function rtsRecordTakePhoto()
            {
                $('#record_file').click();
                return false;
            };

            function rtsRecordChangeFile()
            {
                $('#rts_record_path').val($("#record_file").val());
            }
</script>



<script type="text/javascript">
$(function() {

    $('#result_<?=$op?> tbody tr').hide();


    function formAfterSuccess_<?=$op?>(r){
        var audio=new Audio();
        audio.src='https://os.toplogistics.com.au/site/voice/' + (r.sounds.reverse().join('-')) + '.mp3';
        audio.play();
        $('#result_<?=$op?> tbody tr').hide();
        $(['status', 'area', 'msg', 'area', 'amazon', 'gatepass', 'hold']).each(function(i){
            if(r[this]) $('#result_<?=$op?> td.'+this).html(r[this]).parent().show();
        });

        if ( r.found == 1 ) {
            $('#scaned_shipment_id_<?=$op?>').val(r.id).data('sn', r.sn);
            $('#my_sn_<?=$op?>').val(r.sn);
            $('#barcode_bk_<?=$op?>').val(r.barcode);
        }

        if ( r.found == 0 ) {
            $('#barcode_bk_<?=$op?>').val(r.barcode);
        }

        if (r.print == 1) {
            $('.print_btn_pdf_<?=$op?>').show();
            if($('input#auto_print_<?=$op?>').prop('checked')) $('input.print_btn_pdf_<?=$op?>').trigger('click');
        } else {
            $('.print_btn_pdf_<?=$op?>').hide();
        }

        $('#shipment_<?=$op?>').val('');
        $('.form_<?=$op?>').removeClass('red green blue').addClass(r.color);
        return true;
    }
    function formOnSubmit_<?=$op?>()
    {
        $('#scaned_shipment_id_<?=$op?>').val('');
        $('#my_sn_<?=$op?>').val('');
        $('input#shipment_<?=$op?>').focus();
    };


    $('input#shipment_<?=$op?>').focus().on('keydown', function(e) {
        if (e.which == 13) {
            formOnSubmit_<?=$op?>();
            $(this).trigger('afterBarcode');
            $('input#shipment_<?=$op?>').focus();
            return false;
        }
    }).on('afterBarcode', function() {
        $.ajax({
            'url': '<?=$this->createUrl('rtsProcess/scan')."?op=".$op?>',
            'type': 'POST',
            'data': { 'barcode': $('input#shipment_<?=$op?>').val()},
            success: function(r) {
                r = JSON.parse(r);
                formAfterSuccess_<?=$op?>(r);
            }
        });
        return false;
    });

    let ws_<?=$op?> = null;
    let printHelper_<?=$op?> = false;
    let printLog_<?=$op?> = [];

    function websocket_connect_<?=$op?>() {
        ws = new WebSocket("ws://127.0.0.1:10081");

        ws.onopen = function() {
            $('.printHelper_info_<?=$op?>').html('<b style="color:green">打印工具已开启</b>');
            printHelper_<?=$op?> = true;
        }

        ws.onclose = function (){
            printHelper_<?=$op?> = false;
            websocket_connect_<?=$op?>();
        }

        ws.onerror = function(e) {
            $('.printHelper_info_<?=$op?>').html('<b style="color:red">打印工具未开启</b> <a href="https://os.pcaex.com/PrintHelper.zip" target="_blank">(点击下载)</a>');
            printHelper_<?=$op?> = false;
        }

        ws.onmessage = function(msg) {
            // console.log(msg);
        }
    }

    $('#auto_print_label_<?=$op?>').on('change', function(){
        websocket_connect_<?=$op?>();
    });

    $(window).unload(function() {
        ws_<?=$op?>.close();
    });

    $('.print_btn_pdf_<?=$op?>').click(function(e){
        var sid = $('#scaned_shipment_id_<?=$op?>').val();
        var sn = $('#my_sn_<?=$op?>').val();
        if($.inArray(sid+"_"+sn, printLog_<?=$op?>) > -1){
            if(!window.confirm('Are you sure to reprint? 确认重复打印吗？')) return false;
        }else{
            if(printLog_<?=$op?>.length > 100) printLog_<?=$op?>.pop();
            printLog_<?=$op?>.unshift(sid+"_"+sn);
        }
        
        if(printHelper_<?=$op?>){
            $.get("<?php echo Yii::app()->createAbsoluteUrl("whscan/shipment/connote"); ?>"+"?helper=1&id="+sid+'&sn='+$('#scaned_shipment_id_<?=$op?>').data('sn'), function(r){
                ws_<?=$op?>.send(r.file);
            }, 'json');
            return;
        }
        var pdfFrame_<?=$op?> = window.frames["pdf_label_<?=$op?>"];
        $("#pdf_label_<?=$op?>").attr("src","<?php echo Yii::app()->createAbsoluteUrl("whscan/shipment/connote"); ?>"+"?id="+sid+'&sn='+$('#scaned_shipment_id_<?=$op?>').data('sn'));
        $("#pdf_label_<?=$op?>").load(function(){
             pdfFrame_<?=$op?>.focus();
             pdfFrame_<?=$op?>.print();
        });
    });    

    if(navigator.userAgent.match(/Android|iPhone|iPad|iPod|SymbianOS|Windows Phone/) !== null || (window.screen.width < 500 && window.screen.height < 800)){ //Mobile
        $("#auto_print_label_<?=$op?>").hide();
    }

});
</script>