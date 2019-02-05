<?php
namespace DmitriiKoziuk\yii2Shop\services\supplier;

use yii\db\Connection;
use DmitriiKoziuk\yii2Base\services\EntityActionService;
use DmitriiKoziuk\yii2Shop\entities\SupplierProductSku;
use DmitriiKoziuk\yii2Shop\data\SupplierData;
use DmitriiKoziuk\yii2Shop\repositories\SupplierRepository;
use DmitriiKoziuk\yii2Shop\repositories\SupplierProductSkuRepository;

final class SupplierService extends EntityActionService
{
    /**
     * @var SupplierRepository
     */
    private $_supplierRepository;

    /**
     * @var SupplierProductSkuRepository
     */
    private $_supplierProductSkuRepository;

    public function __construct(
        SupplierRepository $supplierRepository,
        SupplierProductSkuRepository $supplierProductSkuRepository,
        Connection $db = null
    ) {
        parent::__construct($db);
        $this->_supplierRepository = $supplierRepository;
        $this->_supplierProductSkuRepository = $supplierProductSkuRepository;
    }

    /**
     * @param int $productSkuID
     * @return SupplierData[]
     */
    public function getNonSelectedSuppliers(int $productSkuID): array
    {
        $suppliers = $this->_supplierRepository->getNonSelectedSuppliersForProductSku($productSkuID);
        $tmp = [];
        foreach ($suppliers as $supplier) {
            $tmp[] = new SupplierData($supplier);
        }
        return $tmp;
    }

    public function addProductSkuToSuppliers(array $suppliers, int $productSkuId): void
    {
        foreach ($suppliers as $supplierId => $value) {
            $this->addProductSku($supplierId, $productSkuId);
        }
    }

    public function addProductSku(int $supplierId, int $productSkuId): SupplierProductSku
    {
        $relation = new SupplierProductSku();
        $relation->supplier_id = $supplierId;
        $relation->product_sku_id = $productSkuId;
        $this->_supplierProductSkuRepository->save($relation);
        return $relation;
    }
}