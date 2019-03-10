<?php
namespace DmitriiKoziuk\yii2Shop\data\product;

use yii\base\Model;

class ProductSearchParams extends Model
{
    public $category_id;

    /**
     * @var int|array of ProductSku entity stock statuses.
     */
    public $stock_status;

    public function rules()
    {
        return [
            [['category_id'], 'integer'],
            [['category_id'], 'filter', 'filter' => function ($value) {
                return is_null($value) ? null : intval($value);
            }],
            [['stock_status'], 'safe'],
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