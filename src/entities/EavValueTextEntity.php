<?php

namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;

/**
 * This is the model class for table "{{%dk_shop_eav_value_text}}".
 *
 * @property int $id
 * @property int $attribute_id
 * @property string $value
 *
 * @property EavAttributeEntity $eavAttribute
 */
class EavValueTextEntity extends \yii\db\ActiveRecord
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
}
