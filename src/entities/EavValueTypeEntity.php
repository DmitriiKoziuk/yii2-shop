<?php

namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;

/**
 * This is the model class for table "{{%dk_shop_eav_value_types}}".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 *
 * @property EavValueTypeUnitEntity[] $units
 * @property string $fullName
 */
class EavValueTypeEntity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dk_shop_eav_value_types}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'code'], 'required'],
            [['name', 'code'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'code' => 'Code',
        ];
    }

    public function init()
    {
    }

    /**
     * @return \yii\db\ActiveQuery|EavValueTypeUnitEntity[]
     */
    public function getUnits()
    {
        return $this->hasMany(EavValueTypeUnitEntity::class, ['value_type_id' => 'id']);
    }

    public function getFullName()
    {
        $r = "{$this->name} (";
        foreach ($this->units as $unit) {
            $r .= $unit->abbreviation . ', ';
        }
        rtrim($r);
        return $r .= ')';
    }
}
