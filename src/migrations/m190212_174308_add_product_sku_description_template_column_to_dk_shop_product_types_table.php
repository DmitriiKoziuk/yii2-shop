<?php

use yii\db\Migration;

/**
 * Handles adding product_sku_description_template to table `{{%dk_shop_product_types}}`.
 */
class m190212_174308_add_product_sku_description_template_column_to_dk_shop_product_types_table extends Migration
{
    private $_productsTypesTable = '{{%dk_shop_product_types}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            $this->_productsTypesTable,
            'product_sku_description_template',
            $this->string(255)->defaultValue(NULL)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(
            $this->_productsTypesTable,
            'product_sku_description_template'
        );
    }
}
