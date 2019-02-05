<?php
namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\db\ActiveRecord;
use DmitriiKoziuk\yii2Shop\ShopModule;

/**
 * This is the model class for table "{{%dk_shop_cart_orders}}".
 *
 * @property int    $id
 * @property string $customer_comment
 */
class Order extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dk_shop_orders}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'integer'],
            [['id'], 'unique'],
            [
                ['id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Cart::class,
                'targetAttribute' => ['id' => 'id']
            ],
            [['customer_comment'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t(ShopModule::TRANSLATION_ORDER, 'Cart ID'),
            'customer_comment' => Yii::t(ShopModule::TRANSLATION_ORDER, 'Customer comment'),
        ];
    }
}
