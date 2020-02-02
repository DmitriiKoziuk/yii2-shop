<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dk_shop_eav_value_varchar_seo_values}}`.
 */
class m200130_132648_create_dk_shop_eav_value_varchar_seo_values_table extends Migration
{
    private $eavValueVarcharSeoValuesTable = '{{%dk_shop_eav_value_varchar_seo_values}}';

    private $eavValueVarcharTableName = '{{%dk_shop_eav_value_varchar}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->eavValueVarcharSeoValuesTable, [
            'varchar_id' => $this->integer()->notNull(),
            'code'       => $this->string(10)->notNull(),
            'value'      => $this->string(255)->notNull(),
        ]);
        $this->addPrimaryKey(
            'pk_evvsv',
            $this->eavValueVarcharSeoValuesTable,
            [
                'varchar_id',
                'code',
            ]
        );
        $this->createIndex(
            'dk_shop_eav_value_varchar_seo_values_idx_value_id',
            $this->eavValueVarcharSeoValuesTable,
            'varchar_id'
        );
        $this->addForeignKey(
            'dk_shop_eav_value_varchar_seo_values_fk_value_id',
            $this->eavValueVarcharSeoValuesTable,
            'varchar_id',
            $this->eavValueVarcharTableName,
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
            'dk_shop_eav_value_varchar_seo_values_fk_value_id',
            $this->eavValueVarcharSeoValuesTable
        );
        $this->dropTable('{{%dk_shop_eav_value_varchar_seo_values}}');
    }
}
