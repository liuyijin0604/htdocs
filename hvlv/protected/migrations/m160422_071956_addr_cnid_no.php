<?php

class m160422_071956_addr_cnid_no extends CDbMigration
{
	public function up()
	{
		$this->_addColumn('addr', 'cnid_no', 'varchar(20) NOT NULL AFTER `acc`');
		$this->createIndex('cnid_no', 'addr', 'cnid_no');
		$this->refreshTableSchema('addr');
		$rs = ExParcel::model()->findAll('meta LIKE :c', [':c' => '%cnid%']);
		foreach($rs as $r){
			$r->cnee->cnid_no = $r->mdata['cnid'];
			$r->cnee->save();
			$r->update('meta');
		}
	}

	public function down()
	{
		echo "m160422_071956_addr_cnid_no does not support migration down.\n";
		return false;
	}

	public function _addColumn($table, $column, $type) {
      // Fetch the table schema
      $table_to_check = Yii::app()->db->schema->getTable($table);
      if ( ! isset( $table_to_check->columns[$column] )) {
        $this->addColumn($table, $column, $type);
      }
    }

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}