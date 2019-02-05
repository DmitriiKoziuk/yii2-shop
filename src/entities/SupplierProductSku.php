<?php

namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%dk_shop_supplier_product_sku}}".
 *
 * @property int    $supplier_id
 * @property int    $product_sku_id
 * @property string $supplier_product_unique_id
 * @property int    $quantity
 * @property string $purchase_price
 * @property string $recommended_sell_price
 * @property int    $currency_id
 * @property int    $created_at
 * @property int    $updated_at
 */
class SupplierProductSku extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dk_shop_supplier_product_sku}}';
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
            [['supplier_id', 'product_sku_id'], 'required'],
            [['supplier_product_unique_id'], 'string', 'max' => 45],
            [['supplier_id', 'product_sku_id', 'quantity', 'currency_id', 'created_at', 'updated_at'], 'integer'],
            [['purchase_price', 'recommended_sell_price'], 'number'],
            [['supplier_id', 'product_sku_id'], 'unique', 'targetAttribute' => ['supplier_id', 'product_sku_id']],
            [
                ['currency_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Currency::class,
                'targetAttribute' => ['currency_id' => 'id']
            ],
            [
                ['product_sku_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ProductSku::class,
                'targetAttribute' => ['product_sku_id' => 'id']
            ],
            [
                ['supplier_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Supplier::class,
                'targetAttribute' => ['supplier_id' => 'id']
            ],
            [
                ['supplier_product_unique_id', 'quantity', 'purchase_price', 'recommended_sell_price', 'currency_id'],
                'default',
                'value' => null
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'supplier_id' => Yii::t('app', 'Supplier ID'),
            'product_sku_id' => Yii::t('app', 'Product Sku ID'),
            'supplier_product_unique_id' => Yii::t('app', 'Supplier product unique id'),
            'quantity' => Yii::t('app', 'Quantity'),
            'purchase_price' => Yii::t('app', 'Purchase Price'),
            'recommended_sell_price' => Yii::t('app', 'Recommended Sell Price'),
            'currency_id' => Yii::t('app', 'Currency ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
