<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%dk_shop_carts}}".
 *
 * @property int    $id
 * @property string $key
 * @property int    $created_at
 * @property int    $updated_at
 * @property int    $customer_id
 *
 * @property Customer      $customer
 * @property CartProduct[] $cartProductSkus
 * @property ProductSku[]  $orderedProductSkus
 */
class Cart extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dk_shop_carts}}';
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
            [['key'], 'required'],
            [['created_at', 'updated_at', 'customer_id'], 'integer'],
            [['key'], 'string', 'max' => 32],
            [['customer_id'], 'default', 'value' => NULL],
            [
                ['customer_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Customer::class,
                'targetAttribute' => ['customer_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => Yii::t('app', 'ID'),
            'key'         => Yii::t('app', 'Key'),
            'created_at'  => Yii::t('app', 'Created At'),
            'updated_at'  => Yii::t('app', 'Updated At'),
            'customer_id' => Yii::t('app', 'Customer ID'),
        ];
    }

    public function init()
    {
    }

    public function afterFind()
    {
    }

    public function getCustomer(): ActiveQuery
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    public function getCartProductSkus(): ActiveQuery
    {
        return $this->hasMany(CartProduct::class, ['cart_id' => 'id']);
    }

    public function getTotalProducts(): int
    {
        $total = 0;
        foreach ($this->cartProductSkus as $product) {
            $total += $product->quantity;
        }
        return $total;
    }

    public function getTotalPrice(): float
    {
        $price = 0;
        foreach ($this->cartProductSkus as $cartProduct) {
            if ($cartProduct->productSku->isCurrencySet()) {
                $price += $cartProduct->quantity * $cartProduct->productSku->getCustomerPrice();
            }
        }
        return $price;
    }

    /**
     * @return ProductSku[]
     */
    public function getOrderedProducts(): array
    {
        $products = [];
        foreach ($this->cartProductSkus as $cartProduct) {
            $products[] = $cartProduct->productSku;
        }
        return $products;
    }

    public function getProductTypes(): array
    {
        $types = [];
        foreach ($this->cartProductSkus as $cartProduct) {
            $types[] = $cartProduct->productSku->getTypeName();
        }
        return $types;
    }
}
