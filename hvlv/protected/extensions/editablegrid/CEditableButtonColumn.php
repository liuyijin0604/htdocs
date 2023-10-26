<?php

class CEditableButtonColumn extends CButtonColumn
{

	public $template='{edit} {cancel} {save} {delete}';
	
	public $editButtonLabel = 'Edit';
	public $editButtonUrl;
	public $editButtonOptions=array('class'=>'edit_btn');

	public $cancelButtonLabel = 'Cancel';
	public $cancelButtonUrl;
	public $cancelButtonOptions=array('class'=>'cancel_btn');

	public $saveButtonLabel = 'Save';
	public $saveButtonUrl;
	public $saveButtonOptions=array('class'=>'save_btn');
	public $deleteButtonOptions=array('class'=>'delete_btn');
	public $deleteButtonImageUrl=false;
	
	public $editable = true;

	/**
	 * Initializes the default buttons (view, update and delete).
	 */
	protected function initDefaultButtons(){
		foreach(array('edit','cancel','save') as $id){
			$button=array(
				'label'=>$this->{$id.'ButtonLabel'},
				'url'=>$this->{$id.'ButtonUrl'},
				'imageUrl'=>false,
				'options'=>$this->{$id.'ButtonOptions'},
			);
			if(isset($this->buttons[$id]))
				$this->buttons[$id]=array_merge($button, $this->buttons[$id]);
			else
				$this->buttons[$id]=$button;
		}
		parent::initDefaultButtons();
	}
	
	protected function registerClientScript(){
		$js=array();
		foreach($this->buttons as $id=>$button){
			if(isset($button['click'])){
				$function=CJavaScript::encode($button['click']);
				$class=preg_replace('/\s+/','.',$button['options']['class']);
				$js[]="$(document).off('click','#{$this->grid->id} a.{$class}').on('click','#{$this->grid->id} a.{$class}',$function);";
			}
		}

		if($js!==array())
			Yii::app()->getClientScript()->registerScript(__CLASS__.'#'.$this->id, implode("\n",$js));
	}
	
	protected function renderDataCellContent($row,$data){
		$enabled = is_bool($this->editable)? $this->editable : $this->evaluateExpression($this->editable, array('data'=>$data,'row'=>$row));
		if(!$enabled) return;
		$pk = empty($data->tableSchema)? 'id' : $data->tableSchema->primaryKey;
		printf('<input type="hidden" class="row_id" name="%s[%s]" value="%s" />', empty($this->grid->dataProvider->modelClass)? get_class($data) : $this->grid->dataProvider->modelClass, $pk , $data->{$pk});
		parent::renderDataCellContent($row,$data);
	}
}
