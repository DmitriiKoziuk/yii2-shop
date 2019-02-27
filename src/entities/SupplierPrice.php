<?php

namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\behaviors\TimestampBehavior;
use DmitriiKoziuk\yii2Base\BaseModule;
use DmitriiKoziuk\yii2Shop\ShopModule;

/**
 * This is the model class for table "dk_shop_supplier_prices".
 *
 * @property int $id
 * @property int $supplier_id
 * @property int $job_id
 * @property int $created_at
 */
class SupplierPrice extends \yii\db\ActiveRecord
{
    const FILE_ENTITY_NAME = 'dk-shop-supplier-price';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dk_shop_supplier_prices}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['supplier_id'], 'required'],
            [['supplier_id', 'created_at'], 'integer'],
            [['job_id'], 'string'],
            [['job_id'], 'default', 'value' => null],
            [
                ['supplier_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Supplier::class,
                'targetAttribute' => ['supplier_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => Yii::t(BaseModule::TRANSLATE, 'ID'),
            'supplier_id' => Yii::t(ShopModule::TRANSLATION_SUPPLIER, 'Supplier ID'),
            'job_id'      => Yii::t(ShopModule::TRANSLATION_SUPPLIER, 'Job ID'),
            'created_at'  => Yii::t(BaseModule::TRANSLATE, 'Created at'),
        ];
    }
}
