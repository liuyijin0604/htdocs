<?php
Yii::import('zii.widgets.grid.CButtonColumn');
class oSpanableButtonColumn extends CButtonColumn
{
	/**
	 * @var this is used for identifying whether this column this rowspan or colspan
	 * @see spanable
	 */
	public $spanable = false;
	/**
	*	this is used for identifying what data that the column depands on when it is spanable
	*
	**/
	public $spanDepands = [];

	public $ids ="";

	protected function renderButton($id,$button,$row,$data){
		if(!empty($button['options']['title'])){
			$button['options']['title']=$this->evaluateExpression($button['options']['title'],array('row'=>$row,'data'=>$data));
		}
		parent::renderButton($id,$button,$row,$data);
	}

	/**
	 * Renders a data cell.
	 * @param integer $row the row number (zero-based)
	 */
	public function renderDataCell($row)
	{
		$data=$this->grid->dataProvider->data[$row];// fistlt get the data row
			$options=$this->htmlOptions;// get the htmlOptioin
			$checkSpan = false; 
			$rowspan = 0;
			$this->ids ="";
			if($this->spanable)//if this is a spanable row
			{
				$myDependsValue = $this->getDataValueForDepends($row); 
				
				if($row ==0)
				{
					$lastDependsValue = $myDependsValue;
				}else
				{
					$lastDependsValue = $this->getDataValueForDepends($row-1);
				}

				if($myDependsValue == $lastDependsValue)$checkSpan = true;

				if($row==0||!$checkSpan)
				{
					for($i=$row;$i<sizeof($this->grid->dataProvider->data);$i++) 
					{	
						$thisDependsValue = $this->getDataValueForDepends($i);
						if($myDependsValue == $thisDependsValue)
						{
							$rowspan++;
							$this->ids .=$this->grid->dataProvider->data[$i]["id"].CargoProcess::IP_SEPERATOR;
						}else
						{
							break;
						}
					}

					if($rowspan>1)
					{
						$options["rowspan"] = $rowspan;
					}

					$this->ids = substr($this->ids,0,-1);
				}
			}
			if($this->cssClassExpression!==null)
			{
				$class=$this->evaluateExpression($this->cssClassExpression,array('row'=>$row,'data'=>$data));
				if(!empty($class))
				{
					if(isset($options['class']))
						$options['class'].=' '.$class;
					else
						$options['class']=$class;
				}
			}

			if($rowspan>1)
			{
				$options["class"] .= " rowspan_td ";
			}

			if($row==0||!($checkSpan&&$this->spanable))
			{
				echo CHtml::openTag('td',$options);
				$this->renderDataCellContent($row,$data);
				echo '</td>';
			}
	}

	




	private function getDataValueForDepends($row)
	{
		$spanDependsValue="";
		$data=$this->grid->dataProvider->data[$row];
		foreach ($this->spanDepands as $key => $spanDepand) {
			if($spanDepand!==null){
				$value=$this->evaluateExpression($spanDepand,array('data'=>$data,'row'=>$row));
				$spanDependsValue.=$value."--";
			};
		}
		return $spanDependsValue;
	}

}
