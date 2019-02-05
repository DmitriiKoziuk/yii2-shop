<?php
namespace DmitriiKoziuk\yii2Shop\repositories;

use yii\db\Expression;
use DmitriiKoziuk\yii2Base\repositories\EntityRepository;
use DmitriiKoziuk\yii2Shop\entities\Supplier;
use DmitriiKoziuk\yii2Shop\entities\SupplierProductSku;

class SupplierRepository extends EntityRepository
{
    /**
     * @return Supplier[]
     */
    public function getAll(): array
    {
        /** @var Supplier[] $suppliers */
        $suppliers = Supplier::find()->all();
        return $suppliers;
    }

    /**
     * @param int $productSkuId
     * @return Supplier[]
     */
    public function getNonSelectedSuppliersForProductSku(int $productSkuId): array
    {
        $query = Supplier::find();
        $query->leftJoin(
            SupplierProductSku::tableName(),
            [
                SupplierProductSku::tableName() . '.supplier_id' => new Expression(Supplier::tableName() . '.id'),
                SupplierProductSku::tableName() . '.product_sku_id' => $productSkuId,
            ]
        );
        $query->where([
            SupplierProductSku::tableName() . '.supplier_id' => null
        ]);
        /** @var Supplier[] $suppliers */
        $suppliers = $query->all();
        return $suppliers;
    }

    public function getById(int $id): ?Supplier
    {
        /** @var Supplier|null $supplier */
        $supplier = Supplier::find()->where(['id' => $id])->one();
        return $supplier;
    }

    /**
     * @param int $productSkuId
     * @return Supplier[]
     */
    public function getProductSkuSuppliers(int $productSkuId): array
    {
        $query = Supplier::find();
        $query->innerJoin(
            SupplierProductSku::tableName(),
            [
                SupplierProductSku::tableName() . '.supplier_id' => new Expression(Supplier::tableName() . '.id'),
                SupplierProductSku::tableName() . '.product_sku_id' => $productSkuId,
            ]
        );
        /** @var Supplier[] $suppliers */
        $suppliers = $query->all();
        return $suppliers;
    }
}