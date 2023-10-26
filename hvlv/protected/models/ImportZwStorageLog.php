<?php

/**
 * This is the model class for table "import_zw_storage_log".
 *
 * The followings are the available columns in table 'import_zw_storage_log':
 * @property string $id
 * @property string $time
 * @property integer $type
 * @property string $model
 * @property string $lid
 * @property string $meta
 * @property string $user_id
 */
class ImportZwStorageLog extends Log
{

	public static function add($model, $type, $extra=array()){
		$log = new ImportZwStorageLog;
		$log->attributes = array(
			'type' => $type,
			'model' => get_class($model),
			'lid' => $model->id,
		);
		if(!empty($extra)){
			$log->extra = $extra;
		}
		$log->save();
		return $log;
	}
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ImportZwStorageLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
