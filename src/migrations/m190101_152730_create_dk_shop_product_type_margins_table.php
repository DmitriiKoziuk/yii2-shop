<?php

use yii\db\Migration;

/**
 * Handles the creation of table `dk_shop_product_type_margins`.
 */
class m190101_152730_create_dk_shop_product_type_margins_table extends Migration
{
    private $_productTypeMarginsTable = '{{%dk_shop_product_type_margins}}';
    private $_productTypesTable = '{{%dk_shop_product_types}}';
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
        $this->createTable($this->_productTypeMarginsTable, [
            'product_type_id' => $this->integer()->notNull(),
            'currency_id' => $this->integer()->notNull(),
            'type' => $this->integer()->notNull()->unsigned(),
            'value' => $this->money(10,2)
                ->notNull()->unsigned()->defaultValue(0.00),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull(),
        ], $tableOptions);
        $this->addPrimaryKey(
            'idx_primary',
            $this->_productTypeMarginsTable,
            [
                'product_type_id',
                'currency_id',
            ]
        );
        $this->createIndex(
            'idx_product_type_margins_type_id',
            $this->_productTypeMarginsTable,
            'product_type_id'
        );
        $this->createIndex(
            'idx_product_type_margins_currency_id',
            $this->_productTypeMarginsTable,
            'currency_id'
        );
        $this->addForeignKey(
            'fk_product_type_margins_product_type_id',
            $this->_productTypeMarginsTable,
            'product_type_id',
            $this->_productTypesTable,
            'id',
            'RESTRICT',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_product_type_margins_currency_id',
            $this->_productTypeMarginsTable,
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
            'fk_product_type_margins_currency_id',
            $this->_productTypeMarginsTable
        );
        $this->dropForeignKey(
            'fk_product_type_margins_product_type_id',
            $this->_productTypeMarginsTable
        );
        $this->dropTable($this->_productTypeMarginsTable);
    }
}
