<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\forms\product;

use yii\base\Model;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2FileManager\entities\FileEntity;

class ProductSkuUpdateForm extends Model
{
    public $id;
    public $name;
    public $slug;
    public $url;
    public $stock_status;
    public $sell_price;
    public $old_price;
    public $customer_price;
    public $sell_price_strategy;
    public $meta_title;
    public $meta_description;
    public $short_description;
    public $description;
    public $currency_id;

    /**
     * @var FileEntity[]
     */
    public $files;

    public function rules()
    {
        return [
            [['id', 'slug', 'url'], 'required'],
            [['stock_status', 'currency_id'], 'integer'],
            [['sell_price', 'old_price', 'customer_price'], 'number'],
            [['name'], 'string', 'max' => 45],
            [['name'], 'trim'],
            [['slug'], 'string', 'max' => 65],
            [['slug'], 'trim'],
            [['url'], 'string', 'max' => 355],
            [['url'], 'trim'],
            [['stock_status'], 'default', 'value' => ProductSku::STOCK_STATUS_NOT_SET],
            [['sell_price_strategy'], 'default', 'value' => ProductSku::SELL_PRICE_STRATEGY_STATIC],
            [['sell_price', 'old_price', 'customer_price'], 'default', 'value' => NULL],
            [['meta_title'], 'string', 'max' => 255],
            [['meta_description'], 'string', 'max' => 500],
            [['short_description', 'description'], 'string'],
        ];
    }

    public function getUpdatedAttributes()
    {
        return $this->getAttributes([
            'name',
            'slug',
            'url',
            'stock_status',
            'sell_price',
            'old_price',
            'sell_price_strategy',
            'meta_title',
            'meta_description',
            'short_description',
            'description',
            'currency_id',
        ]);
    }
}
