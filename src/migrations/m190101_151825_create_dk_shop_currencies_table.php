<?php
namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `dk_shop_currencies`.
 */
class m190101_151825_create_dk_shop_currencies_table extends Migration
{
    private $_currenciesTable = '{{%dk_shop_currencies}}';
    private $_codeComment = 'The International Organization for Standardization publishes a list ' .
    'of standard currency codes referred to as the ISO 4217 code list';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->_currenciesTable, [
            'id' => $this->primaryKey(),
            'code' => $this->string(25)->notNull()->unique()->comment($this->_codeComment),
            'name' => $this->string(25)->notNull(),
            'symbol' => $this->string(25)->notNull(),
            'rate' => $this->money(10,2)->notNull()->defaultValue(1.00),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex(
            'idx_currencies_code',
            $this->_currenciesTable,
            'code',
            true
        );

        $this->insert(
            $this->_currenciesTable,
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'rate' => '1.00',
                'created_at' => time(),
                'updated_at' => time(),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->_currenciesTable);
    }
}
