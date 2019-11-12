<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dk_shop_eav_value_text}}`.
 */
class m191021_094105_create_dk_shop_eav_value_text_table extends Migration
{
    private $eavValueTextTableName = '{{%dk_shop_eav_value_text}}';

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
        $this->createTable($this->eavValueTextTableName, [
            'id' => $this->primaryKey(),
            'attribute_id' => $this->integer()->notNull(),
            'value' => $this->text()->notNull(),
        ], $tableOptions);
        $this->createIndex(
            'dk_shop_eav_value_text_idx_attribute_id',
            $this->eavValueTextTableName,
            'attribute_id'
        );
        $this->addForeignKey(
            'dk_shop_eav_value_text_fk_attribute_id',
            $this->eavValueTextTableName,
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
            'dk_shop_eav_value_text_fk_attribute_id',
            $this->eavValueTextTableName
        );
        $this->dropTable($this->eavValueTextTableName);
    }
}
