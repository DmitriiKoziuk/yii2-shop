<?php

use yii\db\Migration;

/**
 * Handles the creation of table `dk_shop_products`.
 */
class m190101_155004_create_dk_shop_products_table extends Migration
{
    private $_productsTable = '{{%dk_shop_products}}';
    private $_categoriesTable = '{{%dk_shop_categories}}';
    private $_productTypesTable = '{{%dk_shop_product_types}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->_productsTable, [
            'id' => $this->primaryKey(),
            'name' => $this->string(110)->notNull(),
            'slug' => $this->string(130)->notNull()
                ->comment('Depends from name.'),
            'url'         => $this->string(255)->notNull()
                ->comment('Depends form slug and shop_product_types.product_url_prefix.'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'category_id' => $this->integer()->defaultValue(NULL),
            'type_id' => $this->integer()->defaultValue(NULL),
        ], $tableOptions);

        $this->createIndex(
            'idx_products_name',
            $this->_productsTable,
            'name',
            true
        );
        $this->createIndex(
            'idx_products_url',
            $this->_productsTable,
            'url',
            true
        );
        $this->createIndex(
            'idx_products_category_id',
            $this->_productsTable,
            'category_id'
        );
        $this->createIndex(
            'idx_products_type_id',
            $this->_productsTable,
            'type_id'
        );

        $this->addForeignKey(
            'fk_products_category_id',
            $this->_productsTable,
            'category_id',
            $this->_categoriesTable,
            'id',
            'SET NULL',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_products_type_id',
            $this->_productsTable,
            'type_id',
            $this->_productTypesTable,
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
        $this->dropForeignKey('fk_products_category_id', $this->_productsTable);
        $this->dropForeignKey('fk_products_type_id', $this->_productsTable);
        $this->dropTable($this->_productsTable);
    }
}
