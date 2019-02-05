<?php

namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%dk_shop_category_product_sku}}".
 *
 * @property int $category_id
 * @property int $product_sku_id
 * @property int $sort
 *
 * @property Category   $category
 * @property ProductSku $productSku
 */
class CategoryProductSku extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dk_shop_category_product_sku}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'product_sku_id', 'sort'], 'required'],
            [['category_id', 'product_sku_id', 'sort'], 'integer'],
            [['category_id', 'sort'], 'unique', 'targetAttribute' => ['category_id', 'sort']],
            [['category_id', 'product_sku_id'], 'unique', 'targetAttribute' => ['category_id', 'product_sku_id']],
            [
                ['category_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Category::class,
                'targetAttribute' => ['category_id' => 'id']
            ],
            [
                ['product_sku_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ProductSku::class,
                'targetAttribute' => ['product_sku_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'category_id'    => Yii::t('app', 'Category ID'),
            'product_sku_id' => Yii::t('app', 'Product Sku ID'),
            'sort'           => Yii::t('app', 'Sort'),
        ];
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
    public function getProductSku()
    {
        return $this->hasOne(ProductSku::class, ['id' => 'product_sku_id']);
    }
}
