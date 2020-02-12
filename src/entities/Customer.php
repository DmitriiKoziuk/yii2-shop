<?php

namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%dk_shop_customers}}".
 *
 * @property integer $id
 * @property string  $first_name
 * @property string  $middle_name
 * @property string  $last_name
 * @property string  $phone_number
 * @property string  $email
 * @property string  $password_hash
 * @property string  $password_reset_token
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Cart[] $carts
 */
class Customer extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%dk_shop_customers}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first_name', 'phone_number'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['first_name', 'middle_name', 'last_name', 'phone_number'], 'string', 'max' => 45],
            [['email', 'password_hash', 'password_reset_token'], 'string', 'max' => 255],
            [['email'], 'default', 'value' => null],
            [['first_name', 'middle_name', 'last_name', 'phone_number', 'email'], 'trim'],
            [['password_hash'], 'default', 'value' => Yii::$app->security->generateRandomString()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                   => Yii::t('customer', 'ID'),
            'first_name'           => Yii::t('customer', 'First Name'),
            'middle_name'          => Yii::t('customer', 'Middle Name'),
            'last_name'            => Yii::t('customer', 'Last Name'),
            'phone_number'         => Yii::t('customer', 'Phone Number'),
            'email'                => Yii::t('customer', 'Email'),
            'password_hash'        => Yii::t('customer', 'Password Hash'),
            'password_reset_token' => Yii::t('customer', 'Password Reset Token'),
            'created_at'           => Yii::t('customer', 'Created At'),
            'updated_at'           => Yii::t('customer', 'Updated At'),
        ];
    }

    public function init()
    {
    }

    public function afterFind()
    {
    }

    public function getCarts(): ActiveQuery
    {
        return $this->hasMany(Cart::class, ['customer_id' => 'id']);
    }

    public function getPhoneNumber(): string
    {
        $phoneNumber = $this->phone_number;
        $digitsNumber = mb_strlen($phoneNumber);
        switch ($digitsNumber) {
            case 13:
                return preg_replace(
                    "/([+]{1})([0-9]{3})([0-9]{2})([0-9]{3})([0-9]{2})([0-9]{2})/",
                    "$1$2 ($3) $4-$5-$6",
                    $phoneNumber
                );
                break;
            case 10:
                return preg_replace(
                    "/([0-9]{3})([0-9]{3})([0-9]{2})([0-9]{2})/",
                    "($1) $2-$3-$4",
                    $phoneNumber
                );
                break;
            default:
                return $phoneNumber;
                break;
        }
    }
}
