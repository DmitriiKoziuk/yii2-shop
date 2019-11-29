<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dk_shop_product_type_attribute}}`.
 */
class m191021_091234_create_dk_shop_product_type_attribute_table extends Migration
{
    private $productTypeAttributeTableName = '{{%dk_shop_product_type_attribute}}';

    private $productsTypesTable = '{{%dk_shop_product_types}}';

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
        $this->createTable('{{%dk_shop_product_type_attribute}}', [
            'product_type_id' => $this->integer()->notNull(),
            'attribute_id' => $this->integer()->notNull(),
            'view_attribute_at_product_preview' => $this->tinyInteger()->notNull()->defaultValue(0),
            'sort' => $this->smallInteger()->notNull()->defaultValue(1),
        ], $tableOptions);
        $this->addPrimaryKey(
            'dk_shop_product_type_attribute_primary_key',
            $this->productTypeAttributeTableName,
            [
                'product_type_id',
                'attribute_id',
            ]
        );
        $this->createIndex(
            'dk_shop_product_type_attribute_idx_product_type_id',
            $this->productTypeAttributeTableName,
            'product_type_id'
        );
        $this->createIndex(
            'dk_shop_product_type_attribute_idx_attribute_id',
            $this->productTypeAttributeTableName,
            'attribute_id'
        );
        $this->addForeignKey(
            'dk_shop_product_type_attribute_fk_product_type_id',
            $this->productTypeAttributeTableName,
            'product_type_id',
            $this->productsTypesTable,
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'dk_shop_product_type_attribute_fk_attribute_id',
            $this->productTypeAttributeTableName,
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
            'dk_shop_product_type_attribute_fk_attribute_id',
            $this->productTypeAttributeTableName
        );
        $this->dropForeignKey(
            'dk_shop_product_type_attribute_fk_product_type_id',
            $this->productTypeAttributeTableName
        );
        $this->dropTable('{{%dk_shop_product_type_attribute}}');
    }
}
