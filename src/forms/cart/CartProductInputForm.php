<?php
namespace DmitriiKoziuk\yii2Shop\forms\cart;

use yii\base\Model;

class CartProductInputForm extends Model
{
    public $productSkuId;
    public $cartKey;

    public function rules()
    {
        return [
            [['productSkuId'], 'required'],
            [['productSkuId'], 'filter', 'filter' => function ($value) {
                return intval($value);
            }],
            [['cartKey'], 'string', 'max' => 32],
        ];
    }
}