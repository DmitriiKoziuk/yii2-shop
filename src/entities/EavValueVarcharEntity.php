<?php

namespace DmitriiKoziuk\yii2Shop\entities;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use DmitriiKoziuk\yii2Shop\interfaces\productEav\ProductEavValueInterface;

/**
 * This is the model class for table "dk_shop_eav_value_varchar".
 *
 * @property int $id
 * @property int $attribute_id
 * @property string $value
 * @property string $code
 *
 * @property EavAttributeEntity $eavAttribute
 */
class EavValueVarcharEntity extends ActiveRecord implements ProductEavValueInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dk_shop_eav_value_varchar}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['attribute_id', 'value', 'code'], 'required'],
            [['attribute_id'], 'integer'],
            [['value', 'code'], 'string', 'max' => 255],
            [['value', 'code'], 'trim'],
            [
                ['attribute_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => EavAttributeEntity::class,
                'targetAttribute' => ['attribute_id' => 'id']
            ],
            [['value', 'attribute_id'], 'unique', 'targetAttribute' => ['value', 'attribute_id']],
            [['code', 'attribute_id'], 'unique', 'targetAttribute' => ['code', 'attribute_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'attribute_id' => 'Attribute ID',
            'value' => 'Value',
            'code' => 'Code',
        ];
    }

    public function init()
    {
    }

    public function afterFind()
    {
    }

    public function getEavAttribute(): ActiveQuery
    {
        return $this->hasOne(EavAttributeEntity::class, ['id' => 'attribute_id']);
    }

    public function getRelatedProductSkuNumber()
    {
        return EavValueVarcharProductSkuEntity::find()
            ->where(['value_id' => $this->id])
            ->count();
    }

    public function isUnitSet(): bool
    {
        return false;
    }

    public function getUnitAbbreviation(): string
    {
        return '';
    }

    public function getEavAttributeId(): int
    {
        return $this->eavAttribute->id;
    }

    public function getEavAttributeEntity(): ?EavAttributeEntity
    {
        return $this->eavAttribute;
    }
}
