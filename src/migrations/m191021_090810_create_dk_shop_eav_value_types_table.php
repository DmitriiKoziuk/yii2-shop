<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dk_shop_eav_value_types}}`.
 */
class m191021_090810_create_dk_shop_eav_value_types_table extends Migration
{
    private $eavValueTypesTableName = '{{%dk_shop_eav_value_types}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->eavValueTypesTableName, [
            'id' => $this->primaryKey(),
            'name' => $this->string(45)->notNull(),
            'code' => $this->string(45)->notNull(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->eavValueTypesTableName);
    }
}
