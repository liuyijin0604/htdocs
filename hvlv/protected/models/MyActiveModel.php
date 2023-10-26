<?php


class MyActiveModel extends CActiveRecord
{
	public function beforeSave()
	{
		return parent::beforeSave();
	}


	public function afterFind()
	{
		return parent::afterFind();
	}

	public function smart_comp($f, $v,&$criteria)
	{
		if(preg_match('/^\?/', $v)){
				$criteria->compare($f, substr($v,1), true);
		}else{
			$criteria->compare($f, $v, false);
		}
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ShipmentQuestion the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model(get_called_class());
	}
}
