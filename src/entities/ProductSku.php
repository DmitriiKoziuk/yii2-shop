<?php
namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use DmitriiKoziuk\yii2Base\BaseModule;
use DmitriiKoziuk\yii2Shop\ShopModule;

/**
 * This is the model class for table "{{%dk_shop_product_skus}}".
 *
 * @property int    $id
 * @property int    $product_id
 * @property string $name
 * @property string $slug
 * @property string $url
 * @property int    $stock_status
 * @property string $sell_price
 * @property string $old_price
 * @property string $price_on_site
 * @property int    $sell_price_strategy
 * @property string $meta_title
 * @property string $meta_description
 * @property string $short_description
 * @property string $description
 * @property int    $sort
 * @property int    $created_at
 * @property int    $updated_at
 * @property int    $currency_id
 *
 * @property Currency $currency
 * @property Product  $product
 */
class ProductSku extends ActiveRecord
{
    const FRONTEND_CONTROLLER_NAME = 'product-sku';
    const FRONTEND_ACTION_NAME = 'index';

    const FILE_ENTITY_NAME = 'dk-shop-product-skus';

    const STOCK_IN = 1;
    const STOCK_OUT = 2;
    const STOCK_AWAIT = 3;
    const STOCK_STATUS_NOT_SET = 4;

    const SELL_PRICE_STRATEGY_MARGIN = 1;
    const SELL_PRICE_STRATEGY_STATIC = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dk_shop_product_skus}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                ['product_id', 'slug', 'url', 'sort'],
                'required'
            ],
            [
                ['product_id', 'stock_status', 'sell_price_strategy', 'created_at', 'updated_at', 'currency_id', 'sort'],
                'integer'
            ],
            [['name'], 'string', 'max' => 45],
            ['name', 'unique', 'targetAttribute' => ['product_id', 'name']],
            [['slug'], 'string', 'max' => 180],
            [['url'], 'string', 'max' => 255],
            [['url'], 'unique'],
            ['sort', 'unique', 'targetAttribute' => ['product_id', 'sort']],
            [['sell_price', 'old_price', 'price_on_site'], 'number'],
            [['sell_price', 'old_price', 'price_on_site'], 'default', 'value' => '0.00'],
            [['stock_status'], 'default', 'value' => ProductSku::STOCK_STATUS_NOT_SET],
            [['sell_price_strategy'], 'default', 'value' => ProductSku::SELL_PRICE_STRATEGY_STATIC],
            [['meta_title'], 'string', 'max' => 255],
            [['meta_description'], 'string', 'max' => 500],
            [['short_description', 'description'], 'string'],
            [['name', 'slug', 'url', 'sell_price', 'old_price', 'meta_title', 'meta_description'], 'trim'],
            [['meta_title', 'meta_description', 'short_description', 'description'], 'default', 'value' => null],
            [
                ['currency_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Currency::class,
                'targetAttribute' => ['currency_id' => 'id']
            ],
            [
                ['product_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Product::class,
                'targetAttribute' => ['product_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                  => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'ID'),
            'product_id'          => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'Product ID'),
            'name'                => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'Name'),
            'slug'                => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'Slug'),
            'url'                 => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'Url'),
            'stock_status'        => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'Stock Status'),
            'sell_price'          => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'Sell Price'),
            'old_price'           => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'Old Price'),
            'price_on_site'       => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'Price on site'),
            'sell_price_strategy' => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'Sell price strategy'),
            'meta_title'          => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'Meta title'),
            'meta_description'    => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'Meta description'),
            'short_description'   => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'Short description'),
            'description'         => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'Description'),
            'sort'                => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'Sort'),
            'created_at'          => Yii::t(BaseModule::TRANSLATE, 'Created At'),
            'updated_at'          => Yii::t(BaseModule::TRANSLATE, 'Updated At'),
            'currency_id'         => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'Currency ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency()
    {
        return $this->hasOne(Currency::class, ['id' => 'currency_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * Use to find product images.
     * @return string
     */
    public function getImageEntityName()
    {
        return self::FILE_ENTITY_NAME;
    }

    public function getImageName()
    {
        return $this->product->slug . '-' . $this->slug;
    }

    public function getImageSavePath()
    {
        $path = \Yii::getAlias('@frontend') .
            '/web/uploads/' .
            static::FILE_ENTITY_NAME .
            '/' .
            $this->id .
            '/images/originals';
        return $path;
    }

    public function getTypeID()
    {
        if (! empty($this->product)) {
            return $this->product->type_id;
        }
        return null;
    }

    public function getCategoryID()
    {
        if (! empty($this->product)) {
            return $this->product->category_id;
        }
        return null;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getProductName()
    {
        return $this->product->name;
    }

    public function getTypeName()
    {
        return $this->product->getTypeName();
    }

    public function getCategoryName()
    {
        return $this->product->getCategoryName();
    }

    public function getCurrencyCode()
    {
        if (! empty($this->currency)) {
            return $this->currency->code;
        }
        return null;
    }

    public function getBrandId(): ?int
    {
        return $this->product->brand_id;
    }

    public static function getStockVariation($key = null)
    {
        $variation = [
            static::STOCK_IN             => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'In Stock'),
            static::STOCK_OUT            => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Out of Stock'),
            static::STOCK_AWAIT          => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Await'),
            static::STOCK_STATUS_NOT_SET => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Not set'),
        ];

        if (! empty($key)) {
            return $variation[$key];
        }

        return $variation;
    }

    public static function getSellPriceStrategyVariation($key = null)
    {
        $variation = [
            static::SELL_PRICE_STRATEGY_MARGIN => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Margin'),
            static::SELL_PRICE_STRATEGY_STATIC => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Static'),
        ];

        if (! empty($key)) {
            return $variation[$key];
        }

        return $variation;
    }

    public static function getNextSortNumber(int $productID)
    {
        $count = (int) ProductSku::find()->where(['product_id' => $productID])->count();
        return ++$count;
    }
}
