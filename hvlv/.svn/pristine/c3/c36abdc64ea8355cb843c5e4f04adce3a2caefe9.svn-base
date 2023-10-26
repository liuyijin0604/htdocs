<h1><?=$this->t('New Org Quote');?></h1>


<?php
echo $this->renderPartial('_form_quote',['model' => $model,'quoteTemplate' => $quote,'type'=>$type]);
?>

<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');

        $('form#org-quote-form', tab.data('panel')).on('success', function(e, r){
          //  var url = tab.data('url').replace('imParcel/create','imParcel/update/'+r.id);
        //    tab.data('url', url).trigger('load');
        });

    });
</script>
