<?php
//Yii::import('zii.widgets.grid.CButtonColumn');
class oButtonColumn extends CButtonColumn
{
	protected function renderButton($id,$button,$row,$data){
		if(is_array($button['options'])){
			foreach($button['options'] as $k => $v){
				if(preg_match('/\$(data|row)/', $v)){
					$button['options'][$k]=$this->evaluateExpression($v, ['row'=>$row,'data'=>$data]);
				}
			}
		}
		parent::renderButton($id,$button,$row,$data);
	}
}
