<?php
/**
 * CEditableColumn class file.
 *
 * @author Herbert Maschke <thyseus@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2010 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * CEditableColumn represents a grid view column that is editable.
 *
 * @author Herbert Maschke <thyseus@gmail.com>
 * @package zii.widgets.grid
 * @since 1.1
 */
class CEditableColumn extends CDataColumn
{
	/**
	 * Renders the data cell content.
	 * @param integer the row number (zero-based)
	 * @param mixed the data associated with the row
	 */
	public $nonew;
	public $inputOptions = [];
	public $acOptions = [];
	public $sourceUrl;
	public $editable = true;
	
	public function init()
	{
		parent::init();
		//if(empty($this->type)){
		$this->inputOptions = array_merge(['style' => 'width:100%'], $this->inputOptions);
		//}
		if ($this->type == 'autocomplete') {
			$aco = ['showAnim' => 'fold', 'minLength' => 2,	'delay' => 200,
				'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]); return false; }',
				'change' => 'js:function(event, ui){ if(ui.item == null) $(this).val(""); return false; }',
			];
			foreach ($aco as $k=>$v) {
				if (empty($this->acOptions[$k])) {
					$this->acOptions[$k] = $v;
				}
			}
			$func = "function(){
	if($(this).data('acinit') == 1) return;
	$(this).autocomplete(".CJavaScript::encode($this->acOptions).").data('acinit', 1);
}";
			Yii::app()->getClientScript()->registerScript(__CLASS__.'#'.$this->grid->id.'_'.$this->name, "$(document).off('focus','#{$this->grid->id} input.egacol_{$this->name}').on('focus','#{$this->grid->id} input.egacol_{$this->name}', $func);");
		}
	}
	
	protected function renderHeaderCellContent()
	{
		if ($this->type == 'selection') {
			echo CHtml::checkBox('', false, array_merge(['class' => 'grid_selection', 'id' => 'select_'.$this->id], $this->inputOptions));
		} else {
			parent::renderHeaderCellContent();
		}
	}
	
	public function inputName($data)
	{
		return get_class($data).'['.$this->name.']';
	}
	
	public function inputID($data)
	{
		return $this->grid->getId().'_'.$this->name;
	}
	
	public function renderDataCellContent($row, $data)
	{
		if ($this->nonew && $row < 0) {
			return;
		}
		$this->inputOptions['id'] = $this->inputID($data).($row < 0? '':'_r'.$row);
		$enabled = $row < 0? true : (is_bool($this->editable)? $this->editable : $this->evaluateExpression($this->editable, ['data'=>$data,'row'=>$row]));
		if ($enabled) {
			switch ($this->type) {
				case 'raw':
					echo $this->evaluateExpression($this->value, ['data'=>$data,'row'=>$row]);
				break;
				case 'selection':
					if ($row < 0) {
						break;
					}
					echo CHtml::checkBox($this->name, false, array_merge(['class' => 'grid_select select_'.$this->id, 'value' => $this->evaluateExpression($this->value, ['data'=>$data,'row'=>$row])], $this->inputOptions));
				break;
				case 'checkbox':
					echo CHtml::checkBox($this->name, !empty($data->{$this->name}), array_merge(['value' => 1], $this->inputOption));
				break;
				case 'list':
					$vl = is_array($this->filter)? $this->filter : $this->evaluateExpression($this->filter, array('data' => $data));
					if (!is_array($vl)) {
						break;
					}
					echo CHtml::activeDropDownList($data, $this->name, $vl, array_merge(['id'=>false, 'prompt'=>''], empty($this->inputOptions)? [] : $this->inputOptions));
				break;
				case 'autocomplete':
					echo CHtml::activeHiddenField($data, $this->name, ['id' => $this->inputID($data).($row < 0? '':'_r'.$row)]);
					echo CHtml::textField(empty($this->inputOptions['name'])? '' : $this->inputOptions['name'], $this->evaluateExpression($this->value, ['data'=>$data,'row'=>$row]), ['width' => '100%', 'style' => 'width:100%', 'id' => 'egac_'.$this->id.'_r'.$row, 'class' => 'egacol_'.$this->name]);

				break;
				case 'input':
					echo CHtml::textField(get_class($data) .'[' . $this->name . ']', $this->evaluateExpression($this->value, array('data'=>$data,'row'=>$row)), array('width' => '100%', 'style' => 'width:100%'));
				break;
				default:
					echo CHtml::activeTextField($data, $this->name, $this->inputOptions);
				break;
			}
		} else {
			switch ($this->type) {
				case 'raw':
					echo $this->evaluateExpression($this->value, ['data'=>$data,'row'=>$row]);
				break;
				case 'selection':
					if ($row < 0) {
						break;
					}
					echo '';
				break;
				case 'checkbox':
					echo empty($data->{$this->name})? 'N' : 'Y';
				break;
				case 'list':
					$vl = is_array($this->filter)? $this->filter : $this->evaluateExpression($this->filter, ['data' => $data]);
					if (!is_array($vl) || !isset($data->{$this->name})) {
						break;
					}
					echo isset($vl[$data->{$this->name}])? $vl[$data->{$this->name}] : '';
				break;
				case 'autocomplete':
					echo $this->evaluateExpression($this->value, ['data'=>$data,'row'=>$row]);
				break;
				default:
					echo isset($data->{$this->name})? $data->{$this->name} : '';
				break;
			}
		}
	}
}
