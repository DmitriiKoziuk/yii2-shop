<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dk_shop_eav_value_double_product_sku}}`.
 */
class m191021_095804_create_dk_shop_eav_value_double_product_sku_table extends Migration
{
    private $eavValueDoubleProductSkuTableName = '{{%dk_shop_eav_value_double_product_sku}}';

    private $eavValueDoubleTableName = '{{%dk_shop_eav_value_double}}';

    private $productSkusTable = '{{%dk_shop_product_skus}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->eavValueDoubleProductSkuTableName, [
            'value_id' => $this->integer()->notNull(),
            'product_sku_id' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addPrimaryKey(
            'dk_shop_eav_value_double_product_sku_primary_key',
            $this->eavValueDoubleProductSkuTableName,
            [
                'value_id',
                'product_sku_id',
            ]
        );
        $this->createIndex(
            'dk_shop_eav_value_double_product_sku_idx_value_id',
            $this->eavValueDoubleProductSkuTableName,
            'value_id'
        );
        $this->createIndex(
            'dk_shop_eav_value_double_product_sku_idx_product_sku_id',
            $this->eavValueDoubleProductSkuTableName,
            'product_sku_id'
        );
        $this->addForeignKey(
            'dk_shop_eav_value_double_product_sku_fk_value_id',
            $this->eavValueDoubleProductSkuTableName,
            'value_id',
            $this->eavValueDoubleTableName,
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'dk_shop_eav_value_double_product_sku_fk_product_sku_id',
            $this->eavValueDoubleProductSkuTableName,
            'product_sku_id',
            $this->productSkusTable,
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
            'dk_shop_eav_value_double_product_sku_fk_product_sku_id',
            $this->eavValueDoubleProductSkuTableName
        );
        $this->dropForeignKey(
            'dk_shop_eav_value_double_product_sku_fk_value_id',
            $this->eavValueDoubleProductSkuTableName
        );
        $this->dropTable($this->eavValueDoubleProductSkuTableName);
    }
}
