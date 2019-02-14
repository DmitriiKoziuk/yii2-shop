<?php
namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Handles adding margin_strategy to table `dk_shop_product_types`.
 */
class m190206_102451_add_margin_strategy_column_to_dk_shop_product_types_table extends Migration
{
    private $_productsTypesTable = '{{%dk_shop_product_types}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            $this->_productsTypesTable,
            'margin_strategy',
            $this->integer()->unsigned()->defaultValue(null)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(
            $this->_productsTypesTable,
            'margin_strategy'
        );
    }
}
