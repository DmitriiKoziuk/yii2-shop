<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dk_shop_eav_value_type_units}}`.
 */
class m191021_090811_create_dk_shop_eav_value_type_units_table extends Migration
{
    private $eavValueTypeUnitsTableName = '{{%dk_shop_eav_value_type_units}}';

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
        $this->createTable($this->eavValueTypeUnitsTableName, [
            'id' => $this->primaryKey(),
            'value_type_id' => $this->integer()->notNull(),
            'name' => $this->string(45)->notNull(),
            'abbreviation' => $this->string(45)->notNull(),
            'code' => $this->string(45)->notNull(),
        ], $tableOptions);
        $this->createIndex(
            'dk_shop_eav_value_type_units_idx_value_type_id',
            $this->eavValueTypeUnitsTableName,
            'value_type_id'
        );
        $this->addForeignKey(
            'dk_shop_eav_value_type_units_fk_value_type_id',
            $this->eavValueTypeUnitsTableName,
            'value_type_id',
            $this->eavValueTypesTableName,
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'dk_shop_eav_value_type_units_fk_value_type_id',
            $this->eavValueTypeUnitsTableName
        );
        $this->dropTable($this->eavValueTypeUnitsTableName);
    }
}
