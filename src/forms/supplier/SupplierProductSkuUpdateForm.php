<?php
namespace DmitriiKoziuk\yii2Shop\forms\supplier;

use Yii;
use DmitriiKoziuk\yii2Base\data\Data;

class SupplierProductSkuUpdateForm extends Data
{
    public $supplier_id;
    public $product_sku_id;
    public $supplier_product_unique_id;
    public $quantity;
    public $purchase_price;
    public $recommended_sell_price;
    public $currency_id;

    public function rules()
    {
        return [
            [['supplier_product_unique_id'], 'string', 'max' => 45],
            [['supplier_id', 'product_sku_id', 'quantity', 'currency_id'], 'integer'],
            [['purchase_price', 'recommended_sell_price'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'supplier_id' => Yii::t('app', 'Supplier ID'),
            'supplier_product_unique_id' => Yii::t('app', 'Supplier product unique id'),
            'quantity' => Yii::t('app', 'Quantity'),
            'purchase_price' => Yii::t('app', 'Purchase Price'),
            'recommended_sell_price' => Yii::t('app', 'Recommended Sell Price'),
            'currency_id' => Yii::t('app', 'Currency ID'),
        ];
    }

    public function getUpdatedAttributes()
    {
        return $this->getAttributes([
            'quantity',
            'currency_id',
            'purchase_price',
            'recommended_sell_price',
        ]);
    }
}