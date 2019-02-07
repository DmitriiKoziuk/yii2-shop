<?php
namespace DmitriiKoziuk\yii2Shop\forms\product;

use DmitriiKoziuk\yii2Base\data\Data;

class ProductMarginUpdateForm extends Data
{
    public $product_type_id;
    public $currency_id;
    public $margin_type;
    public $margin_value;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_type_id', 'currency_id'], 'required'],
            [['product_type_id', 'currency_id', 'margin_type'], 'integer'],
            [['margin_value'], 'number'],
        ];
    }

    public function getUpdatedAttributes()
    {
        return $this->getAttributes([
            'margin_type',
            'margin_value',
        ]);
    }
}