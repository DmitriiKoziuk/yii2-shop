<?php
namespace DmitriiKoziuk\yii2Shop\repositories;

use DmitriiKoziuk\yii2Base\repositories\ActiveRecordRepository;
use DmitriiKoziuk\yii2Shop\entities\SupplierProductSku;

class SupplierProductSkuRepository extends ActiveRecordRepository
{
    public function getProductSku(int $supplierId, int $productSkuId): SupplierProductSku
    {
        /** @var SupplierProductSku|null $supplierProductSku */
        $supplierProductSku = SupplierProductSku::find()
            ->where([
                'supplier_id' => $supplierId, 'product_sku_id' => $productSkuId
            ])->one();
        return $supplierProductSku;
    }
}