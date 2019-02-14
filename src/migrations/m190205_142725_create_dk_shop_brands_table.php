<?php
namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `dk_shop_brands`.
 */
class m190205_142725_create_dk_shop_brands_table extends Migration
{
    private $_brandsTable = '{{%dk_shop_brands}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->_brandsTable, [
            'id'         => $this->primaryKey(),
            'name'       => $this->string(45)->notNull(),
            'code'       => $this->string(55)->notNull(),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull(),
        ]);
        $this->createIndex(
            'idx_dk_shop_name',
            $this->_brandsTable,
            'name',
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->_brandsTable);
    }
}
