<?php
namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `dk_shop_cart_products`.
 */
class m190102_115028_create_dk_shop_cart_products_table extends Migration
{
    private $_cartProductsTable = '{{%dk_shop_cart_products}}';
    private $_cartsTable = '{{%dk_shop_carts}}';
    private $_productSkusTable = '{{%dk_shop_product_skus}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->_cartProductsTable, [
            'cart_id' => $this->integer()->notNull(),
            'product_sku_id' => $this->integer()->notNull(),
            'quantity' => $this->integer()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull(),
        ], $tableOptions);
        $this->addPrimaryKey(
            'PRIMARY_KEY',
            $this->_cartProductsTable,
            [
                'cart_id',
                'product_sku_id',
            ]
        );
        $this->createIndex(
            'idx_cart_products_cart_id',
            $this->_cartProductsTable,
            'cart_id'
        );
        $this->createIndex(
            'idx_cart_products_product_sku_id',
            $this->_cartProductsTable,
            'product_sku_id'
        );
        $this->addForeignKey(
            'fk_cart_products_cart_id',
            $this->_cartProductsTable,
            'cart_id',
            $this->_cartsTable,
            'id',
            'RESTRICT',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_cart_products_product_sku_id',
            $this->_cartProductsTable,
            'product_sku_id',
            $this->_productSkusTable,
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
        $this->dropForeignKey(
            'fk_cart_products_cart_id',
            $this->_cartProductsTable
        );
        $this->dropForeignKey(
            'fk_cart_products_product_sku_id',
            $this->_cartProductsTable
        );
        $this->dropTable($this->_cartProductsTable);
    }
}
