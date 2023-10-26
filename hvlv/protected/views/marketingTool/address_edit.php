<style>
input[type=text]{
    width: 500px;
}

div.form select{
    width: 500px;
}

</style>

<div>
    <h1>Email Address</h1>

    <div class="form">
    <form id="form_address">

        <div class="row rowcol rowleft">
            <?= CHtml::label('Company','company'); ?>
            <?= CHtml::textField( 'company',$model->company); ?>
        </div>
        <div class="row rowcol rowleft">
            <?= CHtml::label('Country','country'); ?>
            <?= CHtml::textField( 'country',$model->country); ?>
        </div>
        <div class="row rowcol rowleft">
            <?= CHtml::label('Contact','contact'); ?>
            <?= CHtml::textField( 'contact',$model->contact); ?>
        </div>
        <div class="row rowcol rowleft">
            <?= CHtml::label('Email','email'); ?>
            <?= CHtml::textField( 'email',$model->email); ?>
        </div>
        <div class="row rowcol rowleft">
            <?= CHtml::label('Tel','tel'); ?>
            <?= CHtml::textField( 'tel',$model->tel); ?>
        </div>
        <div class="row rowcol rowleft">
            <?= CHtml::label('Imports','imp'); ?>
            <?= CHtml::dropDownList('imp', $model->imp, MarketingAddress::listYesNo ) ?>
        </div>
        <div class="row rowcol rowleft">
            <?= CHtml::label('3PL','tpl'); ?>
            <?= CHtml::dropDownList('tpl', $model->tpl, MarketingAddress::listYesNo ) ?>
        </div>
        <div class="row rowcol rowleft">
            <?= CHtml::label('Top Logistics Delivery','tld'); ?>
            <?= CHtml::dropDownList('tld', $model->tld, MarketingAddress::listYesNo ) ?>
        </div>
        <div class="row rowcol rowleft">
            <?= CHtml::label('group','group'); ?>
            <?= CHtml::textField( 'group',$model->group); ?>
        </div>
        <div class="row rowcol rowleft">
            <?= CHtml::label('Status','status'); ?>
            <?= CHtml::dropDownList('status', $model->status, MarketingAddress::listStatus ) ?>
        </div>

        <input type="hidden" name="id" value="<?=$model->id?>" />
    </form>


    <div class="row rowcol rowleft">
        <input type="button" value="Save" onclick="funcSave()"/>
    </div>
    </div>

</div>


<script type="text/javascript">
    $(function(){
        var win = $('#jqmw_<?=$_GET["tabid"];?>');
        $('#form_address', win).on('success', function(e, r){
            win.data('opener').trigger('onOpen');
            win.jqmHide();
        });
    });

    function funcSave() {
        setTimeout(() => {
            listData = $('#form_address').serializeArray();
            htmlobj = $.ajax({
                type:"POST",
                url: "/marketingTool/editAddress",
                data: listData,
                async: false
            });
            obj = JSON.parse(htmlobj.responseText);
            if(obj.isSuccess){
                $('#jqmw_<?=$_GET["tabid"];?>').jqmHide();
                $('#<?=$_GET["tabid"]?>_address-grid').yiiGridView('update');
                myApp.notice('success', 5000);
            }
            else{
                alert(obj.strMessage);
            }

        }, 0);
    }
</script>