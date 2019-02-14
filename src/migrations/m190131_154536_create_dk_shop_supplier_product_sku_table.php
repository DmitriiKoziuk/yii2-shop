<?php
namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `dk_shop_supplier_product_sku`.
 */
class m190131_154536_create_dk_shop_supplier_product_sku_table extends Migration
{
    private $_supplierProductSkuTable = '{{%dk_shop_supplier_product_sku}}';
    private $_suppliersTable = '{{%dk_shop_suppliers}}';
    private $_productSkusTable = '{{%dk_shop_product_skus}}';
    private $_currenciesTable = '{{%dk_shop_currencies}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->_supplierProductSkuTable, [
            'supplier_id'                => $this->integer()->notNull(),
            'product_sku_id'             => $this->integer()->notNull(),
            'supplier_product_unique_id' => $this->string(45)->defaultValue(NULL),
            'quantity'                   => $this->integer()->unsigned()->defaultValue(NULL),
            'purchase_price'             => $this->money(10, 2)->unsigned()->defaultValue(NULL),
            'recommended_sell_price'     => $this->money(10, 2)->unsigned()->defaultValue(NULL),
            'currency_id'                => $this->integer()->defaultValue(NULL),
            'created_at'                 => $this->integer()->unsigned()->notNull(),
            'updated_at'                 => $this->integer()->unsigned()->notNull(),
        ], $tableOptions);
        $this->addPrimaryKey(
            'primary_key',
            $this->_supplierProductSkuTable,
            [
                'supplier_id',
                'product_sku_id',
            ]
        );
        $this->createIndex(
            'idx_dk_shop_supplier_product_sku_supplier_id',
            $this->_supplierProductSkuTable,
            'supplier_id'
        );
        $this->createIndex(
            'idx_dk_shop_supplier_product_sku_product_skus_id',
            $this->_supplierProductSkuTable,
            'product_sku_id'
        );
        $this->createIndex(
            'idx_dk_shop_supplier_product_sku_currency_id',
            $this->_supplierProductSkuTable,
            'currency_id'
        );
        $this->addForeignKey(
            'fk_dk_shop_supplier_product_sku_supplier_id',
            $this->_supplierProductSkuTable,
            'supplier_id',
            $this->_suppliersTable,
            'id',
            'RESTRICT',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_dk_shop_supplier_product_sku_product_sku_id',
            $this->_supplierProductSkuTable,
            'product_sku_id',
            $this->_productSkusTable,
            'id',
            'RESTRICT',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_dk_shop_supplier_product_sku_currency_id',
            $this->_supplierProductSkuTable,
            'currency_id',
            $this->_currenciesTable,
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
            'fk_dk_shop_supplier_product_sku_supplier_id',
            $this->_supplierProductSkuTable
        );
        $this->dropForeignKey(
            'fk_dk_shop_supplier_product_sku_product_sku_id',
            $this->_supplierProductSkuTable
        );
        $this->dropForeignKey(
            'fk_dk_shop_supplier_product_sku_currency_id',
            $this->_supplierProductSkuTable
        );
        $this->dropTable($this->_supplierProductSkuTable);
    }
}
