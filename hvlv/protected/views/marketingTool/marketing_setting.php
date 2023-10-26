<style>
input[type=text]{
    width: 500px;
}

textarea{
    width: 500px;
}
.min_height_300{
    min-height: 300px;
}

</style>

<div>
    <h1>Email Settings</h1>

    <div class="form">
        <form id="form_max">
            <div class="row rowcol rowleft">
                <?php echo CHtml::label('Max Limitation','max_limitation'); ?>
                <?php echo CHtml::textField( 'max_limitation',$model->mdata['email_max']); ?>
            </div>
        </form>
        <div class="row rowcol rowleft">
            <input type="button" value="Save" onclick="funcSave()"/>
        </div>

    </div>



</div>


<script type="text/javascript">
    function funcSave(){
        setTimeout(() => {
            listData = $('#form_max').serializeArray();

            htmlobj = $.ajax({
                type:"POST",
                url: "/marketingTool/emailMaxLimitation",
                data: listData,
                async: false
            });
            obj = JSON.parse(htmlobj.responseText);
            if(obj.isSuccess){
                $('#jqmw_<?=$_GET["tabid"];?>').jqmHide();
                myApp.notice('success', 5000);
            }
        }, 0);
    }
</script>