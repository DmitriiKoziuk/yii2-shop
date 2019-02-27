<?php
namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dk_shop_supplier_prices}}`.
 */
class m190220_105820_create_dk_shop_supplier_prices_table extends Migration
{
    private $_supplierPriceTable = '{{%dk_shop_supplier_prices}}';
    private $_suppliersTable = '{{%dk_shop_suppliers}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->_supplierPriceTable, [
            'id' => $this->primaryKey(),
            'supplier_id' => $this->integer(),
            'job_id' => $this->string(45)->defaultValue(NULL),
            'created_at' => $this->integer()->unsigned()->notNull(),
        ]);
        $this->createIndex(
            'idx_dk_shop_supplier_prices_supplier_id',
            $this->_supplierPriceTable,
            'supplier_id'
        );
        $this->addForeignKey(
            'fk_dk_shop_supplier_prices_supplier_id',
            $this->_supplierPriceTable,
            'supplier_id',
            $this->_suppliersTable,
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
            'fk_dk_shop_supplier_prices_supplier_id',
            $this->_supplierPriceTable
        );
        $this->dropTable($this->_supplierPriceTable);
    }
}
