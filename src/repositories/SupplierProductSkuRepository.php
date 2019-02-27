<?php
namespace DmitriiKoziuk\yii2Shop\repositories;

use DmitriiKoziuk\yii2Base\repositories\AbstractActiveRecordRepository;
use DmitriiKoziuk\yii2Shop\entities\SupplierProductSku;

class SupplierProductSkuRepository extends AbstractActiveRecordRepository
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

    public function getProductSkuBySupplierUniqueProductId(string $supplierProductUniqueId): ?int
    {
        /** @var SupplierProductSku|null $supplierProductSku */
        $supplierProductSku = SupplierProductSku::find()
            ->where(['supplier_product_unique_id' => $supplierProductUniqueId])
            ->one();
        if (empty($supplierProductSku)) {
            return null;
        }
        return $supplierProductSku->product_sku_id;
    }
}