<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\forms\product;

use yii\base\Model;

class ProductCreateForm extends Model
{
    public $name;

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 110],
            [['name'], 'trim'],
        ];
    }
}
