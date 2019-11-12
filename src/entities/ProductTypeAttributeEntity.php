<?php

namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;

/**
 * This is the model class for table "dk_shop_product_type_attribute".
 *
 * @property int $product_type_id
 * @property int $attribute_id
 *
 * @property ProductType $productType
 * @property EavAttributeEntity $attributeData
 */
class ProductTypeAttributeEntity extends \yii\db\ActiveRecord
{
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
            [['product_type_id', 'attribute_id'], 'integer'],
            [['product_type_id', 'attribute_id'], 'unique', 'targetAttribute' => ['product_type_id', 'attribute_id']],
            [['attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => EavAttributeEntity::class, 'targetAttribute' => ['attribute_id' => 'id']],
            [['product_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductType::class, 'targetAttribute' => ['product_type_id' => 'id']],
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
