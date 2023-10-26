<?php

class m160412_063235_create_crm_types extends CDbMigration
{
	public function up()
	{
        $this->insert('olist',array('id' => 1 , 'pid' => 0 , 'item' => '客服类型' , 'weight' => 0 , 'active' => 1));
        $this->insert('olist',array('id' => 2 , 'pid' => 1 , 'item' => '索赔' , 'weight' => 0 , 'active' => 1));
        $this->insert('olist',array('id' => 3 , 'pid' => 1 , 'item' => '信息不全' , 'weight' => 0 , 'active' => 1));
        $this->insert('olist',array('id' => 4 , 'pid' => 1 , 'item' => '包裹丢失' , 'weight' => 0 , 'active' => 1));

        $this->insert('olist',array('id' => 5 , 'pid' => 0 , 'item' => '处理级别' , 'weight' => 0 , 'active' => 1));
        $this->insert('olist',array('id' => 6 , 'pid' => 5 , 'item' => '普通' , 'weight' => 0 , 'active' => 1));
        $this->insert('olist',array('id' => 7 , 'pid' => 5 , 'item' => '加急' , 'weight' => 0 , 'active' => 1));
        $this->insert('olist',array('id' => 8 , 'pid' => 5 , 'item' => '特急' , 'weight' => 0 , 'active' => 1));
        $this->insert('olist',array('id' => 9 , 'pid' => 1 , 'item' => '其他' , 'weight' => 0 , 'active' => 1));

	}

	public function down()
	{
		echo "m160412_063235_create_crm_types does not support migration down.\n";
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