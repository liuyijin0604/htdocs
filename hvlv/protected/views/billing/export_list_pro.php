<h1><?=$this->t('Export Service Cost Billing Operation');?> </h1>

<div id="export-billing-pro-tabs">
    <ul>
        <?php
        $tabs = array(
            array('exportop', $this->t('OP'), true),
            // array('exportaccounting', $this->t('Accounting')),
            // array('exportall', $this->t('All')),
        );
        foreach($tabs as $tab){
            if(Acl::hasAccess($this->CaName.'/'.$tab[0]) && $tab[1]){
                $href = strpos($tab[0], '/') === false? $this->createUrl('billing/exportUpdate',array('tab'=>$tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
                echo '<li><a href="'.$href.'">'.$tab[1].'</a></li>';
            }
        }
        ?>
    </ul>
</div>
<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        $('#export-billing-pro-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
            myApp.ajaxifyForm(this);
        }});
    });
</script>
