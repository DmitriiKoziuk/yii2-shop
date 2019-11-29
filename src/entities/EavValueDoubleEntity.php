<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\entities;

use yii\db\ActiveRecord;
use DmitriiKoziuk\yii2Shop\interfaces\productEav\ProductEavValueInterface;

/**
 * This is the model class for table "{{%dk_shop_eav_value_double}}".
 *
 * @property int $id
 * @property int $attribute_id
 * @property double $value
 * @property string $code
 * @property int $value_type_unit_id
 *
 * @property EavAttributeEntity $eavAttribute
 * @property EavValueTypeUnitEntity $unit
 */
class EavValueDoubleEntity extends ActiveRecord implements ProductEavValueInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dk_shop_eav_value_double}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['attribute_id', 'value'], 'required'],
            [['attribute_id', 'value_type_unit_id'], 'integer'],
            [['value'], 'number'],
            [['value', 'code'], 'string', 'max' => 45],
            [
                ['attribute_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => EavAttributeEntity::class,
                'targetAttribute' => ['attribute_id' => 'id']
            ],
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
            'value_type_unit_id' => 'Value type unit id',
        ];
    }

    public function getEavAttribute()
    {
        return $this->hasOne(EavAttributeEntity::class, ['id' => 'attribute_id']);
    }

    public function getUnit()
    {
        return $this->hasOne(EavValueTypeUnitEntity::class, ['id' => 'value_type_unit_id']);
    }

    public function getRelatedProductSkuNumber()
    {
        return EavValueDoubleProductSkuEntity::find()
            ->where(['value_id' => $this->id])
            ->count();
    }

    public function getEavAttributeId(): int
    {
        return $this->eavAttribute->id;
    }
}
