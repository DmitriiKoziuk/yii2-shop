<?php

use yii\db\Migration;

/**
 * Handles the creation of table `dk_shop_category_product_sku`.
 */
class m190102_120453_create_dk_shop_category_product_sku_table extends Migration
{
    private $_productSkuCategoryTable = '{{%dk_shop_category_product_sku}}';
    private $_productCategoriesTable = '{{%dk_shop_categories}}';
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
        $this->createTable($this->_productSkuCategoryTable, [
            'category_id' => $this->integer()->notNull(),
            'product_sku_id' => $this->integer()->notNull(),
            'sort' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey(
            'primary-key',
            $this->_productSkuCategoryTable,
            [
                'category_id',
                'product_sku_id',
            ]
        );

        $this->createIndex(
            'idx_category_product_sku_product_sku_id',
            $this->_productSkuCategoryTable,
            'product_sku_id'
        );
        $this->createIndex(
            'idx_category_product_sku_category_id',
            $this->_productSkuCategoryTable,
            'product_sku_id'
        );
        $this->createIndex(
            'idx_category_product_sku_sort',
            $this->_productSkuCategoryTable,
            [
                'category_id',
                'sort',
            ],
            true
        );

        $this->addForeignKey(
            'fk_category_product_sku_category_id',
            $this->_productSkuCategoryTable,
            'category_id',
            $this->_productCategoriesTable,
            'id',
            'RESTRICT',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_category_product_sku_product_sku_id',
            $this->_productSkuCategoryTable,
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
            'fk_category_product_sku_category_id',
            $this->_productSkuCategoryTable
        );
        $this->dropForeignKey(
            'fk_category_product_sku_product_sku_id',
            $this->_productSkuCategoryTable
        );
        $this->dropTable($this->_productSkuCategoryTable);
    }
}
