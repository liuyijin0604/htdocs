<div class="container">
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
  'id'=>'wms-task32-form',
  'enableAjaxValidation'=>false,
  'htmlOptions' => array(
    'enctype' => 'multipart/form-data')
));
$cnee = new Addr;
?>
    <div class="row">
        <div class="col col-md-3 col-sm-6">
            <div class="form-group">
                <?php echo $form->labelEx($model, 'Ref - Photo & Mark'); ?>
                <?php echo CHtml::textField('WmsTask[ref]', @$model->ref, array('size'=>60, 'class'=>'form-control')); ?>
                (Leave empty if no requirement)
            </div>
        </div>
    </div>
    <?php 
    if(!empty($model->id)):?>
        <input type="hidden" value="<?=$model->id?>" name="id">
    <?php endif;?>
       <div class="form-group">
    <?php echo CHtml::submitButton($this->t('Save'),array('class'=>'btn btn-primary')); ?>
  </div>

  <?php if ($model->mainTask->type != 3040) { ?>
  <table id="tsk_item" class="items table">
  <thead><tr><th width="25">#</th><th width="100" class="col_pl">Weight (Kg)</th><th width="100">DIM (cm)</th><th width="100">Materials</th><th width="200">Time</th></tr></thead>
  <tbody>
  <?php
  $twt = 0;
  $tdim = 0;
  if (isset($model->mdata['pkg'])) {
    $pkgs = json_decode($model->mdata['pkg'], true);
    if (!empty($pkgs)) {
      foreach ($pkgs as $i => $item) {
        // if (empty($item['wt']) || empty($item['nt'])) continue;
        echo '<tr class="'.($i%2==0? 'odd' : 'even').'"><td>'.($i+1).'</td><td>'.$item['wt'].'</td><td>'.$item['w'].' X '.$item['h'].' X '.$item['d'].'</td>' . (!empty($item['nt']) ? '<td>'.(explode(' ', $item['nt'])[0] == '是' ? 'Yes' : 'No').'</td><td>'.explode(' ', $item['nt'])[1].' '.explode(' ', $item['nt'])[2].'</td>' : '<td></td><td></td>') . '</tr>';
        $twt += $item['wt'];
        if (!empty($item['w'] && !empty($item['h']) && !empty($item['d']))) {
          $tdim += $item['w'] * $item['h'] * $item['d'];
        }
      }
    }
  }
  ?>
  </tbody>
  <tfoot>
    <tr><td>Total</td><td class="tot_wt" align="right"><?=$twt?></td><td class="tot_vol" align="right"><?=$tdim?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
  </tfoot>
  </table>
  <?php } else { ?>
  <table id="t32_item" class="items tsk_item">
  <thead><tr><th width="25">#</th><th width="100" class="col_pl">Weight (Kg)</th></tr></thead>
  <tbody>
  <?php
  $twt = 0;
  if (isset($model->mdata['pkg'])) {
    $pkgs = json_decode($model->mdata['pkg'], true);
    if (!empty($pkgs)) {
      foreach ($pkgs as $i => $item) {
        if (empty($item['wt'])) continue;
        echo '<tr><td>'.($i+1).'</td><td><input name="wt['.$i.']" value="'.$item['wt'].'"/></td></tr>';
        $twt += $item['wt'];
      }
    }
  }
  ?>
  </tbody>
  <tfoot>
    <tr><td><button class="add btn btn-info"><b>+</b></button></td><td class="tot_wt" align="right"></td><td class="tot_vol" align="right"></td><td>&nbsp;</td></tr>
  </tfoot>
  </table>
  <script>
  $(function() {
    var tab = $("#<?=$_GET['tabid'];?>");
    var panel = tab.data('panel');
    var t = $('#t32_item', panel);
    var i = '<?=empty($pkgs) ? 0 : count($pkgs)?>';

    t.off('click', '.add.btn').on('click', '.add.btn', function() {
      t.append('<tr><td>'+(parseInt(i)+1)+'</td><td><input name="wt['+i+']"/></td></tr>');
      i++;
    });
  });
  </script>
  <?php } ?>

</div><!-- form -->
<?php $this->endWidget(); ?>
<br />
</div>