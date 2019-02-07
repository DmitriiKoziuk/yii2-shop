<?php
namespace DmitriiKoziuk\yii2Shop\data;

use DmitriiKoziuk\yii2Shop\forms\product\ProductMarginCompositeUpdateForm;
use DmitriiKoziuk\yii2Shop\forms\product\ProductMarginUpdateForm;

class ProductMarginUpdateData
{
    /**
     * @var ProductMarginCompositeUpdateForm
     */
    private $_compositeUpdateForm;

    /**
     * @var CurrencyData[]
     */
    private $_allCurrencyDataList;

    /**
     * @var ProductTypeData
     */
    private $_productTypeData;

    public function __construct(
        ProductMarginCompositeUpdateForm $compositeUpdateForm,
        array $allCurrencyDataList,
        ProductTypeData $productTypeData
    ) {
        $this->_compositeUpdateForm = $compositeUpdateForm;
        $this->_allCurrencyDataList = $allCurrencyDataList;
        $this->_productTypeData = $productTypeData;
    }

    /**
     * @return ProductMarginUpdateForm[]
     */
    public function getUpdateForms(): array
    {
        return $this->_compositeUpdateForm->getForms();
    }

    public function getCurrencyNameById(int $currencyId): string
    {
        return $this->_allCurrencyDataList[ $currencyId ]->getName();
    }

    public function getProductTypeData(): ProductTypeData
    {
        return $this->_productTypeData;
    }
}