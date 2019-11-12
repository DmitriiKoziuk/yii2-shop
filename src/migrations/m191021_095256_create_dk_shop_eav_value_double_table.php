<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dk_shop_eav_value_double}}`.
 */
class m191021_095256_create_dk_shop_eav_value_double_table extends Migration
{
    private $eavValueDoubleTableName = '{{%dk_shop_eav_value_double}}';

    private $eavAttributeTableName = '{{%dk_shop_eav_attributes}}';

    private $eavValueTypeUnitsTableName = '{{%dk_shop_eav_value_type_units}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->eavValueDoubleTableName, [
            'id' => $this->primaryKey(),
            'attribute_id' => $this->integer()->notNull(),
            'value' => $this->double()->notNull(),
            'code' => $this->string(45)->notNull(),
            'value_type_unit_id' => $this->integer()->null()->defaultValue(NULL),
        ], $tableOptions);
        $this->createIndex(
            'dk_shop_eav_value_double_idx_attribute_id',
            $this->eavValueDoubleTableName,
            'attribute_id'
        );
        $this->createIndex(
            'dk_shop_eav_value_double_idx_value_type_unit_id',
            $this->eavValueDoubleTableName,
            'value_type_unit_id'
        );
        $this->addForeignKey(
            'dk_shop_eav_value_double_fk_attribute_id',
            $this->eavValueDoubleTableName,
            'attribute_id',
            $this->eavAttributeTableName,
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'dk_shop_eav_value_double_fk_value_type_unit_id',
            $this->eavValueDoubleTableName,
            'value_type_unit_id',
            $this->eavValueTypeUnitsTableName,
            'id',
            'CASCADE',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'dk_shop_eav_value_double_fk_attribute_id',
            $this->eavValueDoubleTableName
        );
        $this->dropForeignKey(
            'dk_shop_eav_value_double_fk_value_type_unit_id',
            $this->eavValueDoubleTableName
        );
        $this->dropTable($this->eavValueDoubleTableName);
    }
}
