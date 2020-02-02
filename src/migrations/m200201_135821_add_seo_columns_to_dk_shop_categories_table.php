<?php

namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%dk_shop_categories}}`.
 */
class m200201_135821_add_seo_columns_to_dk_shop_categories_table extends Migration
{
    private $categoriesTable = '{{%dk_shop_categories}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            $this->categoriesTable,
            'filtered_title_template',
            $this->string(255)->null()->defaultValue(NULL)
        );
        $this->addColumn(
            $this->categoriesTable,
            'filtered_description_template',
            $this->string(255)->null()->defaultValue(NULL)
        );
        $this->addColumn(
            $this->categoriesTable,
            'filtered_h1_template',
            $this->string(255)->null()->defaultValue(NULL)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(
            $this->categoriesTable,
            'filtered_title_template'
        );
        $this->dropColumn(
            $this->categoriesTable,
            'filtered_description_template'
        );
        $this->dropColumn(
            $this->categoriesTable,
            'filtered_h1_template'
        );
    }
}
