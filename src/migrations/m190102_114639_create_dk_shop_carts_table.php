<?php

use yii\db\Migration;

/**
 * Handles the creation of table `dk_shop_carts`.
 */
class m190102_114639_create_dk_shop_carts_table extends Migration
{
    private $_cartsTableName = '{{%dk_shop_carts}}';
    private $_customersTableName = '{{%dk_shop_customers}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->_cartsTableName, [
            'id' => $this->primaryKey(),
            'key' => $this->string(32)->defaultValue(NULL),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'customer_id' => $this->integer()->defaultValue(NULL),
        ], $tableOptions);

        $this->createIndex(
            'idx_carts_key',
            $this->_cartsTableName,
            'key'
        );
        $this->createIndex(
            'idx_carts_customer_id',
            $this->_cartsTableName,
            'customer_id'
        );

        $this->addForeignKey(
            'fk_carts_customer_id',
            $this->_cartsTableName,
            'customer_id',
            $this->_customersTableName,
            'id',
            'RESTRICT',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_carts_customer_id', $this->_cartsTableName);
        $this->dropTable($this->_cartsTableName);
    }
}
