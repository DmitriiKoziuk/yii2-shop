<?php
namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use DmitriiKoziuk\yii2Shop\ShopModule;

/**
 * This is the model class for table "{{%dk_shop_currencies}}".
 *
 * @property integer $id
 * @property string  $code
 * @property string  $name
 * @property string  $symbol
 * @property string  $rate
 * @property integer $created_at
 * @property integer $updated_at
 */
class Currency extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%dk_shop_currencies}}';
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
            [['code', 'name', 'symbol'], 'required'],
            [['code', 'name', 'symbol'], 'string', 'max' => 25],
            [['rate'], 'number'],
            [['created_at', 'updated_at'], 'integer'],
            [['rate'], 'default', 'value' => '1.00'],
            [['code', 'name', 'symbol', 'rate'], 'trim'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => Yii::t(ShopModule::TRANSLATION_CURRENCY, 'ID'),
            'code'       => Yii::t(ShopModule::TRANSLATION_CURRENCY, 'Code'),
            'name'       => Yii::t(ShopModule::TRANSLATION_CURRENCY, 'Name'),
            'symbol'     => Yii::t(ShopModule::TRANSLATION_CURRENCY, 'Symbol'),
            'rate'       => Yii::t(ShopModule::TRANSLATION_CURRENCY, 'Rate'),
            'created_at' => Yii::t(ShopModule::TRANSLATION_CURRENCY, 'Created at'),
            'updated_at' => Yii::t(ShopModule::TRANSLATION_CURRENCY, 'Updated at'),
        ];
    }

    public function init()
    {
    }
}
