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

    public function getId(): int
    {
        return $this->_productRecord->id;
    }

    public function getName(): string
    {
        return $this->_productRecord->name;
    }
}