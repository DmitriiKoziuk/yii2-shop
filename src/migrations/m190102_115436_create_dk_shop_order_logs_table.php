<?php
namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `dk_shop_order_logs`.
 */
class m190102_115436_create_dk_shop_order_logs_table extends Migration
{
    private $_orderLogsTable = '{{%dk_shop_order_stage_logs}}';
    private $_ordersTable = '{{%dk_shop_orders}}';
    private $_userTable = '{{%user}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->_orderLogsTable, [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->defaultValue(NULL),
            'stage_id' => $this->integer()->notNull(),
            'comment' => $this->text()->defaultValue(NULL),
            'created_at' => $this->integer()->unsigned()->notNull(),
        ], $tableOptions);
        $this->createIndex(
            'idx_order_stage_logs_order_id',
            $this->_orderLogsTable,
            'order_id'
        );
        $this->createIndex(
            'idx_order_stage_logs_user_id',
            $this->_orderLogsTable,
            'user_id'
        );
        $this->addForeignKey(
            'fk_order_stage_logs_order_id',
            $this->_orderLogsTable,
            'order_id',
            $this->_ordersTable,
            'id',
            'RESTRICT',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_order_stage_logs_user_id',
            $this->_orderLogsTable,
            'user_id',
            $this->_userTable,
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
        $this->dropForeignKey('fk_order_stage_logs_order_id', $this->_orderLogsTable);
        $this->dropForeignKey('fk_order_stage_logs_user_id', $this->_orderLogsTable);
        $this->dropTable($this->_orderLogsTable);
    }
}
