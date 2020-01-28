<?php

namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%column_sort_from_dk_shop_category_product}}`.
 */
class m200128_133615_drop_column_sort_from_dk_shop_category_product_table extends Migration
{
    private $categoryProductTable = '{{%dk_shop_category_product}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex(
            'idx_category_product_sort',
            $this->categoryProductTable
        );
        $this->dropColumn(
            $this->categoryProductTable,
            'sort'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
