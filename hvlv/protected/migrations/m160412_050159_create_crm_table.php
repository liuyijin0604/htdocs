<?php

class m160412_050159_create_crm_table extends CDbMigration
{
	public function up()
	{
        try {
            $this->createTable('crm', array(
                'id' => 'pk',
                'operator_id' => 'int(11) NOT NULL',
                'parcel_id' => 'int(11) NOT NULL',
                'level' => 'tinyint(3) NOT NULL',
                'type' => 'int(11) NOT NULL',
                'create_time' => 'datetime NOT NULL DEFAULT CURRENT_TIMESTAMP',
                'close_time' => 'datetime DEFAULT NULL',
                'status' => "tinyint(3) NOT NULL DEFAULT '0' COMMENT 'case processing status:\\n0 : just open in progress\\n1 :  finished\\n'"
            ),
                "ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='save CRM related data'");


            $this->createIndex('idx_operator','crm','operator_id');
            $this->createIndex('idx_parcel','crm','parcel_id');

        }catch(Exception $e)
        {
            echo "Exception: ".$e->getMessage()."\n";
            return false;
        }
	}

	public function down()
	{
        $this->dropTable('crm');
		//echo "m160412_050159_create_crm_table does not support migration down.\n";
		return true;
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