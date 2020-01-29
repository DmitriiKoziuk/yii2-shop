<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use DmitriiKoziuk\yii2Shop\ShopModule;
use DmitriiKoziuk\yii2FileManager\entities\FileEntity;
use DmitriiKoziuk\yii2FileManager\repositories\FileRepository;
use DmitriiKoziuk\yii2Shop\interfaces\productEav\ProductEavValueInterface;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2UrlIndex\repositories\UrlRepository;

/**
 * This is the model class for table "{{%dk_shop_product_skus}}".
 *
 * @property int    $id
 * @property int    $product_id
 * @property string $name
 * @property string $slug
 * @property int    $stock_status
 * @property string $sell_price
 * @property string $old_price
 * @property int    $customer_price
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
 * @property Currency                $currency
 * @property Product                 $product
 * @property EavValueVarcharEntity[] $eavVarcharValues
 * @property EavValueTextEntity[]    $eavTextValues
 * @property EavValueDoubleEntity[]  $eavDoubleValues
 * @property UrlEntity               $urlEntity
 * @property FileEntity              $mainImageEntity
 * @property FileEntity[]            $imageEntities
 * @property CategoryProductSku[]    $categoryProductSkuEntities
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
     * @var FileRepository
     */
    private $fileRepository;

    /**
     * @var FileEntity[]
     */
    private $images;

    /**
     * @var UrlRepository
     */
    private $urlRepository;

    /**
     * @var null|ProductEavValueInterface[]
     */
    private $previewEavValues;

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
            [['product_id', 'slug', 'sort'], 'required'],
            [
                [
                    'product_id',
                    'stock_status',
                    'sell_price_strategy',
                    'created_at',
                    'updated_at',
                    'currency_id',
                    'sort'
                ],
                'integer'
            ],
            [['name'], 'string', 'max' => 45],
            ['name', 'unique', 'targetAttribute' => ['product_id', 'name']],
            [['slug'], 'string', 'max' => 180],
            ['sort', 'unique', 'targetAttribute' => ['product_id', 'sort']],
            [['sell_price', 'old_price', 'customer_price'], 'integer'],
            [['sell_price', 'old_price', 'customer_price'], 'default', 'value' => NULL],
            [['stock_status'], 'default', 'value' => ProductSku::STOCK_STATUS_NOT_SET],
            [['sell_price_strategy'], 'default', 'value' => ProductSku::SELL_PRICE_STRATEGY_STATIC],
            [['meta_title'], 'string', 'max' => 255],
            [['meta_description'], 'string', 'max' => 500],
            [['short_description', 'description'], 'string'],
            [['name', 'slug', 'sell_price', 'old_price', 'meta_title', 'meta_description'], 'trim'],
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
            'stock_status'        => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'Stock Status'),
            'sell_price'          => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'Sell Price'),
            'old_price'           => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'Old Price'),
            'customer_price'      => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'Customer price'),
            'sell_price_strategy' => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'Sell price strategy'),
            'meta_title'          => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'Meta title'),
            'meta_description'    => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'Meta description'),
            'short_description'   => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'Short description'),
            'description'         => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'Description'),
            'sort'                => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'Sort'),
            'created_at'          => Yii::t('app', 'Created At'),
            'updated_at'          => Yii::t('app', 'Updated At'),
            'currency_id'         => Yii::t(ShopModule::TRANSLATION_PRODUCT_SKU, 'Currency ID'),
        ];
    }

    /**
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     */
    public function init()
    {
        /** @var FileRepository fileRepository */
        $this->fileRepository = Yii::$container->get(FileRepository::class);
        /** @var UrlRepository urlIndexService */
        $this->urlRepository = Yii::$container->get(UrlRepository::class);
    }

    public function afterFind()
    {
    }

    public function getCurrency(): ActiveQuery
    {
        return $this->hasOne(Currency::class, ['id' => 'currency_id']);
    }

    public function getProduct(): ActiveQuery
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * @throws InvalidConfigException
     */
    public function getEavVarcharValues(): ActiveQuery
    {
        return $this->hasMany(EavValueVarcharEntity::class, ['id' => 'value_id'])
            ->viaTable(EavValueVarcharProductSkuEntity::tableName(), ['product_sku_id' => 'id'])
            ->indexBy('id');
    }

    /**
     * @throws InvalidConfigException
     */
    public function getEavTextValues(): ActiveQuery
    {
        return $this->hasMany(EavValueTextEntity::class, ['id' => 'value_id'])
            ->viaTable(EavValueTextProductSkuEntity::tableName(), ['product_sku_id' => 'id'])
            ->indexBy('id');
    }

    /**
     * @throws InvalidConfigException
     */
    public function getEavDoubleValues(): ActiveQuery
    {
        return $this->hasMany(EavValueDoubleEntity::class, ['id' => 'value_id'])
            ->viaTable(EavValueDoubleProductSkuEntity::tableName(), ['product_sku_id' => 'id'])
            ->indexBy('id');
    }

    public function getUrlEntity(): ActiveQuery
    {
        return $this->hasOne(UrlEntity::class, ['entity_id' => 'id'])
            ->andWhere([
                'module_name' => ShopModule::getId(),
                'controller_name' => self::FRONTEND_CONTROLLER_NAME,
                'action_name' => self::FRONTEND_ACTION_NAME,
            ]);
    }

    public function getMainImageEntity(): ActiveQuery
    {
        return $this->hasOne(FileEntity::class, ['entity_id' => 'id'])
            ->andWhere([FileEntity::tableName() . '.entity_name' => self::FILE_ENTITY_NAME])
            ->andWhere(['like', FileEntity::tableName() . '.mime_type', 'image%', false])
            ->orderBy([FileEntity::tableName() . '.sort' => SORT_ASC]);
    }

    public function getImageEntities(): ActiveQuery
    {
        return $this->hasMany(FileEntity::class, ['entity_id' => 'id'])
            ->andWhere(['entity_name' => self::FILE_ENTITY_NAME])
            ->andWhere(['like', FileEntity::tableName() . '.mime_type', 'image%', false])
            ->orderBy([FileEntity::tableName() . '.sort' => SORT_ASC])
            ->offset(1);
    }

    public function getCategoryProductSkuEntities(): ActiveQuery
    {
        return $this->hasMany(CategoryProductSku::class, ['product_sku_id' => 'id']);
    }

    public function isCustomerPriceSet(): bool
    {
        return !empty($this->customer_price);
    }

    public function isOldPriceSet(): bool
    {
        return !empty($this->old_price);
    }

    public function isCurrencySet(): bool
    {
        return empty($this->currency_id) ? false : true;
    }

    public function isPreviewEavValuesSet(): bool
    {
        return empty($this->getPreviewEavValues()) ? false : true;
    }

    public function isInStock(): bool
    {
        return $this->stock_status === self::STOCK_IN;
    }

    public function getTypeID()
    {
        if (! empty($this->product)) {
            return $this->product->type_id;
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


    /**
     * @return FileEntity[]
     */
    public function getImages(): array
    {
        return $this->imageEntities;
    }

    public function getMainImage(): ?FileEntity
    {
        return $this->mainImageEntity;
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

    public function getEavAttributes()
    {
        return ArrayHelper::merge(
            $this->eavVarcharValues,
            $this->eavTextValues,
            $this->eavDoubleValues
        );
    }

    /**
     * @return ProductEavValueInterface[]
     */
    public function getPreviewEavValues(): array
    {
        if (! empty($this->previewEavValues)) {
            return $this->previewEavValues;
        }
        $values = [];
        if ($this->product->isTypeSet()) {
            $previewAttributes = $this->product->type->productPreviewEavAttributes;
            if (! empty($previewAttributes)) {
                foreach ($this->eavVarcharValues as $value) {
                    if (array_key_exists($value->attribute_id, $previewAttributes)) {
                        $values[] = $value;
                    }
                }
                foreach ($this->eavDoubleValues as $value) {
                    if (array_key_exists($value->attribute_id, $previewAttributes)) {
                        $values[] = $value;
                    }
                }
                foreach ($this->eavTextValues as $value) {
                    if (array_key_exists($value->attribute_id, $previewAttributes)) {
                        $values[] = $value;
                    }
                }
                $this->sortEavValues(
                    $values,
                    $previewAttributes,
                    ProductTypeAttributeEntity::SORT_AT_PRODUCT_SKU_PREVIEW_PROPERTY_NAME
                );
                return $this->previewEavValues = &$values;
            }
        }
        return [];
    }

    /**
     * @return ProductEavValueInterface[]
     */
    public function getEavValues(): array
    {
        $values = [];
        if ($this->product->isTypeSet()) {
            $attributesSort = $this->getAttributeSortForProductPage();
            foreach ($this->eavVarcharValues as $value) {
                $values[] = $value;
            }
            foreach ($this->eavDoubleValues as $value) {
                $values[] = $value;
            }
            foreach ($this->eavTextValues as $value) {
                $values[] = $value;
            }
            if (! empty($values)) {
                $this->sortEavValues(
                    $values,
                    $attributesSort,
                    ProductTypeAttributeEntity::SORT_AT_PRODUCT_SKU_PAGE_PROPERTY_NAME
                );
            }
        }
        return $values;
    }

    public function getUrl(): string
    {
        return $this->urlEntity->url;
    }

    public function getOldPrice(): float
    {
        return $this->old_price / 100;
    }

    public function getSellPrice(): float
    {
        return $this->sell_price / 100;
    }

    public function getCustomerPrice(): float
    {
        return $this->customer_price / 100;
    }

    public function getSaving(): float
    {
        $value = 0;
        if (
            ! empty($this->old_price) &&
            ! empty($this->sell_price) &&
            $this->sell_price < $this->old_price
        ) {
            $value = ($this->getOldPrice() - $this->getSellPrice());
            if (! empty($value) && ! empty($this->currency)) {
                $value = $value * $this->currency->rate;
            }
        }
        return (float) $value;
    }

    private function getAttributeSortForProductPage(): array
    {
        return ProductTypeAttributeEntity::find()
            ->where([
                'product_type_id' => $this->product->type->id,
            ])
            ->indexBy('attribute_id')
            ->all();
    }

    /**
     * @param array $values
     * @param ProductTypeAttributeEntity[] $previewAttributes
     * @param string $byAttribute
     * @return void
     */
    private function sortEavValues(array &$values, array &$previewAttributes, string $byAttribute): void
    {
        usort(
            $values,
            function (
                ProductEavValueInterface $previousValue,
                ProductEavValueInterface $currentValue
            ) use ($previewAttributes, $byAttribute) {
                $previousValueSort = $previewAttributes[ $previousValue->getEavAttributeId() ]->$byAttribute;
                $currentValueSort = $previewAttributes[ $currentValue->getEavAttributeId() ]->$byAttribute;
                return $previousValueSort <=> $currentValueSort;
            }
        );
    }
}
