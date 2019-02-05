<?php
namespace DmitriiKoziuk\yii2Shop\forms\product;

use yii\base\Model;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2FileManager\entities\File;

class ProductSkuInputForm extends Model
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public $id;
    public $name;
    public $slug;
    public $url;
    public $stock_status;
    public $sell_price;
    public $old_price;
    public $price_on_site;
    public $sell_price_strategy;
    public $meta_title;
    public $meta_description;
    public $short_description;
    public $description;
    public $currency_id;

    /**
     * @var File[]
     */
    public $files;

    public function rules()
    {
        return [
            [
                ['name'], 'required',
                'on' => [self::SCENARIO_CREATE]
            ],
            [
                ['name', 'id', 'slug', 'url'], 'required',
                'on' => [self::SCENARIO_UPDATE]
            ],
            [
                ['stock_status', 'currency_id'], 'integer',
                'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]
            ],
            [
                ['sell_price', 'old_price', 'price_on_site'], 'number',
                'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]
            ],
            [
                ['name'], 'string', 'max' => 45,
                'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]
            ],
            [
                ['name'], 'trim',
                'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]
            ],
            [
                ['slug'], 'string', 'max' => 65,
                'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]
            ],
            [
                ['slug'], 'trim',
                'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]
            ],
            [
                ['url'], 'string', 'max' => 355,
                'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]
            ],
            [
                ['url'], 'trim',
                'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]
            ],
            [
                ['stock_status'], 'default', 'value' => ProductSku::STOCK_IN,
                'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]
            ],
            [
                ['sell_price_strategy'], 'default', 'value' => ProductSku::SELL_PRICE_STRATEGY_STATIC,
                'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]
            ],
            [
                ['sell_price', 'old_price', 'price_on_site'],
                'default', 'value' => '0.00',
                'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]
            ],
            [
                ['meta_title'], 'string', 'max' => 255,
                'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]
            ],
            [
                ['meta_description'], 'string', 'max' => 500,
                'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]
            ],
            [
                ['short_description', 'description'], 'string',
                'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]
            ],
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