<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\forms\product;

use yii\base\Model;

class ProductUpdateForm extends Model
{
    public $name;
    public $slug;
    public $url;
    public $category_id;
    public $type_id;
    public $brand_id;

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 110],
            [['name'], 'trim'],
            [['slug'], 'required'],
            [['slug'], 'string', 'max' => 130],
            [['slug'], 'trim'],
            [['url'], 'required'],
            [['url'], 'string', 'max' => 255],
            [['url'], 'trim'],
            [['category_id', 'type_id'], 'integer'],
            [['category_id', 'type_id', 'brand_id'], 'filter', 'filter' => function ($value) {
                    return empty($value) ? null : intval($value);
                }
            ],
        ];
    }
}
