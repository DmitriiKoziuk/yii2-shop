<?php

namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%dk_shop_cart_products}}".
 *
 * @property int $cart_id
 * @property int $product_sku_id
 * @property int $quantity
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Cart $cart
 * @property ProductSku $productSku
 */
class CartProduct extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dk_shop_cart_products}}';
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
            [['cart_id', 'product_sku_id'], 'required'],
            [['cart_id', 'product_sku_id', 'quantity', 'created_at', 'updated_at'], 'integer'],
            [['quantity'], 'default', 'value' => 1],
            [
                ['cart_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Cart::class,
                'targetAttribute' => ['cart_id' => 'id']
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
            'cart_id'        => Yii::t('app', 'Cart ID'),
            'product_sku_id' => Yii::t('app', 'Product Sku ID'),
            'quantity'       => Yii::t('app', 'Quantity'),
            'created_at'     => Yii::t('app', 'Created At'),
            'updated_at'     => Yii::t('app', 'Updated At'),
        ];
    }

    public function init()
    {
    }

    public function getCart(): ActiveQuery
    {
        return $this->hasOne(Cart::class, ['id' => 'cart_id']);
    }

    public function getProductSku(): ActiveQuery
    {
        return $this->hasOne(ProductSku::class, ['id' => 'product_sku_id']);
    }
}
