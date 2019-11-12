<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\entities\categoryFaceted;


class EavValueDoubleEntity extends \DmitriiKoziuk\yii2Shop\entities\EavValueDoubleEntity
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