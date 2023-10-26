<?php


class MetaModel extends CActiveRecord
{
	private $thisUserDptId = 0;
	private $thisUserOrgId = 0;
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

	public function getUserDptId()
	{
		if($this->thisUserDptId!==0)
		{
			return $this->thisUserDptId;
		}else
		{
			$this->thisUserDptId = User::currentUserDptId();
			$dpt = Org::model()->findByPk($this->thisUserDptId);
			if(Acl::hasAccess('B:admin/allDpt')||!empty($dpt->extra['checkAllDpt']))
			{
				$this->thisUserDptId = null;
				return $this->thisUserDptId;
			}
			if($this->thisUserDptId==Org::TLA_DEPARTMENT_PERTH)
			{
				$this->thisUserDptId = [Org::TLA_DEPARTMENT_PERTH,Org::TLA_DEPARTMENT_FREMANTLE];
			}else
			{
				$this->thisUserDptId = [$this->thisUserDptId];
			}

			return $this->thisUserDptId;
		}
	}

	public function canUserCheckAll()
	{
		$thisUserDptId = User::currentUserDptId();
		$dpt = Org::model()->findByPk($thisUserDptId);
		if(Acl::hasAccess('B:admin/allDpt')||!empty($dpt->extra['checkAllDpt']))
		{
			return true;
		}
		return false;
	}

	public function getUserOrgId()
	{
		if($this->thisUserOrgId!==0)
		{
			return $this->thisUserOrgId;
		}else
		{
			$this->thisUserOrgId = User::currentUserOrgId();
			return $this->thisUserOrgId;
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
