<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\forms\product;

use yii\base\Model;

class ProductSkuCreateForm extends Model
{
    public $name;

    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 45],
        ];
    }
}
