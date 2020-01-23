<?php

namespace DmitriiKoziuk\yii2Shop\entities;

use Exception;

/**
 * This is the model class for table "{{%dk_shop_eav_attributes}}".
 *
 * @property int $id
 * @property string $name
 * @property string $name_for_product
 * @property string $name_for_filter
 * @property string $code
 * @property string $storage_type
 * @property int $selectable
 * @property int $multiple
 * @property int $view_at_frontend_faceted_navigation
 * @property string $description_backend
 * @property string $description_frontend
 * @property int $value_type_id
 * @property int $default_value_type_unit_id
 *
 * @property EavValueTypeEntity $valueType
 * @property EavValueTypeUnitEntity $defaultValueTypeUnit
 * @property EavValueDoubleEntity[]|EavValueVarcharEntity[] $selectableValues
 * @property EavValueVarcharEntity[]|EavValueTextEntity[]|EavValueDoubleEntity[] $values
 */
class EavAttributeEntity extends \yii\db\ActiveRecord
{
    const STORAGE_TYPE_DOUBLE = 'double';

    const STORAGE_TYPE_VARCHAR = 'varchar';

    const STORAGE_TYPE_TEXT = 'text';

    const SELECTABLE_YES = 1;

    const VIEW_AT_FRONTEND_FACETED_NAVIGATION_NO = 0;
    const VIEW_AT_FRONTEND_FACETED_NAVIGATION_YES = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dk_shop_eav_attributes}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'code', 'storage_type'], 'required'],
            [['storage_type'], 'string'],
            [
                [
                    'selectable',
                    'multiple',
                    'view_at_frontend_faceted_navigation',
                    'value_type_id',
                    'default_value_type_unit_id'
                ],
                'integer'
            ],
            [['name', 'name_for_product', 'name_for_filter'], 'string', 'max' => 100],
            [['code'], 'string', 'max' => 120],
            [['name_for_product', 'name_for_filter'], 'default', 'value' => null],
            [['description_backend', 'description_frontend'], 'string'],
            [['description_backend', 'description_frontend'], 'default', 'value' => null],
            [['selectable', 'multiple', 'view_at_frontend_faceted_navigation'], 'default', 'value' => 0],
            [['name'], 'unique'],
            [['code'], 'unique'],
            [
                ['value_type_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => EavValueTypeEntity::class,
                'targetAttribute' => ['value_type_id' => 'id']
            ],
            [
                ['default_value_type_unit_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => EavValueTypeUnitEntity::class,
                'targetAttribute' => ['default_value_type_unit_id' => 'id']
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
            'name' => 'Name',
            'name_for_product' => 'Name For Product',
            'name_for_filter' => 'Name For Filter',
            'code' => 'Code',
            'storage_type' => 'Storage Type',
            'selectable' => 'Selectable',
            'multiple' => 'Multiple',
            'view_at_frontend_faceted_navigation' => 'View at frontend faceted navigation',
            'description_backend' => 'Description Backend',
            'description_frontend' => 'Description Frontend',
            'value_type_id' => 'Value Type ID',
            'default_value_type_unit_id' => 'Default value type unit id',
        ];
    }

    public function init()
    {
    }

    public function getValueType()
    {
        return $this->hasOne(EavValueTypeEntity::class, ['id' => 'value_type_id']);
    }

    public function getDefaultValueTypeUnit()
    {
        return $this->hasOne(EavValueTypeUnitEntity::class, ['id' => 'default_value_type_unit_id']);
    }

    public function getSelectableValues()
    {
        if ($this->storage_type === self::STORAGE_TYPE_DOUBLE) {
            return $this->hasMany(EavValueDoubleEntity::class, ['attribute_id' => 'id'])
                ->orderBy('value');
        } elseif ($this->storage_type === self::STORAGE_TYPE_VARCHAR) {
            return $this->hasMany(EavValueVarcharEntity::class, ['attribute_id' => 'id'])
                ->orderBy('value');
        } else {
            return null;
        }
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isHasValues(): bool
    {
        switch ($this->storage_type) {
            case EavAttributeEntity::STORAGE_TYPE_DOUBLE:
                $query = EavValueDoubleEntity::find();
                break;
            case EavAttributeEntity::STORAGE_TYPE_VARCHAR:
                $query = EavValueVarcharEntity::find();
                break;
            case EavAttributeEntity::STORAGE_TYPE_TEXT:
                $query = EavValueTextEntity::find();
                break;
            case null:
                return false;
            default:
                throw new Exception("Storage type '$this->storage_type' not exist.");
        }
        $query->where(['attribute_id' => $this->id]);
        return $query->count() == 0 ? false : true;
    }

    public function getValues()
    {
        switch ($this->storage_type) {
            case EavAttributeEntity::STORAGE_TYPE_DOUBLE:
                return $this->hasMany(EavValueDoubleEntity::class, ['attribute_id' => 'id']);
            case EavAttributeEntity::STORAGE_TYPE_VARCHAR:
                return $this->hasMany(EavValueVarcharEntity::class, ['attribute_id' => 'id']);
            case EavAttributeEntity::STORAGE_TYPE_TEXT:
                return $this->hasMany(EavValueTextEntity::class, ['attribute_id' => 'id']);
            default:
                throw new Exception("Storage type '$this->storage_type' not exist.");
        }
    }
}
