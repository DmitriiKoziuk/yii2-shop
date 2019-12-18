<?php

namespace DmitriiKoziuk\yii2Shop\entities;

use yii\db\ActiveRecord;
use DmitriiKoziuk\yii2Shop\interfaces\productEav\ProductEavValueInterface;

/**
 * This is the model class for table "{{%dk_shop_eav_value_text}}".
 *
 * @property int $id
 * @property int $attribute_id
 * @property string $value
 *
 * @property EavAttributeEntity $eavAttribute
 */
class EavValueTextEntity extends ActiveRecord implements ProductEavValueInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dk_shop_eav_value_text}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['attribute_id', 'value'], 'required'],
            [['attribute_id'], 'integer'],
            [['value'], 'string'],
            [['attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => EavAttributeEntity::class, 'targetAttribute' => ['attribute_id' => 'id']],
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
        ];
    }

    public function getEavAttribute()
    {
        return $this->hasOne(EavAttributeEntity::class, ['id' => 'attribute_id']);
    }

    public function getRelatedProductSkuNumber()
    {
        return EavValueTextProductSkuEntity::find()
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
