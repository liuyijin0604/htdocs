<?php

class m160412_050214_create_crm_log_table extends CDbMigration
{
	public function up()
	{

        try {
            $this->createTable('crm_log', array(
                'id' => 'pk',
                'operator_id' => 'int(11) unsigned NOT NULL',
                'crm_id' => 'int(11) unsigned NOT NULL',
                'time' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP',
                'note' => 'text NOT NULL'
            ),
                "ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='save CRM related logs'");


            $this->createIndex('idx_operator','crm_log','operator_id');
            $this->createIndex('idx_crm','crm_log','crm_id');

        }catch(Exception $e)
        {
            echo "Exception: ".$e->getMessage()."\n";
            return false;
        }
	}

	public function down()
	{
        $this->dropTable('crm_log');
		//echo "m160412_050214_create_crm_log_table does not support migration down.\n";
		return false;
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