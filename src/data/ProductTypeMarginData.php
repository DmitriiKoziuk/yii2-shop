<?php
namespace DmitriiKoziuk\yii2Shop\data;

use DmitriiKoziuk\yii2Shop\entities\ProductTypeMargin;

class ProductTypeMarginData
{
    /**
     * @var ProductTypeMargin
     */
    private $_productTypeMarginRecord;

    public function __construct(ProductTypeMargin $productTypeMarginRecord)
    {
        $this->_productTypeMarginRecord = $productTypeMarginRecord;
    }

    public function getMarginType()
    {
        return $this->_productTypeMarginRecord->margin_type;
    }

    public function getMarginValue(): float
    {
        return $this->_productTypeMarginRecord->margin_value;
    }
}