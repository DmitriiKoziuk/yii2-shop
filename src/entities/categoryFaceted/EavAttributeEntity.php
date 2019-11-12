<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\entities\categoryFaceted;

use DmitriiKoziuk\yii2Shop\entities\EavAttributeEntity AS Attribute;

class EavAttributeEntity extends Attribute
{
    /**
     * @var EavValueDoubleEntity[]|EavValueVarcharEntity[]
     */
    public $values;

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [
            [['values'], 'safe'],
        ];
        return $rules;
    }
}