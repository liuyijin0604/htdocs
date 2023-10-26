<?php

class m160417_213841_invoice_add_sent extends CDbMigration
{
	public function up()
	{
		$this->_addColumn('invoice', 'sent', 'TINYINT(2) NOT NULL DEFAULT 0 AFTER `currency`');
		$this->createIndex('sent', 'invoice', 'sent');
		$this->refreshTableSchema('invoice');
	}

	public function down()
	{
		echo "m160417_213841_invoice_add_sent does not support migration down.\n";
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