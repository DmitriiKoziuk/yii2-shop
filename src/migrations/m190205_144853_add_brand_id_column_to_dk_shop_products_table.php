<?php

use yii\db\Migration;

/**
 * Handles adding brand_id to table `dk_shop_products`.
 */
class m190205_144853_add_brand_id_column_to_dk_shop_products_table extends Migration
{
    private $_productsTable = '{{%dk_shop_products}}';
    private $_brandsTable = '{{%dk_shop_brands}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            $this->_productsTable,
            'brand_id',
            $this->integer()->defaultValue(NULL)
        );
        $this->createIndex(
            'idx_dk_shop_products_brand_id',
            $this->_productsTable,
            'brand_id'
        );
        $this->addForeignKey(
            'fk_dk_shop_products_brand_id',
            $this->_productsTable,
            'brand_id',
            $this->_brandsTable,
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
            'fk_dk_shop_products_brand_id',
            $this->_productsTable
        );
        $this->dropColumn(
            $this->_productsTable,
            'brand_id'
        );
    }
}
