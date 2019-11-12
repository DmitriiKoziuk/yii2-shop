<?php

namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;

/**
 * This is the model class for table "{{%dk_shop_eav_value_type_units}}".
 *
 * @property int $id
 * @property int $value_type_id
 * @property string $name
 * @property string $abbreviation
 * @property string $code
 *
 * @property EavValueTypeEntity $valueType
 */
class EavValueTypeUnitEntity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dk_shop_eav_value_type_units}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['value_type_id', 'name', 'abbreviation', 'code'], 'required'],
            [['value_type_id'], 'integer'],
            [['name', 'abbreviation', 'code'], 'string', 'max' => 45],
            [['value_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => EavValueTypeEntity::class, 'targetAttribute' => ['value_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'value_type_id' => 'Value Type ID',
            'name' => 'Name',
            'abbreviation' => 'Abbreviation',
            'code' => 'Code',
        ];
    }

    public function getFullName()
    {
        return "{$this->name} ({$this->abbreviation})";
    }

    public function getValueTypeName()
    {
        return $this->valueType->name;
    }

    public function getValueType()
    {
        return $this->hasOne(EavValueTypeEntity::class, ['id' => 'value_type_id']);
    }
}
