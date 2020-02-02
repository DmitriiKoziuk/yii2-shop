<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\forms\eav;

use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharSeoValueEntity;

class EavVarcharSeoValuesCompositeForm extends \yii\base\Model
{
    /**
     * @var EavValueVarcharSeoValueEntity[]
     */
    public $seoValues;

    public function rules()
    {
        return [
            ['seoValues', 'safe'],
            ['seoValues', 'required'],
            ['seoValues', function ($attribute) {
                foreach ($this->$attribute as $code => $value) {
                    if (! $value instanceof EavValueVarcharSeoValueEntity) {
                        $this->addError($attribute, "All values must by instance of 'EavValueVarcharSeoValueEntity'");
                    }
                    if (! $value->validate(['code'])) {
                        $this->addError($attribute, "Item with code '{$code}' not valid.");
                    }
                }
            }],
        ];
    }

    public function load($data, $formName = null)
    {
        if (parent::load($data, $formName)) {
            $values = [];
            foreach ($this->seoValues as $seoValue) {
                $values[ $seoValue['code'] ] = new EavValueVarcharSeoValueEntity([
                    'code' => $seoValue['code'],
                    'value' => $seoValue['value'],
                ]);
            }
            $this->seoValues = $values;
            unset($values);
            return true;
        }
        return false;
    }
}