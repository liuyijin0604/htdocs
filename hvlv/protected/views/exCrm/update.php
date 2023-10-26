<h1><?=$this->t('Update');?> <?php echo $model->no; ?> - <i><?=$model->getStatus();?></i></h1>
<div id="excrm-tabs">
    <ul>
        <?php 
        $tabs=[
            ['overview', $this->t('Overview'), true],
            ['shipments', $this->t('Shipments'), true],
            ['comp',$this->t('Compensation'),$model->type==30?true:false],
            ['files', $this->t('Files'), true],
            ['log', $this->t('Logs'), true],
        ];
        foreach ($tabs as $tab){
            if($tab[2]){
                 $href = strpos($tab[0], '/') === false? $this->createUrl('exCrm/update',array('id'=>$model->id, 'tab'=>$tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
                 echo '<li><a href="'.$href.'">'.$tab[1].'</a></li>';
            }
        }
        ?>
    </ul>
</div>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	$('#excrm-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
		myApp.ajaxifyForm(this);
	}});
});
</script>