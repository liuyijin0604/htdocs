<?php
class oActiveRecord extends CActiveRecord {

	private static $db_tla = null;

	protected static function getTlaConnection(){
		if (self::$db_tla !== null){
			return self::$db_tla;
		}else{
			self::$db_tla = Yii::app()->db_tla;
			if (self::$db_tla instanceof CDbConnection){
				self::$db_tla->setActive(true);
				return self::$db_tla;
			}else{
				throw new CDbException(Yii::t('yii','Active Record requires a "db_tla" CDbConnection application component.'));
			}
		}
	}

	public function getDbConnection(){
		if(Yii::app()->name == 'TLA' && !empty(Yii::app()->db_tla) && in_array($this->tableName(), Yii::app()->db_tla->schema->getTableNames())) return self::getTlaConnection();
		return parent::getDbConnection();
	}

}