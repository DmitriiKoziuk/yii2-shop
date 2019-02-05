<?php
namespace DmitriiKoziuk\yii2Shop\data\product;

use yii\base\Model;

class ProductSearchParams extends Model
{
    public $category_id;

    public function rules()
    {
        return [
            [['category_id'], 'integer'],
            [['category_id'], 'filter', 'filter' => function ($value) {
                return is_null($value) ? null : intval($value);
            }],
        ];
    }

    /**
     * @return int|null
     */
    public function getCategoryId()
    {
        return $this->category_id;
    }
}