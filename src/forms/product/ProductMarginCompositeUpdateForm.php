<?php
namespace DmitriiKoziuk\yii2Shop\forms\product;

class ProductMarginCompositeUpdateForm
{
    /**
     * @var ProductMarginUpdateForm[]
     */
    private $_productMarginUpdateForms;

    public function __construct(array $productMarginUpdateForms = [])
    {
        $this->_productMarginUpdateForms = $productMarginUpdateForms;
    }

    public function load(array $data): bool
    {
        foreach ($data as $element) {
            $this->_productMarginUpdateForms[] = new ProductMarginUpdateForm([
                'product_type_id' => $element['product_type_id'],
                'currency_id' => $element['currency_id'],
                'margin_type' => $element['margin_type'],
                'margin_value' => $element['margin_value'],
            ]);
        }
        return true;
    }

    /**
     * @return ProductMarginUpdateForm[]
     */
    public function getForms(): array
    {
        return $this->_productMarginUpdateForms;
    }
}