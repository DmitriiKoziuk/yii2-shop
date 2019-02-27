<?php
namespace DmitriiKoziuk\yii2Shop\repositories;

use DmitriiKoziuk\yii2Base\repositories\AbstractActiveRecordRepository;
use DmitriiKoziuk\yii2Shop\entities\SupplierPrice;

class SupplierPriceRepository extends AbstractActiveRecordRepository
{
    public function getSupplierPriceById(int $id): ?SupplierPrice
    {
        /** @var SupplierPrice|null $supplierPrice */
        $supplierPrice = SupplierPrice::find()->where(['id' => $id])->one();
        return $supplierPrice;
    }
}