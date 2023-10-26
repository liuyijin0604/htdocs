<h1><?=$this->t('Tow Service Cost Billing Operation');?> </h1>

<div id="tow-billing-pro-tabs">
    <ul>
        <?php
        $tabs = array(
            array('towop', $this->t('OP'), true),
            array('towaccounting', $this->t('Accounting')),
            array('towall', $this->t('All')),
        );
        foreach($tabs as $tab){
            if(Acl::hasAccess($this->CaName.'/'.$tab[0]) && $tab[1]){
                $href = strpos($tab[0], '/') === false? $this->createUrl('billing/towUpdate',array('tab'=>$tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
                echo '<li><a href="'.$href.'">'.$tab[1].'</a></li>';
            }
        }
        ?>
    </ul>
</div>
<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        $('#tow-billing-pro-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
            myApp.ajaxifyForm(this);
        }});
    });
</script>
