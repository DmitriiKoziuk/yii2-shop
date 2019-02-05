<?php

use yii\db\Migration;

/**
 * Handles the creation of table `dk_shop_customers`.
 */
class m190102_114426_create_dk_shop_customers_table extends Migration
{
    private $_customersTable = '{{%dk_shop_customers}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->_customersTable, [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(45)->notNull(),
            'middle_name' => $this->string(45)->defaultValue(NULL),
            'last_name' => $this->string(45)->defaultValue(NULL),
            'phone_number' => $this->string(45)->notNull(),
            'email' => $this->string(255)->defaultValue(NULL),
            'password_hash' => $this->string(255)->defaultValue(NULL),
            'password_reset_token' => $this->string(255)->defaultValue(NULL),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull(),
        ], $tableOptions);

        $this->createIndex(
            'idx_customers_phone_number',
            $this->_customersTable,
            'phone_number',
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->_customersTable);
    }
}
