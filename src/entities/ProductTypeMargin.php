<?php

namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%dk_shop_product_type_margins}}".
 *
 * @property int    $product_type_id
 * @property int    $currency_id
 * @property int    $margin_type
 * @property string $margin_value
 * @property int    $created_at
 * @property int    $updated_at
 */
class ProductTypeMargin extends \yii\db\ActiveRecord
{
    const MARGIN_TYPE_SUM = 1;
    const MARGIN_TYPE_PERCENT = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dk_shop_product_type_margins}}';
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
            [['product_type_id', 'currency_id', 'margin_type', 'margin_value'], 'required'],
            [['product_type_id', 'currency_id', 'margin_type', 'created_at', 'updated_at'], 'integer'],
            [['margin_value'], 'number'],
            [['product_type_id', 'currency_id'], 'unique', 'targetAttribute' => ['product_type_id', 'currency_id']],
            [
                ['currency_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Currency::class,
                'targetAttribute' => ['currency_id' => 'id']
            ],
            [
                ['product_type_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ProductType::class,
                'targetAttribute' => ['product_type_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_type_id' => Yii::t('app', 'Product Type ID'),
            'currency_id'     => Yii::t('app', 'Currency ID'),
            'margin_type'     => Yii::t('app', 'Margin Type'),
            'margin_value'    => Yii::t('app', 'Margin Value'),
            'created_at'      => Yii::t('app', 'Created At'),
            'updated_at'      => Yii::t('app', 'Updated At'),
        ];
    }
}
