<?php
namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Handles adding main_sku_id to table `dk_shop_products`.
 */
class m190102_115537_add_main_sku_id_column_to_dk_shop_products_table extends Migration
{
    private $_productsTable = '{{%dk_shop_products}}';
    private $_productSkusTable = '{{%dk_shop_product_skus}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            $this->_productsTable,
            'main_sku_id',
            $this->integer()->defaultValue(NULL)
        );
        $this->createIndex(
            'idx_products_main_sku_id',
            $this->_productsTable,
            'main_sku_id',
            true
        );
        $this->addForeignKey(
            'fk_products_main_sku_id',
            $this->_productsTable,
            'main_sku_id',
            $this->_productSkusTable,
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_products_main_sku_id', $this->_productsTable);
        $this->dropColumn($this->_productsTable, 'main_sku_id');
    }
}
