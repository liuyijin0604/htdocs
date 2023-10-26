<?php 
class MyCDbCriteria extends CDbCriteria
{
	public function compareWithObj($obj,$key,$valueKey,$model,$targetSearchingKey,$targetIdKey,$searchContent="ref =:content or hbn=:content")
	{
		if(!empty($obj->$key))
		{
			if(!empty($targetSearchingKey))
			{
				$objs = $model::model()->findAll("{$targetSearchingKey} =:content",[":content"=>$obj->$key]);
			}else
			{
				$objs = $model::model()->findAll($searchContent,[":content"=>$obj->$key]);
			}

			if(empty($objs))
			{
				$this->compare($targetIdKey,-1);
			}else
			{
				$ids = array_column($objs,$valueKey);
				$this->compare($targetIdKey,$ids);
			}

		}
	}
}
?>