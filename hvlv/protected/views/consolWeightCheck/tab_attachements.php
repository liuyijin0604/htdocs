
<div style="margin-bottom: 20px;">
    <a class="jqm_link" data-win-class="L" href="consolWeightCheck/attach/<?php echo $model->id; ?>">
        <div style="background-position:-16px 0" class="icon"></div>
        Attach files</a>
</div>


<?php

// get latest uploaded zone map file
$attachements = FileRepo::model()->findAll('fid = :oid and type = 88 order by id desc', [':oid' => $model->id]);

$index = 1;
?>

<div><ul id="attachements-div">

<?php

foreach ( $attachements as $attachement ) {
    $line = '<li>' . $index++ . '. <a target="_blank" href="' . $attachement->getUrl() . '" >' . $attachement->name . '</a></li>';
    echo $line;
}
?>

    </ul></div>
<script type="text/javascript">
    $(function(){
        var tab = $("#<?=$_GET['tabid'];?>");
        var panel = tab.data('panel');
        tab.bind('onOpen', function(){
            var baseUrl = <?php echo json_encode(Yii::app()->createAbsoluteUrl("consolWeightCheck/getAttachs")); ?>;
            baseUrl +=  "?id=" + <?php echo json_encode($model->id); ?>;
            $.ajax({
                type : 'GET',
                url : baseUrl,
                dataType: 'json',
                success:function(resp){
                    if ( resp.success == 1 ) {
                        $('#attachements-div').html(resp.data);
                    }

                }
            });


        });
    });
</script>