<?php
namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use DmitriiKoziuk\yii2Shop\ShopModule;
use DmitriiKoziuk\yii2UserManager\entities\User;

/**
 * This is the model class for table "{{%dk_shop_order_logs}}".
 *
 * @property int    $id
 * @property int    $order_id
 * @property int    $user_id
 * @property int    $stage_id
 * @property string $comment
 * @property int    $created_at
 */
class OrderStageLog extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dk_shop_order_stage_logs}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'stage_id'], 'required'],
            [['order_id', 'user_id', 'stage_id', 'created_at'], 'integer'],
            [['comment'], 'string'],
            [
                ['order_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Order::class,
                'targetAttribute' => ['order_id' => 'id']
            ],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t(ShopModule::TRANSLATION_ORDER, 'ID'),
            'order_id' => Yii::t(ShopModule::TRANSLATION_ORDER, 'Order ID'),
            'user_id' => Yii::t(ShopModule::TRANSLATION_ORDER, 'User ID'),
            'stage_id' => Yii::t(ShopModule::TRANSLATION_ORDER, 'Stage ID'),
            'comment' => Yii::t(ShopModule::TRANSLATION_ORDER, 'Comment'),
            'created_at' => Yii::t(ShopModule::TRANSLATION_ORDER, 'Created At'),
        ];
    }

    public function init()
    {
    }

    public function afterFind()
    {
    }
}