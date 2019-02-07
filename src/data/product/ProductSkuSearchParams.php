<?php
namespace DmitriiKoziuk\yii2Shop\data\product;

use yii\base\Model;

class ProductSkuSearchParams extends Model
{
    public $product_sku_id;
    public $sell_price_strategy;
    public $currency_id;
    public $type_id;

    public function rules()
    {
        return [
            [['product_sku_id', 'sell_price_strategy', 'currency_id', 'type_id'], 'integer'],
        ];
    }
}