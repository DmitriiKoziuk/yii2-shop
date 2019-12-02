<?php
namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `dk_shop_product_skus`.
 */
class m190101_155340_create_dk_shop_product_skus_table extends Migration
{
    private $_productSkusTable = '{{%dk_shop_product_skus}}';
    private $_productsTable = '{{%dk_shop_products}}';
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
        $this->createTable($this->_productSkusTable, [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer(),
            'name' => $this->string(45)->notNull(),
            'slug' => $this->string(65)->notNull()
                ->comment('Depends from name.'),
            'url' => $this->string(355)->notNull()
                ->comment('Depends from slug and parent product url.'),
            'stock_status' => $this->integer()->unsigned()->notNull(),
            'sell_price_strategy' => $this->integer()->unsigned()->notNull(),
            'sell_price' => $this->integer()->unsigned()->null()->defaultValue(NULL),
            'old_price' => $this->integer()->unsigned()->null()->defaultValue(NULL),
            'customer_price' => $this->integer()->unsigned()->null()->defaultValue(NULL),
            'meta_title' => $this->string(255)->defaultValue(NULL),
            'meta_description' => $this->string(500)->defaultValue(NULL),
            'short_description' => $this->text()->defaultValue(NULL),
            'description' => $this->text()->defaultValue(NULL),
            'sort' => $this->integer()->unsigned()->notNull(),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull(),
            'currency_id' => $this->integer()->defaultValue(NULL),
        ], $tableOptions);

        $this->createIndex(
            'idx_product_skus_product_id',
            $this->_productSkusTable,
            'product_id'
        );
        $this->createIndex(
            'idx_product_skus_name',
            $this->_productSkusTable,
            [
                'product_id',
                'name',
            ],
            true
        );
        $this->createIndex(
            'idx_product_skus_slug',
            $this->_productSkusTable,
            [
                'product_id',
                'slug',
            ],
            true
        );
        $this->createIndex(
            'idx_product_skus_url',
            $this->_productSkusTable,
            'url',
            true
        );
        //Prevent duplicate product sku sort.
        $this->createIndex(
            'idx_product_skus_sort',
            $this->_productSkusTable,
            [
                'product_id',
                'sort',
            ],
            true
        );
        $this->createIndex(
            'idx_product_skus_currency_id',
            $this->_productSkusTable,
            'currency_id'
        );

        $this->addForeignKey(
            'fk_product_skus_product_id',
            $this->_productSkusTable,
            'product_id',
            $this->_productsTable,
            'id',
            'RESTRICT',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_product_skus_currency_id',
            $this->_productSkusTable,
            'currency_id',
            $this->_currenciesTable,
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
        $this->dropForeignKey('fk_product_skus_product_id', $this->_productSkusTable);
        $this->dropForeignKey('fk_product_skus_currency_id', $this->_productSkusTable);
        $this->dropTable($this->_productSkusTable);
    }
}
