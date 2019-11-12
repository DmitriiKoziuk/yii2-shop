<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dk_shop_eav_value_varchar_product_sku}}`.
 */
class m191021_092554_create_dk_shop_eav_value_varchar_product_sku_table extends Migration
{
    private $eavValueVarcharProductSkuTableName = '{{%dk_shop_eav_value_varchar_product_sku}}';

    private $eavValueVarcharTableName = '{{%dk_shop_eav_value_varchar}}';

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
        $this->createTable($this->eavValueVarcharProductSkuTableName, [
            'value_id' => $this->integer()->notNull(),
            'product_sku_id' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addPrimaryKey(
            'dk_shop_eav_value_varchar_product_sku_primary_key',
            $this->eavValueVarcharProductSkuTableName,
            [
                'value_id',
                'product_sku_id',
            ]
        );
        $this->createIndex(
            'dk_shop_eav_value_varchar_product_sku_idx_value_id',
            $this->eavValueVarcharProductSkuTableName,
            'value_id'
        );
        $this->createIndex(
            'dk_shop_eav_value_varchar_product_sku_idx_product_sku_id',
            $this->eavValueVarcharProductSkuTableName,
            'product_sku_id'
        );
        $this->addForeignKey(
            'dk_shop_eav_value_varchar_product_sku_fk_value_id',
            $this->eavValueVarcharProductSkuTableName,
            'value_id',
            $this->eavValueVarcharTableName,
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'dk_shop_eav_value_varchar_product_sku_fk_product_sku_id',
            $this->eavValueVarcharProductSkuTableName,
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
            'dk_shop_eav_value_varchar_product_sku_fk_product_sku_id',
            $this->eavValueVarcharProductSkuTableName
        );
        $this->dropForeignKey(
            'dk_shop_eav_value_varchar_product_sku_fk_value_id',
            $this->eavValueVarcharProductSkuTableName
        );
        $this->dropTable($this->eavValueVarcharProductSkuTableName);
    }
}
