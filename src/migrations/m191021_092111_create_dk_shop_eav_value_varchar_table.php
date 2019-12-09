<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dk_shop_eav_value_varchar}}`.
 */
class m191021_092111_create_dk_shop_eav_value_varchar_table extends Migration
{
    private $eavValueVarcharTableName = '{{%dk_shop_eav_value_varchar}}';

    private $eavAttributeTableName = '{{%dk_shop_eav_attributes}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->eavValueVarcharTableName, [
            'id' => $this->primaryKey(),
            'attribute_id' => $this->integer()->notNull(),
            'value' => $this->string(255)->notNull(),
            'code' => $this->string(255)->notNull(),
        ], $tableOptions);
        $this->createIndex(
            'dk_shop_eav_value_varchar_idx_attribute_id',
            $this->eavValueVarcharTableName,
            'attribute_id'
        );
        $this->addForeignKey(
            'dk_shop_eav_value_varchar_fk_attribute_id',
            $this->eavValueVarcharTableName,
            'attribute_id',
            $this->eavAttributeTableName,
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
            'dk_shop_eav_value_varchar_fk_attribute_id',
            $this->eavValueVarcharTableName
        );
        $this->dropTable($this->eavValueVarcharTableName);
    }
}
