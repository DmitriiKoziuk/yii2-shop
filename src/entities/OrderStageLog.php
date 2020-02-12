<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Inflector;
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
    const STATUS_NEW = 1;
    const STATUS_IN_WORK = 2;
    const STATUS_DONE = 3;
    const STATUS_SUSPEND = 4;
    const STATUS_DELETED = 5;

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
            'id' => Yii::t(ShopModule::TRANSLATION_ORDER_STAGES, 'ID'),
            'order_id' => Yii::t(ShopModule::TRANSLATION_ORDER_STAGES, 'Order ID'),
            'user_id' => Yii::t(ShopModule::TRANSLATION_ORDER_STAGES, 'User ID'),
            'stage_id' => Yii::t(ShopModule::TRANSLATION_ORDER_STAGES, 'Stage ID'),
            'comment' => Yii::t(ShopModule::TRANSLATION_ORDER_STAGES, 'Comment'),
            'created_at' => Yii::t(ShopModule::TRANSLATION_ORDER_STAGES, 'Created At'),
        ];
    }

    public function init()
    {
    }

    public function afterFind()
    {
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_NEW     => Yii::t(ShopModule::TRANSLATION_ORDER_STAGES, 'New'),
            self::STATUS_IN_WORK => Yii::t(ShopModule::TRANSLATION_ORDER_STAGES, 'In work'),
            self::STATUS_DONE    => Yii::t(ShopModule::TRANSLATION_ORDER_STAGES, 'Done'),
            self::STATUS_SUSPEND => Yii::t(ShopModule::TRANSLATION_ORDER_STAGES, 'Suspended'),
            self::STATUS_DELETED => Yii::t(ShopModule::TRANSLATION_ORDER_STAGES, 'Deleted'),
        ];
    }

    public function getStatusName(): string
    {
        return self::getStatuses()[ $this->stage_id ];
    }

    public function getStatusCode(): string
    {
        return Inflector::slug(self::getStatuses()[ $this->stage_id ]);
    }
}