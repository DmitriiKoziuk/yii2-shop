<?php

namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%dk_shop_brands}}".
 *
 * @property int    $id
 * @property string $name
 * @property string $code
 * @property int    $created_at
 * @property int    $updated_at
 */
class Brand extends \yii\db\ActiveRecord
{
    /**
     * @var int
     */
    public $quantity;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dk_shop_brands}}';
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
            [['name', 'code'], 'required'],
            [['name'], 'string', 'max' => 45],
            [['code'], 'string', 'max' => 55],
            [['created_at', 'updated_at', 'quantity'], 'integer'],
            [['name'], 'unique'],
            [['quantity'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'         => Yii::t('app', 'ID'),
            'name'       => Yii::t('app', 'Name'),
            'code'       => Yii::t('app', 'Code'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),
        ];
    }

    public function init()
    {
    }

    public function afterFind()
    {
    }
}
