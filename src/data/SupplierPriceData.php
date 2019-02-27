<?php
namespace DmitriiKoziuk\yii2Shop\data;

use DmitriiKoziuk\yii2Shop\entities\SupplierPrice;

class SupplierPriceData
{
    private $_supplierPriceRecord;

    public function __construct(SupplierPrice $supplierPriceRecord)
    {
        $this->_supplierPriceRecord = $supplierPriceRecord;
    }

    public function getId(): int
    {
        return $this->_supplierPriceRecord->id;
    }

    public function getSupplierId(): int
    {
        return $this->_supplierPriceRecord->supplier_id;
    }
}