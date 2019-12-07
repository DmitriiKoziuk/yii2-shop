<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dk_shop_eav_attributes}}`.
 */
class m191021_090814_create_dk_shop_eav_attributes_table extends Migration
{
    private $eavAttributeTableName = '{{%dk_shop_eav_attributes}}';

    private $eavValueTypesTableName = '{{%dk_shop_eav_value_types}}';

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
        $this->createTable($this->eavAttributeTableName, [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'name_for_product' => $this->string(100)->null()->defaultValue(NULL),
            'name_for_filter' => $this->string(100)->null()->defaultValue(NULL),
            'code' => $this->string(120)->notNull(),
            'storage_type' => 'ENUM("varchar","text","double") NOT NULL',
            'selectable' => $this->boolean()->notNull()->defaultValue(0),
            'multiple' => $this->boolean()->notNull()->defaultValue(0),
            'view_at_frontend_faceted_navigation' => $this->boolean()->notNull()->defaultValue(0),
            'description_backend' => $this->text()->null()->defaultValue(NULL),
            'description_frontend' => $this->text()->null()->defaultValue(NULL),
            'value_type_id' => $this->integer()->null()->defaultValue(null),
            'default_value_type_unit_id' => $this->integer()->null()->defaultValue(NULL),
        ], $tableOptions);
        $this->createIndex(
            'dk_shop_eav_attributes_idx_value_type_id',
            $this->eavAttributeTableName,
            'value_type_id'
        );
        $this->createIndex(
            'dk_shop_eav_attributes_idx_default_value_type_unit_id',
            $this->eavAttributeTableName,
            'default_value_type_unit_id'
        );
        $this->addForeignKey(
            'dk_shop_eav_attributes_fk_value_type_id',
            $this->eavAttributeTableName,
            'value_type_id',
            $this->eavValueTypesTableName,
            'id',
            'SET NULL',
            'CASCADE'
        );
        $this->addForeignKey(
            'dk_shop_eav_attributes_fk_default_value_type_unit_id',
            $this->eavAttributeTableName,
            'default_value_type_unit_id',
            $this->eavValueTypeUnitsTableName,
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
        $this->dropForeignKey(
            'dk_shop_eav_attributes_fk_default_value_type_unit_id',
            $this->eavAttributeTableName
        );
        $this->dropForeignKey(
            'dk_shop_eav_attributes_fk_value_type_id',
            $this->eavAttributeTableName
        );
        $this->dropTable($this->eavAttributeTableName);
    }
}
