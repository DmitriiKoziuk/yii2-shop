<?php
namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use DmitriiKoziuk\yii2Shop\ShopModule;

/**
 * This is the model class for table "{{%dk_shop_products}}".
 *
 * @property int    $id
 * @property string $name
 * @property string $slug
 * @property string $url
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
 */
class Product extends ActiveRecord
{
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
            ['name', 'unique'],
            [['slug'], 'required'],
            [['slug'], 'string', 'max' => 130],
            [['url'], 'required'],
            [['url'], 'string', 'max' => 255],
            ['url', 'unique'],
            [['name', 'slug', 'url'], 'trim'],
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
            'url'         => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Url'),
            'created_at'  => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Created At'),
            'updated_at'  => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Updated At'),
            'category_id' => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Category'),
            'type_id'     => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Type'),
            'main_sku_id' => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Main sku id'),
            'brand_id'    => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Brand id'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSkus()
    {
        return $this->hasMany(ProductSku::class, ['product_id' => 'id'])
            ->orderBy('sort')
            ->indexBy('id');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(ProductType::class, ['id' => 'type_id']);
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
}
