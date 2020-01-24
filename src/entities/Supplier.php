<?php

namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%dk_shop_suppliers}}".
 *
 * @property int $id
 * @property string $name
 * @property string $phone_number
 * @property string $email
 * @property string $info
 * @property int $created_at
 * @property int $updated_at
 */
class Supplier extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dk_shop_suppliers}}';
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
            [['name', 'phone_number'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['name', 'phone_number', 'email'], 'string', 'max' => 45],
            [['info'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'phone_number' => Yii::t('app', 'Phone Number'),
            'email' => Yii::t('app', 'Email'),
            'info' => Yii::t('app', 'Info'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public function init()
    {
    }
}
