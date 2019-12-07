<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\forms\eav;

use yii\base\Model;

class EavAttributeCreateForm extends Model
{
    public $name;
    public $storage_type;
    public $selectable;
    public $multiple;
    public $value_type_id;

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['storage_type'], 'string'],
            [['selectable', 'multiple', 'value_type_id'], 'integer'],
            [['selectable', 'multiple'], 'default', 'value' => 0],
        ];
    }
}
