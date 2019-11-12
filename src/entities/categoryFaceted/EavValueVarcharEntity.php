<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\entities\categoryFaceted;

class EavValueVarcharEntity extends \DmitriiKoziuk\yii2Shop\entities\EavValueVarcharEntity
{
    public $count;

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [
            [['count'], 'safe'],
        ];
        return $rules;
    }
}