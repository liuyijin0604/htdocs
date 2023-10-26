<h2><?=$this->t('Driver Salary Settings');?></h2>
<div class="form">
<form name="settings-form" action="<?php echo Yii::app()->request->requestUri; ?>" method="post">
<?php
$ss = Yii::app()->params['settings'];
foreach($ss as $k => $p):
  if ($k == 'drv_sal_rate0' || $k == 'drv_sal_rate1' || $k == 'drv_sal_rate2' || $k == 'drv_sal_rate3') {
    $id = 'settings_'.$k;
    $index = substr($k, strlen($k) - 1);

    echo '<div class="row">';

    echo '<div class="col">';
    echo CHtml::label($p['label'], $id);
    echo CHtml::textField('Settings['.$k.'][value]', $p['value'], array('id' => $id));
    echo '</div>';

    if ($k != 'drv_sal_rate0') {
      echo '<div class="col" style="margin-left: 20px;">';
      echo CHtml::label($p['label'].' From', $id.'_from');
      echo CHtml::textField('Settings['.$k.'_from'.'][value]', $ss[$k.'_from']['value'], array('id' => $id.'_from'));
      echo '</div>';

      if ($index == 1 || $index == 2) {
        echo '<div class="col" style="margin-left: 20px;">';
        echo CHtml::label($p['label'].' To', $id.'_to');
        echo CHtml::textField('', $ss[str_replace($index, $index+1, $k).'_from']['value'] - 1, array('id' => $id.'_to'));
        echo '</div>';
      }
    }

    echo '</div>';
  }
endforeach; ?>
<div class="row">
<input type="submit" name="submit" value="Save" />
</div>
</form>
</div>