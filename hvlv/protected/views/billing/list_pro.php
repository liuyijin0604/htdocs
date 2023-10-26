<h1><?=$this->t('Billing Operation');?> </h1>

<div id="billing-pro-tabs">
    <ul>
        <?php
        $tabs = array(
            array('op', $this->t('OP'), true),
            // array('accounting', $this->t('Accounting')),
            // array('all', $this->t('All')),
            array('afimport', $this->t('Import Priority')),
            array('tneimport', $this->t('Import T&E')),
            array('yyimport', $this->t('Import Yuyang')),
            array('sjcheck', $this->t('Check SkyJet')),
            array('ifimport', $this->t('Air Insurance')),
        );
        foreach($tabs as $tab){
            if(Acl::hasAccess($this->CaName.'/'.$tab[0]) && $tab[1]){
                $href = strpos($tab[0], '/') === false? $this->createUrl('billing/update',array('tab'=>$tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
                echo '<li><a href="'.$href.'">'.$tab[1].'</a></li>';
            }
        }
        ?>
    </ul>
</div>
<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        $('#billing-pro-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
            myApp.ajaxifyForm(this);
        }});
    });
</script>
