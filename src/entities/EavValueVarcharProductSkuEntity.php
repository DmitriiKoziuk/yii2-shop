<?php

namespace DmitriiKoziuk\yii2Shop\entities;

/**
 * This is the model class for table "dk_shop_eav_value_varchar_product_sku".
 *
 * @property int $value_id
 * @property int $product_sku_id
 */
class EavValueVarcharProductSkuEntity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dk_shop_eav_value_varchar_product_sku}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['value_id', 'product_sku_id'], 'required'],
            [['value_id', 'product_sku_id'], 'integer'],
            [['value_id', 'product_sku_id'], 'unique', 'targetAttribute' => ['value_id', 'product_sku_id']],
            [['product_sku_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductSku::class, 'targetAttribute' => ['product_sku_id' => 'id']],
            [['value_id'], 'exist', 'skipOnError' => true, 'targetClass' => EavValueVarcharEntity::class, 'targetAttribute' => ['value_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'value_id' => 'Value ID',
            'product_sku_id' => 'Product Sku ID',
        ];
    }

    public function init()
    {
    }

    public function afterFind()
    {
    }
}
