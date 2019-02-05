<?php

use yii\db\Migration;

/**
 * Handles the creation of table `dk_shop_category_product`.
 */
class m190102_115807_create_dk_shop_category_product_table extends Migration
{
    private $_productCategoryTable = '{{%dk_shop_category_product}}';
    private $_productCategoriesTable = '{{%dk_shop_categories}}';
    private $_productsTable = '{{%dk_shop_products}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->_productCategoryTable, [
            'category_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'sort' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey(
            'PRIMARY_KEY',
            $this->_productCategoryTable,
            [
                'category_id',
                'product_id',
            ]
        );

        $this->createIndex(
            'idx_category_product_product_id',
            $this->_productCategoryTable,
            'product_id'
        );
        $this->createIndex(
            'idx_category_product_category_id',
            $this->_productCategoryTable,
            'category_id'
        );
        $this->createIndex(
            'idx_category_product_sort',
            $this->_productCategoryTable,
            [
                'category_id',
                'sort',
            ],
            true
        );

        $this->addForeignKey(
            'fk_category_product_category_id',
            $this->_productCategoryTable,
            'category_id',
            $this->_productCategoriesTable,
            'id',
            'RESTRICT',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_category_product_product_id',
            $this->_productCategoryTable,
            'product_id',
            $this->_productsTable,
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
            'fk_category_product_category_id',
            $this->_productCategoryTable
        );
        $this->dropForeignKey(
            'fk_category_product_product_id',
            $this->_productCategoryTable
        );
        $this->dropTable($this->_productCategoryTable);
    }
}
