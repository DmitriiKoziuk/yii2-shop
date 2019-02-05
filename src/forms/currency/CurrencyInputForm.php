<?php
namespace DmitriiKoziuk\yii2Shop\forms\currency;

use yii\base\Model;

class CurrencyInputForm extends Model
{
    public $code;
    public $name;
    public $symbol;
    public $rate;

    public function rules()
    {
        return [
            [['code', 'name', 'symbol'], 'required'],
            [['code', 'name', 'symbol'], 'string', 'max' => 25],
            [['rate'], 'number'],
            [['rate'], 'default', 'value' => '1.00'],
        ];
    }
}