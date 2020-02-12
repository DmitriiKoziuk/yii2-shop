<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use DmitriiKoziuk\yii2Shop\ShopModule;

/**
 * This is the model class for table "{{%dk_shop_cart_orders}}".
 *
 * @property int    $id
 * @property string $customer_comment
 *
 * @property Cart            $cart
 * @property OrderStageLog[] $fullStageList
 * @property OrderStageLog   $currentStage
 * @property OrderStageLog   $firstStage
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

    public function init()
    {
    }

    public function afterFind()
    {
    }

    public function getCart(): ActiveQuery
    {
        return $this->hasOne(Cart::class, ['id' => 'id']);
    }

    public function getFullStageList(): ActiveQuery
    {
        return $this->hasMany(OrderStageLog::class, ['order_id' => 'id'])
            ->orderBy(['created_at' => SORT_DESC]);
    }

    public function getCurrentStage(): ActiveQuery
    {
        return $this->hasOne(OrderStageLog::class, ['order_id' => 'id'])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(1);
    }

    public function getFirstStage(): ActiveQuery
    {
        return $this->hasOne(OrderStageLog::class, ['order_id' => 'id'])
            ->orderBy(['created_at' => SORT_ASC])
            ->limit(1);
    }
}
