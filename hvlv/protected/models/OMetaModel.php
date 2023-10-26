<?php


class OMetaModel extends oActiveRecord
{
	public $mdata =[];
	public function beforeSave()
	{
		if(empty($this->meta)&&empty($this->mdata))
		{
			$this->meta = "{}";
		}else
		{
			$this->meta = json_encode($this->mdata);
		}
		return parent::beforeSave();
	}


	public function afterFind()
	{
		if (!empty($this->meta)) {
			$this->mdata = json_decode($this->meta,true);
		}else
		{
			$this->mdata = [];
		}
		return parent::afterFind();
	}

	public function savePOSTMeta($postMetaData)
	{
		foreach($postMetaData['mdata'] as $k => $v)
		{
				$this->mdata[$k] = $v;
		}

		if($this->mdata!=null)
		{
			$this->meta = json_encode($this->mdata);
		}
	}

	public function updateMeta()
	{
		$this->meta = json_encode($this->mdata);
		$this->update(['meta']);
	}

	public function getDbConnection(){
		//if(Yii::app()->name == 'TLA' && !empty(Yii::app()->db_tla) && in_array($this->tableName(), Yii::app()->db_tla->schema->getTableNames())) return self::getTlaConnection();
		
		//return parent::getDbConnection();
		return self::getTlaConnection();
	}

	
	public function getDatabaseName()
	{
		$databaseName = 'hvlv';
		if(Yii::app()->name=="TLA")
		{
			$databaseName = 'hvlv_top';
		}
		return $databaseName;
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
