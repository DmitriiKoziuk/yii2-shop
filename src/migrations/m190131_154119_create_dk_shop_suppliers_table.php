<?php

use yii\db\Migration;

/**
 * Handles the creation of table `dk_shop_suppliers`.
 */
class m190131_154119_create_dk_shop_suppliers_table extends Migration
{
    private $_suppliersTable = '{{%dk_shop_suppliers}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->_suppliersTable, [
            'id' => $this->primaryKey(),
            'name' => $this->string(45)->notNull(),
            'phone_number' => $this->string(45)->notNull(),
            'email' => $this->string(45)->defaultValue(NULL),
            'info' => $this->text()->defaultValue(NULL),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->_suppliersTable);
    }
}
