<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\entities;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "dk_shop_product_type_attribute".
 *
 * @property int $product_type_id
 * @property int $attribute_id
 * @property int $view_attribute_at_product_preview
 * @property int $sort
 * @property int $sort_at_product_sku_page
 *
 * @property ProductType $productType
 * @property EavAttributeEntity $attributeData
 */
class ProductTypeAttributeEntity extends ActiveRecord
{
    const PREVIEW_NO = 0;
    const PREVIEW_YES = 1;

    const SORT_AT_PRODUCT_SKU_PREVIEW_PROPERTY_NAME = 'sort';
    const SORT_AT_PRODUCT_SKU_PAGE_PROPERTY_NAME = 'sort_at_product_sku_page';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dk_shop_product_type_attribute}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_type_id', 'attribute_id'], 'required'],
            [
                [
                    'product_type_id',
                    'attribute_id',
                    'view_attribute_at_product_preview',
                    'sort',
                    'sort_at_product_sku_page',
                ],
                'integer'
            ],
            [['view_attribute_at_product_preview'], 'default', 'value' => 0],
            [['sort', 'sort_at_product_sku_page'], 'default', 'value' => 1],
            [['product_type_id', 'attribute_id'], 'unique', 'targetAttribute' => ['product_type_id', 'attribute_id']],
            [
                ['attribute_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => EavAttributeEntity::class,
                'targetAttribute' => ['attribute_id' => 'id']
            ],
            [
                ['product_type_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ProductType::class,
                'targetAttribute' => ['product_type_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_type_id' => 'Product Type ID',
            'attribute_id' => 'Attribute ID',
            'view_attribute_at_product_preview' => 'View attribute at product preview',
            'sort' => 'Sort',
            'sort_at_product_sku_page' => 'Sort at product sku page',
        ];
    }

    public function getProductType()
    {
        return $this->hasOne(ProductType::class, ['id' => 'product_type_id']);
    }

    public function getAttributeData()
    {
        return $this->hasOne(EavAttributeEntity::class, ['id' => 'attribute_id']);
    }
}
