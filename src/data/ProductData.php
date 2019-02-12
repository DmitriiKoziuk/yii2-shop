<?php
namespace DmitriiKoziuk\yii2Shop\data;

use DmitriiKoziuk\yii2Shop\entities\Product;

class ProductData
{
    private $_productRecord;

    public function __construct(Product $productRecord)
    {
        $this->_productRecord = $productRecord;
    }
}