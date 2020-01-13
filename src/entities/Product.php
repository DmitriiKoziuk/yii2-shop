<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use DmitriiKoziuk\yii2Shop\ShopModule;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2UrlIndex\repositories\UrlRepository;

/**
 * This is the model class for table "{{%dk_shop_products}}".
 *
 * @property int    $id
 * @property string $name
 * @property string $slug
 * @property string $created_at
 * @property string $updated_at
 * @property int    $category_id
 * @property int    $type_id
 * @property int    $main_sku_id
 * @property int    $brand_id
 *
 * @property ProductSku[] $skus
 * @property Currency     $currency
 * @property Category     $category
 * @property ProductType  $type;
 * @property Brand        $brand
 */
class Product extends ActiveRecord
{
    const FRONTEND_CONTROLLER_NAME = 'product';
    const FRONTEND_ACTION_NAME = 'index';

    /**
     * @var UrlRepository
     */
    private $urlRepository;

    /**
     * @var UrlEntity
     */
    private $urlEntity;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%dk_shop_products}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 110],
            ['name', 'unique', 'targetAttribute' => ['type_id', 'name']],
            [['slug'], 'required'],
            [['slug'], 'string', 'max' => 130],
            [['name', 'slug'], 'trim'],
            [['category_id', 'type_id', 'main_sku_id', 'brand_id'], 'integer'],
            [['category_id', 'type_id', 'main_sku_id', 'brand_id'], 'filter', 'filter' => function ($value) {
                return empty($value) ? null : intval($value);
            }],
            [['category_id', 'type_id', 'main_sku_id', 'brand_id'], 'default', 'value' => NULL],
            [
                'main_sku_id',
                'exist',
                'skipOnError' => true,
                'targetClass' => ProductSku::class,
                'targetAttribute' => ['main_sku_id' => 'id']
            ],
            [
                'brand_id',
                'exist',
                'skipOnError' => true,
                'targetClass' => Brand::class,
                'targetAttribute' => ['brand_id' => 'id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'ID'),
            'name'        => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Name'),
            'slug'        => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Slug'),
            'created_at'  => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Created At'),
            'updated_at'  => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Updated At'),
            'category_id' => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Category'),
            'type_id'     => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Type'),
            'main_sku_id' => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Main sku id'),
            'brand_id'    => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Brand id'),
        ];
    }

    public function init()
    {
        parent::init();
        /** @var UrlRepository urlIndexService */
        $this->urlRepository = Yii::$container->get(UrlRepository::class);
    }

    /**
     * @return ActiveQuery
     */
    public function getSkus()
    {
        return $this->hasMany(ProductSku::class, ['product_id' => 'id'])
            ->orderBy('sort')
            ->indexBy('id');
    }

    /**
     * @return ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(ProductType::class, ['id' => 'type_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::class, ['id' => 'brand_id']);
    }

    public function getUrlEntity(): UrlEntity
    {
        if (empty($this->urlEntity)) {
            $this->urlEntity = $this->urlRepository->getEntityUrl(
                ShopModule::getId(),
                self::FRONTEND_CONTROLLER_NAME,
                self::FRONTEND_ACTION_NAME,
                (string) $this->id
            );
        }
        return $this->urlEntity;
    }

    public function getUrl(): string
    {
        return $this->getUrlEntity()->url;
    }

    public function getTypeName()
    {
        if (! empty($this->type)) {
            return $this->type->name;
        }
        return NULL;
    }

    public function getCategoryName()
    {
        if (! empty($this->category))
            return $this->category->name;
        return NULL;
    }

    public function getMainSku(): ProductSku
    {
        return $this->skus[ $this->main_sku_id ];
    }

    public function isMainSkuSet(): bool
    {
        return ! empty($this->main_sku_id);
    }

    public function isCategorySet(): bool
    {
        return ! empty($this->category_id);
    }

    public function isTypeSet(): bool
    {
        return ! empty($this->type_id);
    }

    public function isBrandSet(): bool
    {
        return ! empty($this->brand_id);
    }
}
