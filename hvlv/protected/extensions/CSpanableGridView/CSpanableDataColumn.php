<?php
Yii::import('zii.widgets.grid.CDataColumn');
class CSpanableDataColumn extends CDataColumn
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


	/**
	 * Renders a data cell.
	 * @param integer $row the row number (zero-based)
	 */
	public function renderDataCell($row)
	{
		$data=$this->grid->dataProvider->data[$row];
		$options=$this->htmlOptions;
		$checkSpan = false;
		if($this->spanable)
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
				$rowspan = 0;
				for($i=$row;$i<sizeof($this->grid->dataProvider->data);$i++) 
				{	
					$thisDependsValue = $this->getDataValueForDepends($i);
					if($myDependsValue == $thisDependsValue)
					{
						$rowspan++;
					}
					else
					{
						break;
					}
				}

				if($rowspan>1)
				{
					$options["rowspan"] = $rowspan;
				}

				if(strpos($this->getDataValueForDepends($row),date("Y-m-d"))!==false)
				{
					$options["style"] = " background-color:orange; ";
				}
				
				if(strpos($this->getDataValueForDepends($row),date("Y-m-d",strtotime("-1 day")))!==false)
				{
					$options["style"] = " background-color:red; ";
				}
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

		if($row==0||!($checkSpan&&$this->spanable))
		{
			$this->renderMyCell($options,$row,$data);
		}
	}

	private function renderMyCell($options,$row,$data)
	{
		echo CHtml::openTag('td',$options);

		$this->renderDataCellContent($row,$data);
		echo '</td>';
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
?>