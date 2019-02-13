<?php
namespace DmitriiKoziuk\yii2Shop\data;

use DmitriiKoziuk\yii2Shop\entities\ProductType;

class ProductTypeData
{
    private $_productTypeRecord;

    public function __construct(ProductType $productTypeRecord)
    {
        $this->_productTypeRecord = $productTypeRecord;
    }

    public function getId()
    {
        return $this->_productTypeRecord->id;
    }

    public function getName()
    {
        return $this->_productTypeRecord->name;
    }

    public function getMarginStrategy()
    {
        return $this->_productTypeRecord->margin_strategy;
    }

    public function getProductSkuMetaTitleTemplate(): ?string
    {
        return $this->_productTypeRecord->product_sku_title_template;
    }

    public function getProductSkuMetaDescriptionTemplate(): ?string
    {
        return $this->_productTypeRecord->product_sku_description_template;
    }
}