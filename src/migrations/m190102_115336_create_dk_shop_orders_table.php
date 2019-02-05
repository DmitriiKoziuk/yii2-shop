<?php

use yii\db\Migration;

/**
 * Handles the creation of table `dk_shop_cart_orders`.
 */
class m190102_115336_create_dk_shop_orders_table extends Migration
{
    private $_ordersTable = '{{%dk_shop_orders}}';
    private $_cartsTable = '{{%dk_shop_carts}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->_ordersTable, [
            'id' => $this->primaryKey(),
            'customer_comment' => $this->text()->defaultValue(NULL),
        ], $tableOptions);
        $this->addForeignKey(
            'fk_orders_id_cart_id',
            $this->_ordersTable,
            'id',
            $this->_cartsTable,
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
        $this->dropForeignKey('fk_orders_id_cart_id', $this->_ordersTable);
        $this->dropTable($this->_ordersTable);
    }
}
