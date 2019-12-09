<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\widgets\frontend;

use yii\base\Widget;
use DmitriiKoziuk\yii2Shop\interfaces\productEav\ProductEavValueInterface;

class ProductSkuViewAttributesWidget extends Widget
{
    /**
     * @var ProductEavValueInterface[]
     */
    public $values;

    public function run(): string
    {
        if (! empty($this->values)) {
            $attributes = [];
            $values = [];
            foreach ($this->values as $value) {
                $attributes[ $value->getEavAttributeId() ] = $value->getEavAttributeEntity();
            }
            foreach ($this->values as $value) {
                $values[ $value->getEavAttributeId() ] = [];
                array_push($values[ $value->getEavAttributeId() ], $value);
            }
            return $this->render('product-sku-view-attributes', [
                'attributes' => $attributes,
                'values' => $values,
            ]);
        }
        return '';
    }
}
