<?php

use yii\db\Migration;

/**
 * Handles the creation of table `dk_shop_product_type_margins`.
 */
class m190206_102930_create_dk_shop_product_type_margins_table extends Migration
{
    private $_marginsTable = '{{%dk_shop_product_type_margins}}';
    private $_productsTypesTable = '{{%dk_shop_product_types}}';
    private $_currenciesTable = '{{%dk_shop_currencies}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->_marginsTable, [
            'product_type_id' => $this->integer(),
            'currency_id' => $this->integer(),
            'margin_type' => $this->integer()->unsigned()->notNull(),
            'margin_value' => $this->money(10, 2)->unsigned()->notNull(),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull(),
        ]);
        $this->addPrimaryKey(
            'PRIMARY_KEY',
            $this->_marginsTable,
            [
                'product_type_id',
                'currency_id',
            ]
        );
        $this->createIndex(
            'idx_dk_shop_product_type_margins_product_type_id',
            $this->_marginsTable,
            'product_type_id'
        );
        $this->createIndex(
            'idx_dk_shop_product_type_margins_currency_id',
            $this->_marginsTable,
            'currency_id'
        );
        $this->addForeignKey(
            'fk_dk_shop_product_type_margins_product_type_id',
            $this->_marginsTable,
            'product_type_id',
            $this->_productsTypesTable,
            'id',
            'RESTRICT',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_dk_shop_product_type_margins_currency_id',
            $this->_marginsTable,
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
            'fk_dk_shop_product_type_margins_product_type_id',
            $this->_marginsTable
        );
        $this->dropForeignKey(
            'fk_dk_shop_product_type_margins_currency_id',
            $this->_marginsTable
        );
        $this->dropTable($this->_marginsTable);
    }
}
