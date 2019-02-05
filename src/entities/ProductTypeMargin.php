<?php

namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use DmitriiKoziuk\yii2Shop\ShopModule;

/**
 * This is the model class for table "{{%dk_shop_product_type_margins}}".
 *
 * @property int    $product_type_id
 * @property int    $currency_id
 * @property int    $type
 * @property string $value
 * @property int    $created_at
 * @property int    $updated_at
 *
 * @property Currency    $currency
 * @property ProductType $productType
 */
class ProductTypeMargin extends ActiveRecord
{
    const MARGIN_TYPE_SUM     = 1;
    const MARGIN_TYPE_PERCENT = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dk_shop_product_type_margins}}';
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
            [['product_type_id', 'currency_id', 'type', 'value'], 'required'],
            [['product_type_id', 'currency_id', 'type', 'created_at', 'updated_at'], 'integer'],
            [['value'], 'number'],
            [['value'], 'default', 'value' => 0.00],
            [
                ['product_type_id', 'currency_id'],
                'unique',
                'targetAttribute' => ['product_type_id', 'currency_id']
            ],
            [
                ['currency_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Currency::class,
                'targetAttribute' => ['currency_id' => 'id']
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
            'product_type_id' => Yii::t('product-type', 'Product type id'),
            'currency_id'     => Yii::t('product-type', 'Currency id'),
            'type'            => Yii::t('product-type', 'Margin type'),
            'value'           => Yii::t('product-type', 'Value'),
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
    public function getProductType()
    {
        return $this->hasOne(ProductType::class, ['id' => 'product_type_id']);
    }

    public static function getTypes()
    {
        return [
            static::MARGIN_TYPE_SUM     => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Sum'),
            static::MARGIN_TYPE_PERCENT => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Percent'),
        ];
    }
}
